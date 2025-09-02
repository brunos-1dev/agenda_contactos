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

        {{-- 1) DNI y CUIL --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="dni" class="form-label">DNI</label>
                <input type="number"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       id="dni"
                       value="{{ $contact->dni }}"
                       disabled>
            </div>
            <div class="col-md-6">
                <label for="cuil" class="form-label">CUIL</label>
                <input type="text"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="cuil" id="cuil"
                       maxlength="11" inputmode="numeric" pattern="\d{0,11}"
                       placeholder="Solo números (11)"
                       value="{{ old('cuil', $contact->cuil) }}">
            </div>
        </div>

        {{-- 2) Nombre y Apellido --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="nombre" class="form-label">Nombre *</label>
                <input type="text"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="nombre" id="nombre" required
                       value="{{ old('nombre', $contact->nombre) }}">
            </div>
            <div class="col-md-6">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="apellido" id="apellido"
                       value="{{ old('apellido', $contact->apellido) }}">
            </div>
        </div>

        {{-- 3) Organización --}}
        <div class="row mb-3">
            <div class="col-md-12">
                <label for="organizacion_id" class="form-label">Organización</label>
                <select class="form-select bg-dark text-white border-secondary placeholder-light"
                        name="organizacion_id" id="organizacion_id">
                    <option value="">— Sin asignar —</option>
                    @foreach($orgs as $o)
                        <option value="{{ $o->id }}"
                            {{ (string)old('organizacion_id', $contact->organizacion_id) === (string)$o->id ? 'selected' : '' }}>
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
                       value="{{ old('iup', $contact->iup) }}">
            </div>
            <div class="col-md-6">
                <label for="ni" class="form-label">NI</label>
                <input type="text"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="ni" id="ni"
                       value="{{ old('ni', $contact->ni) }}">
            </div>
        </div>

        {{-- 5) Jerarquía (select) --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="jerarquia" class="form-label">Jerarquía</label>
                @php $jerOld = old('jerarquia', $contact->jerarquia); @endphp
                <select name="jerarquia" id="jerarquia"
                        class="form-select bg-dark text-white border-secondary placeholder-light">
                    <option value="">— Seleccionar —</option>
                    <option value="Suboficial"           {{ $jerOld === 'Suboficial' ? 'selected' : '' }}>Suboficial</option>
                    <option value="Oficial"              {{ $jerOld === 'Oficial' ? 'selected' : '' }}>Oficial</option>
                    <option value="Subinspector"         {{ $jerOld === 'Subinspector' ? 'selected' : '' }}>Subinspector</option>
                    <option value="Inspector"            {{ $jerOld === 'Inspector' ? 'selected' : '' }}>Inspector</option>
                    <option value="Subcomisario"         {{ $jerOld === 'Subcomisario' ? 'selected' : '' }}>Subcomisario</option>
                    <option value="Comisario"            {{ $jerOld === 'Comisario' ? 'selected' : '' }}>Comisario</option>
                    <option value="Comisario Supervisor" {{ $jerOld === 'Comisario Supervisor' ? 'selected' : '' }}>Comisario Supervisor</option>
                    <option value="Subdirector"          {{ $jerOld === 'Subdirector' ? 'selected' : '' }}>Subdirector</option>
                    <option value="Director"             {{ $jerOld === 'Director' ? 'selected' : '' }}>Director</option>
                    <option value="Director General"     {{ $jerOld === 'Director General' ? 'selected' : '' }}>Director General</option>
                </select>
            </div>
        </div>

        {{-- 6) Domicilio y Localidad --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="domicilio" class="form-label">Domicilio</label>
                <input type="text"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="domicilio" id="domicilio"
                       value="{{ old('domicilio', $contact->domicilio) }}">
            </div>
            <div class="col-md-6">
                <label for="localidad" class="form-label">Localidad</label>
                <input type="text"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="localidad" id="localidad"
                       value="{{ old('localidad', $contact->localidad) }}">
            </div>
        </div>

        {{-- 7) Teléfono y Teléfono de Emergencia --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="telefono" class="form-label">Teléfono *</label>
                <input type="number"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="telefono" id="telefono" required
                       value="{{ old('telefono', $contact->telefono) }}">
            </div>
            <div class="col-md-6">
                <label for="contacto_emergencia" class="form-label">Teléfono de Emergencia</label>
                <input type="text"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="contacto_emergencia" id="contacto_emergencia"
                       value="{{ old('contacto_emergencia', $contact->contacto_emergencia) }}">
            </div>
        </div>

        {{-- 8) Email --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="email" class="form-label">Email *</label>
                <input type="email"
                       class="form-control bg-dark text-white border-secondary placeholder-light"
                       name="email" id="email" required
                       value="{{ old('email', $contact->email) }}">
            </div>
        </div>

        {{-- 9) Aplicaciones --}}
        <div class="mb-4">
            <label class="form-label">Aplicaciones</label><br>
            @foreach ($aplicaciones as $aplicacion)
                @php
                    $checkedList = old('aplicaciones', $aplicacionesSeleccionadas ?? []);
                    $isChecked = in_array($aplicacion->id, $checkedList);
                    $pivotNombre = optional(($pivotData ?? collect())->get($aplicacion->id))->pivot->nombre_usuario ?? '';
                    $nombreUsuarioValue = old("nombre_usuario.$aplicacion->id", $pivotNombre);
                @endphp

                <div class="form-check form-switch mb-3">
                    <input
                        class="form-check-input app-checkbox"
                        type="checkbox"
                        role="switch"
                        id="switch-app-{{ $aplicacion->id }}"
                        name="aplicaciones[]"
                        value="{{ $aplicacion->id }}"
                        {{ $isChecked ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="switch-app-{{ $aplicacion->id }}">
                        {{ $aplicacion->nombre }}
                    </label>

                    <input
                        type="text"
                        name="nombre_usuario[{{ $aplicacion->id }}]"
                        placeholder="Nombre de usuario para {{ $aplicacion->nombre }}"
                        class="form-control mt-2 bg-dark text-white border-secondary placeholder-light usuario-field"
                        style="{{ $isChecked ? 'display: block;' : 'display: none;' }}"
                        {{ $isChecked ? '' : 'disabled' }}
                        value="{{ $nombreUsuarioValue }}"
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
