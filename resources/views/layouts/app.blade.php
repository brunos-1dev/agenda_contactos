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
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4">


    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="{{ url('/') }}">Inicio</a>

        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('contacts.index') }}">Personas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('departamentos.index') }}">Departamentos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('aplicaciones.index') }}">Aplicaciones</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Usuarios</a>
            </li>
        </ul>

        @auth
            <form method="POST" action="{{ route('logout') }}" class="d-flex">
                @csrf
                <button class="btn btn-outline-info btn-sm">Cerrar sesión</button>


            </form>
        @endauth
    </div>
</nav>



<div class="container">
    {{-- Aquí va el contenido de cada vista --}}
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
