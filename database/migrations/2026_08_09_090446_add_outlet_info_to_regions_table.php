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
        Schema::table('regions', function (Blueprint $table) {
            $table->text('address')->nullable()->after('escalation_contact_phone');
            $table->json('operating_hours')->nullable()->after('address');
            $table->string('maps_link')->nullable()->after('operating_hours');
            $table->string('contact_email')->nullable()->after('maps_link');
            $table->string('contact_phone')->nullable()->after('contact_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->dropColumn(['address', 'operating_hours', 'maps_link', 'contact_email', 'contact_phone']);
        });
    }
};
