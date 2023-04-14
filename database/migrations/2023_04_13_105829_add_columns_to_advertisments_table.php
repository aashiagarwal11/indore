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
        Schema::table('advertisments', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->string('link')->nullable();
            $table->integer('status')->nullable()->default(1)->comment('0=>deactive,1=>active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advertisments', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('link');
            $table->dropColumn('status');
        });
    }
};
