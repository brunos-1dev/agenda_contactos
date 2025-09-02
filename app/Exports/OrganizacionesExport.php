<?php

namespace App\Exports;

use App\Models\Organizacion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrganizacionesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private ?string $search = null) {}

    public function collection()
    {
        $q = trim((string) $this->search);

        return Organizacion::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('nombre', 'like', "%{$q}%");
            })
            ->orderBy('ruta')->orderBy('orden')
            ->get(['id','nombre','tipo','id_padre','ruta','nivel','orden','activo','created_at']);
    }

    public function headings(): array
    {
        return ['ID', 'Nombre', 'Tipo'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->nombre,
            $row->tipo,
            
        ];
    }
}
