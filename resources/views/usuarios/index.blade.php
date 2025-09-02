@extends('layouts.app')

@section('title', 'Listado de Usuarios')

@section('content')

<div class="container">
    <h2 class="mb-4 text-center">Listado de Usuarios</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form action="{{ route('usuarios.index') }}" method="GET" class="d-flex mb-3 justify-content-between align-items-center">
            <div class="d-flex">
                <input type="text"
                       name="search"
                       class="form-control me-2 bg-dark text-white border-secondary placeholder-light"
                       style="width: 350px;"
                       placeholder="Buscar por nombre, apellido o email"
                       value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-light px-4">Buscar</button>
            </div>
        </form>

        <a href="{{ route('usuarios.create') }}" class="btn btn-outline-light px-4">Nuevo Usuario</a>
        <a href="{{ route('usuarios.export') }}" class="btn btn-outline-success px-4">Exportar a Excel</a>
    </div>

    <table class="table table-dark table-hover text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Email</th>
                <th>Rol</th>
                <th style="width: 260px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->nombre }}</td>
                    <td>{{ $usuario->apellido }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ ucfirst($usuario->rol) }}</td>
                    <td>
                        <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-outline-warning btn-sm me-1">Editar</a>

                        <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
