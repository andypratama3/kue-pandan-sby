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
        Schema::table('customers', function (Blueprint $table) {
            // Tambahkan kolom foreign key untuk region
            $table->foreignId('region_id')
                  ->constrained('regions') // Terhubung ke tabel 'regions'
                  ->onDelete('cascade')    // Jika region dihapus, customer terkait juga dihapus
                  ->after('id');          // (Opsional) Posisi kolom setelah 'id'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Hapus foreign key dan kolom jika migration di-rollback
            $table->dropForeign(['region_id']);
            $table->dropColumn('region_id');
        });
    }
};
