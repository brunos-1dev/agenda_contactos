<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTableChangeNameAddFields extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Renombrar columna name a nombre
            $table->renameColumn('name', 'nombre');

            // Agregar apellido y rol
            $table->string('apellido')->nullable()->after('nombre');
            $table->string('rol')->default('consulta')->after('apellido');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Volver a renombrar nombre a name
            $table->renameColumn('nombre', 'name');

            // Borrar columnas agregadas
            $table->dropColumn(['apellido', 'rol']);
        });
    }
}
