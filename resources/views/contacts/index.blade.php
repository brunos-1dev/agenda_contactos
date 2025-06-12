@extends('layouts.app')

@section('title', 'Listado de Contactos')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center">Listado del Personal</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
    <form action="{{ route('contacts.index') }}" method="GET" class="d-flex mb-3 justify-content-between align-items-center">

    <div class="d-flex">
        <input type="text" name="search" class="form-control me-2" style="width: 350px;" placeholder="Buscar por DNI, nombre o apellido" value="{{ request('search') }}">
        <button type="submit" class="btn btn-outline-secondary">Buscar</button>
    </div>
</form>

    <a href="{{ route('contacts.create') }}" class="btn btn-outline-secondary">Nuevo Contacto</a>
</div>


    <table class="table table-bordered">
        <thead>
            <tr>
                <th>DNI</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>NI</th>
                <th>Departamento</th>
                 <!--<th>Teléfono</th>
                <th>Email</th>
                <th>Domicilio</th>
                <th>Contacto de Emergencia</th>
                -->
                <th>Acciones</th>
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

                    <!--<td>{{ $contact->telefono }}</td>
                    <td>{{ $contact->email }}</td>
                    <td>{{ $contact->domicilio }}</td>
                    <td>{{ $contact->contacto_emergencia }}</td>
                    -->

                    <td>

                        <a href="{{ route('contacts.show', $contact->dni) }}" class="btn btn-secondary btn-sm">Ver</a>

                        <a href="{{ route('contacts.edit', $contact->dni) }}" class="btn btn-sm btn-warning">Editar</a>

                        <form action="{{ route('contacts.destroy', $contact->dni) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que desea eliminar?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
