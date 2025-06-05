@extends('layouts.app')

@section('title', 'Página de Inicio')

@section('content')
<div class="text-center my-5">
    <h1 class="mb-4">Bienvenido a la Agenda</h1>
    <div class="d-flex justify-content-center gap-3">
        <a href="{{ route('contacts.index') }}" class="btn btn-primary btn-lg">Contactos</a>
        <a href="{{ route('departamentos.index') }}" class="btn btn-secondary btn-lg">Departamentos</a>
    </div>
</div>
@endsection
