@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
 <br><br>
<div class="text-center mt-2 mb-4">
    <div class="text-light py-2 w-100">
        <h1 class="text-center m-0">Sistema de Registro de Usuarios</h1>
    </div>

    <br><br>

   <div class="menu-grid mt-4 mb-5">
    <a href="{{ route('contacts.index') }}" class="menu-card">
        <img src="{{ asset('images/persona3.png') }}" alt="Personas">
        <div class="menu-btn-text">PERSONAS</div>
    </a>
    <a href="{{ route('departamentos.index') }}" class="menu-card">
        <img src="{{ asset('images/depto2.png') }}" alt="Departamentos">
        <div class="menu-btn-text">DEPARTAMENTOS</div>
    </a>
    <a href="{{ route('aplicaciones.index') }}" class="menu-card">
        <img src="{{ asset('images/logo app corto.png') }}" alt="Aplicaciones">
        <div class="menu-btn-text">APLICACIONES</div>
    </a>
    <a href="{{ route('usuarios.index') }}" class="menu-card">
        <img src="{{ asset('images/usuarios.jpg') }}" alt="Usuarios">
        <div class="menu-btn-text">USUARIOS</div>
    </a>
</div>


    </div>

    <br><br>
    <div class="d-flex justify-content-center align-items-center gap-4">
        <img src="{{ asset('images/ssa_lea2.svg') }}" alt="Logo SSA" style="height: 200px;">
        <img src="{{ asset('images/tics_lea2.svg') }}" alt="Logo TICS" style="height: 120px;">
    </div>
</div>
@endsection