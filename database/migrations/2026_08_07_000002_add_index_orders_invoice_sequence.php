<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Index pendukung untuk perhitungan nomor invoice di PesananController:
     * query `whereDate(created_at) + where(created_by_user_id) + where(customer_id)`
     * dengan lockForUpdate. Tanpa index, kunci baris menjadi terlalu luas dan
     * race condition masih mungkin terjadi.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['created_by_user_id', 'customer_id', 'created_at'], 'idx_orders_invoice_sequence');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_invoice_sequence');
        });
    }
};
