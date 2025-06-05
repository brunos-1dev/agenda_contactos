<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    // Mostrar lista de departamentos
    public function index()
    {
        $departamentos = Departamento::all();
        return view('departamentos.index', compact('departamentos'));
    }

    // Mostrar formulario para crear departamento
    public function create()
    {
        return view('departamentos.create');
    }

    // Guardar nuevo departamento
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:departamento,nombre',
        ]);

        Departamento::create($request->all());

        return redirect()->route('departamentos.index')->with('success', 'Departamento creado exitosamente.');
    }

    // Mostrar formulario para editar departamento
    public function edit($id)
    {
        $departamento = Departamento::findOrFail($id);
        return view('departamentos.edit', compact('departamento'));
    }

    // Actualizar departamento
    public function update(Request $request, $id)
    {
        $departamento = Departamento::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:50|unique:departamento,nombre,' . $id,
        ]);

        $departamento->update($request->all());

        return redirect()->route('departamentos.index')->with('success', 'Departamento actualizado exitosamente.');
    }

    // Eliminar departamento
    public function destroy($id)
    {
        $departamento = Departamento::findOrFail($id);
        $departamento->delete();

        return redirect()->route('departamentos.index')->with('success', 'Departamento eliminado exitosamente.');
    }
}
