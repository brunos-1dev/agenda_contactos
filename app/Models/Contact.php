<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contact extends Model
{
    // Tabla y clave primaria
    protected $table = 'contacto';
    protected $primaryKey = 'dni';
    public $incrementing = false;
    protected $keyType = 'int';

    // Si la tabla no tiene created_at/updated_at
    public $timestamps = false;

    // Campos asignables en masa
    protected $fillable = [
        'dni',
        'nombre',
        'apellido',
        'ni',
        'domicilio',
        'localidad',
        'telefono',
        'email',
        'contacto_emergencia',
        'departamento_id',   // legacy
        'organizacion_id',   // nueva FK
        'cuil',
        'iup',
        'jerarquia',
    ];

    /* ========================
     | Relaciones
     ========================*/

    public function departamento(): BelongsTo
    {
        // Tabla: departamento, PK: id
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function organizacion(): BelongsTo
    {
        // Tabla: organizaciones, PK: id
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }

    public function aplicaciones(): BelongsToMany
    {
        // Pivote: aplicacion_contacto (dni, id, nombre_usuario)
        return $this->belongsToMany(
            Aplicacion::class,
            'aplicacion_contacto', // tabla pivote
            'dni',                 // FK del contacto en pivote
            'id',                  // FK de la aplicación en pivote
            'dni',                 // clave local en este modelo
            'id'                   // clave local en Aplicacion
        )->withPivot('nombre_usuario');
    }
}
