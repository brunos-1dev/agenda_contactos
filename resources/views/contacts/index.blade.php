@extends('layouts.app')

@section('title', 'Listado de Contactos')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center text-white">Listado del Personal</h2>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form action="{{ route('contacts.index') }}" method="GET" class="d-flex mb-3 justify-content-between align-items-center">
            <div class="d-flex">
                <input type="text"
                       name="search"
                       class="form-control me-2 bg-dark text-white border-secondary placeholder-light"
                       style="width: 350px;"
                       placeholder="Buscar por DNI, nombre o apellido"
                       value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-secondary">Buscar</button>
            </div>
        </form>

        @if (auth()->user()->rol === 'admin')
            <a href="{{ route('contacts.create') }}" class="btn btn-outline-secondary">Nuevo Contacto</a>
        @endif
    </div>

    <table class="table table-dark table-hover text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>DNI</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>NI</th>
                <th>Departamento</th>
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
                    <td>{{ $contact->departamento->nombre ?? 'Sin asignar' }}</td>
                    <td>
                        <a href="{{ route('contacts.show', $contact->dni) }}" class="btn btn-outline-info btn-sm me-1">Ver</a>

                        @if (auth()->user()->rol === 'admin')
                            <a href="{{ route('contacts.edit', $contact->dni) }}" class="btn btn-outline-warning btn-sm me-1">Editar</a>
                            <form action="{{ route('contacts.destroy', $contact->dni) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Seguro que desea eliminar?')">Eliminar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
