<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Index untuk kolom yang sering di-lookup:
 * - customers.phone  (unique per region saat checkout/pencarian)
 * - order_items.product_id / variant_id (join & agregasi retur)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->index('phone', 'customers_phone_index');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('product_id', 'order_items_product_id_index');
            $table->index('variant_id', 'order_items_variant_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_phone_index');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_product_id_index');
            $table->dropIndex('order_items_variant_id_index');
        });
    }
};
