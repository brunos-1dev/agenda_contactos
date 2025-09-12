@extends('layouts.app')

@section('title', 'Árbol de Organizaciones')

@section('content')
@php
    // Para mostrar/ocultar acciones de gestión (crear, etc.)
    $canManage = auth()->check() && in_array(auth()->user()->rol, ['admin','superadmin']);
@endphp

<div class="container">
    <h2 class="mb-4 text-center text-white">Árbol de Organizaciones</h2>

    {{-- Barra superior: búsqueda + acciones --}}
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <form action="{{ route('organizaciones.index') }}" method="GET" class="d-flex gap-2 flex-grow-1">
            <input type="text"
                   name="search"
                   value="{{ $search ?? '' }}"
                   class="form-control bg-dark text-white border-secondary"
                   placeholder="Buscar por nombre o tipo (Dirección, Subdirección, Departamento, División, Sección)…">
            <button class="btn btn-outline-light">Buscar</button>
            @if(!empty($search))
                <a href="{{ route('organizaciones.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            @endif
        </form>

        <a href="{{ route('organizaciones.export', ['search' => $search ?? '']) }}"
           class="btn btn-outline-success">Exportar Excel</a>

        @if($canManage)
            <a href="{{ route('organizaciones.create') }}" class="btn btn-outline-info">Nueva Organización</a>
        @endif
    </div>

        {{-- Contenedor del árbol --}}
    <div class="org-panel rounded-4 p-3 shadow-sm">
        @if(($tree ?? collect())->isEmpty())
            <div class="text-center text-muted py-4">
                @if(!empty($search))
                    No se encontraron resultados para <strong>{{ $search }}</strong>.
                @else
                    No hay organizaciones cargadas.
                @endif
            </div>
        @else
            <ul class="org-root list-unstyled m-0">
                @foreach ($tree as $root)
                    <li class="mb-1">
                        @include('organizaciones.partials.node', [
                            'node'     => $root,
                            'openIds'  => $openIds ?? [],
                            'search'   => $search ?? '',
                        ])
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

</div>
@endsection
