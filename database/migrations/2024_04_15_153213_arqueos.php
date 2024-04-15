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
        Schema::create('arqueos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->decimal('saldo_inicial', 8, 2);
            $table->decimal('saldo_efectivo', 8, 2);
            $table->decimal('total', 8, 2);
            $table->decimal('saldo_total', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arqueos');
    }
};
