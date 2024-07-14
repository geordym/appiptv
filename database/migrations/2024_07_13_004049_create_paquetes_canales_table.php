<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paquetes_canales', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('canal_id');
            $table->unsignedBigInteger('paquete_id');
            $table->timestamps();

            $table->foreign('canal_id')->references('id')->on('canales');
            $table->foreign('paquete_id')->references('id')->on('paquetes');

            $table->unique(['canal_id', 'paquete_id']); // Agregar restricción única para evitar duplicados

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paquetes_canales');
    }
};
