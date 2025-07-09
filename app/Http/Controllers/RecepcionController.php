<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Recepcion;
use App\Models\Solicitud;
use App\Models\User;
use App\Services\KeyMaker;
use Spatie\Permission\Models\Role;
use App\Models\Area;
use Illuminate\Support\Facades\DB;
use App\Models\Equipo;
use App\Models\Actividad;
use App\Models\Atencion;
use App\Models\Estado;
use Illuminate\Support\Facades\Cache;

class RecepcionController extends Controller
{

    public function index()
    {
        $recepciones = Recepcion::orWhere('user_id_origen', auth()->user()->id)
        ->orWhere('user_id_destino', auth()->user()->id)
        ->with('solicitud')->get();
        return view('modelos.recepcion.index', compact('recepciones'));
    }

    public function areas(Solicitud $solicitud)
    {
        $areas = Area::where('zona_id', auth()->user()->oficina->area->zona_id)
        ->whereHas('oficinas.users.solicitudes', function($query) use ($solicitud){
            $query->where('solicitudes.id', $solicitud->id);
        })->get();
        return response()->json([
            'areas' => $areas,
            'cantidad_operadores' => $areas->count()
        ]);
    }
    public function equipos(Solicitud $solicitud)
    {
        $equipos = Equipo::whereHas('usuarios.oficina', function($query) use ($solicitud){
            $query->where('area_id', auth()->user()->oficina->area_id);
        })->whereHas('usuarios.solicitudes', function($query) use ($solicitud){
            $query->where('solicitudes.id', $solicitud->id);
        })->get();
        return response()->json([
            'equipos' => $equipos,
            'unidades' => $equipos->count()
        ]);
    }

    public function operadores(Solicitud $solicitud)
    {
        $operadores = User::role('Operador')->whereHas('oficina.area', function($query) use ($solicitud){
            $query->where('area_id', auth()->user()->oficina->area_id);
        })->whereHas('solicitudes', function($query) use ($solicitud){
            $query->where('solicitudes.id', $solicitud->id);
        })->get();
        $operadores_activos = User::role('Operador')->where('activo', true)->get();
        return response()->json([
            'operadores' => $operadores,
            'operadores_activos' => $operadores_activos
        ]);
    }

    public function recibidas()
    {
        $requestStart = microtime(true);
        \Log::info("🟢 INICIO recibidas() - Request ID: " . request()->ip());
        
        $userId = auth()->user()->id;
        $cacheKey = "recepciones_recibidas_user_{$userId}";
        
        $startTime = microtime(true);
        
        // Verificar si el cache existe
        if (Cache::has($cacheKey)) {
            $datos = Cache::get($cacheKey);
            $cacheTime = (microtime(true) - $startTime) * 1000;
            \Log::info("🚀 Cache HIT - Usuario: {$userId}, Tiempo: {$cacheTime}ms");
        } else {
            // Cache de servidor por 3 minutos
            $datos = Cache::remember($cacheKey, 180, function() use ($userId) {
                $dbStartTime = microtime(true);
                
                //Consulta de recepciones
                $recepciones = Recepcion::where('user_id_destino', $userId)
                ->with(['solicitud', 'estado'])->orderBy('created_at', 'desc')
                ->limit(20)->get(); //Bloque de procesamiento: 20 unidades cada vez

                $dbTime = (microtime(true) - $dbStartTime) * 1000;
                \Log::info("💾 Consulta BD (con estados) - Usuario: {$userId}, Tiempo: {$dbTime}ms, Registros: " . $recepciones->count());

                //Transformando a la estructura de la tarjeta
                return $recepciones->map(function($tarjeta) {
                    return [
                        'id' => $tarjeta->atencion_id,
                        'titulo' => $tarjeta->solicitud->solicitud,
                        'detalle' => $tarjeta->detalle,
                        'estado' => $tarjeta->estado->estado
                    ];
                });
            });
            
            $totalTime = (microtime(true) - $startTime) * 1000;
            \Log::info("❌ Cache MISS - Usuario: {$userId}, Tiempo cache: {$totalTime}ms");
        }
        
        $totalRequestTime = (microtime(true) - $requestStart) * 1000;
        \Log::info("🔴 FIN recibidas() - Usuario: {$userId}, Tiempo TOTAL del método: {$totalRequestTime}ms");
        
        return response()->json(['recepciones' => $datos])
            ->header('Cache-Control', 'public, max-age=60'); // Cache HTTP por 1 minuto
    }

    public function create()
    {
        $solicitudes = Solicitud::where('activo', true)->get();
        return view('modelos.recepcion.create', compact('solicitudes'));
    }

    public function store(Request $request)
    {
        //Seleccionando un recepcionista aleatorio de la oficina destino
        $recepcionistas = User::role('Recepcionista')->where('oficina_id', auth()->user()->oficina_id)->get();
        if ($recepcionistas->isEmpty()) {
            return back()->with('error', 'La funcionalidad se encuentra inhabilitada, consulte con el administrador del sistema');
        }
        $recepcionista = $recepcionistas->random();
        //Iniciando la transacción
        DB::beginTransaction();
        try {
            $atencion = new Atencion(); //Creando el número de atención
            $atencion->id = (new KeyMaker())->generate('Atencion', $request->solicitud_id);
            $atencion->solicitud_id = $request->solicitud_id;
            $atencion->estado_id = Estado::where('estado', 'Recibida')->first()->id;
            $atencion_id = $atencion->id;
            $atencion->save();
            $recepcion = new Recepcion(); //Creando la recepción
            $recepcion->id = (new KeyMaker())->generate('Recepcion', $request->solicitud_id);
            $recepcion->solicitud_id = $request->solicitud_id;
            $recepcion->oficina_id = $recepcionista->oficina_id; 
            $recepcion->area_id = $recepcionista->oficina->area_id; 
            $recepcion->zona_id = $recepcionista->oficina->area->zona_id; 
            $recepcion->distrito_id = $recepcionista->oficina->area->zona->distrito_id; 
            $recepcion->user_id_origen = auth()->user()->id; 
            $recepcion->user_id_destino = $recepcionista->id; 
            $recepcion->role_id = Role::where('name', 'Recepcionista')->first()->id;
            $recepcion->estado_id = Estado::where('estado', 'Recibida')->first()->id;
            $recepcion->atencion_id = $atencion_id;
            $recepcion->detalle = $request->detalle;
            $recepcion->activo = false; //Por defecto invalidada, se valida al derivar la solicitud
            $recepcion->save();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error cuando se intentaba enviar la solicitud:' . $e->getMessage());
        }
        DB::commit(); //Finalizando la transacción
        return redirect()->route('recepcion')->with('success', 'La solicitud número "' . $atencion_id . '" ha sido recibida en el area ' . auth()->user()->oficina->area->area);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Recepcion $recepcion, string $estado)
    {
        $estado = Estado::where('estado', $estado)->first();
        if (!$estado) {
            return response()->json(['success' => false, 'message' => 'Estado no encontrado']);
        }
        $recepcion->estado_id = $estado->id;
        $recepcion->save();
        return response()->json(['success' => true, 'message' => 'Estado actualizado correctamente']);
    }

    public function derivar(Recepcion $recepcion, Area $area)
    {
        //Validando el número de atención
        $role_id = Role::where('name', 'Supervisor')->first()->id;
        $derivada = Recepcion::where('atencion_id', $recepcion->atencion_id)->where('role_id', $role_id)->first();
        if ($derivada) {
            return back()->with('error', 'La solicitud con número de atención ' . $derivada->atencion_id . ' ya ha sido derivada a ' . $derivada->usuarioDestino->name . ' en el área ' . $derivada->area->area);
        }
        //Seleccionando el supervisor
        $supervisores = User::role('Supervisor')->whereHas('oficina.area', function($query) use ($area){
            $query->where('area_id', $area->id);
        })->get();
        if ($supervisores->isEmpty()) {
            return back()->with('error', 'La funcionalidad se encuentra inhabilitada, consulte con el administrador del sistema');
        }
        $supervisor = $supervisores->random();
        //Derivando la solicitud
        DB::beginTransaction();
        try {
            $new_recepcion = new Recepcion(); //Creando la nueva recepción
            $new_recepcion->id = (new KeyMaker())->generate('Recepcion', $recepcion->solicitud_id);
            $new_recepcion->solicitud_id = $recepcion->solicitud_id;
            $new_recepcion->oficina_id = $recepcion->oficina_id;
            $new_recepcion->area_id = $area->id;
            $new_recepcion->zona_id = $area->zona_id;
            $new_recepcion->distrito_id = $area->zona->distrito_id;
            $new_recepcion->user_id_origen = auth()->user()->id;
            $new_recepcion->user_id_destino = $supervisor->id;
            $new_recepcion->role_id = $role_id;
            $new_recepcion->estado_id = Estado::where('estado', 'Recibida')->first()->id;
            $new_recepcion->atencion_id = $recepcion->atencion_id;
            $new_recepcion->detalle = $recepcion->detalle;
            $new_recepcion->activo = false;
            $new_recepcion->save();
            $recepcion->activo = true; //Se transforma en una solicitud válida al ser derivada a un área
            $recepcion->save();
            DB::commit(); //Finalizando la transacción
            return redirect()->route('recepcion')->with('success', 'La solicitud "' . $recepcion->atencion_id . '" ha sido derivada a ' . $recepcion->usuarioDestino->name . ' del area ' . $recepcion->area->area);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al derivar la solicitud:' . $e->getMessage());
        }
    }

    public function asignar(Recepcion $recepcion, Equipo $equipo)
    {
        //Validando el número de atención
        $role_id = Role::where('name', 'Gestor')->first()->id;
        $asignada = Recepcion::where('atencion_id', $recepcion->atencion_id)->where('role_id', $role_id)->first();
        if ($asignada) {
            return back()->with('error', 'La solicitud con número de atención ' . $asignada->atencion_id . ' ya ha sido asignada a ' . $asignada->usuarioDestino->name . ' en el área ' . $asignada->area->area);
        }
        //Seleccionando el gestor
        $gestores = User::role('Gestor')->whereHas('oficina.area', function($query) use ($recepcion){
            $query->where('area_id', $recepcion->area_id);
        })->whereHas('equipos', function($query) use ($equipo){
            $query->where('equipos.id', $equipo->id);
        })->get();
        if ($gestores->isEmpty()) {
            return back()->with('error', 'La funcionalidad se encuentra inhabilitada, consulte con el administrador del sistema');
        }
        $gestor = $gestores->random();
        //Asignando la solicitud
        DB::beginTransaction();
        try {
            $new_recepcion = new Recepcion(); //Creando una nueva recepción para el gestor
            $new_recepcion->id = (new KeyMaker())->generate('Recepcion', $recepcion->solicitud_id);
            $new_recepcion->solicitud_id = $recepcion->solicitud_id;
            $new_recepcion->oficina_id = $recepcion->oficina_id;
            $new_recepcion->area_id = $recepcion->area_id;
            $new_recepcion->zona_id = $recepcion->zona_id;
            $new_recepcion->distrito_id = $recepcion->distrito_id;
            $new_recepcion->user_id_origen = auth()->user()->id;
            $new_recepcion->user_id_destino = $gestor->id;
            $new_recepcion->role_id = $role_id;
            $new_recepcion->estado_id = Estado::where('estado', 'Recibida')->first()->id;
            $new_recepcion->atencion_id = $recepcion->atencion_id;
            $new_recepcion->detalle = $recepcion->detalle;
            $new_recepcion->activo = false;
            $new_recepcion->save();
            $recepcion->activo = true; //Se transforma en una solicitud válida al ser asignada a un gestor
            $recepcion->save();
            DB::commit(); //Finalizando la transacción
            return redirect()->route('recepcion')->with('success', 'La solicitud "' . $recepcion->atencion_id . '" ha sido asignada a ' . $recepcion->usuarioDestino->name . ' del area ' . $recepcion->area->area);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al asignar la solicitud:' . $e->getMessage());
        }
    }

    public function delegar(Recepcion $recepcion, User $user)
    {
        //Validando el número de atención
        $role_id = Role::where('name', 'Operador')->first()->id;
        $delegada = Recepcion::where('atencion_id', $recepcion->atencion_id)->where('role_id', $role_id)->first();
        if ($delegada) {
            return back()->with('error', 'La solicitud con número de atención ' . $delegada->atencion_id . ' ya ha sido delegada a ' . $delegada->usuarioDestino->name . ' en el área ' . $delegada->area->area);
        }
        //Delegando la solicitud
        DB::beginTransaction();
        try {
            $new_recepcion = new Recepcion(); //Creando la nueva recepción
            $new_recepcion->id = (new KeyMaker())->generate('Recepcion', $recepcion->solicitud_id);
            $new_recepcion->solicitud_id = $recepcion->solicitud_id;
            $new_recepcion->oficina_id = $recepcion->oficina_id;
            $new_recepcion->area_id = $recepcion->area_id;
            $new_recepcion->zona_id = $recepcion->zona_id;
            $new_recepcion->distrito_id = $recepcion->distrito_id;
            $new_recepcion->user_id_origen = auth()->user()->id;
            $new_recepcion->user_id_destino = $user->id;
            $new_recepcion->role_id = $role_id;
            $new_recepcion->estado_id = Estado::where('estado', 'Recibida')->first()->id;
            $new_recepcion->atencion_id = $recepcion->atencion_id;
            $new_recepcion->detalle = $recepcion->detalle;
            $new_recepcion->activo = false;
            $new_recepcion_id = $new_recepcion->id;
            $new_recepcion->save();
            $recepcion->activo = true; //Validando la solicitud delegada
            $recepcion->save();
            foreach ($recepcion->solicitud->tareas as $tarea) { //Delegando las actividades
                $actividad = new Actividad();
                $actividad->id = (new KeyMaker())->generate('Actividad', $recepcion->solicitud_id);
                $actividad->recepcion_id = $new_recepcion_id;
                $actividad->tarea_id = $tarea->id;
                $actividad->role_id = $role_id;
                $actividad->user_id_origen = auth()->user()->id;
                $actividad->user_id_destino = $user->id;
                $actividad->activo = false;
                $actividad->save();
            }
            DB::commit();
            return redirect()->route('recepcion')->with('success', 'La solicitud "' . $recepcion->atencion_id . '" ha sido delegada a ' . $user->name . ' del área ' . $recepcion->area->area);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al delegar la solicitud:' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        //
    }

    public function activate(Recepcion $recepcion)
    {
    }

    public function misTareas()
    {
        $recepciones = Recepcion::where('user_id_destino', auth()->user()->id)->get();
        return view('modelos.recepcion.mis-tareas', compact('recepciones'));
    }

    public function solicitudesPorEstado(Request $request)
    {
        $estados = $request->get('estados', ['Recibida', 'En Proceso', 'Finalizada']);
        
        $recepciones = Recepcion::where('user_id_destino', auth()->user()->id)
            ->with(['solicitud', 'estado'])
            ->whereHas('estado', function($query) use ($estados) {
                $query->whereIn('estado', $estados);
            })
            ->orderBy('created_at', 'desc')
            ->limit(60) // 20 por cada estado aprox
            ->get();

        // Agrupamos por estado
        $datos = $recepciones->groupBy('estado.estado')->map(function($grupo, $estado) {
            return $grupo->map(function($tarjeta) use ($estado) {
                return [
                    'id' => $tarjeta->id,
                    'titulo' => $tarjeta->solicitud->solicitud,
                    'detalle' => $tarjeta->detalle,
                    'estado' => $estado,
                    'fecha' => $tarjeta->created_at->format('Y-m-d H:i')
                ];
            });
        });
        
        return response()->json([
            'solicitudes' => $datos,
            'total_por_estado' => $datos->map->count()
        ]);
    }

    public function todasLasSolicitudes()
    {
        $recepciones = Recepcion::where('user_id_destino', auth()->user()->id)
            ->with(['solicitud', 'estado'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $datos = $recepciones->map(function($tarjeta) {
            return [
                'id' => $tarjeta->id,
                'titulo' => $tarjeta->solicitud->solicitud,
                'detalle' => $tarjeta->detalle,
                'estado' => $tarjeta->estado->estado,
                'estado_slug' => strtolower(str_replace(' ', '_', $tarjeta->estado->estado)),
                'fecha' => $tarjeta->created_at->diffForHumans()
            ];
        });
        
        return response()->json(['recepciones' => $datos]);
    }

    public function dashboard()
    {
        $recepciones = Recepcion::where('user_id_destino', auth()->user()->id)
            ->with(['solicitud:id,solicitud', 'estado:id,estado']) // Solo campos necesarios
            ->select(['id', 'solicitud_id', 'estado_id', 'detalle', 'created_at']) 
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        $datos = $recepciones->groupBy('estado.estado');
        
        return response()->json([
            'recibidas' => $datos->get('Recibida', collect())->take(10),
            'en_proceso' => $datos->get('En Proceso', collect())->take(10), 
            'finalizadas' => $datos->get('Finalizada', collect())->take(10),
            'totales' => [
                'recibidas' => $datos->get('Recibida', collect())->count(),
                'en_proceso' => $datos->get('En Proceso', collect())->count(),
                'finalizadas' => $datos->get('Finalizada', collect())->count(),
            ]
        ]);
    }

}
