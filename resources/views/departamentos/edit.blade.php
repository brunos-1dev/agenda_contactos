{{-- resources/views/departamentos/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Departamento')

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Departamento</h1>

    <form action="{{ route('departamentos.update', $departamento) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required value="{{ old('nombre', $departamento->nombre) }}">
            @error('nombre')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-success">Actualizar</button>
        <a href="{{ route('departamentos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
