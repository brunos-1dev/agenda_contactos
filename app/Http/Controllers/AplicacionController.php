<?php

namespace App\Http\Controllers;

use App\Models\Aplicacion;
use Illuminate\Http\Request;

class AplicacionController extends Controller
{
    public function index()
    {
        $aplicaciones = Aplicacion::all();
        return view('aplicaciones.index', compact('aplicaciones'));
    }

    public function create()
    {
        return view('aplicaciones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        Aplicacion::create($request->all());

        return redirect()->route('aplicaciones.index')->with('success', 'Aplicación creada correctamente.');
    }

    public function edit($id)
    {
        $aplicacion = Aplicacion::findOrFail($id);
        return view('aplicaciones.edit', compact('aplicacion'));
    }

    public function update(Request $request, $id)
    {
        $aplicacion = Aplicacion::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $aplicacion->update($request->only('nombre'));

        return redirect()->route('aplicaciones.index')
                         ->with('success', 'Aplicación actualizada correctamente.');
    }

    public function destroy($id)
    {
        Aplicacion::destroy($id);
        return redirect()->route('aplicaciones.index')
                         ->with('success', 'Aplicación eliminada correctamente.');
    }
}
