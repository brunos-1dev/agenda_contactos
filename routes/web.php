<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DepartamentoController;

Route::get('/', function () {
    return view('inicio');  // Ya tenés esta vista con el contenido correcto
});

Route::resource('contacts', ContactController::class);
Route::resource('departamentos', DepartamentoController::class);


