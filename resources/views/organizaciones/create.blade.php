@extends('layouts.app')

@section('title', 'Nueva Organización')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center text-white">Nueva Organización</h2>

    <div class="card card-people border-0 shadow-none">
        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Revisá los campos:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('organizaciones.store') }}" method="POST">
                @csrf

                {{-- Fila 1: Nombre (full width) --}}
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Nombre</label>
                        <input
                            type="text"
                            name="nombre"
                            value="{{ old('nombre') }}"
                            class="form-control bg-dark text-white border-secondary @error('nombre') is-invalid @enderror"
                            required
                        >
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Fila 2: Tipo (más chico) + Padre (al lado) --}}
                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label class="form-label">Tipo</label>
                        <select
                            name="tipo"
                            class="form-select bg-dark text-white border-secondary @error('tipo') is-invalid @enderror"
                            required
                        >
                            <option value="" hidden>Seleccionar…</option>
                            @foreach($tipos as $t)
                                <option value="{{ $t }}" @selected(old('tipo')===$t)>{{ $t }}</option>
                            @endforeach
                        </select>
                        @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Padre</label>
                        <select
                            name="id_padre"
                            class="form-select bg-dark text-white border-secondary @error('id_padre') is-invalid @enderror"
                        >
                            <option value="">— Sin padre (raíz) —</option>
                            @foreach($parents as $p)
                                <option value="{{ $p->id }}" @selected(old('id_padre')==$p->id)>{{ $p->label }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Elegí la organización padre o dejá vacío para crearla en la raíz.</div>
                        @error('id_padre') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Acciones: Cancelar + Activo + Guardar --}}
                <div class="mt-4 d-flex align-items-center gap-3">
                    <a href="{{ route('organizaciones.index') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>

                    <div class="form-check ms-auto">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            value="1"
                            name="activo"
                            id="chkActivo"
                            {{ old('activo', 1) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="chkActivo">
                            Activo
                        </label>
                    </div>

                    <button type="submit" class="btn btn-outline-secondary">
                        Guardar
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

