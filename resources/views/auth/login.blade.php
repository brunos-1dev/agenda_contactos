<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Iniciar Sesión</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  {{-- SOLO app.css --}}
  @vite('resources/css/app.css')
</head>
<body class="login-page d-flex justify-content-center align-items-center vh-100">

  {{-- IDENTIA + título (arriba) --}}
  <div class="brand-wrap">
    <div class="brand-row">
      <img src="{{ asset('images/IDENTIA1.svg') }}" alt="Identia" class="brand-identia">
    </div>
  </div>

  {{-- Contenido principal en columna --}}
  <div class="page-wrap d-flex align-items-center justify-content-center">

    <div class="card login-card p-4 p-md-5">
<h4 class="mb-3 text-center login-title">Iniciar Sesión</h4>

      @if ($errors->any())
        <div class="alert alert-danger py-2 mb-4">
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}" class="mt-2">
        @csrf

        <div class="form-floating mb-3">
          <input type="email" name="email" id="email"
                 class="form-control login-control" required autofocus value="{{ old('email') }}">
          <label for="email">Correo electrónico</label>
        </div>

        <div class="form-floating mb-4">
          <input type="password" name="password" id="password"
                 class="form-control login-control" required>
          <label for="password">Contraseña</label>
        </div>

        <button type="submit" class="btn-identia w-100">Ingresar</button>
      </form>
    </div>

    {{-- Logos INFERIORES (debajo del card) --}}
    <div class="brand-bottom mt-5">
      <img src="{{ asset('images/tics_lea2.svg') }}" alt="TICS" class="brand-tics">
      <img src="{{ asset('images/ssa_lea2.svg') }}"  alt="SSA"  class="brand-ssa">
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
