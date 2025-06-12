{{-- resources/views/aplicaciones/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Aplicaciones</h1>

    <a href="{{ route('aplicaciones.create') }}" class="btn btn-primary mb-3">
        Nueva Aplicación
    </a>

    @if (session('success'))
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
            @foreach($aplicaciones as $aplicacion)
                <tr>
                    <td>{{ $aplicacion->id_aplicacion }}</td>
                    <td>{{ $aplicacion->nombre }}</td>
                    <td>
                        <a href="{{ route('aplicaciones.edit', $aplicacion->id_aplicacion) }}"
                           class="btn btn-sm btn-warning">Editar</a>

                        <form action="{{ route('aplicaciones.destroy', $aplicacion->id_aplicacion) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('¿Estás seguro?')">
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
