@extends('layouts.app')

@section('title', 'Listado de Aplicaciones')

@section('content')

<div class="container">
    <h2 class="mb-4 text-center">Listado de Aplicaciones</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form action="{{ route('aplicaciones.index') }}" method="GET" class="d-flex mb-3 justify-content-between align-items-center">
            <div class="d-flex">
                <input type="text" name="search" class="form-control me-2 bg-dark text-white border-secondary placeholder-light" style="width: 350px;" placeholder="Buscar por nombre" value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-secondary">Buscar</button>
            </div>
        </form>

        <a href="{{ route('aplicaciones.create') }}" class="btn btn-outline-secondary">Nueva Aplicación</a>
    </div>

    <table class="table table-dark table-hover text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>Nombre</th>
                <th style="width: 260px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($aplicaciones as $aplicacion)
                <tr>
                    <td>{{ $aplicacion->nombre }}</td>
                    <td>
                        <a href="{{ route('aplicaciones.edit', $aplicacion->id) }}" class="btn btn-outline-warning btn-sm me-1">Editar</a>

                        <form action="{{ route('aplicaciones.destroy', $aplicacion->id) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Estás seguro de eliminar esta aplicación?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
