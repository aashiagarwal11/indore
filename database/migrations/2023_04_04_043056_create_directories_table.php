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
        Schema::create('directories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('biz_name')->nullable();
            $table->text('contact_per1')->nullable();
            $table->text('number1')->nullable();
            $table->text('category')->nullable();
            $table->text('city')->nullable();
            $table->text('state')->nullable();
            $table->text('contact_per2')->nullable();
            $table->text('contact_per3')->nullable();
            $table->text('number2')->nullable();
            $table->text('number3')->nullable();
            $table->text('address')->nullable();
            $table->text('detail')->nullable();
            $table->text('image')->nullable();
            $table->string('status')->nullable()->default(0)->comment('0=>pending,1=>accept,2=>deny');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('directories');
    }
};
