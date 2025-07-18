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
    Schema::create('contacto', function (Blueprint $table) {
        $table->unsignedBigInteger('dni')->primary(); // clave primaria
        $table->string('nombre');
        $table->string('apellido');
        $table->string('ni');
        $table->string('domicilio');
        $table->string('telefono')->nullable();
        $table->string('email')->nullable();
        $table->string('contacto_emergencia')->nullable();
        $table->unsignedBigInteger('departamento_id')->nullable();

        $table->foreign('departamento_id')->references('id')->on('departamento')->onDelete('set null');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacto');
    }
};
