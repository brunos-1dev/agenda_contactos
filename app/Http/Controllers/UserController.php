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
    /** Opciones de organizaciones con sangría */
    private function orgOptions()
    {
        return Organizacion::orderBy('ruta')->orderBy('orden')->get()
            ->map(function ($o) {
                $o->label = str_repeat('— ', max(0, (int)$o->nivel)) . $o->nombre;
                return $o;
            });
    }

    // Listado
    public function index()
    {
        $usuarios = User::all();
        return view('usuarios.index', compact('usuarios'));
    }

    // Formulario de alta
    public function create()
    {
        $orgs = $this->orgOptions();
        return view('usuarios.create', compact('orgs'));
    }

    // Guardar
    public function store(Request $request)
    {
        $request->validate([
            'nombre'           => 'required|string|max:255',
            'apellido'         => 'nullable|string|max:255',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|string|min:6|confirmed',
            'rol'              => 'required|in:superadmin,admin,consulta',
            // Admin y Consulta DEBEN tener organización; SuperAdmin puede (opcional)
            'organizacion_id'  => 'required_unless:rol,superadmin|nullable|exists:organizaciones,id',
        ]);

        User::create([
            'nombre'          => $request->nombre,
            'apellido'        => $request->apellido,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'rol'             => $request->rol,
            'organizacion_id' => $request->organizacion_id, // <-- se guarda
            'created_by'      => auth()->id(),
            'updated_by'      => auth()->id(),
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    // Formulario de edición
    public function edit(User $usuario)
    {
        $orgs = $this->orgOptions();
        return view('usuarios.edit', compact('usuario', 'orgs'));
    }

    // Actualizar
    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'nombre'           => 'required|string|max:255',
            'apellido'         => 'nullable|string|max:255',
            'email'            => 'required|email|unique:users,email,' . $usuario->id,
            'rol'              => 'required|in:superadmin,admin,consulta',
            'password'         => 'nullable|string|min:6|confirmed',
            'organizacion_id'  => 'required_unless:rol,superadmin|nullable|exists:organizaciones,id',
        ]);

        $data = [
            'nombre'          => $request->nombre,
            'apellido'        => $request->apellido,
            'email'           => $request->email,
            'rol'             => $request->rol,
            'organizacion_id' => $request->organizacion_id, // <-- se actualiza
            'updated_by'      => auth()->id(),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    // Eliminar
    public function destroy(User $usuario)
    {
        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    // Exportar
    public function export()
    {
        return Excel::download(new UsuariosExport, 'usuarios.xlsx');
    }
}
