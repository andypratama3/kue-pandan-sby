<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('name');
            $table->string('opening_hours')->nullable()->after('phone');
            $table->string('payment_type')->nullable()->after('opening_hours');
            $table->string('landmark')->nullable()->after('address'); // Patokan Tempat
            $table->foreignId('added_by_user_id')
                  ->nullable()
                  ->after('customer_category_id')
                  ->constrained('users')
                  ->onDelete('set null'); // Jika user kurir dihapus, data customer tetap ada
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['added_by_user_id']);
            $table->dropColumn([
                'company_name',
                'opening_hours',
                'payment_type',
                'landmark',
                'added_by_user_id',
            ]);
        });
    }
};
