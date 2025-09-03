<?php

namespace App\Http\Controllers;

use App\Models\Organizacion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Exports\OrganizacionesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Rules\TipoHijoValido;

class OrganizacionController extends Controller
{
    /** Árbol + búsqueda */
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $all = Organizacion::orderBy('ruta')->orderBy('orden')->get();

        if ($search === '') {
            $byParent = $all->groupBy('id_padre');

            $build = function ($parentId) use (&$build, $byParent) {
                return $byParent->get($parentId, collect())
                    ->map(function ($node) use (&$build) {
                        $node->children = $build($node->id);
                        return $node;
                    })
                    ->values();
            };

            $tree    = $build(null);
            $openIds = [];

            return view('organizaciones.index', [
                'tree'    => $tree,
                'search'  => $search,
                'openIds' => $openIds,
            ]);
        }

        // Con búsqueda: matches + ancestros
        $byId    = $all->keyBy('id');
        $matches = $all->filter(function ($o) use ($search) {
            return stripos($o->nombre ?? '', $search) !== false
                || stripos($o->tipo ?? '', $search) !== false;
        });

        $idsToKeep = collect();
        foreach ($matches as $m) {
            $id = $m->id;
            while ($id !== null && !$idsToKeep->contains($id)) {
                $idsToKeep->push($id);
                $id = optional($byId->get($id))->id_padre;
            }
        }

        $openIds          = $idsToKeep->values()->all();
        $filtered         = $all->whereIn('id', $idsToKeep);
        $byParentFiltered = $filtered->groupBy('id_padre');

        $buildFiltered = function ($parentId) use (&$buildFiltered, $byParentFiltered) {
            return $byParentFiltered->get($parentId, collect())
                ->map(function ($node) use (&$buildFiltered) {
                    $node->children = $buildFiltered($node->id);
                    return $node;
                })
                ->values();
        };

        $tree = $buildFiltered(null);

        return view('organizaciones.index', [
            'tree'    => $tree,
            'search'  => $search,
            'openIds' => $openIds,
        ]);
    }

    /** Formulario de alta */
    public function create()
    {
        $organizaciones = Organizacion::orderBy('ruta')->get();
        $parents = $organizaciones->map(function ($o) {
            $o->label = str_repeat('— ', (int) $o->nivel) . $o->nombre;
            return $o;
        });

        $tipos = Organizacion::TIPOS;

        return view('organizaciones.create', compact('parents', 'tipos'));
    }

    /** Guarda el alta */
    public function store(Request $request)
    {
        $tipos = Organizacion::TIPOS;

        $validated = $request->validate([
            'nombre'   => ['required', 'string', 'max:190'],
            'tipo'     => ['required', 'string', Rule::in($tipos), new TipoHijoValido($request->input('id_padre'))],
            'id_padre' => ['nullable', 'integer', 'exists:organizaciones,id'],
            'activo'   => ['sometimes', 'boolean'],
        ]);

        $padre = null;
        $nivel = 0;
        if (!empty($validated['id_padre'])) {
            $padre = Organizacion::find($validated['id_padre']);
            $nivel = ($padre?->nivel ?? -1) + 1;
        }

        // Orden: último entre sus hermanos
        $padreId = $validated['id_padre'] ?? null;
        $orden   = (Organizacion::where('id_padre', $padreId)->max('orden') ?? -1) + 1;

        $org = new Organizacion();
        $org->nombre   = $validated['nombre'];
        $org->tipo     = $validated['tipo'];
        $org->id_padre = $padreId;
        $org->nivel    = $nivel;
        $org->orden    = $orden;
        $org->activo   = $request->boolean('activo', true);
        $org->ruta     = ''; // luego de tener ID
        $org->save();

        // Ruta: [ruta padre] + id + '/'
        $org->ruta = ($padre?->ruta ?? '/') . $org->id . '/';
        $org->save();

        return redirect()
            ->route('organizaciones.index')
            ->with('success', 'Organización creada correctamente.');
    }

    /** Formulario de edición */
    public function edit($id)
    {
        $org = Organizacion::findOrFail($id);

        $organizaciones = Organizacion::orderBy('ruta')->get();
        // Evitar poder elegirte a vos mismo como padre
        $parents = $organizaciones
            ->reject(fn ($o) => (int)$o->id === (int)$org->id)
            ->map(function ($o) {
                $o->label = str_repeat('— ', (int) $o->nivel) . $o->nombre;
                return $o;
            });

        $tipos = Organizacion::TIPOS;

        return view('organizaciones.edit', [
            'org'     => $org,
            'parents' => $parents,
            'tipos'   => $tipos,
        ]);
    }

    /** Actualiza la organización */
    public function update(Request $request, $id)
    {
        $org   = Organizacion::findOrFail($id);
        $tipos = Organizacion::TIPOS;

        $validated = $request->validate([
            'nombre'   => ['required', 'string', 'max:190'],
            'tipo'     => ['required', 'string', Rule::in($tipos), new TipoHijoValido($request->input('id_padre'))],
            'id_padre' => ['nullable', 'integer', 'exists:organizaciones,id', 'not_in:'.$id],
            'activo'   => ['sometimes', 'boolean'],
        ]);

        $padreIdNuevo = $validated['id_padre'] ?? null;
        $padreNuevo   = $padreIdNuevo ? Organizacion::find($padreIdNuevo) : null;

        // 1) Evitar ciclos: el nuevo padre no puede ser un descendiente del propio nodo
        if ($padreIdNuevo) {
            $descendientesIds = Organizacion::where('ruta', 'like', $org->ruta . '%')->pluck('id');
            if ($descendientesIds->contains((int)$padreIdNuevo)) {
                return back()
                    ->withErrors(['id_padre' => 'No se puede asignar como padre un descendiente de la misma organización.'])
                    ->withInput();
            }
        }

        // 2) Guardar old ruta para propagar si cambia el padre
        $oldRuta = $org->ruta;

        // Si cambia el padre, recalculamos el orden
        if ((int)$org->id_padre !== (int)$padreIdNuevo) {
            $org->orden = (Organizacion::where('id_padre', $padreIdNuevo)->max('orden') ?? -1) + 1;
        }

        // Actualizamos campos base del nodo
        $org->nombre   = $validated['nombre'];
        $org->tipo     = $validated['tipo'];
        $org->id_padre = $padreIdNuevo;
        $org->nivel    = $padreNuevo ? ($padreNuevo->nivel + 1) : 0;
        $org->activo   = $request->boolean('activo', true);

        // Recalcular nueva ruta del nodo
        $org->ruta = ($padreNuevo?->ruta ?? '/') . $org->id . '/';
        $org->save();

        // 3) Si cambió la ruta, propagar a los descendientes
        if ($oldRuta !== $org->ruta) {
            $children = Organizacion::where('ruta', 'like', $oldRuta.'%')
                ->where('id', '<>', $org->id)
                ->get();

            foreach ($children as $child) {
                // reemplazar prefijo oldRuta por newRuta
                if (str_starts_with($child->ruta, $oldRuta)) {
                    $child->ruta = $org->ruta . substr($child->ruta, strlen($oldRuta));
                    // nivel del hijo = cantidad de / - 2 (pero mejor: padre + 1 del propio)
                    $child->nivel = (substr_count($child->ruta, '/') - 1); // opcional: mantener consistencia
                    $child->save();
                }
            }
        }

        return redirect()
            ->route('organizaciones.index')
            ->with('success', 'Organización actualizada correctamente.');
    }

    /** Exportar a Excel */
    public function export(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        return Excel::download(new OrganizacionesExport($search), 'organizaciones.xlsx');
    }
}
