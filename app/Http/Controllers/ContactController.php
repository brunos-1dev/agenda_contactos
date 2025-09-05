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
    // Helper: opciones para el <select> de organizaciones (con sangría)
    private function orgOptions()
    {
        return Organizacion::orderBy('ruta')->orderBy('orden')->get()
            ->map(function ($o) {
                $o->label = str_repeat('— ', max(0, (int)$o->nivel)) . $o->nombre;
                return $o;
            });
    }

    public function show($dni)
    {
        $contact = Contact::findOrFail($dni);
        $departamentos = Departamento::all(); // si la vista lo usa
        return view('contacts.show', compact('contact', 'departamentos'));
    }

    // Listado
    public function index(Request $request)
    {
        $auth = auth()->user();

        $query = Contact::query();

        // Búsqueda opcional
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nombre', 'like', "%$s%")
                  ->orWhere('apellido', 'like', "%$s%")
                  ->orWhere('dni', 'like', "%$s%");
            });
        }

        // Alcance por organización
        if (in_array($auth->rol, ['admin','consulta'])) {
            if ($auth->organizacion_id) {
                $ids = app('org')->subtreeIds($auth->organizacion_id);
                $query->whereIn('organizacion_id', $ids);
            } else {
                $query->whereRaw('1=0'); // fail-safe
            }
        }

        $contacts = $query->get();
        return view('contacts.index', compact('contacts'));
    }

    // Crear
    public function create()
    {
        $auth = auth()->user();
        if ($auth->rol === 'consulta') {
            return redirect()->route('contacts.index')->with('error', 'Tu rol es de solo lectura.');
        }

        $aplicaciones = Aplicacion::all();
        $orgs = $this->orgOptions();
        return view('contacts.create', compact('aplicaciones', 'orgs'));
    }

    // Guardar
    public function store(Request $request)
    {
        $auth = auth()->user();
        if ($auth->rol === 'consulta') {
            return redirect()->route('contacts.index')->with('error', 'Tu rol es de solo lectura.');
        }

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
        ], [
            'cuil.digits' => 'El CUIL debe tener exactamente 11 dígitos.',
        ]);

        // Admin sólo puede asignar organización dentro de su subárbol
        if ($auth->rol === 'admin' && $request->filled('organizacion_id')) {
            if (!app('org')->inSameTree($auth->organizacion_id, (int)$request->organizacion_id)) {
                return back()->withInput()->with('error', 'Organización fuera de tu alcance.');
            }
        }

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
        $contact->organizacion_id     = $request->organizacion_id ?: null;
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
        $auth = auth()->user();
        if ($auth->rol === 'consulta') {
            return redirect()->route('contacts.index')->with('error', 'Tu rol es de solo lectura.');
        }

        $contact = Contact::findOrFail($dni);

        // Admin sólo puede editar contactos de su subárbol
        if ($auth->rol === 'admin') {
            if (!$contact->organizacion_id || !app('org')->inSameTree($auth->organizacion_id, (int)$contact->organizacion_id)) {
                return redirect()->route('contacts.index')->with('error', 'Contacto fuera de tu alcance.');
            }
        }

        $aplicaciones = Aplicacion::all();
        $orgs = $this->orgOptions();

        $pivotUsuarios = [];
        foreach ($contact->aplicaciones as $a) {
            $pivotUsuarios[$a->id] = $a->pivot->nombre_usuario;
        }

        return view('contacts.edit', [
            'contact'        => $contact,
            'aplicaciones'   => $aplicaciones,
            'orgs'           => $orgs,
            'pivotUsuarios'  => $pivotUsuarios,
        ]);
    }

    // Actualizar
    public function update(Request $request, $dni)
    {
        $auth = auth()->user();
        if ($auth->rol === 'consulta') {
            return redirect()->route('contacts.index')->with('error', 'Tu rol es de solo lectura.');
        }

        $contact = Contact::findOrFail($dni);

        // Admin sólo puede actualizar contactos de su subárbol
        if ($auth->rol === 'admin') {
            if (!$contact->organizacion_id || !app('org')->inSameTree($auth->organizacion_id, (int)$contact->organizacion_id)) {
                return redirect()->route('contacts.index')->with('error', 'Contacto fuera de tu alcance.');
            }
        }

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
        ], [
            'cuil.digits' => 'El CUIL debe tener exactamente 11 dígitos.',
        ]);

        // Si cambia organización, debe quedar dentro del subárbol del admin
        if ($auth->rol === 'admin' && $request->filled('organizacion_id')) {
            if (!app('org')->inSameTree($auth->organizacion_id, (int)$request->organizacion_id)) {
                return back()->withInput()->with('error', 'Organización destino fuera de tu alcance.');
            }
        }

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
        $auth = auth()->user();
        if ($auth->rol === 'consulta') {
            return redirect()->route('contacts.index')->with('error', 'Tu rol es de solo lectura.');
        }

        $contact = Contact::findOrFail($dni);

        // Admin sólo puede eliminar contactos de su subárbol
        if ($auth->rol === 'admin') {
            if (!$contact->organizacion_id || !app('org')->inSameTree($auth->organizacion_id, (int)$contact->organizacion_id)) {
                return redirect()->route('contacts.index')->with('error', 'Contacto fuera de tu alcance.');
            }
        }

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
