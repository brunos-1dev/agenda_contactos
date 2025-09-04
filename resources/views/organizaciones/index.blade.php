@extends('layouts.app')

@section('title', 'Organizaciones')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center text-white">Árbol de Organizaciones</h2>
    @php
        $canManage = in_array(auth()->user()->rol ?? 'consulta', ['admin','superadmin']);
    @endphp

    {{-- Barra superior: buscador + acciones --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <form action="{{ route('organizaciones.index') }}" method="GET" class="d-flex gap-2">
            <input
                type="text"
                name="search"
                value="{{ $search ?? '' }}"
                class="form-control bg-dark text-white border-secondary placeholder-light"
                placeholder="Buscar por nombre o tipo (Dirección, Subdirección, Departamento, División, etc.)"
                style="width: 420px;"
            >
            <button type="submit" class="btn btn-outline-light px-4">Buscar</button>

            @if(!empty($search))
                <a href="{{ route('organizaciones.index') }}" class="btn btn-outline-light">Limpiar</a>
            @endif
        </form>

        <div class="d-flex gap-2">
            {{-- Botón Exportar (si tienes la ruta y el export configurado) --}}
            <a href="{{ route('organizaciones.export', ['search' => $search ?? '']) }}"
               class="btn btn-outline-success px-4">
                Exportar Excel
            </a>

                    {{-- Botón Nueva Organización (admin y superadmin) --}}
        @if($canManage)
            <a href="{{ route('organizaciones.create') }}" class="btn btn-outline-light px-4">
                Nueva Organización
            </a>
        @endif

        </div>
    </div>

    {{-- Contenedor del árbol --}}
    <div class="bg-white rounded-4 p-3 shadow-sm">
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

{{-- Estilos rápidos para que se vea prolijo --}}
<style>
    .org-root details summary {
        cursor: pointer;
        user-select: none;
        list-style: none;
    }
    .org-root details summary::-webkit-details-marker { display: none; }

    .org-node > summary {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .25rem .5rem;
        border-radius: 10px;
        transition: transform .08s ease, background-color .15s ease;
    }
    .org-node[open] > summary {
        background: #f4f6fb;
    }
    .org-children {
        margin-left: 1.25rem;
        padding-left: .75rem;
        border-left: 2px dashed rgba(0,0,0,.06);
    }
    .chip {
        display: inline-block;
        font-size: .70rem;
        line-height: 1;
        padding: .35rem .5rem;
        border-radius: 999px;
        background: #e9eef8;
        color: #2a3d66;
        vertical-align: middle;
    }
    .chip--tipo { background: #e5e7eb; color: #374151; }
    mark {
        background: #ffed4a;
        padding: 0 .15rem;
        border-radius: .2rem;
    }
    /* === Organización: que todo el contenido del panel sea negro === */
.org-card, .org-card * { 
  color:#111 !important;
}

/* Chips/píldoras en gris claro con texto negro */
.org-chip{
  background:#eef2f7;
  color:#111 !important;
  border:1px solid #d5dbe6;
}
</style>
@endsection
