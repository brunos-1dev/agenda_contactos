<?php

namespace App\Exports;

use App\Models\Departamento;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DepartamentosExport implements FromCollection, WithHeadings
{
    /**
     * Retorna la colección de departamentos para exportar
     */
    public function collection()
    {
        // Solo traemos la columna 'nombre'
        return Departamento::all(['nombre']);
    }

    /**
     * Encabezados de la planilla
     */
    public function headings(): array
    {
        return [
            'Nombre',
        ];
    }
}
