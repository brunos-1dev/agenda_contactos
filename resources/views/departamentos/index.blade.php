{{-- resources/views/departamentos/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Departamentos')

@section('content')
<div class="container">
    <h1 class="mb-4 text-center">Listado de Departamentos</h1>

    <a href="{{ route('departamentos.create') }}" class="btn btn-success mb-3">Nuevo Departamento</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($departamentos as $departamento)
                <tr>
                    <td>{{ $departamento->id }}</td>
                    <td>{{ $departamento->nombre }}</td>
                    <td>
                        <a href="{{ route('departamentos.edit', $departamento) }}" class="btn btn-sm btn-warning">Editar</a>

                        <form action="{{ route('departamentos.destroy', $departamento) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Estás seguro de eliminar este departamento?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
