@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="container home-wrap">

  <div class="home-hero">
    <img src="{{ asset('images/IDENTIA1.svg') }}" alt="Identia" class="home-logo">
  </div>

  @php
      $canManageUsers = in_array(auth()->user()->rol ?? 'consulta', ['admin','superadmin']);
  @endphp

  <div class="menu-grid home-grid">
      <a href="{{ route('contacts.index') }}" class="menu-card">
          <img src="{{ asset('images/persona3.png') }}" alt="Personas">
          <div class="menu-btn-text">PERSONAS</div>
      </a>

      <a href="{{ route('organizaciones.index') }}" class="menu-card">
          <img src="{{ asset('images/depto2.png') }}" alt="Organizaciones">
          <div class="menu-btn-text">ORGANIZACIONES</div>
      </a>

      <a href="{{ route('aplicaciones.index') }}" class="menu-card">
          <img src="{{ asset('images/logo app corto.png') }}" alt="Aplicaciones">
          <div class="menu-btn-text">APLICACIONES</div>
      </a>

      @if($canManageUsers)
          <a href="{{ route('usuarios.index') }}" class="menu-card">
              <img src="{{ asset('images/usuarios.jpg') }}" alt="Usuarios">
              <div class="menu-btn-text">USUARIOS</div>
          </a>
      @endif
  </div>

  <div class="home-logos-bottom">
      <img src="{{ asset('images/tics_lea2.svg') }}" alt="Logo TICS">
      <img src="{{ asset('images/ssa_lea2.svg') }}" alt="Logo SSA">
  </div>
</div>
@endsection
