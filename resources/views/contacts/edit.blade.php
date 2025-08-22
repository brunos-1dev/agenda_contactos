@extends('layouts.app')

@section('title', 'Editar Contacto')

@section('content')

<div class="container text-white">
    <h2 class="mb-4 text-white text-center">Editar Contacto</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('contacts.update', $contact->dni) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row mb-3">
            <div class="col">
                <label for="dni" class="form-label">DNI</label>
                <input type="number" class="form-control bg-dark text-white border-secondary placeholder-light" name="dni" value="{{ $contact->dni }}" disabled>
            </div>
            <div class="col">
                <label for="ni" class="form-label">NI</label>
                <input type="text" class="form-control bg-dark text-white border-secondary placeholder-light" name="ni" value="{{ $contact->ni }}">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control bg-dark text-white border-secondary placeholder-light" name="nombre" value="{{ $contact->nombre }}" required>
            </div>
            <div class="col">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" class="form-control bg-dark text-white border-secondary placeholder-light" name="apellido" value="{{ $contact->apellido }}">
            </div>
        </div>

        <div class="mb-3">
            <label for="domicilio" class="form-label">Domicilio</label>
            <input type="text" class="form-control bg-dark text-white border-secondary placeholder-light" name="domicilio" value="{{ $contact->domicilio }}">
        </div>

        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control bg-dark text-white border-secondary placeholder-light" name="telefono" value="{{ $contact->telefono }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control bg-dark text-white border-secondary placeholder-light" name="email" value="{{ $contact->email }}" required>
        </div>

        <div class="mb-3">
            <label for="contacto_emergencia" class="form-label">Contacto de Emergencia</label>
            <input type="text" class="form-control bg-dark text-white border-secondary placeholder-light" name="contacto_emergencia" value="{{ $contact->contacto_emergencia }}">
        </div>

        <div class="mb-4">
            <label for="departamento_id" class="form-label">Departamento</label>
            <select class="form-select bg-dark text-white border-secondary" name="departamento_id">
                <option value="">Seleccione</option>
                @foreach ($departamentos as $dep)
                    <option value="{{ $dep->id }}" @if($contact->departamento_id == $dep->id) selected @endif>
                        {{ $dep->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- APLICACIONES --}}
        <div class="mb-4">
            <label class="form-label">Aplicaciones</label><br>
            @foreach ($aplicaciones as $aplicacion)
                @php
                    // Obtener nombre_usuario de la tabla pivote para la aplicación actual o null
                    $nombreUsuario = $contact->aplicaciones->where('id', $aplicacion->id)->first()->pivot->nombre_usuario ?? '';
                @endphp

                <div class="form-check form-switch mb-3">
                    <input
                        class="form-check-input app-checkbox"
                        type="checkbox"
                        role="switch"
                        id="switch-app-{{ $aplicacion->id }}"
                        name="aplicaciones[]"
                        value="{{ $aplicacion->id }}"
                        {{ in_array($aplicacion->id, $aplicacionesSeleccionadas) ? 'checked' : '' }}
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
                        style="display: {{ in_array($aplicacion->id, $aplicacionesSeleccionadas) ? 'block' : 'none' }};"
                        value="{{ old('nombre_usuario.' . $aplicacion->id, $nombreUsuario) }}"
                    >
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-outline-light px-4">Actualizar</button>
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
            } else {
                input.style.display = 'none';
                input.value = ''; // limpiar si se desmarca
            }
        }

        checkboxes.forEach(cb => {
            toggleUsernameField(cb); // estado inicial
            cb.addEventListener('change', () => toggleUsernameField(cb));
        });
    });
</script>

@endsection
