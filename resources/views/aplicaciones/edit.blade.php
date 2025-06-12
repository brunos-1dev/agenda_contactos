@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Aplicación</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('aplicaciones.update', $aplicacion->id_aplicacion) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de la Aplicación</label>
            <input type="text" name="nombre" class="form-control" value="{{ $aplicacion->nombre }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('aplicaciones.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
