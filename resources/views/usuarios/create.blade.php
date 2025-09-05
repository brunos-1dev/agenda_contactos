{{-- resources/views/usuarios/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nuevo Usuario')

@section('content')

<div class="container text-white">
    <h2 class="mb-4 text-white text-center">Agregar Usuario</h2>

    <form action="{{ route('usuarios.store') }}" method="POST">
        @csrf

        {{-- Nombre --}}
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input
                type="text"
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

        {{-- Apellido --}}
        <div class="mb-3">
            <label for="apellido" class="form-label">Apellido</label>
            <input
                type="text"
                name="apellido"
                id="apellido"
                class="form-control bg-dark text-white border-secondary placeholder-light"
                value="{{ old('apellido') }}"
                placeholder="Ej. Pérez">
            @error('apellido')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        {{-- Organización --}}
        <div class="mb-3">
            <label for="organizacion_id" class="form-label">Organización</label>
            <select
                name="organizacion_id"
                id="organizacion_id"
                class="form-select bg-dark text-white border-secondary">
                <option value="">— Sin asignar —</option>
                @foreach($orgs as $o)
                    <option value="{{ $o->id }}" {{ (string)old('organizacion_id') === (string)$o->id ? 'selected' : '' }}>
                        {{ $o->label }}
                    </option>
                @endforeach
            </select>
            
            @error('organizacion_id')
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
                value="{{ old('email') }}"
                placeholder="Ej. juan@email.com">
            @error('email')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Contraseña --}}
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input
                type="password"
                name="password"
                id="password"
                class="form-control bg-dark text-white border-secondary placeholder-light"
                required
                placeholder="******">
            @error('password')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Confirmación de contraseña --}}
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
            <input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                class="form-control bg-dark text-white border-secondary placeholder-light"
                required
                placeholder="******">
        </div>

        {{-- Rol --}}
        <div class="mb-3">
            <label for="rol" class="form-label">Rol</label>
            <select
                name="rol"
                id="rol"
                class="form-select bg-dark text-white border-secondary"
                required>
                <option value="">-- Seleccione un rol --</option>
                <option value="superadmin" {{ old('rol') === 'superadmin' ? 'selected' : '' }}>SuperAdmin</option>
                <option value="admin"      {{ old('rol') === 'admin' ? 'selected' : '' }}>Administrador</option>
                <option value="consulta"   {{ old('rol') === 'consulta' ? 'selected' : '' }}>Consulta</option>
            </select>
            @error('rol')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>


        <button class="btn btn-outline-light px-4">Guardar</button>
        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-light px-4">Cancelar</a>
    </form>
</div>

{{-- (Opcional) Si querés marcar Organización como requerida cuando el rol NO es SuperAdmin --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const rolSel = document.getElementById('rol');
        const orgSel = document.getElementById('organizacion_id');

        function toggleOrgRequired() {
            const r = rolSel.value;
            // Requerir organización para admin/consulta, no para superadmin
            if (r === 'admin' || r === 'consulta') {
                orgSel.setAttribute('required', 'required');
            } else {
                orgSel.removeAttribute('required');
            }
        }

        rolSel.addEventListener('change', toggleOrgRequired);
        toggleOrgRequired(); // estado inicial
    });
</script>
@endsection
