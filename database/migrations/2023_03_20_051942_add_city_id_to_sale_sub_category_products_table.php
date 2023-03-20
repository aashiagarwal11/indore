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
        Schema::table('sale_sub_category_products', function (Blueprint $table) {
            $table->unsignedBigInteger('city_id')->nullable()->after('sub_cat_id');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_sub_category_products', function (Blueprint $table) {
            $table->dropColumn('city_id');
        });
    }
};
