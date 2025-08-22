{{-- resources/views/departamentos/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Departamento')

@section('content')

<div class="container text-white">
    <h2 class="mb-4 text-white text-center">Editar Departamento</h2>

    <form action="{{ route('departamentos.update', $departamento) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text"
                   name="nombre"
                   id="nombre"
                   class="form-control bg-dark text-white border-secondary placeholder-light"
                   required
                   value="{{ old('nombre', $departamento->nombre) }}"
                   placeholder="Ej. Tics">
            @error('nombre')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-outline-light px-4">Actualizar</button>
        <a href="{{ route('departamentos.index') }}" class="btn btn-outline-light px-4">Cancelar</a>
    </form>
</div>

@endsection
