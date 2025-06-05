<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contacto';  // <-- Aquí indicamos el nombre exacto de la tabla

    protected $primaryKey = 'dni';  // Si tu clave primaria es dni y no 'id'

    public $incrementing = false;   // Si el DNI no es autoincremental

    protected $keyType = 'int';     // Si el dni es entero

    public $timestamps = false;

    protected $fillable = ['dni', 'nombre', 'email', 'telefono']; // Campos que se pueden llenar masivamente
}
