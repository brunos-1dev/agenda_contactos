<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) CUIL después de DNI
        if (!Schema::hasColumn('contacto', 'cuil')) {
            Schema::table('contacto', function (Blueprint $table) {
                $table->string('cuil', 11)
                    ->nullable()
                    ->unique()
                    ->after('dni'); // solo dígitos, sin guiones
            });
        }

        // 2) IUP después de APELLIDO
        if (!Schema::hasColumn('contacto', 'iup')) {
            Schema::table('contacto', function (Blueprint $table) {
                $table->string('iup', 20)
                    ->nullable()
                    ->unique()
                    ->after('apellido');
            });
        }

        // 3) FK a ORGANIZACIONES después de IUP
        if (!Schema::hasColumn('contacto', 'organizacion_id')) {
            Schema::table('contacto', function (Blueprint $table) {
                $table->foreignId('organizacion_id')
                    ->nullable()
                    ->after('iup')
                    ->constrained('organizaciones')
                    ->nullOnDelete();
            });
        }

        // 4) JERARQUÍA después de organizacion_id
        if (!Schema::hasColumn('contacto', 'jerarquia')) {
            Schema::table('contacto', function (Blueprint $table) {
                $table->string('jerarquia', 50)
                    ->nullable()
                    ->after('organizacion_id');
            });
        }

        // 5) LOCALIDAD después de DOMICILIO
        if (!Schema::hasColumn('contacto', 'localidad')) {
            Schema::table('contacto', function (Blueprint $table) {
                $table->string('localidad', 100)
                    ->nullable()
                    ->after('domicilio');
            });
        }
    }

    public function down(): void
    {
        // Primero la FK si existe
        if (Schema::hasColumn('contacto', 'organizacion_id')) {
            Schema::table('contacto', function (Blueprint $table) {
                $table->dropConstrainedForeignId('organizacion_id');
            });
        }

        // Luego columnas si existen
        foreach (['cuil', 'iup', 'jerarquia', 'localidad'] as $col) {
            if (Schema::hasColumn('contacto', $col)) {
                Schema::table('contacto', function (Blueprint $table) use ($col) {
                    $table->dropColumn($col);
                });
            }
        }
    }
};
