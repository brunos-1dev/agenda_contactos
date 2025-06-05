@extends('layouts.app')
@section('title', 'Agenda de Contactos')
@section('content')
<div class="container">
    <h1>Contactos</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('contacts.create') }}" class="btn btn-primary mb-3">Crear nuevo contacto</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>DNI</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contacts as $contact)
                <tr>
                    <td>{{ $contact->dni }}</td>
                    <td>{{ $contact->nombre }}</td>
                    <td>{{ $contact->email }}</td>
                    <td>{{ $contact->telefono }}</td>
                    <td>
                        <a href="{{ route('contacts.edit', $contact->dni) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('contacts.destroy', $contact->dni) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar contacto?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
