<?php

namespace App\Http\Controllers\Chatbot;

use App\Contracts\WhatsAppProviderInterface;
use App\Http\Controllers\Controller;
use App\Models\ChatbotConversation;
use App\Models\Region;
use App\Models\WhatsAppBusinessNumber;
use App\Services\DeepSeekService;
use App\Services\WhatsApp\WhatsAppConversationStateService;
use App\Services\WhatsApp\WhatsAppReplyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        protected WhatsAppProviderInterface $provider,
        protected WhatsAppReplyService $replyService,
        protected WhatsAppConversationStateService $stateService,
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
        // Meta Cloud API: verifikasi X-Hub-Signature-256 (HMAC-SHA256 dari raw body
        // dengan App Secret). Tanpa secret (belum setup), verifikasi dilewati
        // dengan peringatan di log.
        $this->assertMetaSignature($request);

        $incoming = $this->provider->parseIncoming($request->all());

        if (empty($incoming['sender'])) {
            // Event non-percakapan (statuses, echo, alert, update, dll.) tetap
            // dicatat di log agar seluruh aktivitas webhook Meta terpantau,
            // lalu dijawab 200 tanpa diproses lebih lanjut.
            $this->logNonMessageEvent($request->all());

            return response()->json(['status' => true]);
        }

        // Deduplikasi idempotent: id pesan dari Meta (wamid) sudah diproses ->
        // jawab 200 tanpa memproses ulang, mencegah balasan ganda saat
        // provider retry / redeliver.
        $messageId = $incoming['raw_reply_context']['message_id'] ?? null;
        if (is_string($messageId) && $messageId !== ''
            && ChatbotConversation::where('provider_message_id', $messageId)->exists()) {
            Log::channel('whatsapp')->debug('Webhook mengabaikan pesan duplikat (id sudah diproses)', ['message_id' => $messageId]);

            return response()->json(['status' => true, 'duplicate' => true]);
        }

        // State machine: amati state saat ini (termasuk timeout 24 jam).
        $currentState = $this->stateService->getCurrentState($incoming['sender']);

        // Region ditentukan dari nomor WhatsApp tujuan yang di-hubungi customer.
        $region = $this->resolveRegion($incoming['recipient'] ?? null, $incoming['sender']);

        $context = $this->replyService->buildContext($region?->id);
        $context['current_step'] = $currentState['step'];
        $context['state_context'] = $currentState['context'] ?? [];

        $reply = $this->resolveReply($incoming, $context);
        $handledByAi = (bool) ($context['handled_by_ai'] ?? false);

        $intent = $context['intent'] ?? 'lainnya';
        $nextStep = $this->stateService->determineNextStep(
            $currentState['step'],
            $intent,
            $incoming['text'] ?? ''
        );

        try {
            $this->provider->sendTyping($incoming['sender']);
        } catch (\Throwable $e) {
            Log::channel('whatsapp')->warning('Gagal mengirim indikator mengetik.', ['error' => $e->getMessage()]);
        }

        $data = [];

        if ($reply) {
            // Dispatch WhatsApp message as background job for better performance
            \App\Jobs\SendWhatsAppMessageJob::dispatch(
                $incoming['sender'],
                $reply,
                $incoming['raw_reply_context'] ?? []
            );
            
            // Mark as queued
            $data = ['success' => true, 'queued' => true, 'message_id' => null];
        }

        $conversationId = $this->createConversationRecord(
            $incoming,
            $region?->id,
            $context,
            $reply,
            $handledByAi,
            $messageId,
        );

        // Simpan step berikutnya + konteks aktual ke record percakapan-nya.
        $this->stateService->updateState(
            $incoming['sender'],
            $nextStep,
            $this->persistableContext($context, $intent),
            $conversationId
        );

        if (! $reply) {
            Log::channel('whatsapp')->debug('Server default - reply kosong, tidak memproses pesan.', [
                'text' => $incoming['text'] ?? '',
                'type' => $incoming['type'] ?? '',
            ]);
        }

        $debug = $this->debugBlock(
            $incoming,
            $reply,
            $region,
            $handledByAi,
            $context,
            $data
        );

        Log::channel('whatsapp')->info('Webhook diproses.', $debug);

        return response()->json(['status' => true]);
    }

    // ====== VERIFIKASI SIGNATURE META ======

    /**
     * Verifikasi X-Hub-Signature-256: HMAC-SHA256(raw body, META_APP_SECRET),
         * dibandingkan secara constant-time dengan hash_equals.
     */
    protected function assertMetaSignature(Request $request): void
    {
        $signature = $request->header('X-Hub-Signature-256', '');
        $appSecret = config('services.meta_whatsapp.app_secret');

        // Secret belum dikonfigurasi -> lewati verifikasi (token masih aktif).
        if (! is_string($appSecret) || $appSecret === '') {
            Log::channel('whatsapp')->warning('META_APP_SECRET belum dikonfigurasi; verifikasi tanda tangan webhook dilewati.');

            return;
        }

        $expected = 'sha256='.hash_hmac('sha256', $request->getContent(), $appSecret);

        if (! is_string($signature) || ! hash_equals($expected, $signature)) {
            Log::channel('whatsapp')->warning('Webhook Meta ditolak: X-Hub-Signature-256 tidak valid.', ['ip' => $request->ip()]);

            abort(403);
        }
    }

    // ====== LOG EVENT NON-PESAN ======

    /**
     * Catat event webhook Meta yang bukan percakapan masuk (statuses, echo,
     * account alert, template update, quality update, dll.) ke log whatsapp
     * agar semua aktivitas webhook tetap terpantau meski tidak diproses.
     */
    protected function logNonMessageEvent(array $payload): void
    {
        $entry = $payload['entry'][0] ?? [];
        $change = $entry['changes'][0] ?? [];
        $field = (string) ($change['field'] ?? 'unknown');
        $value = is_array($change['value'] ?? null) ? $change['value'] : [];

        $statuses = [];
        foreach (($value['statuses'] ?? []) as $status) {
            $statuses[] = [
                'message_id' => $status['id'] ?? null,
                'status' => $status['status'] ?? null,
                'recipient_id' => $status['recipient_id'] ?? null,
            ];
        }

        $echoIds = [];
        foreach (($value['messages'] ?? []) as $message) {
            $echoIds[] = $message['id'] ?? null;
        }

        $summary = [];
        foreach ($value as $key => $item) {
            if (in_array($key, ['messages', 'statuses', 'metadata', 'contacts'], true)) {
                continue;
            }
            $summary[$key] = $item;
        }

        Log::channel('whatsapp')->info('Webhook event non-pesan diterima.', [
            'field' => $field,
            'statuses' => $statuses,
            'echoed_message_ids' => $field === 'message_echoes' ? $echoIds : null,
            'event' => $summary,
        ]);
    }

    // ====== REGION RESOLUTION ======

    /**
     * Tentukan region dari nomor WhatsApp tujuan (recipient).
     *
     * Urutan prioritas:
     * 1. Nomor tujuan cocok dengan nomor WhatsApp Business aktif -> region tsb.
     * 2. Riwayat percakapan sender (fallback saat provider tidak mengirim recipient).
     * 3. Region aktif pertama / region default.
     */
    protected function resolveRegion(?string $recipient, ?string $sender): ?Region
    {
        if (is_string($recipient) && $recipient !== '') {
            $normalizedRecipient = $this->normalizePhone($recipient);

            foreach (WhatsAppBusinessNumber::active()->with('region')->get() as $business) {
                if ($business->phone_number
                    && $this->normalizePhone($business->phone_number) === $normalizedRecipient) {
                    return $business->region;
                }
            }
        }

        if (is_string($sender) && $sender !== '') {
            $lastConversation = ChatbotConversation::where('sender_number', $sender)
                ->whereNotNull('region_id')
                ->orderByDesc('id')
                ->first();

            if ($lastConversation?->region_id) {
                return $lastConversation->region;
            }
        }

        return WhatsAppBusinessNumber::active()
            ->whereHas('region')
            ->first()
            ?->region ?? Region::first();
    }

    protected function normalizePhone(string $number): string
    {
        $number = preg_replace('/[^0-9]/', '', $number);

        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        } elseif (strlen($number) >= 9 && str_starts_with($number, '8')) {
            $number = '62'.$number;
        }

        return $number;
    }

    // ====== REPLY ======

    /**
     * Susun balasan. $context dikirim by-ref agar intent/handled_by_ai hasil
     * deteksi ikut terbaca oleh pemanggil.
     */
    protected function resolveReply(array $incoming, array &$context): string
    {
        $text = trim((string) ($incoming['text'] ?? ''));

        // Media (gambar/video/stiker/dokumen) tanpa caption tidak berisi pertanyaan
        // yang bisa dijawab -> balas sopan bahwa bot hanya membalas pesan teks.
        $incomingType = (string) ($incoming['type'] ?? '');
        if (! in_array($incomingType, ['text', 'location'], true) && $text === '') {
            $context['intent'] = 'lainnya';

            return "Maaf, saya hanya bisa membalas pesan teks ya. 😊\n"
                .'Ketik "menu", "harga", "lokasi", atau "cara order" untuk info produk dan outlet.';
        }

        // Intent: prefer DeepSeek bila aktif & terkonfigurasi, else rule-based.
        $intent = null;
        if (config('services.deepseek.enabled', false) && $this->ai?->isConfigured()) {
            $intent = $this->ai->detectIntent($text)['intent'] ?? null;
        }
        $intent = $intent ?: $this->replyService->detectIntent($text);

        $context['handled_by_ai'] = false;
        $context['intent'] = $intent;

        $reply = $this->replyService->buildReply($text, $intent, $context);

        if (config('services.deepseek.enabled', false) && $this->ai?->isConfigured()) {
            try {
                $reply = $this->ai->generateReply($text, array_merge($context, ['draft_reply' => $reply]));
                $context['handled_by_ai'] = true;
            } catch (\Throwable $e) {
                Log::channel('whatsapp')->warning('DeepSeek gagal, fallback ke aturan bot.', ['error' => $e->getMessage()]);
            }
        }

        return is_string($reply) ? $reply : '';
    }

    /**
     * Konteks yang disimpan ke DB (current_step/context_data) — data nyata
     * percakapan ini, bukan salinan state lama.
     */
    protected function persistableContext(array $context, string $intent): array
    {
        return [
            'last_intent' => $intent,
            'last_region_id' => $context['region']?->id,
            'state_context' => $context['state_context'] ?? [],
            'product_names' => collect($context['productData'] ?? [])
                ->pluck('name')
                ->values()
                ->all(),
        ];
    }

    // ====== RECORD & DEBUG ======

    protected function createConversationRecord(
        array $incoming,
        ?int $regionId,
        array $context,
        string $reply,
        bool $handledByAi,
        ?string $messageId,
    ): ?int {
        $row = ChatbotConversation::create([
            'provider' => config('services.whatsapp.provider', 'meta'),
            'sender_number' => $incoming['sender'],
            'sender_name' => $incoming['name'] ?? null,
            'region_id' => $regionId,
            'incoming_message' => $incoming['text'] ?? '',
            'detected_intent' => $context['intent'] ?? null,
            'bot_reply' => $reply,
            'handled_by_ai' => $handledByAi,
            'provider_message_id' => $messageId,
            'current_step' => $context['current_step'] ?? null,
            'context_data' => $this->persistableContext($context, $context['intent'] ?? 'lainnya'),
            'last_interaction_at' => now(),
        ]);

        return $row->id;
    }

    protected function debugBlock(
        array $incoming,
        string $reply,
        ?Region $region,
        bool $handledByAi,
        array $context,
        array $data,
    ): array {
        $text = $incoming['text'] ?? '';

        return [
            'provider' => config('services.whatsapp.provider', 'meta'),
            'text' => $text,
            'reply' => $reply,
            'type' => $incoming['type'] ?? '',
            'sender' => $incoming['sender'],
            'intent' => $context['intent'] ?? null,
            'regionName' => $region?->name,
            'regionId' => $region?->id,
            'handledByAi' => $handledByAi,
            'productDataCount' => count($context['productData'] ?? []),
            'sendSuccess' => $data['success'] ?? null,
            'sendMessageId' => $data['message_id'] ?? null,
            'sendError' => $data['error'] ?? null,
        ];
    }
}