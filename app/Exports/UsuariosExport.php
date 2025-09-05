<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsuariosExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private ?string $search = '')
    {
        $this->search = trim((string)$search);
    }

    public function collection()
    {
        $auth = Auth::user();

        $q = User::with(['organizacion:id,nombre,tipo', 'creador:id,nombre,apellido', 'actualizador:id,nombre,apellido'])
                 ->orderBy('apellido')->orderBy('nombre');

        // 🔎 búsqueda opcional
        if ($this->search !== '') {
            $s = $this->search;
            $q->where(function ($qq) use ($s) {
                $qq->where('nombre', 'like', "%{$s}%")
                   ->orWhere('apellido', 'like', "%{$s}%")
                   ->orWhere('email', 'like', "%{$s}%");
            });
        }

        // 🌳 alcance por organización
        if (in_array($auth->rol, ['admin','consulta'])) {
            if ($auth->organizacion_id) {
                $ids = app('org')->subtreeIds($auth->organizacion_id);
                $q->whereIn('organizacion_id', $ids);
            } else {
                $q->whereRaw('1=0');
            }
        }

        return $q->get();
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
            'Creado el',
            'Actualizado el',
        ];
    }

    public function map($u): array
    {
        $orgLabel  = $u->organizacion->nombre ?? '';
        $creadoPor = trim(($u->creador->nombre ?? '').' '.($u->creador->apellido ?? ''));
        $actPor    = trim(($u->actualizador->nombre ?? '').' '.($u->actualizador->apellido ?? ''));

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
