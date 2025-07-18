@extends('layouts.app')

@section('title', 'Ver Contacto')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center text-white">Detalle del Contacto</h2>

    <form>
        @csrf

        <div class="row mb-3">
            <div class="col">
                <label for="dni" class="form-label text-white">DNI</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" value="{{ $contact->dni }}" disabled>
            </div>
            <div class="col">
                <label for="ni" class="form-label text-white">NI</label>
                <input type="text" class="form-control bg-dark text-light border-secondary" value="{{ $contact->ni }}" disabled>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label for="nombre" class="form-label text-white">Nombre</label>
                <input type="text" class="form-control bg-dark text-light border-secondary" value="{{ $contact->nombre }}" disabled>
            </div>
            <div class="col">
                <label for="apellido" class="form-label text-white">Apellido</label>
                <input type="text" class="form-control bg-dark text-light border-secondary" value="{{ $contact->apellido }}" disabled>
            </div>
        </div>

        <div class="mb-3">
            <label for="domicilio" class="form-label text-white">Domicilio</label>
            <input type="text" class="form-control bg-dark text-light border-secondary" value="{{ $contact->domicilio }}" disabled>
        </div>

        <div class="mb-3">
            <label for="telefono" class="form-label text-white">Teléfono</label>
            <input type="text" class="form-control bg-dark text-light border-secondary" value="{{ $contact->telefono }}" disabled>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label text-white">Email</label>
            <input type="email" class="form-control bg-dark text-light border-secondary" value="{{ $contact->email }}" disabled>
        </div>

        <div class="mb-3">
            <label for="contacto_emergencia" class="form-label text-white">Contacto de Emergencia</label>
            <input type="text" class="form-control bg-dark text-light border-secondary" value="{{ $contact->contacto_emergencia }}" disabled>
        </div>

        <div class="mb-4">
            <label for="departamento_id" class="form-label text-white">Departamento</label>
            <select class="form-select bg-dark text-light border-secondary" disabled>
                <option value="">Seleccione</option>
                @foreach($departamentos as $departamento)
                    <option value="{{ $departamento->id }}" {{ $contact->departamento_id == $departamento->id ? 'selected' : '' }}>
                        {{ $departamento->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label text-white">Aplicaciones Asignadas</label>
            <ul class="list-group">
                @forelse ($contact->aplicaciones as $aplicacion)
                    <li class="list-group-item bg-dark text-light border-secondary">
                        {{ $aplicacion->nombre }}
                        @php
                            // Obtener nombre_usuario del pivot
                            $username = $aplicacion->pivot->nombre_usuario ?? null;
                        @endphp
                        @if ($username)
                            <br><small class="text-white">Usuario: {{ $username }}</small>
                        @endif
                    </li>
                @empty
                    <li class="list-group-item bg-dark text-muted border-secondary">No tiene aplicaciones asignadas.</li>
                @endforelse
            </ul>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('contacts.index') }}" class="btn btn-outline-light px-4">Volver</a>
        </div>

    </form>
</div>
@endsection
