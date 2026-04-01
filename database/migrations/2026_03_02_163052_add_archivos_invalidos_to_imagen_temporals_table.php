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
        Schema::table('imagen_temporals', function (Blueprint $table) {
            $table->text('archivos_invalidos')->nullable()->after('status');
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('imagen_temporals', function (Blueprint $table) {
            //
        });
    }
};
