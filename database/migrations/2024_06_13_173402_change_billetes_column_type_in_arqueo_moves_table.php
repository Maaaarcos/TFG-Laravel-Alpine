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
        Schema::table('arqueo_moves', function (Blueprint $table) {
            $table->json('billetes')->change();
            $table->string('moves')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('arqueo_moves', function (Blueprint $table) {
            $table->integer('billetes')->change();
            $table->decimal('billetes')->change();
        });
    }
};
