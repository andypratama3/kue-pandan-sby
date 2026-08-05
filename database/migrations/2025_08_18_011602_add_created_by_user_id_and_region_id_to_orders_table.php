<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan kolom `created_by_user_id` dan `region_id` ke tabel `orders`.
     */
    public function up(): void
    {
        // Gunakan Schema::table() karena tabel 'orders' sudah ada.
        Schema::table('orders', function (Blueprint $table) {
            // Tambahkan kolom created_by_user_id (ID kurir yang membuat pesanan)
            // Foreign key yang mengacu ke tabel 'users'.
            $table->foreignId('created_by_user_id')
                  ->nullable() // Atur nullable jika pesanan bisa dibuat tanpa user (misal oleh sistem)
                  ->constrained('users') // Mengacu ke tabel 'users'
                  ->onDelete('set null') // Jika user dihapus, set ID ini menjadi null
                  ->after('note'); // Posisikan setelah kolom 'note' (atau di mana pun Anda inginkan)

            // Tambahkan kolom region_id
            // Foreign key yang mengacu ke tabel 'regions'.
            // Berguna untuk filter cepat berdasarkan region tanpa join terlalu banyak.
            $table->foreignId('region_id')
                  ->nullable() // Atur nullable jika region bisa null (jarang terjadi)
                  ->constrained('regions') // Mengacu ke tabel 'regions'
                  ->onDelete('set null') // Jika region dihapus, set ID ini menjadi null
                  ->after('created_by_user_id'); // Posisikan setelah 'created_by_user_id'
        });
    }

    /**
     * Balikkan migrasi.
     *
     * Menghapus kolom `created_by_user_id` dan `region_id` jika migrasi di-rollback.
     */
    public function down(): void
    {
        // Gunakan Schema::table() untuk memodifikasi tabel yang sudah ada.
        Schema::table('orders', function (Blueprint $table) {
            // Hapus foreign key dan kolom 'created_by_user_id'
            $table->dropForeign(['created_by_user_id']);
            $table->dropColumn('created_by_user_id');

            // Hapus foreign key dan kolom 'region_id'
            $table->dropForeign(['region_id']);
            $table->dropColumn('region_id');
        });
    }
};

