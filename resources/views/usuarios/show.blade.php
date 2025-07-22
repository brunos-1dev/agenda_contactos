{{-- resources/views/usuarios/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalle de Usuario')

@section('content')

<div class="container text-white">
    <h2 class="mb-4 text-center">Detalle del Usuario</h2>

    <div class="mb-3">
        <label class="form-label">Nombre:</label>
        <p class="form-control bg-dark text-white border-secondary">{{ $usuario->nombre }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label">Apellido:</label>
        <p class="form-control bg-dark text-white border-secondary">{{ $usuario->apellido }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label">Email:</label>
        <p class="form-control bg-dark text-white border-secondary">{{ $usuario->email }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label">Rol:</label>
        <p class="form-control bg-dark text-white border-secondary">
            {{ $usuario->rol === 'admin' ? 'Administrador' : 'Consulta' }}
        </p>
    </div>

    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-gray">Volver</a>
</div>

@endsection
