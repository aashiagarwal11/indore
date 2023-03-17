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
            $table->integer('status')->nullable()->default(0)->comment('0=>deny,1=>accept');;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_sub_category_products', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
