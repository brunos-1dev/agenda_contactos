<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Exports\UsuariosExport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /** Construye opciones de organizaciones con sangría (para create/edit) */
    private function orgOptions(): array
    {
        // Ordenamos por ruta para respetar el árbol
        $list = Organizacion::orderBy('ruta')->get(['id','nombre','tipo','nivel','ruta']);
        return $list->map(function ($o) {
            $indent = str_repeat('— ', (int)($o->nivel ?? 0));
            return (object)[
                'id'    => $o->id,
                'label' => trim($indent . ($o->nombre ?? '')),
            ];
        })->all();
    }

    /** Listado con búsqueda y filtro por organización según rol */
    public function index(Request $request)
    {
        $auth = auth()->user();
        $q = User::with('organizacion');

        // 🔎 Buscar por nombre, apellido o email (opcional)
        if ($s = trim((string)$request->get('search', ''))) {
            $q->where(function ($qq) use ($s) {
                $qq->where('nombre', 'like', "%{$s}%")
                   ->orWhere('apellido', 'like', "%{$s}%")
                   ->orWhere('email', 'like', "%{$s}%");
            });
        }

        // 🌳 Alcance por organización (admin/consulta sólo su subárbol)
        if (in_array($auth->rol, ['admin','consulta'])) {
            if ($auth->organizacion_id) {
                $ids = app('org')->subtreeIds($auth->organizacion_id);
                $q->whereIn('organizacion_id', $ids);
            } else {
                // admin/consulta sin organización -> no mostramos nada
                $q->whereRaw('1=0');
            }
        }

        $usuarios = $q->orderBy('apellido')->orderBy('nombre')->paginate(20);
        return view('usuarios.index', compact('usuarios'));
    }

    /** Form crear */
    public function create()
    {
        $orgs = $this->orgOptions();
        return view('usuarios.create', compact('orgs'));
    }

    /** Guardar */
    public function store(Request $request)
    {
        $auth = auth()->user();
        if ($auth->rol === 'consulta') abort(403, 'Tu rol es de solo lectura.');

        $request->validate([
            'nombre'           => 'required|string|max:255',
            'apellido'         => 'nullable|string|max:255',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|string|min:6|confirmed',
            'rol'              => 'required|in:superadmin,admin,consulta',
            'organizacion_id'  => 'required_unless:rol,superadmin|nullable|exists:organizaciones,id',
        ]);

        if ($auth->rol === 'admin') {
            if ($request->rol === 'superadmin') abort(403, 'Un admin no puede crear superadmins.');
            if (!$request->filled('organizacion_id')) abort(422, 'La organización es obligatoria para este rol.');
            if (!app('org')->inSameTree($auth->organizacion_id, (int)$request->organizacion_id)) {
                abort(403, 'Organización fuera de tu alcance.');
            }
        }

        User::create([
            'nombre'          => $request->nombre,
            'apellido'        => $request->apellido,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'rol'             => $request->rol,
            'organizacion_id' => $request->organizacion_id,
            'created_by'      => auth()->id(),
            'updated_by'      => auth()->id(),
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    /** Form editar */
    public function edit(User $usuario)
    {
        $orgs = $this->orgOptions();
        return view('usuarios.edit', compact('usuario','orgs'));
    }

    /** Actualizar */
    public function update(Request $request, User $usuario)
    {
        $auth = auth()->user();
        if ($auth->rol === 'consulta') abort(403, 'Tu rol es de solo lectura.');

        $request->validate([
            'nombre'           => 'required|string|max:255',
            'apellido'         => 'nullable|string|max:255',
            'email'            => 'required|email|unique:users,email,' . $usuario->id,
            'rol'              => 'required|in:superadmin,admin,consulta',
            'password'         => 'nullable|string|min:6|confirmed',
            'organizacion_id'  => 'required_unless:rol,superadmin|nullable|exists:organizaciones,id',
        ]);

        if ($auth->rol === 'admin') {
            if ($usuario->rol === 'superadmin' || $request->rol === 'superadmin') {
                abort(403, 'No puedes editar o ascender superadmins.');
            }
            if (!$usuario->organizacion_id || !app('org')->inSameTree($auth->organizacion_id, (int)$usuario->organizacion_id)) {
                abort(403, 'Usuario fuera de tu alcance.');
            }
            if ($request->filled('organizacion_id') && !app('org')->inSameTree($auth->organizacion_id, (int)$request->organizacion_id)) {
                abort(403, 'Organización destino fuera de tu alcance.');
            }
        }

        $data = [
            'nombre'          => $request->nombre,
            'apellido'        => $request->apellido,
            'email'           => $request->email,
            'rol'             => $request->rol,
            'organizacion_id' => $request->organizacion_id,
            'updated_by'      => auth()->id(),
        ];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /** Eliminar */
    public function destroy(User $usuario)
    {
        $auth = auth()->user();
        if ($auth->rol === 'consulta') abort(403, 'Tu rol es de solo lectura.');
        if ($auth->id === $usuario->id) abort(403, 'No puedes eliminarte a ti mismo.');
        if ($auth->rol === 'admin') {
            if ($usuario->rol === 'superadmin') abort(403, 'No puedes eliminar superadmins.');
            if (
                !$auth->organizacion_id ||
                !$usuario->organizacion_id ||
                !app('org')->inSameTree($auth->organizacion_id, (int)$usuario->organizacion_id)
            ) {
                abort(403, 'Usuario fuera de tu alcance.');
            }
        }

        $usuario->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }

    /** Exportar a Excel (respeta filtros y alcance) */
    public function export(Request $request)
    {
        $search = (string)$request->get('search', '');
        return Excel::download(new UsuariosExport($search), 'usuarios.xlsx');
    }
}
