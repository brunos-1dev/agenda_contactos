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

        {{-- 1) DNI y CUIL --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="dni" class="form-label">DNI *</label>
                <input type="number"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="dni" id="dni" required
                       placeholder="Ingrese DNI"
                       value="{{ old('dni') }}">
            </div>
            <div class="col-md-6">
                <label for="cuil" class="form-label">CUIL</label>
                <input type="text"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="cuil" id="cuil"
                       maxlength="11" inputmode="numeric" pattern="\d{0,11}"
                       placeholder="Ingrese CUIL"
                       value="{{ old('cuil') }}">
            </div>
        </div>

        {{-- 2) Nombre y Apellido --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="nombre" class="form-label">Nombre *</label>
                <input type="text"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="nombre" id="nombre" required
                       placeholder="Ingrese nombre"
                       value="{{ old('nombre') }}">
            </div>
            <div class="col-md-6">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="apellido" id="apellido"
                       placeholder="Ingrese apellido"
                       value="{{ old('apellido') }}">
            </div>
        </div>

        {{-- 3) Organización --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="organizacion_id" class="form-label">Organización</label>
                <select class="form-select bg-dark text-white border-secondary placeholder-light"
                        name="organizacion_id" id="organizacion_id">
                    <option value="">— Seleccionar —</option>
                    @foreach($orgs as $o)
                        <option value="{{ $o->id }}" {{ old('organizacion_id') == $o->id ? 'selected' : '' }}>
                            {{ $o->label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- 4) IUP y NI (juntos) --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="iup" class="form-label">IUP</label>
                <input type="text"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="iup" id="iup"
                       placeholder="Ingrese IUP"
                       value="{{ old('iup') }}">
            </div>
            <div class="col-md-6">
                <label for="ni" class="form-label">NI</label>
                <input type="text"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="ni" id="ni"
                       placeholder="Ingrese NI"
                       value="{{ old('ni') }}">
            </div>
        </div>

        {{-- 6) Jerarquía (select) --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="jerarquia" class="form-label">Jerarquía</label>
                <select name="jerarquia" id="jerarquia"
                        class="form-select bg-dark text-white border-secondary placeholder-light">
                    <option value="">— Seleccionar —</option>
                    <option value="Suboficial"           {{ old('jerarquia') === 'Suboficial' ? 'selected' : '' }}>Suboficial</option>
                    <option value="Oficial"              {{ old('jerarquia') === 'Oficial' ? 'selected' : '' }}>Oficial</option>
                    <option value="Subinspector"         {{ old('jerarquia') === 'Subinspector' ? 'selected' : '' }}>Subinspector</option>
                    <option value="Inspector"            {{ old('jerarquia') === 'Inspector' ? 'selected' : '' }}>Inspector</option>
                    <option value="Subcomisario"         {{ old('jerarquia') === 'Subcomisario' ? 'selected' : '' }}>Subcomisario</option>
                    <option value="Comisario"            {{ old('jerarquia') === 'Comisario' ? 'selected' : '' }}>Comisario</option>
                    <option value="Comisario Supervisor" {{ old('jerarquia') === 'Comisario Supervisor' ? 'selected' : '' }}>Comisario Supervisor</option>
                    <option value="Subdirector"          {{ old('jerarquia') === 'Subdirector' ? 'selected' : '' }}>Subdirector</option>
                    <option value="Director"             {{ old('jerarquia') === 'Director' ? 'selected' : '' }}>Director</option>
                    <option value="Director General"     {{ old('jerarquia') === 'Director General' ? 'selected' : '' }}>Director General</option>
                </select>
            </div>
        </div>

        {{-- 7) Domicilio y Localidad --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="domicilio" class="form-label">Domicilio</label>
                <input type="text"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="domicilio" id="domicilio"
                       placeholder="Ingrese domicilio"
                       value="{{ old('domicilio') }}">
            </div>
            <div class="col-md-6">
                <label for="localidad" class="form-label">Localidad</label>
                <input type="text"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="localidad" id="localidad"
                       placeholder="Ingrese localidad"
                       value="{{ old('localidad') }}">
            </div>
        </div>

        {{-- 8) Teléfono y Teléfono de Emergencia --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="telefono" class="form-label">Teléfono *</label>
                <input type="number"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="telefono" id="telefono" required
                       placeholder="Ingrese teléfono"
                       value="{{ old('telefono') }}">
            </div>
            <div class="col-md-6">
                <label for="contacto_emergencia" class="form-label">Teléfono de Emergencia</label>
                <input type="text"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="contacto_emergencia" id="contacto_emergencia"
                       placeholder="Ingrese teléfono de emergencia"
                       value="{{ old('contacto_emergencia') }}">
            </div>
        </div>

        {{-- 9) Email --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="email" class="form-label">Email *</label>
                <input type="email"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="email" id="email" required
                       placeholder="Ingrese email"
                       value="{{ old('email') }}">
            </div>
        </div>

        {{-- 10) Aplicaciones --}}
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
                input.value = '';
            }
        }

        checkboxes.forEach(cb => {
            toggleUsernameField(cb); // estado inicial
            cb.addEventListener('change', () => toggleUsernameField(cb));
        });
    });
</script>
@endsection
