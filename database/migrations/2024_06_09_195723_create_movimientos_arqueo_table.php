<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientosArqueoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arqueo_moves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('arqueo_id');
            $table->unsignedBigInteger('caja_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('billetes');
            $table->decimal('moves', 15, 2);
            $table->timestamps();

            // Definir las relaciones
            $table->foreign('arqueo_id')->references('id')->on('arqueos')->onDelete('cascade');
            $table->foreign('caja_id')->references('id')->on('caja')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arqueo_moves');
    }
}
