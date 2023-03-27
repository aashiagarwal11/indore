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
        Schema::create('rents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->unsignedBigInteger('sub_cat_id');
            $table->foreign('sub_cat_id')->references('id')->on('sale_sub_categories')->onDelete('cascade');
            $table->unsignedBigInteger('city_id')->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->integer('user_id')->nullable();
            $table->integer('status')->nullable()->default(0)->comment('0=>pending,1=>accept,2=>deny');
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
            $table->string('image')->nullable();
            $table->integer('room_qty')->nullable();
            $table->string('kitchen')->nullable();
            $table->integer('hall')->nullable();
            $table->bigInteger('lat_bath')->nullable();
            $table->string('whatsapp_no')->nullable();
            $table->string('call_no')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rents');
    }
};
