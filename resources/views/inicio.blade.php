@extends('layouts.app')

@section('title', 'Inicio')


@section('content')

<style>
    .menu-btn {
        width: 300px;
        padding: 20px;
        font-size: 1.5rem;
        font-weight: bold;
        border-radius: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    }

    body {
        background-color: #e0f4fa ; /* Color de fondo suave */
    }
</style>

<div class="text-center my-5">
    <div class="bg-primary text-white py-2 w-100">
    <h1 class="text-center m-0">
        Sistema de Registro de Usuarios (SiRUs)
    </h1>
</div>
<br>
<br>
    <h4 class="mb-4">SELECCIONE UNA OPCIÓN PARA CONTINUAR NAVEGANDO:</h4>
    <br>
    <div class="d-flex justify-content-center gap-4 flex-wrap">
        <a href="{{ route('contacts.index') }}" class="btn btn-primary menu-btn">
            <img src="{{ asset('images/persona3.png') }}" alt="Personas" width="200" height="200" class="mb-2"> PERSONAS</a>
        <a href="{{ route('departamentos.index') }}" class="btn btn-primary menu-btn">
            <img src="{{ asset('images/depto2.png') }}" alt="Departamentos" width="200" height="200" class="mb-2">DEPARTAMENTOS</a>
     <a href="{{ route('aplicaciones.index') }}" class="btn btn-primary menu-btn">
            <img src="{{ asset('images/logo app corto.png') }}" alt="Aplicaciones" width="200" height="200" class="mb-2">APLICACIONES</a>
</div>
@endsection
