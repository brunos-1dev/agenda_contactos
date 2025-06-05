@extends('layouts.app')
@section('title', 'Agenda de Contactos')
@section('content')
<div class="container mt-4">
    <h2>Nuevo Contacto</h2>

    <form action="{{ route('contacts.store') }}" method="POST" novalidate>
        @csrf

        <div class="mb-3">
            <label for="dni" class="form-label">DNI</label>
            <input type="number"
                   class="form-control @error('dni') is-invalid @enderror"
                   id="dni" name="dni"
                   value="{{ old('dni') }}"
                   required>
            @error('dni')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text"
                   class="form-control @error('nombre') is-invalid @enderror"
                   id="nombre" name="nombre"
                   value="{{ old('nombre') }}"
                   required maxlength="20">
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   id="email" name="email"
                   value="{{ old('email') }}"
                   required maxlength="30">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="tel"
                   class="form-control @error('telefono') is-invalid @enderror"
                   id="telefono" name="telefono"
                   value="{{ old('telefono') }}"
                   required>
            @error('telefono')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('contacts.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
</div>
@endsection
