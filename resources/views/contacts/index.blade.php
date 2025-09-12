@extends('layouts.app')

@section('title', 'Listado de Contactos')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center text-white">Listado del Personal</h2>

    @php
        $canManage = in_array(auth()->user()->rol ?? 'consulta', ['admin','superadmin']);
    @endphp

    {{-- Barra superior --}}
    <div class="d-flex align-items-center flex-wrap gap-2 mb-3">

        {{-- BUSCADOR --}}
        <form action="{{ route('contacts.index') }}" method="GET"
              class="d-flex align-items-center">
            <div class="d-flex">
                <input
                    type="text"
                    name="search"
                    class="form-control me-2 bg-dark text-white border-secondary placeholder-light"
                    style="width: 420px;"
                    placeholder="Buscar por DNI, nombre, apellido u organización"
                    value="{{ request('search') }}"
                >
                <button type="submit" class="btn btn-outline-light px-4">Buscar</button>
            </div>
        </form>

        {{-- Botones de acción (quedan antes del toggle) --}}
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('contacts.export', ['search' => request('search')]) }}"
               class="btn btn-outline-success px-4">
                Exportar a Excel
            </a>

            @if($canManage)
                <a href="{{ route('contacts.import.form') }}" class="btn btn-outline-info px-4">
                    Importar
                </a>

                <a href="{{ route('contacts.create') }}" class="btn btn-outline-light px-4">
                    Nuevo Contacto
                </a>
            @endif
        </div>

        {{-- TOGGLE VISTA (separado y a la derecha) --}}
        @php($view = request('view', 'cards'))
        <div class="ms-auto">
            <div class="btn-group btn-group-sm" role="group" aria-label="Cambiar vista">
                <a href="{{ request()->fullUrlWithQuery(['view' => 'cards']) }}"
                   class="btn btn-outline-light {{ $view === 'cards' ? 'active' : '' }}">
                    Fichas
                </a>
                <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}"
                   class="btn btn-outline-light {{ $view === 'list' ? 'active' : '' }}">
                    Listado
                </a>
            </div>
        </div>
    </div>

    {{-- ===== VISTA FICHAS ===== --}}
    @if ($view === 'cards')
        <div class="row row-cols-1 row-cols-lg-3 g-4">
            @foreach ($contacts as $contact)
                <div class="col">
                    <article class="people-h-card d-flex position-relative">
                        <div class="people-h-left">
                            <img src="{{ asset('images/persona4.png') }}"
                                 alt="Foto de {{ $contact->nombre }} {{ $contact->apellido }}"
                                 class="people-h-photo">
                        </div>
                        <div class="people-h-right d-flex flex-column">
                            <h4 class="people-surname text-uppercase mb-0">{{ $contact->apellido }}</h4>
                            <div class="people-name text-uppercase">{{ $contact->nombre }}</div>
                            <div class="people-meta">
                                <span><strong>DNI:</strong> {{ $contact->dni }}</span>
                                <br>
                                <span><strong>NI:</strong> {{ $contact->ni }}</span>
                            </div>
                            <br>
                            <div class="mt-1">
                                <span class="people-dept-chip"
                                      title="{{ $contact->organizacion->nombre ?? 'Sin asignar' }}">
                                    {{ $contact->organizacion->nombre ?? 'Sin asignar' }}
                                </span>
                            </div>
                            <a href="{{ route('contacts.show', $contact->dni) }}" class="stretched-link"></a>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>

    {{-- ===== VISTA LISTA ===== --}}
    @else
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead class="table-secondary text-dark">
                    <tr>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>NI</th>
                        <th>Organización</th>
                        <th style="width: 260px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($contacts as $contact)
                        <tr>
                            <td>{{ $contact->dni }}</td>
                            <td>{{ $contact->nombre }}</td>
                            <td>{{ $contact->apellido }}</td>
                            <td>{{ $contact->ni }}</td>
                            <td>{{ $contact->organizacion->nombre ?? 'Sin asignar' }}</td>
                            <td>
                                <a href="{{ route('contacts.show', $contact->dni) }}" class="btn btn-outline-info btn-sm me-1">Ver</a>

                                @if ($canManage)
                                    <a href="{{ route('contacts.edit', $contact->dni) }}" class="btn btn-outline-warning btn-sm me-1">Editar</a>

                                    <form action="{{ route('contacts.destroy', $contact->dni) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('¿Seguro que desea eliminar?')">
                                            Eliminar
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
