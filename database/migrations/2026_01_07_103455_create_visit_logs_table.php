<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45);
            $table->timestamps();

            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_logs');
    }
};
