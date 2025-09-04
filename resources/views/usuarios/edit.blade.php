{{-- resources/views/usuarios/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="container text-white">
    <h2 class="mb-4 text-white text-center">Editar Usuario</h2>

    <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Nombre --}}
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input
                type="text"
                name="nombre"
                id="nombre"
                class="form-control bg-dark text-white border-secondary placeholder-light"
                required
                value="{{ old('nombre', $usuario->nombre) }}"
                placeholder="Ej. Juan"
            >
            @error('nombre')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Apellido --}}
        <div class="mb-3">
            <label for="apellido" class="form-label">Apellido</label>
            <input
                type="text"
                name="apellido"
                id="apellido"
                class="form-control bg-dark text-white border-secondary placeholder-light"
                value="{{ old('apellido', $usuario->apellido) }}"
                placeholder="Ej. Pérez"
            >
            @error('apellido')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input
                type="email"
                name="email"
                id="email"
                class="form-control bg-dark text-white border-secondary placeholder-light"
                required
                value="{{ old('email', $usuario->email) }}"
                placeholder="Ej. juan@email.com"
            >
            @error('email')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Rol --}}
        <div class="mb-3">
            <label for="rol" class="form-label">Rol</label>
            <select
                name="rol"
                id="rol"
                class="form-select bg-dark text-white border-secondary"
                required
            >
                <option value="">-- Seleccione un rol --</option>
                <option value="superadmin" {{ old('rol', $usuario->rol) === 'superadmin' ? 'selected' : '' }}>SuperAdmin</option>
                <option value="admin"      {{ old('rol', $usuario->rol) === 'admin'      ? 'selected' : '' }}>Administrador</option>
                <option value="consulta"   {{ old('rol', $usuario->rol) === 'consulta'   ? 'selected' : '' }}>Consulta</option>
            </select>
            @error('rol')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Organización (siempre visible; requerida para Admin/Consulta, opcional para SuperAdmin) --}}
        <div class="mb-3">
            <label for="organizacion_id" class="form-label">Organización</label>
            <select
                name="organizacion_id"
                id="organizacion_id"
                class="form-select bg-dark text-white border-secondary"
            >
                <option value="">— Seleccionar —</option>
                @foreach($orgs as $o)
                    <option value="{{ $o->id }}"
                        {{ (string)old('organizacion_id', $usuario->organizacion_id) === (string)$o->id ? 'selected' : '' }}>
                        {{ $o->label }}
                    </option>
                @endforeach
            </select>
            
            @error('organizacion_id')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- (Opcional) Campos de cambio de contraseña
        <div class="mb-3">
            <label for="password" class="form-label">Nueva contraseña (opcional)</label>
            <input type="password" name="password" id="password"
                   class="form-control bg-dark text-white border-secondary placeholder-light"
                   placeholder="******">
            @error('password')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                   class="form-control bg-dark text-white border-secondary placeholder-light"
                   placeholder="******">
        </div>
        --}}

        <button class="btn btn-outline-light px-4">Actualizar</button>
        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-light px-4">Cancelar</a>
    </form>
</div>
@endsection
