<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('state')->nullable()->after('neighborhood');      // Estado
            $table->string('city')->nullable()->after('state');             // Municipio/Ciudad
            $table->boolean('show_public_address')->default(false);          // ¿Mostrar dirección pública?
            $table->boolean('is_featured')->default(false);                 // ¿Propiedad destacada?
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['state', 'city', 'show_public_address', 'is_featured']);
        });
    }
};
