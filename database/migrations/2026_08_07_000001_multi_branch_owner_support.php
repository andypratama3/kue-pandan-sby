<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dukungan multi-branch & owner:
     * 1. Flag is_active pada regions (cabang yang beroperasi).
     * 2. Index pada semua kolom region_id + kolom pencarian utama agar
     *    filter per cabang (dan laporan owner lintas cabang) tetap cepat.
     */
    public function up(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('slug');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('region_id');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->index('region_id');
            $table->index('added_by_user_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('region_id');
            $table->index('is_active');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->index('is_active');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('region_id');
            $table->index('status');
            $table->index('created_by_user_id');
            $table->index('customer_id');
            $table->index(['region_id', 'status']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id');
        });

        Schema::table('order_returns', function (Blueprint $table) {
            $table->index('region_id');
            $table->index('status');
            $table->index('order_id');
        });

        Schema::table('order_return_products', function (Blueprint $table) {
            $table->index('order_return_id');
        });

        Schema::table('chatbot_conversations', function (Blueprint $table) {
            $table->index('region_id');
        });
    }

    public function down(): void
    {
        Schema::table('chatbot_conversations', function (Blueprint $table) {
            $table->dropIndex(['region_id']);
        });
        Schema::table('order_return_products', function (Blueprint $table) {
            $table->dropIndex(['order_return_id']);
        });
        Schema::table('order_returns', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['region_id']);
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['region_id', 'status']);
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['created_by_user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['region_id']);
        });
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['region_id']);
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['added_by_user_id']);
            $table->dropIndex(['region_id']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['region_id']);
        });
        Schema::table('regions', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
