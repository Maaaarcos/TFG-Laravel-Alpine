<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Insertar las formas de pago
        DB::table('formas_pagos')->insert([
            [
                'id' => 1,
                'name' => 'Efectivo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Tarjeta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Eliminar las formas de pago insertadas
        DB::table('formas_pagos')->whereIn('id', [1, 2])->delete();
    }
};
