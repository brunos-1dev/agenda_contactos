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
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container d-flex justify-content-between align-items-center">
    <div>
        <a class="navbar-brand" href="{{ url('/') }}">Inicio</a>
        <a class="navbar-brand" href="{{ route('contacts.index') }}">Personas</a>
        <a class="navbar-brand" href="{{ route('departamentos.index') }}">Departamentos</a>
        <a class="navbar-brand" href="{{ route('aplicaciones.index') }}">Aplicaciones</a>
        <a class="navbar-brand" href="#">Usuarios</a>
    </div>

    <div>
        @auth
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light">Cerrar Sesión</button>
            </form>
        @endauth
    </div>
</div>
</nav>

<div class="container">
    {{-- Aquí va el contenido de cada vista --}}
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
