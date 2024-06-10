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
        Schema::create('datos_empresa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tercero_id');
            $table->string('nombre');
            $table->string('direccion');
            $table->string('provincia');
            $table->string('telefono');
            $table->string('email')->unique();
            $table->string('ruc')->unique();
            $table->string('tipo_empresa');
            $table->string('actividad_economica');
            $table->timestamps();
        
            $table->foreign('tercero_id')->references('id')->on('terceros')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datos_empresa');
    }
};
