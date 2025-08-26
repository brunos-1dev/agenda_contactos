<?php

namespace App\Exports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContactsExport implements FromCollection, WithHeadings
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        $query = Contact::with('departamento'); // traer relación departamento

        if ($this->search) {
            $query->where(function($q) {
                $q->where('dni', 'like', "%{$this->search}%")
                  ->orWhere('nombre', 'like', "%{$this->search}%")
                  ->orWhere('apellido', 'like', "%{$this->search}%");
            });
        }

        $contacts = $query->get();

        // Opcional: transformamos para que la relación aparezca como nombre
        return $contacts->map(function($c) {
            return [
                'DNI' => $c->dni,
                'Nombre' => $c->nombre,
                'Apellido' => $c->apellido,
                'NI' => $c->ni,
                'Departamento' => $c->departamento->nombre ?? 'Sin asignar',
                'Email' => $c->email ?? '',
                'Teléfono' => $c->telefono ?? '',
                'Aplicaciones' => $c->aplicaciones->pluck('nombre')->implode(', '), // si tiene relación Many-to-Many
                'Usuario' => $c->usuario ?? '',
                // agregar cualquier otro campo que tengas en la tabla
            ];
        });
    }

    public function headings(): array
    {
        return ['DNI', 'Nombre', 'Apellido', 'NI', 'Departamento', 'Email', 'Teléfono', 'Aplicaciones', 'Usuario'];
    }
}
