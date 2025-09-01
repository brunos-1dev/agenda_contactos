<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organizacion extends Model
{
    protected $table = 'organizaciones';

    protected $fillable = ['nombre','tipo','id_padre','ruta','nivel','orden','activo'];

    // Tipos permitidos (usados en el <select>)
    public const TIPOS = [
        'Dirección General',
        'Subdirección',
        'Departamento',
        'División',
        'Sección',
    ];

    // Relaciones
    public function parent()
    {
        return $this->belongsTo(self::class, 'id_padre');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'id_padre')->orderBy('orden');
    }

    // Scope útil
    public function scopeActivas($q) { return $q->where('activo', 1); }
}
