<?php

namespace App\Http\Controllers;

use App\Models\Aplicacion;
use Illuminate\Http\Request;

class AplicacionController extends Controller
{
     public function index(Request $request)
    {
        $search = $request->input('search');

        $aplicaciones = Aplicacion::when($search, function ($query, $search) {
            return $query->where('nombre', 'like', "%{$search}%");
        })->get();

        return view('aplicaciones.index', compact('aplicaciones'));
    }

    public function create()
    {
        return view('aplicaciones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        Aplicacion::create($request->only('nombre'));

        return redirect()->route('aplicaciones.index')->with('success', 'Aplicación creada correctamente.');
    }

    public function edit($id)
    {
        $aplicacion = Aplicacion::findOrFail($id);
        return view('aplicaciones.edit', compact('aplicacion'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $aplicacion = Aplicacion::findOrFail($id);
        $aplicacion->update($request->only('nombre'));

        return redirect()->route('aplicaciones.index')->with('success', 'Aplicación actualizada correctamente.');
    }

    public function destroy($id)
    {
        $aplicacion = Aplicacion::findOrFail($id);
        $aplicacion->delete();

        return redirect()->route('aplicaciones.index')->with('success', 'Aplicación eliminada correctamente.');
    }
}