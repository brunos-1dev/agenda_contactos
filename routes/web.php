<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/', function () {
    return view('welcome');
});

// Rutas para el CRUD de contactos
Route::resource('contacts', ContactController::class);
