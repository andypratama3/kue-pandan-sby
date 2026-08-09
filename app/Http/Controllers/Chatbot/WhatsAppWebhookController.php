<?php

namespace App\Http\Controllers\Chatbot;

use App\Contracts\WhatsAppProviderInterface;
use App\Http\Controllers\Controller;
use App\Models\ChatbotConversation;
use App\Models\Region;
use App\Services\DeepSeekService;
use App\Services\WhatsApp\WhatsAppReplyService;
use App\Services\WhatsApp\WhatsAppConversationStateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        protected WhatsAppProviderInterface $provider,
        protected WhatsAppReplyService $replyService,
        protected ?DeepSeekService $ai = null,
    ) {}

    public function verify(Request $request)
    {
        $result = $this->provider->verifyWebhook($request->query());

        if ($result && ($result['verified'] ?? false)) {
            return response($result['challenge'] ?? '', 200);
        }

        return response('Forbidden', 403);
    }

    public function handle(Request $request)
    {
        $secret = config('services.whatsapp.webhook_token');

        if (! is_string($secret) || $secret === '') {
            Log::channel('whatsapp')->error('Webhook ditolak: WHATSAPP_WEBHOOK_TOKEN belum dikonfigurasi di .env.');

            abort(403);
        }

        $provided = $request->query('token') ?: $request->header('X-Webhook-Token', '');
        if (! is_string($provided) || ! hash_equals($secret, $provided)) {
            Log::channel('whatsapp')->warning('Webhook ditolak: token tidak valid.', ['ip' => $request->ip()]);

            abort(403);
        }

        $incoming = $this->provider->parseIncoming($request->all());

        if (empty($incoming['sender'])) {
            return response()->json(['status' => true]);
        }

        // Initialize state service
        $stateService = new WhatsAppConversationStateService();
        
        // Get current state
        $currentState = $stateService->getCurrentState($incoming['sender']);
        
        $region = $this->resolveRegion($incoming['sender']);
        $context = $this->replyService->buildContext($region?->id);
        
        // Add state info to context
        $context['current_step'] = $currentState['step'];
        $context['state_context'] = $currentState['context'];

        $reply = $this->resolveReply($incoming, $context);
        $handledByAi = ! empty($context['handled_by_ai']);
        
        // Determine next step
        $intent = $context['intent'] ?? 'lainnya';
        $nextStep = $stateService->determineNextStep(
            $currentState['step'],
            $intent,
            $incoming['text'] ?? ''
        );

        try {
            $this->provider->sendTyping($incoming['sender']);
        } catch (\Throwable $e) {
            Log::channel('whatsapp')->warning('sendTyping gagal: '.$e->getMessage());
        }

        $this->provider->sendMessage($incoming['sender'], $reply, $incoming);

        $conversation = ChatbotConversation::create([
            'provider' => config('services.whatsapp.provider', 'fonnte'),
            'sender_number' => $incoming['sender'],
            'sender_name' => $incoming['name'],
            'region_id' => $region?->id,
            'incoming_message' => $incoming['text'] ?? '',
            'detected_intent' => $intent,
            'current_step' => $nextStep,
            'context_data' => $context['state_context'] ?? [],
            'last_interaction_at' => now(),
            'bot_reply' => $reply,
            'handled_by_ai' => $handledByAi,
        ]);

        return response()->json(['status' => true]);
    }

    protected function resolveRegion(string $sender): ?Region
    {
        // Pertama, cek apakah ada mapping nomor WA Business ke region
        // Nomor WA Business adalah nomor yang dihubungi customer (recipient/to dalam payload)
        // Jika provider kirim info recipient, gunakan itu. Jika tidak, fallback ke histori.
        
        // Untuk sementara, kita gunakan histori sampai payload webhook diperbaiki untuk kirim recipient number
        // TODO: Extract recipient/to number dari webhook payload dan cek di whatsapp_business_numbers
        
        $businessNumber = \App\Models\WhatsAppBusinessNumber::active()
            ->whereHas('region')
            ->first();
            
        if ($businessNumber && $businessNumber->region) {
            return $businessNumber->region;
        }

        // Fallback ke histori sender (backward compatibility)
        $last = ChatbotConversation::where('sender_number', $sender)
            ->orderByDesc('id')
            ->first();

        if ($last?->region_id) {
            return Region::find($last->region_id);
        }

        // Ultimate fallback: region aktif pertama
        return Region::where('is_active', true)->orderBy('id')->first();
    }

    protected function resolveReply(array $incoming, array &$context): string
    {
        $text = $incoming['text'] ?? '';

        if (($incoming['type'] ?? 'text') !== 'text') {
            $context['intent'] = 'media';

            return 'Mohon maaf, saat ini bot baru bisa membaca pesan teks. 🙂 Foto/lokasi '
                .'silakan kirim langsung ke admin/kurir wilayah untuk diproses manual.';
        }

        if (trim($text) === '') {
            $context['intent'] = 'lainnya';

            return $this->replyService->buildReply($text, 'lainnya', $context);
        }

        $intent = $this->replyService->detectIntent($text);
        $context['intent'] = $intent;

        $draft = $this->replyService->buildReply($text, $intent, $context);

        if ($this->ai && $this->ai->isConfigured()) {
            $refined = $this->ai->generateReply($text, array_merge($context, ['draft_reply' => $draft]));
            if (trim($refined) !== '') {
                $context['handled_by_ai'] = true;

                return $refined;
            }
        }

        return $draft;
    }
}
