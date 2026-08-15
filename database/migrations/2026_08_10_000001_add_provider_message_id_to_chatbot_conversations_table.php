<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chatbot_conversations', function (Blueprint $table) {
            // Id unik pesan dari provider (Fonnte: inboxid, Meta: message id).
            // Dipakai untuk deduplikasi idempotent saat provider retry/redeliver.
            $table->string('provider_message_id', 191)->nullable()->after('last_interaction_at');
            $table->index('provider_message_id');
        });
    }

    public function down(): void
    {
        Schema::table('chatbot_conversations', function (Blueprint $table) {
            $table->dropIndex(['provider_message_id']);
            $table->dropColumn('provider_message_id');
        });
    }
};