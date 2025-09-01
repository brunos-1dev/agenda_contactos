<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\AplicacionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrganizacionController;

// Página de inicio (protegida)
Route::get('/', fn () => view('inicio'))
    ->name('inicio')
    ->middleware('auth');

// --- Autenticación ---
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// --- Organizaciones ---
Route::get('/organizaciones', [OrganizacionController::class, 'index'])
    ->name('organizaciones.index');

Route::get('/organizaciones/export', [OrganizacionController::class, 'export'])
    ->name('organizaciones.export');

// Solo create/store desde el resource (evitamos duplicar el index)
Route::resource('organizaciones', OrganizacionController::class)->only(['create', 'store']);

// --- Export de otros módulos ---
Route::get('/departamentos/export', [DepartamentoController::class, 'export'])
    ->name('departamentos.export');

Route::get('/contacts/export', [ContactController::class, 'export'])
    ->name('contacts.export');

// --- CRUDs restantes ---
Route::resource('contacts', ContactController::class);
Route::resource('departamentos', DepartamentoController::class);
Route::resource('aplicaciones', AplicacionController::class);
Route::resource('usuarios', UserController::class);
