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
        $departamentos = Departamento::all();
        return view('contacts.show', compact('contact', 'departamentos'));
    }

    public function index(Request $request)
    {
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

    public function create()
    {
        $aplicaciones = Aplicacion::all();
        $orgs = $this->orgOptions();
        return view('contacts.create', compact('aplicaciones', 'orgs'));
    }

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
        $orgs          = $this->orgOptions();

        return view('contacts.edit', [
            'contact'                   => $contact,
            'aplicaciones'              => $aplicaciones,
            'aplicacionesSeleccionadas' => $aplicSel,
            'pivotData'                 => $pivotData,
            'orgs'                      => $orgs,
        ]);
    }

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

    public function destroy($dni)
    {
        $contact = Contact::findOrFail($dni);
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
            ['22222222','20-22222222-6','Matias','Prueba','mprueba@gmail.com','471236','911','Rosario','pruebaaaa','mprueba','456127','Suboficial', $orgNames[0] ?? '', 'SaeCad|OtraApp'],
            ['33333333','20-33333333-4','Bruno','Soria','brunog.soria@gmail.com','3416000000','','Santa Fe','San Juan 1234','','','Oficial', $orgNames[1] ?? '', ''],
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

        $request->validate([
            'csv' => 'required|file|mimes:xlsx,csv,txt',
        ]);

        // Organizaciones permitidas por el usuario
        if ($user->rol === 'superadmin') {
            $allowedOrgs = Organizacion::pluck('id','nombre'); // nombre => id
        } else {
            $base = Organizacion::find($user->organizacion_id);
            if (!$base) return back()->with('error', 'No tenés organización asignada.');
            $allowedOrgs = Organizacion::where('ruta','like',$base->ruta.'%')
                ->pluck('id','nombre');
        }

        // Abrir archivo
        $file    = $request->file('csv')->getRealPath();
        $reader  = IOFactory::createReaderForFile($file);
        $reader->setReadDataOnly(true);
        $spread  = $reader->load($file);
        $sheet   = $spread->getSheetByName('Carga') ?: $spread->getActiveSheet();
        $rows    = $sheet->toArray(null, true, true, true);

        // Mapear encabezados
        $headerRow = $rows[1] ?? [];
        $map = [];
        foreach ($headerRow as $col => $name) {
            $map[ trim(strtolower($name)) ] = $col;
        }

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

            $dni   = $get('dni');
            $nom   = $get('nombre');
            $ape   = $get('apellido');
            $orgNm = $get('organizacion');

            if ($dni==='' || $nom==='' || $ape==='' || $orgNm==='') {
                $errors[] = ['fila'=>$i, 'error'=>'Faltan campos obligatorios: dni, nombre, apellido y organización.'];
                continue;
            }

            if (!ctype_digit($dni)) {
                $errors[] = ['fila'=>$i, 'error'=>"DNI inválido: $dni"];
                continue;
            }

            $orgId = $allowedOrgs[$orgNm] ?? null;
            if (!$orgId) {
                $errors[] = ['fila'=>$i, 'error'=>"Organización no permitida o inexistente: $orgNm"];
                continue;
            }

            // upsert por DNI
            $c = Contact::find($dni);
            if ($c) {
                // no permitir mover de organización
                if ($c->organizacion_id && (int)$c->organizacion_id !== (int)$orgId) {
                    $errors[] = ['fila'=>$i, 'error'=>"El DNI $dni ya existe en otra organización y no puede moverse por importación."];
                    continue;
                }
                // actualizar solo si viene no vacío
                $c->nombre   = $nom ?: $c->nombre;
                $c->apellido = $ape ?: $c->apellido;
                $c->email    = $get('email') ?: $c->email;
                $c->telefono = $get('telefono') ?: $c->telefono;
                $c->telefono_emergencia = $get('telefono_emergencia') ?: $c->telefono_emergencia;
                $c->localidad = $get('localidad') ?: $c->localidad;
                $c->domicilio = $get('domicilio') ?: $c->domicilio;
                $c->iup       = $get('iup') ?: $c->iup;
                $c->ni        = $get('ni') ?: $c->ni;
                $c->jerarquia = $get('jerarquia') ?: $c->jerarquia;
                if (!$c->organizacion_id) $c->organizacion_id = $orgId;
                $c->save();
                $updated++;
            } else {
                $c = new Contact();
                $c->dni        = $dni;
                $c->nombre     = $nom;
                $c->apellido   = $ape;
                $c->email      = $get('email');
                $c->telefono   = $get('telefono');
                $c->telefono_emergencia = $get('telefono_emergencia');
                $c->localidad  = $get('localidad');
                $c->domicilio  = $get('domicilio');
                $c->iup        = $get('iup');
                $c->ni         = $get('ni');
                $c->jerarquia  = $get('jerarquia');
                $c->organizacion_id = $orgId;
                $c->save();
                $created++;
            }

            // aplicaciones opcionales por nombre (SaeCad|OtraApp)
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

        return view('contacts.import_result', compact('created','updated','errors'));
    }
}
