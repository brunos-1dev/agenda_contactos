<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Models\Departamento;

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
     $departamentos = \App\Models\Departamento::all();
    return view('contacts.create', compact('departamentos'));
    }

    // Guardar nuevo contacto
    public function store(Request $request)
    {
        $request->validate([
            'dni'               => 'required|integer|unique:contacto,dni',
        'nombre'            => 'required|string|max:20',
        'apellido'          => 'nullable|string|max:50',
        'ni'                => 'nullable|string|max:20',
        'domicilio'         => 'nullable|string|max:100',
        'contacto_emergencia'=> 'nullable|string|max:100',
        'email'             => 'required|email|max:30|unique:contacto,email',
        'telefono'          => 'required|integer',
        'departamento_id'   => 'nullable|exists:departamento,id',
        ]);

        Contact::create($request->only([
    'dni',
    'nombre',
    'apellido',
    'ni',
    'domicilio',
    'telefono',
    'email',
    'contacto_emergencia',
    'departamento_id'
        ]));



        return redirect()->route('contacts.index')->with('success', 'Contacto creado exitosamente.');
    }

    // Mostrar formulario para editar contacto
    public function edit($dni)
    {
    $contact = Contact::findOrFail($dni);
    $departamentos = Departamento::all();
    return view('contacts.edit', compact('contact', 'departamentos'));
    }


    // Actualizar contacto
 public function update(Request $request, $dni)
{
     dd('Antes de todo', $dni);
   // $contact = Contact::findOrFail($dni);

    // $request->validate([
        //'nombre'              => 'required|string|max:20',
        //'apellido'            => 'nullable|string|max:50',
        //'ni'                  => 'nullable|string|max:20',
        //'domicilio'           => 'nullable|string|max:100',
        //'contacto_emergencia' => 'nullable|string|max:100',
        //'email'               => 'required|email|max:30|unique:contacto,email,' . $dni . ',dni',
        //'telefono'            => 'required|integer',
       // 'departamento_id'     => 'nullable|exists:departamento,id',
   // ]);

    // TEST: ver si llega acá


    // $contact->update($request->all());

    // return redirect()->route('contacts.index')->with('success', 'Contacto actualizado exitosamente.');
}



    // Eliminar contacto
    public function destroy($dni)
    {
        $contact = Contact::findOrFail($dni);
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Contacto eliminado exitosamente.');
    }
}
