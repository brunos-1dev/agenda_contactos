<?php

namespace App\Rules;

use App\Models\Organizacion;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TipoHijoValido implements ValidationRule
{
    public function __construct(private readonly ?int $parentId) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $childType  = (string) $value;

        $matrix = [
            'Dirección General' => ['Subdirección', 'Departamento'],
            'Subdirección'      => ['Departamento'],
            'Departamento'      => ['División'],
            'División'          => ['Sección'],
            'Sección'           => [], // no admite hijos
        ];

        if (empty($this->parentId)) {
            // en raíz sólo se permite Dirección General
            if ($childType !== 'Dirección General') {
                $fail("En la raíz sólo se puede crear 'Dirección General'.");
            }
            return;
        }

        $parent = Organizacion::find($this->parentId);
        if (!$parent) {
            $fail('El padre seleccionado no existe.');
            return;
        }

        $allowed = $matrix[$parent->tipo] ?? [];
        if (!in_array($childType, $allowed, true)) {
            $fail("El tipo '{$childType}' no puede crearse debajo de '{$parent->tipo}'. Permitidos: "
                . (empty($allowed) ? 'ninguno' : implode(', ', $allowed)) . '.');
        }
    }
}
