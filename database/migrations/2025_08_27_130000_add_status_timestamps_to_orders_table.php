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
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable()->after('note');
            $table->timestamp('picked_up_at')->nullable()->after('paid_at');
            $table->timestamp('delivered_at')->nullable()->after('picked_up_at');
            $table->timestamp('received_by_buyer_at')->nullable()->after('delivered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['paid_at', 'picked_up_at', 'delivered_at', 'received_by_buyer_at']);
        });
    }
};
