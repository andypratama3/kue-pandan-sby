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
        Schema::table('products', function (Blueprint $table) {
            // 1. Tambahkan kolom region_id setelah kolom category_id
            $table->foreignId('region_id')
                  ->nullable() // Buat nullable dulu agar tidak error pada data yang sudah ada
                  ->after('category_id')
                  ->constrained('regions') // Membuat foreign key ke tabel regions
                  ->onDelete('cascade');  // Jika region dihapus, produk terkait juga akan terhapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Urutan pelepasan: drop foreign key dulu, baru drop kolomnya
            $table->dropForeign(['region_id']);
            $table->dropColumn('region_id');
        });
    }
};
