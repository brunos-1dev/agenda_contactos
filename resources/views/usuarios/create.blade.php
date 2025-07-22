{{-- resources/views/usuarios/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nuevo Usuario')

@section('content')

<div class="container text-white">
    <h2 class="mb-4 text-white text-center">Agregar Usuario</h2>

    <form action="{{ route('usuarios.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text"
                   name="nombre"
                   id="nombre"
                   class="form-control bg-dark text-white border-secondary placeholder-light"
                   required
                   value="{{ old('nombre') }}"
                   placeholder="Ej. Juan">
            @error('nombre')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text"
                   name="apellido"
                   id="apellido"
                   class="form-control bg-dark text-white border-secondary placeholder-light"
                   value="{{ old('apellido') }}"
                   placeholder="Ej. Pérez">
            @error('apellido')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email"
                   name="email"
                   id="email"
                   class="form-control bg-dark text-white border-secondary placeholder-light"
                   required
                   value="{{ old('email') }}"
                   placeholder="Ej. juan@email.com">
            @error('email')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password"
                   name="password"
                   id="password"
                   class="form-control bg-dark text-white border-secondary placeholder-light"
                   required
                   placeholder="******">
            @error('password')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
            <input type="password"
                   name="password_confirmation"
                   id="password_confirmation"
                   class="form-control bg-dark text-white border-secondary placeholder-light"
                   required
                   placeholder="******">
        </div>

        <div class="mb-3">
            <label for="rol" class="form-label">Rol</label>
            <select name="rol"
                    id="rol"
                    class="form-select bg-dark text-white border-secondary"
                    required>
                <option value="">-- Seleccione un rol --</option>
                <option value="admin" {{ old('rol') == 'admin' ? 'selected' : '' }}>Administrador</option>
                <option value="consulta" {{ old('rol') == 'consulta' ? 'selected' : '' }}>Consulta</option>
            </select>
            @error('rol')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-outline-gray me-2">Guardar</button>
        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-gray">Cancelar</a>
    </form>
</div>
@endsection
