<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // [!code block:start]
            // Menambahkan kolom setelah kolom 'note'
            $table->text('rejection_note')->nullable()->after('note');
            // [!code block:end]
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // [!code block:start]
            $table->dropColumn('rejection_note');
            // [!code block:end]
        });
    }
};
