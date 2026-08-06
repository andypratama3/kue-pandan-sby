<?php

namespace App\Http\Controllers\Chatbot;

use App\Contracts\WhatsAppProviderInterface;
use App\Http\Controllers\Controller;
use App\Models\ChatbotConversation;
use App\Models\Region;
use App\Services\DeepSeekService;
use App\Services\WhatsApp\WhatsAppReplyService;
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
        $incoming = $this->provider->parseIncoming($request->all());

        if (empty($incoming['sender'])) {
            return response()->json(['status' => true]);
        }

        $region = $this->resolveRegion($incoming['sender']);
        $context = $this->replyService->buildContext($region?->id);

        $reply = $this->resolveReply($incoming, $context);
        $handledByAi = ! empty($context['handled_by_ai']);

        try {
            $this->provider->sendTyping($incoming['sender']);
        } catch (\Throwable $e) {
            Log::channel('whatsapp')->warning('sendTyping gagal: '.$e->getMessage());
        }

        $this->provider->sendMessage($incoming['sender'], $reply, $incoming);

        ChatbotConversation::create([
            'provider' => config('services.whatsapp.provider', 'fonnte'),
            'sender_number' => $incoming['sender'],
            'sender_name' => $incoming['name'],
            'region_id' => $region?->id,
            'incoming_message' => $incoming['text'] ?? '',
            'detected_intent' => $context['intent'] ?? null,
            'bot_reply' => $reply,
            'handled_by_ai' => $handledByAi,
        ]);

        return response()->json(['status' => true]);
    }

    protected function resolveRegion(string $sender): ?Region
    {
        $last = ChatbotConversation::where('sender_number', $sender)
            ->orderByDesc('id')
            ->first();

        if ($last?->region_id) {
            return Region::find($last->region_id);
        }

        return Region::orderBy('id')->first();
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
