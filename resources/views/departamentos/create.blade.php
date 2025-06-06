{{-- resources/views/departamentos/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nuevo Departamento')

@section('content')
<div class="container">
    <h1 class="mb-4">Agregar Departamento</h1>

    <form action="{{ route('departamentos.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required value="{{ old('nombre') }}">
            @error('nombre')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-success">Guardar</button>
        <a href="{{ route('departamentos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
