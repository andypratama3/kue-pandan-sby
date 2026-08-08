<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Harden foreign keys terkait data keuangan/history.
 *
 * Sebelumnya: delete customer/region/produk MENG-HAPUS data keuangan
 * (orders, order_items, order_returns, bukti pembayaran) secara permanen.
 *
 * Sekarang: RESTRICT — data tidak bisa hilang diam-diam; penolakan
 * menampilkan pesan error yang jelas di sisi aplikasi.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // customers -> orders : pesanan adalah catatan keuangan, jangan ter-cascade.
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('restrict');
        });

        // regions -> customers : hapus region tidak boleh menghapus customer.
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->foreign('region_id')
                ->references('id')
                ->on('regions')
                ->onDelete('restrict');
        });

        // regions -> products : hapus region tidak boleh menghapus products.
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('region_id')
                ->references('id')
                ->on('regions')
                ->onDelete('restrict');
        });

        // products -> order_return_products : hapus produk tidak boleh
        // menghapus baris retur (audit trail).
        Schema::table('order_return_products', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['product_variant_id']);
        });
        Schema::table('order_return_products', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('restrict');
            $table->foreign('product_variant_id')
                ->references('id')
                ->on('product_variants')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations (kembalikan perilaku semula).
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
        });

        Schema::table('order_return_products', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['product_variant_id']);
        });
        Schema::table('order_return_products', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
        });
    }
};
