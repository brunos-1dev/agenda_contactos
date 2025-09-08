{{-- resources/views/usuarios/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Listado de Usuarios')

@section('content')
<div class="container">

    <h2 class="mb-4 text-center text-white">Listado de Usuarios</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        $canManage = auth()->check() && in_array(auth()->user()->rol, ['admin','superadmin']);
    @endphp

    {{-- Barra superior: buscador + acciones --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <form action="{{ route('usuarios.index') }}" method="GET" class="d-flex gap-2">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                class="form-control bg-dark text-white border-secondary placeholder-light"
                placeholder="Buscar por nombre, apellido o email"
                style="width: 420px;"
            >
            <button class="btn btn-outline-light">Buscar</button>
            @if (request('search'))
                <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            @endif
        </form>

        <div class="d-flex gap-2">
            @if ($canManage)
                <a href="{{ route('usuarios.export', ['search' => request('search')]) }}" class="btn btn-outline-success">
                    Exportar a Excel
                </a>
                <a href="{{ route('usuarios.create') }}" class="btn btn-outline-light px-4">
                    Nuevo Usuario
                </a>
            @endif
        </div>
    </div>

    {{-- Tabla --}}
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead class="table-secondary text-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Organización</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->nombre }}</td>
                        <td>{{ $usuario->apellido }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>{{ ucfirst($usuario->rol) }}</td>
                        <td>{{ $usuario->organizacion->nombre ?? '—' }}</td>
                        <td class="text-center">
                            @if ($canManage)
                                <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-outline-warning btn-sm me-1">Editar</a>
                                <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm">Eliminar</button>
                                </form>
                            @else
                                <span class="text-muted">Sólo lectura</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No hay usuarios para mostrar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación (mantiene el filtro) --}}
    <div class="mt-3 d-flex justify-content-end">
        {{ $usuarios->appends(['search' => request('search')])->links() }}
    </div>

</div>
@endsection
