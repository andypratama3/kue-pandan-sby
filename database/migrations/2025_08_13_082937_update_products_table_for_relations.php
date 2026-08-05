<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained()->after('id');
            // $table->string('image_path')->after('description');
            $table->string('tag')->nullable()->after('image_path');
            $table->boolean('is_active')->default(true)->after('tag');
            $table->dropColumn('price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            // $table->dropColumn('image_path', 'image');
            $table->dropColumn('tag');
            $table->dropColumn('is_active');
            $table->decimal('price', 10, 2)->after('description');
        });
    }
};
