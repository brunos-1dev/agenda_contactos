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
    Schema::create('aplicacion_contacto', function (Blueprint $table) {
        $table->unsignedBigInteger('dni');       // FK a contacto.dni
        $table->unsignedBigInteger('id');        // FK a aplicacion.id

        $table->foreign('dni')->references('dni')->on('contacto')->onDelete('cascade');
        $table->foreign('id')->references('id')->on('aplicacion')->onDelete('cascade');

        $table->primary(['dni', 'id']);
    });
}

public function down()
{
    Schema::dropIfExists('aplicacion_contacto');
}

};
