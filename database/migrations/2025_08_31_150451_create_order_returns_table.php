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
        Schema::create('order_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');

            // Kolom Tambahan Sesuai Permintaan
            $table->foreignId('courier_id')->constrained('users')->comment('ID kurir yang mengajukan retur');
            $table->foreignId('region_id')->constrained('regions')->comment('Region kurir saat mengajukan retur');

            $table->string('status')->default('menunggu_konfirmasi'); // Contoh: menunggu_konfirmasi, disetujui, ditolak, selesai
            $table->decimal('total_amount_returned', 15, 2)->default(0)->comment('Total nilai dari semua produk yang diretur');

            $table->string('return_proof')->nullable();

            $table->text('reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_returns');
    }
};
