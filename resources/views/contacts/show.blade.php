@extends('layouts.app')

@section('title', 'Ver Contacto')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center">Detalle del Contacto</h2>

    <form>
        @csrf {{-- Aunque no se envía nada, para mantener estructura --}}

        <div class="row mb-3">
            <div class="col">
                <label for="dni" class="form-label">DNI</label>
                <input type="number" class="form-control" name="dni" value="{{ $contact->dni }}" disabled>
            </div>
            <div class="col">
                <label for="ni" class="form-label">NI</label>
                <input type="text" class="form-control" name="ni" value="{{ $contact->ni }}" disabled>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nombre" value="{{ $contact->nombre }}" disabled>
            </div>
            <div class="col">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" class="form-control" name="apellido" value="{{ $contact->apellido }}" disabled>
            </div>
        </div>

        <div class="mb-3">
            <label for="domicilio" class="form-label">Domicilio</label>
            <input type="text" class="form-control" name="domicilio" value="{{ $contact->domicilio }}" disabled>
        </div>

        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control" name="telefono" value="{{ $contact->telefono }}" disabled>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="{{ $contact->email }}" disabled>
        </div>

        <div class="mb-3">
            <label for="contacto_emergencia" class="form-label">Contacto de Emergencia</label>
            <input type="text" class="form-control" name="contacto_emergencia" value="{{ $contact->contacto_emergencia }}" disabled>
        </div>

        <div class="mb-4">
            <label for="departamento_id" class="form-label">Departamento</label>
            <select class="form-select" name="departamento_id" disabled>
                <option value="">Seleccione</option>
                @foreach($departamentos as $departamento)
                    <option value="{{ $departamento->id }}" {{ $contact->departamento_id == $departamento->id ? 'selected' : '' }}>
                        {{ $departamento->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <a href="{{ route('contacts.index') }}" class="btn btn-secondary">Volver</a>
    </form>
</div>
@endsection
