@extends('layouts.app')

@section('title', 'Editar Contacto')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center">Editar Contacto</h2>

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
                <input type="number" class="form-control" name="dni" value="{{ $contact->dni }}" disabled>
            </div>
            <div class="col">
                <label for="ni" class="form-label">NI</label>
                <input type="text" class="form-control" name="ni" value="{{ $contact->ni }}">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nombre" value="{{ $contact->nombre }}" required>
            </div>
            <div class="col">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" class="form-control" name="apellido" value="{{ $contact->apellido }}">
            </div>
        </div>

        <div class="mb-3">
            <label for="domicilio" class="form-label">Domicilio</label>
            <input type="text" class="form-control" name="domicilio" value="{{ $contact->domicilio }}">
        </div>

        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control" name="telefono" value="{{ $contact->telefono }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="{{ $contact->email }}" required>
        </div>

        <div class="mb-3">
            <label for="contacto_emergencia" class="form-label">Contacto de Emergencia</label>
            <input type="text" class="form-control" name="contacto_emergencia" value="{{ $contact->contacto_emergencia }}">
        </div>

        <div class="mb-4">
            <label for="departamento_id" class="form-label">Departamento</label>
            <select class="form-select" name="departamento_id">
                <option value="">Seleccione</option>
                @foreach ($departamentos as $dep)
                    <option value="{{ $dep->id }}" @if($contact->departamento_id == $dep->id) selected @endif>
                        {{ $dep->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('contacts.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
