<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToArqueosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('arqueos', function (Blueprint $table) {
            $table->decimal('saldo_tarjeta', 15, 2)->nullable();
            $table->unsignedBigInteger('caja_id');
            $table->decimal('saldo_final', 15, 2);
            
            
            $table->foreign('caja_id')->references('id')->on('caja')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('arqueos', function (Blueprint $table) {
            $table->dropColumn('saldo_tarjeta');
            $table->dropForeign(['caja_id']);
            $table->dropColumn('caja_id');
            $table->dropColumn('saldo_final');
        });
    }
}

