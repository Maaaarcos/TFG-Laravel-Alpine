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
        Schema::table('datos_empresa', function (Blueprint $table) {
            // Modificar columnas existentes para permitir valores nulos
            $table->string('nombre')->nullable()->change();
            $table->string('direccion')->nullable()->change();
            $table->string('telefono')->nullable()->change();
            $table->string('email')->nullable()->change();
            // Agregar nuevas columnas
            $table->string('ciudad')->nullable();
            $table->string('codigo_postal')->nullable();
            $table->string('nif')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('datos_empresa', function (Blueprint $table) {
            // Revertir las columnas existentes a no permitir valores nulos
            $table->string('nombre')->nullable(false)->change();
            $table->string('direccion')->nullable(false)->change();
            $table->string('telefono')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            // Eliminar las nuevas columnas
            $table->dropColumn(['ciudad', 'codigo_postal', 'nif']);
        });
    }
};
