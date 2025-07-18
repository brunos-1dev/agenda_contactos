<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contacto';

    protected $primaryKey = 'dni';

    public $incrementing = false;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'dni',
        'nombre',
        'apellido',
        'ni',
        'domicilio',
        'telefono',
        'email',
        'contacto_emergencia',
        'departamento_id',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function aplicaciones()
    {
        return $this->belongsToMany(Aplicacion::class, 'aplicacion_contacto', 'dni', 'id')
                    ->withPivot('nombre_usuario'); // <- agregamos campo extra
    }
}
