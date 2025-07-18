@extends('layouts.app')

@section('title', 'Nueva Aplicación')

@section('content')

<div class="container text-white">
    <h2 class="mb-4 text-white text-center">Nueva Aplicación</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('aplicaciones.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de la Aplicación</label>
            <input type="text"
                   name="nombre"
                   id="nombre"
                   class="form-control bg-dark text-white border-secondary placeholder-light"
                   required
                   value="{{ old('nombre') }}"
                   placeholder="Ej. Itop">
        </div>

        <button class="btn btn-outline-gray me-2">Guardar</button>
        <a href="{{ route('aplicaciones.index') }}" class="btn btn-outline-gray">Cancelar</a>
    </form>
</div>

@endsection
