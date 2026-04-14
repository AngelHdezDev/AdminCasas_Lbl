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
            // Añadimos la columna
            $table->string('slug')->unique()->nullable()->after('title')->index();
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Eliminamos la columna si se hace un rollback
            $table->dropColumn('slug');
        });
    }
};
