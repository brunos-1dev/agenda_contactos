{{-- resources/views/departamentos/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nuevo Departamento')

@section('content')

<div class="container text-white">
    <h2 class="mb-4 text-white text-center">Agregar Departamento</h2>


    <form action="{{ route('departamentos.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text"
                   name="nombre"
                   id="nombre"
                   class="form-control bg-dark text-white border-secondary placeholder-light"
                   required
                   value="{{ old('nombre') }}"
                   placeholder="Ej. Tics">
            @error('nombre')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-outline-gray me-2">Guardar</button>
        <a href="{{ route('departamentos.index') }}" class="btn btn-outline-gray">Cancelar</a>
    </form>
</div>
@endsection
