<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacVentaLineaTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Eliminar la tabla si ya existe (esto es opcional y se usa con cuidado)
        Schema::dropIfExists('FacVentaLinea');

        Schema::create('FacVentaLinea', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('fac_ventas')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->decimal('precio', 8, 2);
            $table->decimal('cantidad', 8, 2);
            $table->decimal('total', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('FacVentaLinea');
    }
}

