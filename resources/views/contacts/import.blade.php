@extends('layouts.app')

@section('title','Importar Personas')

@section('content')
<div class="container">
    <h2 class="mb-4 text-white">Importar Personas</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Descarga de plantilla según permisos --}}
    <div class="card bg-dark border-secondary mb-4">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="text-white-50">
                Descargá una <strong>plantilla Excel</strong> con validaciones (jerarquía y organizaciones).
            </div>
            <a href="{{ route('contacts.import.template') }}" class="btn btn-outline-info">
                Descargar plantilla
            </a>
        </div>
    </div>

    {{-- Formulario de carga --}}
    <div class="card bg-dark border-secondary">
        <div class="card-body">
            <form action="{{ route('contacts.import.process') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column gap-3">
                @csrf
                <div>
                    <label class="form-label text-white">Archivo (.xlsx o .csv)</label>
                    <input type="file" name="file" accept=".xlsx,.csv" class="form-control bg-dark text-white border-secondary" required>
                    <div class="form-text text-white-50">
                        La primera fila debe ser el encabezado de columnas de la plantilla.
                    </div>
                </div>
                <button class="btn btn-outline-light px-4" type="submit">Importar</button>
                <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary">Volver a Personas</a>
            </form>
        </div>
    </div>
</div>
@endsection
