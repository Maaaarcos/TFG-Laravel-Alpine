<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacturaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fac_ventas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('tercero_id');
            $table->date('fecha');
            $table->unsignedBigInteger('forma_pago_id');
            $table->decimal('base_imp', 15, 2); 
            $table->decimal('total_iva', 15, 2); 
            $table->decimal('total', 15, 2); 
            $table->timestamps();

            
            $table->foreign('tercero_id')->references('id')->on('terceros')->onDelete('cascade');
            $table->foreign('forma_pago_id')->references('id')->on('pagos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fac_ventas');
    }
}
