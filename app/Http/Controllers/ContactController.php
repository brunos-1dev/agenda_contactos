<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Departamento;
use App\Models\Aplicacion;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show($dni)
    {
        $contact = Contact::findOrFail($dni);
        $departamentos = Departamento::all();
        return view('contacts.show', compact('contact', 'departamentos'));
    }

    // Mostrar lista
    public function index(Request $request)
    {
        $query = Contact::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nombre', 'like', "%$search%")
                ->orWhere('apellido', 'like', "%$search%")
                ->orWhere('dni', 'like', "%$search%");
        }

        $contacts = $query->get();

        return view('contacts.index', compact('contacts'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        $departamentos = Departamento::all();
        $aplicaciones = Aplicacion::all();
        return view('contacts.create', compact('departamentos', 'aplicaciones'));
    }

    // Guardar nuevo contacto (carga manual)
    public function store(Request $request)
    {
        $request->validate([
            'dni'                 => 'required|integer|unique:contacto,dni',
            'nombre'              => 'required|string|max:20',
            'apellido'            => 'nullable|string|max:50',
            'ni'                  => 'nullable|string|max:20',
            'domicilio'           => 'nullable|string|max:100',
            'contacto_emergencia' => 'nullable|string|max:100',
            'email'               => 'required|email|max:30|unique:contacto,email',
            'telefono'            => 'required|integer',
            'departamento_id'     => 'nullable|exists:departamento,id',
            'aplicaciones'        => 'array|exists:aplicacion,id',
            'nombre_usuario'      => 'array',  // validación para el array de nombres de usuario
            'nombre_usuario.*'    => 'nullable|string|max:50', // validación para cada nombre_usuario
        ]);

        $contact = new Contact();
        $contact->dni = $request->dni;
        $contact->nombre = $request->nombre;
        $contact->apellido = $request->apellido;
        $contact->ni = $request->ni;
        $contact->domicilio = $request->domicilio;
        $contact->telefono = $request->telefono;
        $contact->email = $request->email;
        $contact->contacto_emergencia = $request->contacto_emergencia;
        $contact->departamento_id = $request->departamento_id;
        $contact->save();

        if ($request->has('aplicaciones')) {
            $syncData = [];
            foreach ($request->input('aplicaciones') as $appId) {
                $nombreUsuario = $request->input("nombre_usuario.$appId");
                $syncData[$appId] = ['nombre_usuario' => $nombreUsuario];
            }
            $contact->aplicaciones()->sync($syncData);
        }

        return redirect()->route('contacts.index')->with('success', 'Contacto creado exitosamente.');
    }

    // Mostrar formulario de edición
    public function edit($dni)
    {
        $contact = Contact::findOrFail($dni);
        $departamentos = Departamento::all();
        $aplicaciones = Aplicacion::all();

        // Cargar los ids de aplicaciones seleccionadas
        $aplicacionesSeleccionadas = $contact->aplicaciones()->pluck('aplicacion.id')->toArray();

        // Cargar datos pivote para nombre_usuario
        $pivotData = $contact->aplicaciones()->get()->keyBy('id');

        return view('contacts.edit', compact('contact', 'departamentos', 'aplicaciones', 'aplicacionesSeleccionadas', 'pivotData'));
    }

    // Actualizar contacto (también con asignación manual)
    public function update(Request $request, $dni)
    {
        $contact = Contact::findOrFail($dni);

        $request->validate([
            'nombre'              => 'required|string|max:20',
            'apellido'            => 'nullable|string|max:50',
            'ni'                  => 'nullable|string|max:20',
            'domicilio'           => 'nullable|string|max:100',
            'contacto_emergencia' => 'nullable|string|max:100',
            'email'               => 'required|email|max:30|unique:contacto,email,' . $dni . ',dni',
            'telefono'            => 'required|integer',
            'departamento_id'     => 'nullable|exists:departamento,id',
            'aplicaciones'        => 'array|exists:aplicacion,id',
            'nombre_usuario'      => 'array',
            'nombre_usuario.*'    => 'nullable|string|max:50',
        ]);

        $contact->nombre = $request->nombre;
        $contact->apellido = $request->apellido;
        $contact->ni = $request->ni;
        $contact->domicilio = $request->domicilio;
        $contact->telefono = $request->telefono;
        $contact->email = $request->email;
        $contact->contacto_emergencia = $request->contacto_emergencia;
        $contact->departamento_id = $request->departamento_id;
        $contact->save();

        if ($request->has('aplicaciones')) {
            $syncData = [];
            foreach ($request->input('aplicaciones') as $appId) {
                $nombreUsuario = $request->input("nombre_usuario.$appId");
                $syncData[$appId] = ['nombre_usuario' => $nombreUsuario];
            }
            $contact->aplicaciones()->sync($syncData);
        } else {
            $contact->aplicaciones()->sync([]);
        }

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
