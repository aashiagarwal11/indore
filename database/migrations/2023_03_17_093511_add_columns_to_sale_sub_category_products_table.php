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
            $table->string('vendor_name')->nullable();
            $table->string('owner_or_broker')->nullable();
            $table->string('vehicle_sighting')->nullable();
            $table->string('property_location')->nullable();
            $table->float('price')->nullable();
            $table->string('brand')->nullable();
            $table->string('model_name')->nullable();
            $table->integer('model_year')->nullable();
            $table->string('fuel_type')->nullable();
            $table->integer('seater')->nullable();
            $table->bigInteger('kilometer_running')->nullable();
            $table->string('insurance_period')->nullable();
            $table->string('color')->nullable();
            $table->string('other_information')->nullable();
            $table->string('size_length_width')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_sub_category_products', function (Blueprint $table) {
            $table->dropColumn('vendor_name');
            $table->dropColumn('owner_or_broker');
            $table->dropColumn('vehicle_sighting');
            $table->dropColumn('property_location');
            $table->dropColumn('price');
            $table->dropColumn('brand');
            $table->dropColumn('model_name');
            $table->dropColumn('model_year');
            $table->dropColumn('fuel_type');
            $table->dropColumn('seater');
            $table->dropColumn('kilometer_running');
            $table->dropColumn('insurance_period');
            $table->dropColumn('color');
            $table->dropColumn('other_information');
            $table->dropColumn('size_length_width');
        });
    }
};
