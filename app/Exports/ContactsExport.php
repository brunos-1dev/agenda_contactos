<?php

namespace App\Exports;

use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ContactsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected ?string $search;

    public function __construct(?string $search = null)
    {
        $this->search = $search ? trim($search) : null;
    }

    public function collection()
    {
        $query = Contact::with(['organizacion', 'departamento', 'aplicaciones'])
            ->orderBy('apellido')
            ->orderBy('nombre');

        if ($this->search !== null && $this->search !== '') {
            $s = $this->search;
            $query->where(function ($q) use ($s) {
                $q->where('dni', 'like', "%{$s}%")
                  ->orWhere('nombre', 'like', "%{$s}%")
                  ->orWhere('apellido', 'like', "%{$s}%")
                  ->orWhere('cuil', 'like', "%{$s}%")
                  ->orWhere('iup', 'like', "%{$s}%");
            });
        }

        // 🌳 Alcance por organización (admin/consulta sólo su subárbol)
        $auth = Auth::user();
        if (in_array($auth->rol, ['admin','consulta'])) {
            if ($auth->organizacion_id) {
                $ids = app('org')->subtreeIds($auth->organizacion_id);
                $query->whereIn('organizacion_id', $ids);
            } else {
                $query->whereRaw('1=0');
            }
        }

        $contacts = $query->get();

        return $contacts->map(function ($c) {
            // Organización (nombre y tipo)
            $orgNombre = $c->organizacion->nombre ?? null;
            $orgTipo   = $c->organizacion->tipo ?? null;
            $organizacion = $orgNombre ? ($orgTipo ? "{$orgNombre} ({$orgTipo})" : $orgNombre) : 'Sin asignar';

            // Aplicaciones como "App (usuario)" si hay usuario en pivot
            $apps = $c->aplicaciones
                ? $c->aplicaciones->map(function ($a) {
                    $u = $a->pivot->nombre_usuario ?? null;
                    return $u ? "{$a->nombre} ({$u})" : $a->nombre;
                })->implode(', ')
                : '';

            return [
                'DNI'                   => $c->dni,
                'CUIL'                  => $c->cuil ?? '',
                'IUP'                   => $c->iup ?? '',
                'NI'                    => $c->ni ?? '',
                'Nombre'                => $c->nombre ?? '',
                'Apellido'              => $c->apellido ?? '',
                'Jerarquía'             => $c->jerarquia ?? '',
                'Organización'          => $organizacion,
                'Email'                 => $c->email ?? '',
                'Teléfono'              => $c->telefono ?? '',
                'Teléfono emergencia'   => $c->contacto_emergencia ?? '',
                'Domicilio'             => $c->domicilio ?? '',
                'Localidad'             => $c->localidad ?? '',
                'Aplicaciones'          => $apps,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'DNI',
            'CUIL',
            'IUP',
            'NI',
            'Nombre',
            'Apellido',
            'Jerarquía',
            'Organización',
            'Email',
            'Teléfono',
            'Teléfono emergencia',
            'Domicilio',
            'Localidad',
            'Aplicaciones',
        ];
    }
}
