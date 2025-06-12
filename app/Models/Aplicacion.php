<?php
// app/Models/Aplicacion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aplicacion extends Model
{
    protected $table = 'aplicacion';

    // Indica que la clave primaria es 'id_aplicacion'
    protected $primaryKey = 'id_aplicacion';

    // Si fuera distinta la columna autoincremental, pero aquí sí lo es
    public $incrementing = true;

    // Si quieres usar timestamps (created_at / updated_at)
    public $timestamps = true;

    // Campos que puedes rellenar masivamente
    protected $fillable = ['nombre'];
}
