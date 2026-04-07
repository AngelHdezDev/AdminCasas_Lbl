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
            // Asignamos el vendedor responsable de la propiedad
            $table->foreignId('seller_id')->nullable()->constrained('sellers')->onDelete('set null');

            // Asignamos el cliente (si la casa ya está apalabrada o en proceso)
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
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
