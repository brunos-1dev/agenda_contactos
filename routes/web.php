<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\AplicacionController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;


// Página de inicio pública
Route::get('/', function () {
    return view('inicio');
})->name('inicio')->middleware('auth');

// Rutas de autenticación

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::resource('contacts', ContactController::class);
Route::resource('departamentos', DepartamentoController::class);
Route::resource('aplicaciones', AplicacionController::class);
Route::resource('usuarios', UserController::class);