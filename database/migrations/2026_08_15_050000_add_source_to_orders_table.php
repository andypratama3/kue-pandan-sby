<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menandai asal pesanan: 'manual' (diinput kurir via dashboard) atau
     * 'wa_bot' (dibuat otomatis lewat chatbot WhatsApp). Bot DIBUAT_DENGAN
     * created_by_user_id NULL dan region konteks, sehingga dashboard kurir
     * hanya menampilkan pesanan miliknya sedangkan admin tetap melihat semua.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('source', 20)->default('manual')->after('status');
            $table->index(['source', 'region_id', 'created_at'], 'idx_orders_source_region_created');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_source_region_created');
            $table->dropColumn('source');
        });
    }
};