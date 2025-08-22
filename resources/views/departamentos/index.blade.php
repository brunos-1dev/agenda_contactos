@extends('layouts.app')

@section('title', 'Listado de Departamentos')

@section('content')

<div class="container">
    <h2 class="mb-4 text-center">Listado de Departamentos</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form action="{{ route('departamentos.index') }}" method="GET" class="d-flex mb-3 justify-content-between align-items-center">
            <div class="d-flex">
                <input type="text"
                       name="search"
                       class="form-control me-2 bg-dark text-white border-secondary placeholder-light"
                       style="width: 350px;"
                       placeholder="Buscar por nombre"
                       value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-light px-4">Buscar</button>
            </div>
        </form>

        <a href="{{ route('departamentos.create') }}" class="btn btn-outline-light px-4">Nuevo Departamento</a>
    </div>

    <table class="table table-dark table-hover text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>Nombre</th>
                <th style="width: 260px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($departamentos as $departamento)
                <tr>
                    <td>{{ $departamento->nombre }}</td>
                    <td>
                        <a href="{{ route('departamentos.edit', $departamento) }}" class="btn btn-outline-warning btn-sm me-1">Editar</a>

                        <form action="{{ route('departamentos.destroy', $departamento) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Estás seguro de eliminar este departamento?');">
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
