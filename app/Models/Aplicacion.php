<?php
// app/Models/Aplicacion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aplicacion extends Model
{
    protected $table = 'aplicacion';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    public function contactos()
    {
        return $this->belongsToMany(Contacto::class, 'aplicacion_contacto', 'id', 'dni');
    }

}
