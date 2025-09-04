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
        return User::with([
                // Traer organización y quién creó/actualizó
                'organizacion:id,nombre,tipo',
                'creador:id,nombre,apellido',
                'actualizador:id,nombre,apellido',
            ])
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Apellido',
            'Email',
            'Organización',
            'Rol',
            'Creado por',
            'Actualizado por',
            'Fecha de creación',
            'Fecha de actualización',
        ];
    }

    public function map($u): array
    {
        $org = $u->organizacion;
        $orgLabel = $org
            ? $org->nombre . ($org->tipo ? " ({$org->tipo})" : '')
            : 'Sin asignar';

        $creadoPor = $u->creador ? ($u->creador->nombre.' '.$u->creador->apellido) : '';
        $actPor    = $u->actualizador ? ($u->actualizador->nombre.' '.$u->actualizador->apellido) : '';

        return [
            $u->nombre,
            $u->apellido,
            $u->email,
            $orgLabel,
            ucfirst($u->rol),
            $creadoPor,
            $actPor,
            $u->created_at ? $u->created_at->format('d/m/Y H:i') : '',
            $u->updated_at ? $u->updated_at->format('d/m/Y H:i') : '',
        ];
    }
}
