@extends('layouts.app')

@section('title', 'Inicio')

@section('content')


<div class="text-center mt-2 mb-4">

    <div class="d-flex justify-content-center align-items-center gap-4">
        <img src="{{ asset('images/ssa_lea2.svg') }}" alt="Logo SSA" style="height: 200px;">
        <img src="{{ asset('images/tics_lea2.svg') }}" alt="Logo TICS" style="height: 120px;">
    </div>

    <div class="text-light py-2 w-100">
        <h1 class="text-center m-0">
            Sistema de Registro de Usuarios
        </h1>
    </div>

    <br><br>
    
    <br>

    <div id="carouselMenu" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner text-center">
            <div class="carousel-item active" data-bs-interval="5000">
                <a href="{{ route('contacts.index') }}" class="menu-btn">
                    <img src="{{ asset('images/persona3.png') }}" alt="Personas" class="mb-2" />
                    <div class="menu-btn-text">PERSONAS</div>
                </a>
            </div>
            <div class="carousel-item" data-bs-interval="5000">
                <a href="{{ route('departamentos.index') }}" class="menu-btn">
                    <img src="{{ asset('images/depto2.png') }}" alt="Departamentos" class="mb-2" />
                    <div class="menu-btn-text">DEPARTAMENTOS</div>
                </a>
            </div>
            <div class="carousel-item" data-bs-interval="5000">
                <a href="{{ route('aplicaciones.index') }}" class="menu-btn">
                    <img src="{{ asset('images/logo app corto.png') }}" alt="Aplicaciones" class="mb-2" />
                    <div class="menu-btn-text">APLICACIONES</div>
                </a>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselMenu" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselMenu" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>
        <br><br>
    
</div>

@endsection
