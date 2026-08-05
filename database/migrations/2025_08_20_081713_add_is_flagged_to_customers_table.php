<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Tambahkan kolom is_flagged setelah 'note'
            // default(false) berarti customer tidak ditandai secara default
            $table->boolean('is_flagged')->default(false)->after('note');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Hapus kolom jika migrasi di-rollback
            $table->dropColumn('is_flagged');
        });
    }
};
