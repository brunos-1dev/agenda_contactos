@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Editar Contacto - DNI: {{ $contact->dni }}</h2>

    <form action="{{ route('contacts.update', $contact->dni) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="dni" class="form-label">DNI</label>
            <input type="number"
                   class="form-control"
                   id="dni" name="dni"
                   value="{{ $contact->dni }}"
                   disabled>
        </div>

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text"
                   class="form-control @error('nombre') is-invalid @enderror"
                   id="nombre" name="nombre"
                   value="{{ old('nombre', $contact->nombre) }}"
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
                   value="{{ old('email', $contact->email) }}"
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
                   value="{{ old('telefono', $contact->telefono) }}"
                   required>
            @error('telefono')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="{{ route('contacts.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
</div>
@endsection
