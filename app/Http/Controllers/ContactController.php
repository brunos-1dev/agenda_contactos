<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Departamento;
use App\Models\Aplicacion;
use App\Models\Organizacion;
use Illuminate\Http\Request;
use App\Exports\ContactsExport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class ContactController extends Controller
{
    // ===== Helper: opciones para el <select> de organizaciones (con sangría) =====
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
        $departamentos = Departamento::all(); // legacy, si aún lo usas en la vista
        return view('contacts.show', compact('contact', 'departamentos'));
    }

    public function index(Request $request)
    {
        $auth = auth()->user();

        $query = Contact::query();

        // ===== filtro por organización según rol =====
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->rol === 'admin' || $user->rol === 'consulta') {
                $base = Organizacion::find($user->organizacion_id);
                if ($base) {
                    $orgIds = Organizacion::where('ruta', 'like', $base->ruta.'%')->pluck('id');
                    $query->whereIn('organizacion_id', $orgIds);
                } else {
                    $query->whereRaw('1=0');
                }
            }
            // superadmin ve todo
        } else {
            $query->whereRaw('1=0');
        }

        // Búsqueda opcional
        if ($request->filled('search')) {
            $s = trim($request->search);

            $query->where(function ($q) use ($s) {
                $q->where('nombre', 'like', "%{$s}%")
                ->orWhere('apellido', 'like', "%{$s}%")
                ->orWhere('dni', 'like', "%{$s}%")
                ->orWhereHas('organizacion', function ($oq) use ($s) {
                    $oq->where('nombre', 'like', "%{$s}%");
                });
            });
        }

        $contacts = $query
            ->with('organizacion')
            ->paginate(15)
            ->withQueryString();

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

    public function export(Request $request)
    {
        $search = $request->input('search');
        return Excel::download(new ContactsExport($search), 'contacts.xlsx');
    }

    /* =====================  IMPORTACIÓN  ===================== */

    public function importForm()
    {
        $user = auth()->user();
        if (!$user || $user->rol === 'consulta') {
            return redirect()->route('contacts.index')->with('error', 'Tu rol es de solo lectura.');
        }
        return view('contacts.import');
    }

    public function importTemplate()
    {
        $user = auth()->user();
        if (!$user || $user->rol === 'consulta') {
            return redirect()->route('contacts.index')->with('error', 'Tu rol es de solo lectura.');
        }

        // Organizaciones permitidas
        if ($user->rol === 'superadmin') {
            $orgNames = Organizacion::orderBy('ruta')->pluck('nombre')->all();
        } else {
            $base = Organizacion::find($user->organizacion_id);
            if (!$base) return back()->with('error', 'No tenés organización asignada.');
            $orgNames = Organizacion::where('ruta', 'like', $base->ruta.'%')
                ->orderBy('ruta')->pluck('nombre')->all();
        }

        $appNames = Aplicacion::orderBy('nombre')->pluck('nombre')->all();

        $book  = new Spreadsheet();
        $sheet = $book->getActiveSheet();
        $sheet->setTitle('Carga');

        $headers = [
            'dni','cuil','nombre','apellido',
            'email','telefono','telefono_emergencia',
            'localidad','domicilio',
            'iup','ni','jerarquia',
            'organizacion','aplicaciones_nombres',
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Ejemplos
        $sheet->fromArray([
            ['22222222','20-22222222-6','Matias','Garcia','mariasgarcia@gmail.com','471236','911','Rosario','San Juan 1234','mgarcia','456127','Suboficial', $orgNames[0] ?? '', 'SaeCad|OtraApp'],
            
        ], null, 'A2');

        $sheet->freezePane('A2');
        foreach (range('A','N') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2CD3C7']],
        ]);

        // Hoja Listas
        $lists = new Worksheet($book, 'Listas');
        $book->addSheet($lists);
        $lists->setCellValue('A1', 'Organizaciones');
        $r = 2; foreach ($orgNames as $n) { $lists->setCellValue("A{$r}", $n); $r++; }
        $lists->setCellValue('B1', 'Aplicaciones');
        $r = 2; foreach ($appNames as $n) { $lists->setCellValue("B{$r}", $n); $r++; }

        // Validación organización (col M)
        $lastOrgRow = count($orgNames) + 1;
        $orgRange   = 'Listas!$A$2:$A$' . $lastOrgRow; // << fix $A

        $dv = new DataValidation();
        $dv->setType(DataValidation::TYPE_LIST);
        $dv->setShowDropDown(true);
        $dv->setAllowBlank(false);
        $dv->setFormula1('=' . $orgRange);           // << fix
        $dv->setShowErrorMessage(true);
        $dv->setErrorTitle('Valor inválido');
        $dv->setError('Elegí una organización válida.');
        $sheet->setDataValidation("M2:M1000", $dv);

        // Sugerencia aplicaciones (col N)
        if (count($appNames)) {
            $lastAppRow = count($appNames) + 1;
            $appRange   = 'Listas!$B$2:$B$' . $lastAppRow; // << fix $B

            $dvApp = new DataValidation();
            $dvApp->setType(DataValidation::TYPE_LIST);
            $dvApp->setShowDropDown(true);
            $dvApp->setAllowBlank(true);
            $dvApp->setFormula1('=' . $appRange);    // << fix
            $sheet->setDataValidation("N2:N1000", $dvApp);
        }

        // Instrucciones
        $inst = new Worksheet($book, 'Instrucciones');
        $book->addSheet($inst);
        $lines = [
            'Cómo usar esta plantilla',
            '1) Completá los datos en la hoja "Carga".',
            '2) Obligatorios por fila: dni, nombre, apellido y organización.',
            '3) La columna "organizacion" tiene desplegable según tus permisos.',
            '4) "aplicaciones_nombres": separá múltiples por | (ej.: SaeCad|OtraApp).',
            '5) Upsert por DNI: si existe, actualiza solo campos no vacíos. No mueve de organización.',
            '6) Admin: solo subárbol propio. Superadmin: sin límite.',
        ];
        $inst->fromArray([[$lines[0]]], null, 'A1');
        $inst->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $i = 3; foreach (array_slice($lines,1) as $l) { $inst->setCellValue("A{$i}", $l); $i++; }
        $inst->getColumnDimension('A')->setWidth(120);

        $writer = IOFactory::createWriter($book, 'Xlsx');
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'plantilla_personas.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

        public function importProcess(Request $request)
    {
        $user = auth()->user();
        if (!$user || $user->rol === 'consulta') {
            return redirect()->route('contacts.index')->with('error', 'Tu rol es de solo lectura.');
        }

        // El input del form debe llamarse "csv"
        $request->validate([
            'csv' => 'required|file|mimes:xlsx,xls,csv,txt',
        ]);

        // Organizaciones permitidas por el usuario (array: [nombre => id])
        if ($user->rol === 'superadmin') {
            $allowedOrgs = Organizacion::pluck('id','nombre');
        } else {
            $base = Organizacion::find($user->organizacion_id);
            if (!$base) return back()->with('error', 'No tenés organización asignada.');
            $allowedOrgs = Organizacion::where('ruta','like',$base->ruta.'%')->pluck('id','nombre');
        }

        // Normalizador: minúsculas, espacios simples y sin acentos
        $normalize = function (?string $s): string {
            $s = (string)$s;
            $s = trim(preg_replace('/\s+/u', ' ', $s));
            $s = mb_strtolower($s, 'UTF-8');
            $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
            return $s ?? '';
        };

        // Mapa normalizado de organizaciones permitidas: [nombre_normalizado => id]
        $allowedOrgsNorm = [];
        foreach ($allowedOrgs as $name => $id) {
            $allowedOrgsNorm[$normalize($name)] = $id;
        }

        // Abrir archivo
        $file   = $request->file('csv')->getRealPath();
        $reader = IOFactory::createReaderForFile($file);

        // Si es CSV, arreglar encoding y delimitador (; o ,)
        if ($reader instanceof \PhpOffice\PhpSpreadsheet\Reader\Csv) {
            $head = @file_get_contents($file, false, null, 0, 4096) ?: '';
            $hasBom = str_starts_with($head, "\xEF\xBB\xBF");
            $reader->setInputEncoding($hasBom ? 'UTF-8' : 'Windows-1252');
            $reader->setDelimiter(substr_count($head, ';') > substr_count($head, ',') ? ';' : ',');
        }

        $reader->setReadDataOnly(true);
        $spread = $reader->load($file);
        $sheet  = $spread->getSheetByName('Carga') ?: $spread->getActiveSheet();
        $rows   = $sheet->toArray(null, true, true, true);

        // Mapear encabezados (fila 1)
        $headerRow = $rows[1] ?? [];
        $map = [];
        foreach ($headerRow as $col => $name) {
            $map[ trim(strtolower($name)) ] = $col;
        }

        // Columnas obligatorias
        $need = ['dni','nombre','apellido','organizacion'];
        foreach ($need as $n) {
            if (!isset($map[$n])) {
                return back()->with('error', "Falta la columna obligatoria: $n");
            }
        }

        $created = 0; $updated = 0; $errors = [];

        for ($i=2; $i<=count($rows); $i++) {
            $row = $rows[$i] ?? null; if (!$row) continue;

            $get = function($key) use ($map, $row) {
                $col = $map[$key] ?? null;
                return $col ? trim((string)($row[$col] ?? '')) : '';
            };
            // Lee el primero no vacío entre varias posibles columnas
            $getAny = function(array $keys) use ($get) {
                foreach ($keys as $k) {
                    $v = $get($k);
                    if ($v !== '') return $v;
                }
                return '';
            };

            // Normalizamos DNI por si viniera con puntos/espacios
            $dniRaw = $get('dni');
            $dni    = preg_replace('/\D+/', '', $dniRaw ?? '');

            $nom   = $get('nombre');
            $ape   = $get('apellido');
            $orgNm = $get('organizacion');

            if ($dni==='' || $nom==='' || $ape==='' || $orgNm==='') {
                $errors[] = ['fila'=>$i, 'error'=>'Faltan campos obligatorios: dni, nombre, apellido y organización.'];
                continue;
            }
            if (!ctype_digit($dni)) {
                $errors[] = ['fila'=>$i, 'error'=>"DNI inválido: $dniRaw"];
                continue;
            }

            $orgId = $allowedOrgsNorm[$normalize($orgNm)] ?? null;
            if (!$orgId) {
                $errors[] = ['fila'=>$i, 'error'=>"Organización no permitida o inexistente: $orgNm"];
                continue;
            }

            // === CUIL (opcional): normalizar/validar/unicidad ===
            $cuilRaw = $get('cuil');
            $cuil    = preg_replace('/\D+/', '', $cuilRaw ?? '');
            if ($cuil !== '' && strlen($cuil) !== 11) {
                $errors[] = ['fila'=>$i, 'error'=>"CUIL inválido: $cuilRaw (debe tener 11 dígitos)"];
                $cuil = ''; // no lo guardamos si es inválido
            }
            if ($cuil !== '') {
                $dupe = Contact::where('cuil', $cuil)
                        ->where('dni', '!=', $dni)
                        ->exists();
                if ($dupe) {
                    $errors[] = ['fila'=>$i, 'error'=>"CUIL $cuil ya está asignado a otro contacto."];
                    $cuil = ''; // evitar violar la unique
                }
            }

            // Campos opcionales
            $email   = $get('email');
            $tel     = $get('telefono');
            $telEmer = $getAny(['telefono_emergencia','contacto_emergencia']); // acepta ambos encabezados
            $loc     = $get('localidad');
            $dom     = $get('domicilio');
            $iup     = $get('iup');
            $ni      = $get('ni');
            $jera    = $get('jerarquia');

            // (opcional) evitar IUP duplicado con otro DNI
            if ($iup !== '') {
                $dupIup = Contact::where('iup', $iup)->where('dni', '<>', $dni)->exists();
                if ($dupIup) {
                    $errors[] = ['fila'=>$i, 'error'=>"IUP duplicado: $iup"];
                    $iup = '';
                }
            }

            // Upsert por DNI
            $c = Contact::find($dni);
            if ($c) {
                // No permitir mover de organización
                if ($c->organizacion_id && (int)$c->organizacion_id !== (int)$orgId) {
                    $errors[] = ['fila'=>$i, 'error'=>"El DNI $dni ya existe en otra organización y no puede moverse por importación."];
                    continue;
                }
                // Actualizar solo si viene no vacío
                $c->nombre               = $nom   ?: $c->nombre;
                $c->apellido             = $ape   ?: $c->apellido;
                if ($cuil !== '') $c->cuil = $cuil; // ← asignar CUIL en update
                $c->email                = $email ?: $c->email;
                $c->telefono             = $tel   ?: $c->telefono;
                $c->contacto_emergencia  = $telEmer ?: $c->contacto_emergencia;
                $c->localidad            = $loc   ?: $c->localidad;
                $c->domicilio            = $dom   ?: $c->domicilio;
                $c->iup                  = $iup   ?: $c->iup;
                $c->ni                   = $ni    ?: $c->ni;
                $c->jerarquia            = $jera  ?: $c->jerarquia;
                if (!$c->organizacion_id) $c->organizacion_id = $orgId;
                $c->save();
                $updated++;
            } else {
                $c = new Contact();
                $c->dni                 = $dni;
                $c->nombre              = $nom;
                $c->apellido            = $ape;
                $c->cuil                = $cuil ?: null; // ← asignar CUIL en create
                $c->email               = $email ?: null;
                $c->telefono            = $tel ?: null;
                $c->contacto_emergencia = $telEmer ?: null;
                $c->localidad           = $loc ?: null;
                $c->domicilio           = $dom ?: null;
                $c->iup                 = $iup ?: null;
                $c->ni                  = $ni ?: null;
                $c->jerarquia           = $jera ?: null;
                $c->organizacion_id     = $orgId;
                $c->save();
                $created++;
            }

            // Aplicaciones opcionales por nombre (SaeCad|OtraApp)
            $appsStr = $get('aplicaciones_nombres');
            if ($appsStr !== '') {
                $names = array_filter(array_map('trim', explode('|', $appsStr)));
                $ids = Aplicacion::whereIn('nombre', $names)->pluck('id')->all();
                if (!empty($ids)) {
                    $sync = [];
                    foreach ($ids as $id) { $sync[$id] = ['nombre_usuario' => null]; }
                    $c->aplicaciones()->syncWithoutDetaching($sync);
                }
            }
        }

        // Normalizar a lo que espera la vista
        $errorsRows = array_map(function ($e) {
            return [
                'row' => $e['fila']  ?? ($e['row'] ?? null),
                'msg' => $e['error'] ?? ($e['msg'] ?? ''),
            ];
        }, $errors);

        return view('contacts.import_result', [
            'inserted'   => $created,
            'updated'    => $updated,
            'errorsRows' => $errorsRows,
        ]);
}



}
