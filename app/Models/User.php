<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password',
        'rol',
        'organizacion_id',
        'created_by',
        'updated_by',  // <-- agregado
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
        {
            return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            ];
        }
      
    public function organizacion()
        {
            // FQN para evitar problemas de import
            return $this->belongsTo(\App\Models\Organizacion::class, 'organizacion_id', 'id');
        }

    /**
     * Relación para saber quién creó el usuario
     */
    // Relación para creador
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relación para actualizador
    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
