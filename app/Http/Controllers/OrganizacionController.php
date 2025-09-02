<?php

namespace App\Http\Controllers;

use App\Models\Organizacion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;               
use App\Exports\OrganizacionesExport;         
use Maatwebsite\Excel\Facades\Excel;        

class OrganizacionController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        // Traemos todo ordenado para que el árbol salga prolijo
        $all = Organizacion::orderBy('ruta')->orderBy('orden')->get();

        // ===== SIN BÚSQUEDA: árbol completo =====
        if ($search === '') {
            $byParent = $all->groupBy('id_padre');

            $build = function ($parentId) use (&$build, $byParent) {
                // ->get evita problemas con claves nulas o inexistentes
                return $byParent->get($parentId, collect())
                    ->map(function ($node) use (&$build) {
                        $node->children = $build($node->id);
                        return $node;
                    })
                    ->values();
            };

            $tree    = $build(null);
            $openIds = []; // nada abierto por defecto

            return view('organizaciones.index', [
                'tree'    => $tree,
                'search'  => $search,
                'openIds' => $openIds,
            ]);
        }

        // ===== CON BÚSQUEDA: mostramos matches + ancestros =====
        $byId = $all->keyBy('id');

        // Coincidencias por nombre o tipo (case-insensitive)
        $matches = $all->filter(function ($o) use ($search) {
            return stripos($o->nombre ?? '', $search) !== false
                || stripos($o->tipo ?? '', $search) !== false;
        });

        // Recolectamos IDs a mantener (coincidencias + sus padres)
        $idsToKeep = collect();
        foreach ($matches as $m) {
            $id = $m->id;
            while ($id !== null && !$idsToKeep->contains($id)) {
                $idsToKeep->push($id);
                $id = optional($byId->get($id))->id_padre; // sube al padre
            }
        }

        // Estos IDs se usarán para abrir automáticamente los <details> en la vista
        $openIds = $idsToKeep->values()->all();

        // Filtramos el conjunto y reconstruimos el árbol con SOLO lo necesario
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
        // Para el selector de padre (indentado por nivel)
        $organizaciones = Organizacion::orderBy('ruta')->get();
        $parents = $organizaciones->map(function ($o) {
            $o->label = str_repeat('— ', (int) $o->nivel) . $o->nombre;
            return $o;
        });

        $tipos = Organizacion::TIPOS; // constante en el modelo

        return view('organizaciones.create', compact('parents', 'tipos'));
    }

    /** Guarda el alta */
    public function store(Request $request)
    {
        $tipos = Organizacion::TIPOS;

        $validated = $request->validate([
            'nombre'   => ['required', 'string', 'max:190'],
            'tipo'     => ['required', Rule::in($tipos)],
            'id_padre' => ['nullable', 'integer', 'exists:organizaciones,id'],
            'activo'   => ['nullable', 'boolean'],
        ]);

        $padre = null;
        $nivel = 0;

        if (!empty($validated['id_padre'])) {
            $padre = Organizacion::find($validated['id_padre']);
            $nivel = ($padre?->nivel ?? -1) + 1; // padre + 1
        }

     // Orden automático: último entre sus hermanos
    $padreId = $validated['id_padre'] ?? null;
    $orden = (Organizacion::where('id_padre', $padreId)->max('orden') ?? -1) + 1;

        $org = new Organizacion();
        $org->nombre   = $validated['nombre'];
        $org->tipo     = $validated['tipo'];
        $org->id_padre = $validated['id_padre'] ?? null;
        $org->nivel    = $nivel;
           $org->orden    = $orden;
        $org->activo   = $request->boolean('activo', true);
        $org->ruta     = '';               // se completa luego de tener el ID
        $org->save();

        // Autogenero la ruta: [ruta del padre] + ID + '/'
        $org->ruta = ($padre?->ruta ?? '/') . $org->id . '/';
        $org->save();

        return redirect()
            ->route('organizaciones.index')
            ->with('success', 'Organización creada correctamente.');
    }

    /** Exportar a Excel (si lo usás) */
    public function export(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        return Excel::download(new OrganizacionesExport($search), 'organizaciones.xlsx');
    }
}
