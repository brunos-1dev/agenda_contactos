<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamento'; // o el nombre real si es distinto
    protected $primaryKey = 'id';
    public $timestamps = false;

    // ✅ Esta línea soluciona tu problema
    protected $fillable = ['nombre']; // ← poné acá el/los campos reales de tu tabla

    public function contactos()
    {
        return $this->hasMany(Contact::class, 'departamento_id', 'id');
    }
}
