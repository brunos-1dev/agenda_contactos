<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>@yield('title', 'Agenda')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet" />
    @vite('resources/css/app.css')
</head>
<body>
    @php
        $canManage = auth()->check() && in_array(auth()->user()->rol, ['admin', 'superadmin']);
    @endphp
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        {{-- Logos a la izquierda --}}
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('images/ssa_lea2.svg') }}" alt="Logo SSA" style="height: 60px;" />
            <img src="{{ asset('images/tics_lea2.svg') }}" alt="Logo TICS" style="height: 40px;" />
        </div>

        {{-- Navegación centrada --}}
        <ul class="navbar-nav mx-auto d-flex flex-row gap-4">
            <li class="nav-item">
                <a class="nav-link text-uppercase text-info fw-semibold" href="{{ url('/') }}">Inicio</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase text-info fw-semibold" href="{{ route('contacts.index') }}">Personas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase text-info fw-semibold" href="{{ route('organizaciones.index') }}">Departamentos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase text-info fw-semibold" href="{{ route('aplicaciones.index') }}">Aplicaciones</a>
            </li>
            @if($canManage)
                <li class="nav-item">
                    <a class="nav-link text-uppercase text-info fw-semibold" href="{{ route('usuarios.index') }}">Usuarios</a>
                </li>
            @endif
        </ul>

        {{-- Cerrar sesión a la derecha --}}
        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-outline-info btn-sm">Cerrar sesión</button>
            </form>
        @endauth

    </div>
</nav>

<div class="container">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>