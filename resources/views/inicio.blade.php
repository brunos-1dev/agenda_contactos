@extends('layouts.app')

@section('title', 'Inicio')


@section('content')
<div class="text-center my-5">
    <h1 class="mb-4">Bienvenido a la Agenda</h1>
    <h5 class="mb-4">Selecciona una opción para continuar:</h5>
    <div class="d-flex justify-content-center gap-3">
        <a href="{{ route('contacts.index') }}" class="btn btn-secondary btn-lg">Contactos</a>
        <a href="{{ route('departamentos.index') }}" class="btn btn-secondary btn-lg">Departamentos</a>
    </div>
</div>
@endsection
