<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsuariosExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        // Traemos todos los usuarios
        return User::with(['creador', 'actualizador'])->get();
    }

    public function headings(): array
    {
        return [
            
            'Nombre',
            'Apellido',
            'Email',
            'Rol',
            'Creado por',
            'Actualizado por',
            'Fecha de creación',
            'Fecha de actualización'
        ];
    }

    public function map($usuario): array
    {
        return [
            $usuario->nombre,
            $usuario->apellido,
            $usuario->email,
            ucfirst($usuario->rol),
            $usuario->creador ? $usuario->creador->nombre.' '.$usuario->creador->apellido : '',
            $usuario->actualizador ? $usuario->actualizador->nombre.' '.$usuario->actualizador->apellido : '',
            $usuario->created_at ? $usuario->created_at->format('d/m/Y H:i') : '',
            $usuario->updated_at ? $usuario->updated_at->format('d/m/Y H:i') : '',
        ];
    }

}
