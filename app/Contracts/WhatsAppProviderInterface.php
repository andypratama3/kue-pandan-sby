<?php

namespace App\Contracts;

interface WhatsAppProviderInterface
{
    /**
     * Kirim pesan teks. Return array normalized:
     * ['success' => bool, 'message_id' => ?string, 'raw' => array]
     */
    public function sendMessage(string $target, string $message, array $context = []): array;

    /**
     * Kirim indikator "sedang mengetik". No-op jika provider tidak mendukung.
     */
    public function sendTyping(string $target, int $durationSeconds = 2): void;

    /**
     * Normalisasi payload webhook masuk (format tiap provider berbeda-beda)
     * menjadi DTO seragam:
     * ['sender' => string, 'name' => ?string, 'text' => ?string,
     *  'type' => string, 'raw_reply_context' => mixed]
     */
    public function parseIncoming(array $payload): array;

    /**
     * Untuk provider yang butuh verifikasi GET webhook (Meta).
     * Return array ['verified' => bool, 'challenge' => ?string] atau null jika tidak relevan.
     */
    public function verifyWebhook(array $query): ?array;
}
