<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\AplicacionController;
use App\Http\Controllers\UserController;
use App\Exports\DepartamentosExport;
use App\Exports\ContactsExport;

// Página de inicio (protegida)
Route::get('/', fn () => view('inicio'))
    ->name('inicio')
    ->middleware('auth');

// --- Autenticación ---
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('departamentos/export', [DepartamentoController::class, 'export'])->name('departamentos.export');
Route::get('contacts/export', [ContactController::class, 'export'])->name('contacts.export');

// --- CRUDs restantes ---
Route::resource('contacts', ContactController::class);
Route::resource('departamentos', DepartamentoController::class);
Route::resource('aplicaciones', AplicacionController::class);
Route::resource('usuarios', UserController::class);
