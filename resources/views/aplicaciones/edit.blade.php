@extends('layouts.app')

@section('title', 'Editar Aplicación')

@section('content')

<div class="container text-white">
    <h2 class="mb-4 text-white text-center">Editar Aplicación</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('aplicaciones.update', $aplicacion->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de la Aplicación</label>
            <input type="text"
                   name="nombre"
                   id="nombre"
                   class="form-control bg-dark text-white border-secondary placeholder-light"
                   value="{{ old('nombre', $aplicacion->nombre) }}"
                   required
                   placeholder="Ej. Mi Aplicación">
        </div>

        <button type="submit" class="btn btn-outline-gray me-2">Actualizar</button>
        <a href="{{ route('aplicaciones.index') }}" class="btn btn-outline-gray">Cancelar</a>
    </form>
</div>

@endsection
