@extends('layouts.app')

@section('title', 'Listado de Contactos')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center">Listado de Contactos</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('contacts.create') }}" class="btn btn-success mb-3">Nuevo Contacto</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>DNI</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>NI</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Domicilio</th>
                <th>Contacto Emergencia</th>
                <th>Departamento</th>
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
                    <td>{{ $contact->telefono }}</td>
                    <td>{{ $contact->email }}</td>
                    <td>{{ $contact->domicilio }}</td>
                    <td>{{ $contact->contacto_emergencia }}</td>
                    <td>{{ $contact->departamento_id->nombre ?? '-' }}</td>
                    <td>
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
