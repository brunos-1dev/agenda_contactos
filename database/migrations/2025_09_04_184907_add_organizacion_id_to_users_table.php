<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Agrega la columna nullable después de 'rol'
            $table->unsignedBigInteger('organizacion_id')->nullable()->after('rol');

            // Índice (opcional pero recomendado)
            $table->index('organizacion_id');

            // FK hacia organizaciones, y si borran la org => setea NULL
            // Si tu versión da error con nullOnDelete(), cambiá por onDelete('set null')
            $table->foreign('organizacion_id')
                ->references('id')->on('organizaciones')
                ->onDelete('set null');
                // ->nullOnDelete(); // <- alternativo si tu versión lo soporta
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organizacion_id']);
            $table->dropIndex(['organizacion_id']);
            $table->dropColumn('organizacion_id');
        });
    }
};
