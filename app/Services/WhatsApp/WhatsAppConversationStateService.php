<?php

namespace App\Services\WhatsApp;

use App\Models\ChatbotConversation;
use Carbon\Carbon;

/**
 * Service untuk mengelola state percakapan WhatsApp multi-turn.
 * 
 * States (info-only mode):
 * - idle: Belum ada interaksi atau sudah timeout
 * - welcomed: Baru saja menerima sapaan
 * - browsing_catalog: Sedang melihat katalog/produk
 * - awaiting_delivery_question: Menunggu pertanyaan tentang pengiriman
 */
class WhatsAppConversationStateService
{
    const STATE_IDLE = 'idle';
    const STATE_WELCOMED = 'welcomed';
    const STATE_BROWSING_CATALOG = 'browsing_catalog';
    const STATE_AWAITING_DELIVERY = 'awaiting_delivery_question';
    
    const TIMEOUT_HOURS = 24; // Reset state setelah 24 jam tidak aktif

    /**
     * Get current state untuk sender tertentu.
     */
    public function getCurrentState(string $senderNumber): array
    {
        $lastConversation = ChatbotConversation::where('sender_number', $senderNumber)
            ->orderByDesc('id')
            ->first();

        if (!$lastConversation) {
            return [
                'step' => self::STATE_IDLE,
                'context' => [],
                'is_expired' => false,
            ];
        }

        // Check timeout - reset ke idle jika sudah > 24 jam
        $isExpired = $lastConversation->last_interaction_at && 
                     $lastConversation->last_interaction_at instanceof \Carbon\Carbon &&
                     $lastConversation->last_interaction_at->diffInHours(now()) > self::TIMEOUT_HOURS;

        if ($isExpired) {
            return [
                'step' => self::STATE_IDLE,
                'context' => [],
                'is_expired' => true,
            ];
        }

        return [
            'step' => $lastConversation->current_step ?? self::STATE_IDLE,
            'context' => $lastConversation->context_data ?? [],
            'is_expired' => false,
        ];
    }

    /**
     * Update state percakapan.
     */
    public function updateState(
        string $senderNumber,
        string $newStep,
        array $contextData = [],
        ?int $conversationId = null
    ): void {
        if ($conversationId) {
            // Update existing conversation
            ChatbotConversation::where('id', $conversationId)
                ->update([
                    'current_step' => $newStep,
                    'context_data' => $contextData,
                    'last_interaction_at' => now(),
                ]);
        } else {
            // Create new conversation record (akan di-handle di webhook controller)
            // Ini hanya untuk fallback jika dipanggil standalone
            ChatbotConversation::create([
                'sender_number' => $senderNumber,
                'current_step' => $newStep,
                'context_data' => $contextData,
                'last_interaction_at' => now(),
            ]);
        }
    }

    /**
     * Reset state ke idle (untuk timeout atau manual reset).
     */
    public function resetState(string $senderNumber): void
    {
        ChatbotConversation::where('sender_number', $senderNumber)
            ->orderByDesc('id')
            ->first()
            ?->update([
                'current_step' => self::STATE_IDLE,
                'context_data' => [],
            ]);
    }

    /**
     * Determine next step berdasarkan intent dan current step.
     *
     * Info-only mode: intent apa pun yang bisa dijawab mandiri memindahkan
     * state ke tahap yang sesuai, dan sapaan selalu mereset ke welcome —
     * supaya percakapan tidak terjebak di state lama saat topik berganti.
     */
    public function determineNextStep(string $currentStep, string $intent, string $messageText): string
    {
        // Sapaan selalu membuka/mengembalikan percakapan ke welcome.
        if ($intent === 'sapaan') {
            return self::STATE_WELCOMED;
        }

        // Tanya produk / harga -> sedang melihat katalog (dari state mana pun).
        if (in_array($intent, ['tanya_produk', 'tanya_harga'])) {
            return self::STATE_BROWSING_CATALOG;
        }

        // Tanya lokasi/jam operasional/cara order/pengiriman -> info pengiriman (dari state mana pun).
        if (in_array($intent, ['tanya_lokasi_jam', 'cara_order', 'tanya_delivery'])) {
            return self::STATE_AWAITING_DELIVERY;
        }

        // FAQ (komplain/lainnya) tidak mengubah flow.
        return $currentStep;
    }

    /**
     * Check apakah pesan adalah FAQ yang tidak mengubah state.
     */
    public function isFaqIntent(string $intent): bool
    {
        return in_array($intent, ['komplain', 'lainnya', 'tanya_lokasi_jam', 'tanya_delivery']);
    }
}
