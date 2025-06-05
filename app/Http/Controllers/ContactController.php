<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // Mostrar lista de contactos
    public function index()
    {
        $contacts = Contact::all();
        return view('contacts.index', compact('contacts'));
    }

    // Mostrar formulario para crear contacto
    public function create()
    {
        return view('contacts.create');
    }

    // Guardar nuevo contacto
    public function store(Request $request)
    {
        $request->validate([
            'dni'      => 'required|integer|unique:contacto,dni',
            'nombre'   => 'required|string|max:20',
            'email'    => 'required|email|max:30|unique:contacto,email',
            'telefono' => 'required|integer',
        ]);

        Contact::create($request->all());

        return redirect()->route('contacts.index')->with('success', 'Contacto creado exitosamente.');
    }

    // Mostrar formulario para editar contacto
    public function edit($dni)
    {
        $contact = Contact::findOrFail($dni);
        return view('contacts.edit', compact('contact'));
    }

    // Actualizar contacto
    public function update(Request $request, $dni)
    {
        $contact = Contact::findOrFail($dni);

        $request->validate([
            'nombre'   => 'required|string|max:20',
            'email'    => 'required|email|max:30|unique:contacto,email,' . $dni . ',dni',
            'telefono' => 'required|integer',
        ]);

        $contact->update($request->all());

        return redirect()->route('contacts.index')->with('success', 'Contacto actualizado exitosamente.');
    }

    // Eliminar contacto
    public function destroy($dni)
    {
        $contact = Contact::findOrFail($dni);
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Contacto eliminado exitosamente.');
    }
}
