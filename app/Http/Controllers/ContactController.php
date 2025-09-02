<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Departamento;
use App\Models\Aplicacion;
use App\Models\Organizacion;
use Illuminate\Http\Request;
use App\Exports\ContactsExport;
use Maatwebsite\Excel\Facades\Excel;

class ContactController extends Controller
{
    // ===== Helper: opciones para el <select> de organizaciones (con sangría) =====
    private function orgOptions()
    {
        return Organizacion::orderBy('ruta')->orderBy('orden')->get()
            ->map(function ($o) {
                $o->label = str_repeat('— ', max(0, (int)$o->nivel)).$o->nombre;
                return $o;
            });
    }

    public function show($dni)
    {
        $contact = Contact::findOrFail($dni);
        $departamentos = Departamento::all(); // legacy, si aún lo usas en la vista
        return view('contacts.show', compact('contact', 'departamentos'));
    }

    // Listado
    public function index(Request $request)
    {
        $query = Contact::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nombre', 'like', "%$s%")
                  ->orWhere('apellido', 'like', "%$s%")
                  ->orWhere('dni', 'like', "%$s%");
            });
        }

        $contacts = $query->get();
        return view('contacts.index', compact('contacts'));
    }

    // Crear
    public function create()
    {
        $aplicaciones = Aplicacion::all();
        $orgs = $this->orgOptions();   // <<<< enviar $orgs a la vista

        // (departamentos ya no es necesario si no lo usas en la vista)
        return view('contacts.create', compact('aplicaciones', 'orgs'));
    }

    // Guardar
    public function store(Request $request)
    {
        $jerarquias = [
            'Suboficial','Oficial','Subinspector','Inspector','Subcomisario',
            'Comisario','Comisario Supervisor','Subdirector','Director','Director General'
        ];

        $request->validate([
            'dni'                 => 'required|integer|unique:contacto,dni',
            'nombre'              => 'required|string|max:20',
            'apellido'            => 'nullable|string|max:50',

            'cuil'                => 'nullable|digits:11|unique:contacto,cuil',
            'iup'                 => 'nullable|string|max:20|unique:contacto,iup',
            'ni'                  => 'nullable|string|max:20',
            'jerarquia'           => 'nullable|in:'.implode(',', $jerarquias),
            'localidad'           => 'nullable|string|max:100',

            'domicilio'           => 'nullable|string|max:100',
            'contacto_emergencia' => 'nullable|string|max:100',
            'email'               => 'required|email|max:30|unique:contacto,email',
            'telefono'            => 'required|integer',

            'organizacion_id'     => 'nullable|exists:organizaciones,id',

            'aplicaciones'        => 'array|exists:aplicacion,id',
            'nombre_usuario'      => 'array',
            'nombre_usuario.*'    => 'nullable|string|max:50',
        ]);

        $contact = new Contact();
        $contact->dni                 = $request->dni;
        $contact->nombre              = $request->nombre;
        $contact->apellido            = $request->apellido;
        $contact->cuil                = $request->cuil;
        $contact->iup                 = $request->iup;
        $contact->ni                  = $request->ni;
        $contact->jerarquia           = $request->jerarquia;
        $contact->localidad           = $request->localidad;
        $contact->domicilio           = $request->domicilio;
        $contact->telefono            = $request->telefono;
        $contact->email               = $request->email;
        $contact->contacto_emergencia = $request->contacto_emergencia;
        $contact->organizacion_id     = $request->organizacion_id ?: null; // puede venir vacío
        $contact->save();

        if ($request->has('aplicaciones')) {
            $sync = [];
            foreach ($request->input('aplicaciones') as $appId) {
                $sync[$appId] = ['nombre_usuario' => $request->input("nombre_usuario.$appId")];
            }
            $contact->aplicaciones()->sync($sync);
        }

        return redirect()->route('contacts.index')->with('success', 'Contacto creado exitosamente.');
    }

    // Editar
    public function edit($dni)
    {
        $contact       = Contact::findOrFail($dni);
        $aplicaciones  = Aplicacion::all();
        $aplicSel      = $contact->aplicaciones()->pluck('aplicacion.id')->toArray();
        $pivotData     = $contact->aplicaciones()->get()->keyBy('id');
        $orgs          = $this->orgOptions();  // <<<< enviar $orgs a la vista

        return view('contacts.edit', [
            'contact'                   => $contact,
            'aplicaciones'              => $aplicaciones,
            'aplicacionesSeleccionadas' => $aplicSel,
            'pivotData'                 => $pivotData,
            'orgs'                      => $orgs,   // <<<<
        ]);
    }

    // Actualizar
    public function update(Request $request, $dni)
    {
        $contact = Contact::findOrFail($dni);

        $jerarquias = [
            'Suboficial','Oficial','Subinspector','Inspector','Subcomisario',
            'Comisario','Comisario Supervisor','Subdirector','Director','Director General'
        ];

        $request->validate([
            'nombre'              => 'required|string|max:20',
            'apellido'            => 'nullable|string|max:50',

            'cuil'                => 'nullable|digits:11|unique:contacto,cuil,'.$dni.',dni',
            'iup'                 => 'nullable|string|max:20|unique:contacto,iup,'.$dni.',dni',
            'ni'                  => 'nullable|string|max:20',
            'jerarquia'           => 'nullable|in:'.implode(',', $jerarquias),
            'localidad'           => 'nullable|string|max:100',

            'domicilio'           => 'nullable|string|max:100',
            'contacto_emergencia' => 'nullable|string|max:100',
            'email'               => 'required|email|max:30|unique:contacto,email,'.$dni.',dni',
            'telefono'            => 'required|integer',

            'organizacion_id'     => 'nullable|exists:organizaciones,id',

            'aplicaciones'        => 'array|exists:aplicacion,id',
            'nombre_usuario'      => 'array',
            'nombre_usuario.*'    => 'nullable|string|max:50',
        ]);

        $contact->nombre              = $request->nombre;
        $contact->apellido            = $request->apellido;
        $contact->cuil                = $request->cuil;
        $contact->iup                 = $request->iup;
        $contact->ni                  = $request->ni;
        $contact->jerarquia           = $request->jerarquia;
        $contact->localidad           = $request->localidad;
        $contact->domicilio           = $request->domicilio;
        $contact->telefono            = $request->telefono;
        $contact->email               = $request->email;
        $contact->contacto_emergencia = $request->contacto_emergencia;
        $contact->organizacion_id     = $request->organizacion_id ?: null;
        $contact->save();

        if ($request->has('aplicaciones')) {
            $sync = [];
            foreach ($request->input('aplicaciones') as $appId) {
                $sync[$appId] = ['nombre_usuario' => $request->input("nombre_usuario.$appId")];
            }
            $contact->aplicaciones()->sync($sync);
        } else {
            $contact->aplicaciones()->sync([]);
        }

        return redirect()->route('contacts.index')->with('success', 'Contacto actualizado exitosamente.');
    }

    // Eliminar
    public function destroy($dni)
    {
        $contact = Contact::findOrFail($dni);
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Contacto eliminado exitosamente.');
    }

    // Exportar
    public function export(Request $request)
    {
        $search = $request->input('search');
        return Excel::download(new ContactsExport($search), 'contacts.xlsx');
    }
}
