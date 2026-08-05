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
        Schema::table('users', function (Blueprint $table) {
            // 1. Tambahkan kolom region_id sebagai foreign key
            $table->foreignId('region_id')->nullable()->constrained('regions')->after('email');

            // 2. Hapus kolom 'region' yang lama
            $table->dropColumn('region');
        });
    }

    public function down(): void // Fungsi untuk membatalkan migrasi
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Tambahkan kembali kolom region yang lama
            $table->string('region')->after('email');

            // 2. Hapus foreign key dan kolom region_id
            $table->dropForeign(['region_id']);
            $table->dropColumn('region_id');
        });
    }
};
