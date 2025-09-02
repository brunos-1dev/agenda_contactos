@extends('layouts.app')

@section('title', 'Crear Contacto')

@section('content')

<div class="container text-white">
    <h2 class="mb-4 text-white text-center">Nuevo Contacto</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('contacts.store') }}" method="POST">
        @csrf

        <div class="row mb-3">
            <div class="col">
                <label for="dni" class="form-label">DNI</label>
                <input type="number"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="dni" required
                       placeholder="Ingrese DNI"
                       value="{{ old('dni') }}">
            </div>
            <div class="col">
                <label for="ni" class="form-label">NI</label>
                <input type="text"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="ni"
                       placeholder="Ingrese NI"
                       value="{{ old('ni') }}">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="nombre" required
                       placeholder="Ingrese nombre"
                       value="{{ old('nombre') }}">
            </div>
            <div class="col">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="apellido"
                       placeholder="Ingrese apellido"
                       value="{{ old('apellido') }}">
            </div>
        </div>

        <div class="mb-3">
            <label for="domicilio" class="form-label">Domicilio</label>
            <input type="text"
                   class="form-control bg-dark text-white border-secondary placeholder-light"
                   name="domicilio"
                   placeholder="Ingrese domicilio"
                   value="{{ old('domicilio') }}">
        </div>

        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text"
                   class="form-control bg-dark text-white border-secondary placeholder-light"
                   name="telefono" required
                   placeholder="Ingrese teléfono"
                   value="{{ old('telefono') }}">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email"
                   class="form-control bg-dark text-white border-secondary placeholder-light"
                   name="email" required
                   placeholder="Ingrese email"
                   value="{{ old('email') }}">
        </div>

        <div class="mb-3">
            <label for="contacto_emergencia" class="form-label">Contacto de Emergencia</label>
            <input type="text"
                   class="form-control bg-dark text-white border-secondary placeholder-light"
                   name="contacto_emergencia"
                   placeholder="Ingrese contacto de emergencia"
                   value="{{ old('contacto_emergencia') }}">
        </div>

        <div class="mb-4">
            <label for="departamento_id" class="form-label">Departamento</label>
            <select class="form-select bg-dark text-white border-secondary placeholder-light" name="departamento_id">
                <option value="" class="text-muted">Seleccione</option>
                @foreach($departamentos as $departamento)
                    <option value="{{ $departamento->id }}" {{ old('departamento_id') == $departamento->id ? 'selected' : '' }}>
                        {{ $departamento->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- APLICACIONES --}}
        <div class="mb-4">
            <label class="form-label">Aplicaciones</label><br>
            @foreach ($aplicaciones as $aplicacion)
                @php
                    $checked = in_array($aplicacion->id, old('aplicaciones', []));
                @endphp
                <div class="form-check form-switch mb-3">
                    <input
                        class="form-check-input app-checkbox"
                        type="checkbox"
                        role="switch"
                        id="switch-app-{{ $aplicacion->id }}"
                        name="aplicaciones[]"
                        value="{{ $aplicacion->id }}"
                        {{ $checked ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="switch-app-{{ $aplicacion->id }}">
                        {{ $aplicacion->nombre }}
                    </label>

                    {{-- Campo para nombre de usuario --}}
                    <input
                        type="text"
                        name="nombre_usuario[{{ $aplicacion->id }}]"
                        placeholder="Nombre de usuario para {{ $aplicacion->nombre }}"
                        class="form-control mt-2 bg-dark text-white border-secondary placeholder-light usuario-field"
                        style="{{ $checked ? 'display: block;' : 'display: none;' }}"
                        {{ $checked ? '' : 'disabled' }}
                        value="{{ old('nombre_usuario.'. $aplicacion->id) }}"
                    >
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-outline-light px-4">Guardar</button>
        
        <a href="{{ route('contacts.index') }}" class="btn btn-outline-light px-4">Cancelar</a>
    </form>
</div>

{{-- Script para mostrar/ocultar el campo nombre de usuario --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.app-checkbox');

        function toggleUsernameField(checkbox) {
            const input = checkbox.closest('.form-check').querySelector('.usuario-field');
            if (checkbox.checked) {
                input.style.display = 'block';
                input.disabled = false;
            } else {
                input.style.display = 'none';
                input.disabled = true;
                input.value = ''; // opcional: limpia el campo si se desmarca
            }
        }

        checkboxes.forEach(cb => {
            toggleUsernameField(cb); // para el estado inicial con old()
            cb.addEventListener('change', () => toggleUsernameField(cb));
        });
    });
</script>

@endsection
