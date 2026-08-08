<?php

namespace App\Services\WhatsApp;

use App\Contracts\WhatsAppProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteProvider implements WhatsAppProviderInterface
{
    public function sendMessage(string $target, string $message, array $context = []): array
    {
        $token = config('services.fonnte.token');

        if (! $token) {
            Log::channel('whatsapp')->warning('Fonnte token belum dikonfigurasi di .env.');

            return ['success' => false, 'message_id' => null, 'raw' => [], 'error' => 'not_configured'];
        }

        $inboxId = $context['raw_reply_context']['inboxid'] ?? null;
        if (! $inboxId) {
            $inboxId = $context['inboxid'] ?? null;
        }

        $payload = [
            'target' => $this->normalizeNumber($target),
            'message' => $message,
            'typing' => true,
            'duration' => 1,
        ];

        if ($inboxId) {
            $payload['inboxid'] = $inboxId;
        }

        try {
            $response = Http::asForm()
                ->withHeaders(['Authorization' => $token])
                ->timeout(15)
                ->post($this->endpoint(), $payload);

            $body = $response->json() ?? [];

            $ok = $response->successful()
                && in_array($body['status'] ?? false, [true, 'true', 1, '1'], true);

            if (! $ok) {
                Log::channel('whatsapp')->error('Fonnte kirim gagal', [
                    'target' => $target,
                    'http_status' => $response->status(),
                    'body' => $body,
                ]);
            }

            // Merge the raw provider response (e.g. 'status', 'id') alongside
            // our normalized keys so callers can read either shape.
            return array_merge($body, [
                'success' => $ok,
                'message_id' => $body['id'] ?? ($body['data']['id'] ?? null),
                'raw' => $body,
                'error' => $ok ? null : ($body['reason'] ?? 'send_failed'),
            ]);
        } catch (\Throwable $e) {
            Log::channel('whatsapp')->error('Fonnte kirim exception: '.$e->getMessage());

            return ['success' => false, 'message_id' => null, 'raw' => ['exception' => $e->getMessage()], 'error' => 'exception'];
        }
    }

    public function sendTyping(string $target, int $durationSeconds = 2): void
    {
        // Fonnte menampilkan indikator mengetik lewat parameter `typing`/`duration`
        // saat mengirim pesan, bukan via endpoint terpisah -> no-op.
    }

    public function parseIncoming(array $payload): array
    {
        $sender = $payload['sender'] ?? null;
        $message = $payload['message'] ?? null;
        $name = $payload['name'] ?? null;

        $type = 'text';
        if (! empty($payload['location'])) {
            $type = 'location';
        } elseif (! empty($payload['url']) || ! empty($payload['filename']) || ! empty($payload['extension'])) {
            $type = 'media';
        }

        return [
            'sender' => $sender ? (string) $sender : '',
            'name' => $name ? (string) $name : null,
            'text' => $message ? (string) $message : null,
            'type' => $type,
            'raw_reply_context' => [
                'inboxid' => $payload['inboxid'] ?? null,
                'sender' => $sender,
            ],
        ];
    }

    public function verifyWebhook(array $query): ?array
    {
        return null;
    }

    /**
     * Normalize an Indonesian phone number to Fonnte's expected 62-prefixed
     * format, stripping spaces, dashes, and a leading '+' along the way.
     */
    public function normalizeNumber(string $number): string
    {
        $number = preg_replace('/[^0-9+]/', '', $number);
        $number = ltrim($number, '+');

        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        }

        return $number;
    }

    protected function endpoint(): string
    {
        $baseUrl = config('services.fonnte.base_url', 'https://api.fonnte.com');

        return rtrim($baseUrl, '/').'/send';
    }
}
