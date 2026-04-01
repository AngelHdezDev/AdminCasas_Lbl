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
        Schema::table('autos', function (Blueprint $table) {
            // Creamos la columna 'active' como booleano, por defecto activo
            $table->boolean('active')->default(true)->after('consignacion');
        });
    }

    public function down(): void
    {
        Schema::table('autos', function (Blueprint $table) {
            $table->dropColumn('active');
        });
    }
};
