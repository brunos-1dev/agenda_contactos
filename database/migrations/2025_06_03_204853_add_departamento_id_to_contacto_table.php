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
    Schema::table('contacto', function (Blueprint $table) {
        $table->unsignedBigInteger('departamento_id')->nullable()->after('telefono');

        $table->foreign('departamento_id')->references('id')->on('departamento')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('contacto', function (Blueprint $table) {
        $table->dropForeign(['departamento_id']);
        $table->dropColumn('departamento_id');
    });
}

};
