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
        Schema::table('imagen_temporals', function (Blueprint $table) {
            // Por defecto es 0 (no asignado)
            $table->boolean('status')->default(0)->after('fecha_correo');
        });
    }

    public function down(): void
    {
        Schema::table('imagen_temporals', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
