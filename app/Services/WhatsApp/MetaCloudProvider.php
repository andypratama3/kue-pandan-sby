<?php

namespace App\Services\WhatsApp;

use App\Contracts\WhatsAppProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaCloudProvider implements WhatsAppProviderInterface
{
    protected string $endpoint = 'https://graph.facebook.com';

    public function sendMessage(string $target, string $message, array $context = []): array
    {
        $token = config('services.meta_whatsapp.access_token');
        $phoneNumberId = config('services.meta_whatsapp.phone_number_id');
        $version = config('services.meta_whatsapp.api_version', 'v23.0');

        if (! $token || ! $phoneNumberId) {
            Log::channel('whatsapp')->warning('Meta Cloud API belum dikonfigurasi lengkap di .env.');

            return ['success' => false, 'message_id' => null, 'raw' => [], 'error' => 'not_configured'];
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(15)
                ->post("{$this->endpoint}/{$version}/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $target,
                    'type' => 'text',
                    'text' => ['body' => $message],
                ]);

            $body = $response->json() ?? [];

            if (! empty($body['error'])) {
                $code = (int) ($body['error']['code'] ?? 0);

                // 131047: re-engagement message (di luar customer service window 24 jam,
                // teks bebas tidak diizinkan, wajib Message Template ter-approve).
                // 480: pesan lama tidak dapat dibalas.
                if (in_array($code, [131047, 480], true)) {
                    Log::channel('whatsapp')->info('Meta: di luar window 24 jam (window_expired).', [
                        'target' => $target,
                        'code' => $code,
                    ]);

                    return ['success' => false, 'message_id' => null, 'raw' => $body, 'error' => 'window_expired'];
                }

                Log::channel('whatsapp')->error('Meta kirim gagal', [
                    'target' => $target,
                    'http_status' => $response->status(),
                    'error' => $body['error'],
                ]);

                return ['success' => false, 'message_id' => null, 'raw' => $body, 'error' => 'api_error'];
            }

            return [
                'success' => true,
                'message_id' => $body['messages'][0]['id'] ?? null,
                'raw' => $body,
                'error' => null,
            ];
        } catch (\Throwable $e) {
            Log::channel('whatsapp')->error('Meta kirim exception: '.$e->getMessage());

            return ['success' => false, 'message_id' => null, 'raw' => ['exception' => $e->getMessage()], 'error' => 'exception'];
        }
    }

    public function sendTyping(string $target, int $durationSeconds = 2): void
    {
        // Meta Cloud API tidak menyediakan endpoint typing indicator
        // di endpoint /messages standar -> no-op.
    }

    public function parseIncoming(array $payload): array
    {
        $value = $payload['entry'][0]['changes'][0]['value'] ?? [];
        $message = $value['messages'][0] ?? [];
        $metadata = $value['metadata'] ?? [];

        $from = $message['from'] ?? null;
        $type = $message['type'] ?? 'text';
        $text = null;

        if ($type === 'text') {
            $text = $message['text']['body'] ?? null;
        }

        $name = $value['contacts'][0]['profile']['name'] ?? null;

        return [
            'sender' => $from ? (string) $from : '',
            'name' => $name ? (string) $name : null,
            'text' => $text,
            'type' => $type,
            'recipient' => $metadata['display_phone_number'] ?? null,
            'raw_reply_context' => [
                'message_id' => $message['id'] ?? null,
                'sender' => $from,
            ],
        ];
    }

    public function verifyWebhook(array $query): ?array
    {
        $mode = $query['hub_mode'] ?? null;
        $token = $query['hub_verify_token'] ?? null;
        $challenge = $query['hub_challenge'] ?? null;

        $expected = config('services.meta_whatsapp.verify_token');

        // Bandingkan constant-time dengan hash_equals; tolak bila token kosong.
        if ($mode === 'subscribe'
            && is_string($expected) && $expected !== ''
            && is_string($token) && $token !== ''
            && hash_equals($expected, $token)) {
            return ['verified' => true, 'challenge' => is_scalar($challenge) ? (string) $challenge : ''];
        }

        return ['verified' => false, 'challenge' => null];
    }
}
