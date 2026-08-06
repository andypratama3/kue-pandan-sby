<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('fonnte'); // 'fonnte' | 'meta'
            $table->string('sender_number');
            $table->string('sender_name')->nullable();
            $table->foreignId('region_id')->nullable()->constrained();
            $table->text('incoming_message');
            $table->string('detected_intent')->nullable();
            $table->text('bot_reply')->nullable();
            $table->boolean('handled_by_ai')->default(false);
            $table->timestamps();

            $table->index('sender_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_conversations');
    }
};