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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Ejemplo: Casa moderna en Zapopan
            $table->text('description');
            $table->decimal('price', 15, 2);

            // Características físicas
            $table->integer('bedrooms')->default(0);
            $table->integer('bathrooms')->default(0);
            $table->integer('half_bathrooms')->default(0);
            $table->integer('parking_spots')->default(0);
            $table->decimal('m2_construction', 8, 2);
            $table->decimal('m2_land', 8, 2);

            // Ubicación y Estado
            $table->string('address');
            $table->string('neighborhood'); // Colonia
            $table->enum('type', ['house', 'apartment', 'land', 'commercial']);
            $table->enum('status', ['available', 'sold', 'rented', 'reserved']);
            $table->enum('contract_type', ['sale', 'rent']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
