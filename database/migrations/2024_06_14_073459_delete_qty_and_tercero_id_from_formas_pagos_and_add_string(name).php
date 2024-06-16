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
        Schema::table('formas_pagos', function (Blueprint $table) {
            $table->dropColumn('qty');
            $table->dropColumn('tercero_id');
            $table->string('name')->change();
        });
    }

    public function down(): void
    {
        Schema::table('formas_pagos', function (Blueprint $table) {
            $table->integer('qty');
            $table->integer('tercero_id');
            $table->integer('name')->change();
        });
    }
};
