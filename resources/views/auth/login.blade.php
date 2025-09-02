<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            background-color: #1e3a8a !important; /* azul del fondo igual que inicio */
            color: #fff;
        }
        .login-card {
            background-color: #2c3e50;
            color: #fff;
            border-radius: 12px;
        }
        .form-control {
            background-color: #ecf0f1;
            color: #000;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">

<div class="text-center position-absolute top-0 start-50 translate-middle-x mt-4">
    <div class="d-flex justify-content-center align-items-center gap-4">
        <img src="{{ asset('images/ssa_lea2.svg') }}" alt="Logo SSA" style="height: 120px;">
        <img src="{{ asset('images/tics_lea2.svg') }}" alt="Logo TICS" style="height: 80px;">
    </div>
    <h2 class="mt-3">Sistema de Registro de Usuarios</h2>
</div>

<div class="card p-4 shadow login-card" style="min-width: 300px; max-width: 400px;">
    <h4 class="mb-3 text-center">Iniciar Sesión</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" name="email" id="email" class="form-control" required autofocus value="{{ old('email') }}">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-success">Ingresar</button>
        </div>
    </form>
</div>

</body>
</html>
