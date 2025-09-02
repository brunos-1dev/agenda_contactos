@extends('layouts.app')

@section('title', 'Ver Contacto')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center text-white">Detalle del Contacto</h2>

    {{-- SOLO LECTURA (inputs deshabilitados para mantener estética) --}}
    <form>
        @csrf

        {{-- 1) DNI y CUIL --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label text-white">DNI</label>
                <input type="number" class="form-control bg-dark text-light border-secondary"
                       value="{{ $contact->dni }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label text-white">CUIL</label>
                <input type="text" class="form-control bg-dark text-light border-secondary"
                       value="{{ $contact->cuil }}" disabled>
            </div>
        </div>

        {{-- 2) Nombre y Apellido --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label text-white">Nombre</label>
                <input type="text" class="form-control bg-dark text-light border-secondary"
                       value="{{ $contact->nombre }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label text-white">Apellido</label>
                <input type="text" class="form-control bg-dark text-light border-secondary"
                       value="{{ $contact->apellido }}" disabled>
            </div>
        </div>

        {{-- 3) Organización --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label text-white">Organización</label>
                @php
                    $orgNombre = optional($contact->organizacion)->nombre;
                    $orgTipo   = optional($contact->organizacion)->tipo;
                    $orgLabel  = $orgNombre
                                 ? ($orgTipo ? "$orgNombre ($orgTipo)" : $orgNombre)
                                 : 'Sin asignar';
                @endphp
                <input type="text" class="form-control bg-dark text-light border-secondary"
                       value="{{ $orgLabel }}" disabled>
            </div>
        </div>

        {{-- 4) IUP y NI --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label text-white">IUP</label>
                <input type="text" class="form-control bg-dark text-light border-secondary"
                       value="{{ $contact->iup }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label text-white">NI</label>
                <input type="text" class="form-control bg-dark text-light border-secondary"
                       value="{{ $contact->ni }}" disabled>
            </div>
        </div>

        {{-- 5) Jerarquía --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label text-white">Jerarquía</label>
                <input type="text" class="form-control bg-dark text-light border-secondary"
                       value="{{ $contact->jerarquia }}" disabled>
            </div>
        </div>

        {{-- 6) Domicilio y Localidad --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label text-white">Domicilio</label>
                <input type="text" class="form-control bg-dark text-light border-secondary"
                       value="{{ $contact->domicilio }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label text-white">Localidad</label>
                <input type="text" class="form-control bg-dark text-light border-secondary"
                       value="{{ $contact->localidad }}" disabled>
            </div>
        </div>

        {{-- 7) Teléfono y Teléfono de Emergencia --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label text-white">Teléfono</label>
                <input type="text" class="form-control bg-dark text-light border-secondary"
                       value="{{ $contact->telefono }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label text-white">Teléfono de Emergencia</label>
                <input type="text" class="form-control bg-dark text-light border-secondary"
                       value="{{ $contact->contacto_emergencia }}" disabled>
            </div>
        </div>

        {{-- 8) Email --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label text-white">Email</label>
                <input type="email" class="form-control bg-dark text-light border-secondary"
                       value="{{ $contact->email }}" disabled>
            </div>
        </div>

        {{-- 9) Departamento (legacy) - mostrar sólo si hay dato --}}
        @if ($contact->departamento_id && isset($contact->departamento))
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label text-white">Departamento (legacy)</label>
                    <input type="text" class="form-control bg-dark text-light border-secondary"
                           value="{{ $contact->departamento->nombre }}" disabled>
                </div>
            </div>
        @endif

        {{-- 10) Aplicaciones asignadas --}}
        <div class="mb-4">
            <label class="form-label text-white">Aplicaciones Asignadas</label>
            <ul class="list-group">
                @forelse ($contact->aplicaciones as $aplicacion)
                    <li class="list-group-item bg-dark text-light border-secondary">
                        {{ $aplicacion->nombre }}
                        @php $username = $aplicacion->pivot->nombre_usuario ?? null; @endphp
                        @if ($username)
                            <br><small class="text-white">Usuario: {{ $username }}</small>
                        @endif
                    </li>
                @empty
                    <li class="list-group-item bg-dark text-muted border-secondary">
                        No tiene aplicaciones asignadas.
                    </li>
                @endforelse
            </ul>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('contacts.index') }}" class="btn btn-outline-light px-4">Volver</a>
        </div>
    </form>
</div>
@endsection
