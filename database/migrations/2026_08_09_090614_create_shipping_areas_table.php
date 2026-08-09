<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->onDelete('cascade');
            $table->string('area_name'); // Nama kelurahan/kecamatan
            $table->decimal('distance_km', 8, 2); // Jarak dalam kilometer
            $table->integer('shipping_fee'); // Ongkir dalam Rupiah
            $table->text('notes')->nullable(); // Catatan tambahan (landmark, dll)
            $table->timestamps();

            $table->index(['region_id', 'area_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_areas');
    }
};
