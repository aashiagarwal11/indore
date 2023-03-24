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
            $table->integer('room_qty')->nullable();
            $table->string('kitchen')->nullable();
            $table->integer('hall')->nullable();
            $table->bigInteger('lat_bath')->nullable();
            $table->string('whatsapp_no')->nullable();
            $table->string('call_no')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_sub_category_products', function (Blueprint $table) {
            $table->dropColumn('room_qty');
            $table->dropColumn('kitchen');
            $table->dropColumn('hall');
            $table->dropColumn('lat_bath');
            $table->dropColumn('whatsapp_no');
            $table->dropColumn('call_no');
        });
    }
};
