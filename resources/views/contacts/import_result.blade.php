@extends('layouts.app')

@section('title','Resultado de Importación')

@section('content')
<div class="container">
    <h2 class="mb-4 text-white">Resultado de Importación</h2>

    <div class="alert alert-success">
        <strong>Insertados:</strong> {{ $inserted }} &nbsp;|&nbsp;
        <strong>Actualizados:</strong> {{ $updated }}
    </div>

    <div class="card bg-dark border-secondary">
        <div class="card-header border-secondary text-white-50">
            Errores ({{ count($errorsRows) }})
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-striped m-0">
                    <thead>
                        <tr>
                            <th style="width:80px;">Fila</th>
                            <th>Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($errorsRows as $err)
                        <tr>
                            <td>{{ $err['row'] }}</td>
                            <td>{{ $err['msg'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center text-white-50 py-4">Sin errores</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary">Volver a Personas</a>
    </div>
</div>
@endsection
