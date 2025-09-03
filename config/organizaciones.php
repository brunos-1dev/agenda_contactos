<?php

return [
    // Catálogo de tipos (para combos, validaciones, etc.)
    'types' => [
        'Dirección General',
        'Subdirección',
        'Departamento',
        'División',
        'Sección',
    ],

    // Qué hijos admite cada tipo de PADRE
    'allowed_children' => [
        'Dirección General' => ['Subdirección', 'Departamento'],
        'Subdirección'      => ['Departamento'],
        'Departamento'      => ['División'],
        'División'          => ['Sección'],
        'Sección'           => [], // no admite hijos
    ],

    // Tipos que pueden estar en la RAÍZ (sin padre)
    'root_allowed' => ['Dirección General'],
];
