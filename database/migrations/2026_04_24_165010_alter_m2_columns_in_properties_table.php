<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            // decimal(12,2) permite hasta 999,999,999.99
            $table->decimal('m2_land', 12, 2)->change();
            $table->decimal('m2_construction', 12, 2)->change();
            $table->decimal('price', 15, 2)->change(); // Por si las dudas para el precio
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            //
        });
    }
};
