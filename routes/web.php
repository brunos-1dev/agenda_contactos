<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DepartamentoController;

Route::get('/', function () {
    return view('inicio');
});

// No pongas esto antes del resource, se genera solo con el resource
// Route::get('/contacts/{dni}', [ContactController::class, 'show'])->name('contacts.show');

Route::resource('contacts', ContactController::class);
Route::resource('departamentos', DepartamentoController::class);
