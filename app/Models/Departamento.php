<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamento';

    protected $fillable = ['nombre'];

    public function contactos()
    {
        return $this->hasMany(Contact::class, 'departamento_id');
    }
}
