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

    @once
    <style>
        /* Contenedor principal del árbol */
        .org-box { background:#0f224f; border:1px solid #2a3341; border-radius:.9rem; }
        .org-inner { background:#0f224f; border-radius:.9rem; }
        .org-tree  { list-style:none; margin:0; padding:0; }
        /* Card de cada nodo */
        .org-card  { background:#1f252f; color:#e9ecef; border:1px solid #2a3341; border-radius:.6rem; }
        .org-body  { padding:.6rem .75rem; }
        .org-title { font-weight:600; }
        .org-sub   { font-size:.85rem; color:#b7c0cc; }
        /* Indentación visual de hijos */
        .org-children { margin-left: 1rem; border-left:1px dashed #2a3341; padding-left:.9rem; }
        /* Toggle caret */
        .org-toggle { cursor:pointer; user-select:none; display:inline-flex; align-items:center; gap:.35rem; }
        .org-caret  { width:.9rem; height:.9rem; display:inline-block; transition:transform .15s ease; }
        .org-caret::before {
            content:''; display:block; width:0; height:0;
            border-left:.48rem solid currentColor;
            border-top:.34rem solid transparent;
            border-bottom:.34rem solid transparent;
            opacity:.85;
        }
        .org-toggle[aria-expanded="true"] .org-caret { transform: rotate(90deg); }
        .org-badge  { font-size:.72rem; border:1px solid rgba(255,255,255,.12); background:rgba(255,255,255,.06); padding:.1rem .35rem; border-radius:.35rem; }
    </style>
    @endonce

    {{-- Árbol --}}
    <div class="org-box p-2">
        <div class="org-inner p-2">
            @if(!empty($tree) && count($tree))
                <ul class="org-tree">
                    @foreach ($tree as $node)
                        @include('organizaciones.partials.node', [
                            'node'    => $node,
                            'openIds' => $openIds ?? [],
                        ])
                    @endforeach
                </ul>
            @else
                <div class="text-center text-muted py-4">No hay organizaciones para mostrar.</div>
            @endif
        </div>
    </div>
</div>
@endsection
