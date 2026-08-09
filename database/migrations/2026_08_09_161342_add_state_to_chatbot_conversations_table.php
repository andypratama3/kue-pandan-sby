<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chatbot_conversations', function (Blueprint $table) {
            $table->string('current_step')->nullable()->after('detected_intent'); // welcomed, browsing_catalog, awaiting_delivery_question, idle
            $table->json('context_data')->nullable()->after('current_step'); // Data context percakapan
            $table->timestamp('last_interaction_at')->nullable()->after('context_data'); // Untuk timeout check
            
            $table->index(['sender_number', 'last_interaction_at']);
        });
    }

    public function down(): void
    {
        Schema::table('chatbot_conversations', function (Blueprint $table) {
            $table->dropColumn(['current_step', 'context_data', 'last_interaction_at']);
        });
    }
};
