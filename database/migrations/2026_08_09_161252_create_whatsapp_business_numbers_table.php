<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_business_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->unique(); // Format: 62xxx
            $table->foreignId('region_id')->constrained()->onDelete('cascade');
            $table->string('provider')->default('fonnte'); // fonnte | meta
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['phone_number', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_business_numbers');
    }
};
