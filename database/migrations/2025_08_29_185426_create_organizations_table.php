<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();

            // Campos en español (compatibles con el estilo de tu BD)
            $table->string('nombre');                 // p.ej. "DG-OJO-Departamento Administración"
            $table->string('tipo', 32)->nullable();   // p.ej. "departamento", "división", "sección" (opcional)

            // Autorreferencia al padre
            $table->foreignId('id_padre')->nullable()
                  ->constrained('organizations')->nullOnDelete();

            // Materialized path
            $table->string('ruta', 1024);             // p.ej. "/1/5/12"
            $table->unsignedTinyInteger('nivel')->default(0); // raíz=0, hijos=1, etc.

            // Orden y estado
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);

            $table->timestamps();

            // Índices recomendados
            $table->index('id_padre');
            $table->index('nivel');
            // Nota: para rutas largas en utf8mb4, creamos el índice con prefijo vía SQL bruto (abajo)
            // $table->index('ruta'); // <- evitar en utf8mb4 con long index
            $table->unique(['id_padre', 'nombre']); // evita duplicados entre hermanos
        });

        // Índice en 'ruta' con prefijo para MySQL/utf8mb4 (mejora subárbol con LIKE 'prefijo/%')
        // Si tu server no lo necesita, puedes comentar esta línea.
        DB::statement('CREATE INDEX organizations_ruta_idx ON organizations (ruta(255))');
    }

    public function down(): void
    {
        // Borramos el índice manual primero (por si tu motor lo exige)
        try {
            DB::statement('DROP INDEX organizations_ruta_idx ON organizations');
        } catch (\Throwable $e) {
            // no-op si no existe
        }

        Schema::dropIfExists('organizations');
    }
};
