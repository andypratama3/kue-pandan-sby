<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel baru untuk kategori customer
        Schema::create('customer_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // 2. Tambahkan kolom foreign key ke tabel customers
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('customer_category_id')
                ->nullable()
                ->after('region_id')
                ->constrained('customer_categories')
                ->onDelete('set null'); // Jika kategori dihapus, customer tidak ikut terhapus
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['customer_category_id']);
            $table->dropColumn('customer_category_id');
        });

        Schema::dropIfExists('customer_categories');
    }
};
