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
    Schema::create('departamento', function (Blueprint $table) {
        $table->id();
        $table->string('nombre')->unique();
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('departamento');
}

};
