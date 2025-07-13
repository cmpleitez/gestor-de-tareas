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
use Illuminate\Support\Facades\Log;

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
            ->whereHas('oficinas.users.solicitudes', function ($query) use ($solicitud) {
                $query->where('solicitudes.id', $solicitud->id);
            })->get();
        return response()->json([
            'areas' => $areas,
            'cantidad_operadores' => $areas->count()
        ]);
    }
    public function equipos(Solicitud $solicitud)
    {
        $equipos = Equipo::whereHas('usuarios.oficina', function ($query) use ($solicitud) {
            $query->where('area_id', auth()->user()->oficina->area_id);
        })->whereHas('usuarios.solicitudes', function ($query) use ($solicitud) {
            $query->where('solicitudes.id', $solicitud->id);
        })->get();
        return response()->json([
            'equipos' => $equipos,
            'unidades' => $equipos->count()
        ]);
    }

    public function operadores(Solicitud $solicitud)
    {
        $operadores = User::role('Operador')->whereHas('oficina.area', function ($query) use ($solicitud) {
            $query->where('area_id', auth()->user()->oficina->area_id);
        })->whereHas('solicitudes', function ($query) use ($solicitud) {
            $query->where('solicitudes.id', $solicitud->id);
        })->get();
        $operadores_activos = User::role('Operador')->where('activo', true)->get();
        return response()->json([
            'operadores' => $operadores,
            'operadores_activos' => $operadores_activos
        ]);
    }

    public function recepciones()
    {
        //Cache de 3 minutos
        $userId = auth()->user()->id;
        $datos = Cache::remember("recepciones_user_{$userId}", 180, function () use ($userId) {

            //Consulta de recepciones
            return $recepciones = Recepcion::where('user_id_destino', $userId)
                ->with(['solicitud', 'estado'])->orderBy('created_at', 'desc')
                ->limit(20)->get(); //Bloque de procesamiento: 20 unidades cada vez

            //Transformando a la estructura de la tarjeta
            return $recepciones->map(function ($tarjeta) {
                return [
                    'id' => $tarjeta->atencion_id,
                    'titulo' => $tarjeta->solicitud->solicitud,
                    'detalle' => $tarjeta->detalle,
                    'estado' => $tarjeta->estado->estado,
                    'estado_id' => $tarjeta->estado->id,
                    'user_destino_foto' => $tarjeta->usuarioDestino ? $tarjeta->usuarioDestino->profile_photo_url : null,
                    'user_destino_nombre' => $tarjeta->usuarioDestino ? $tarjeta->usuarioDestino->name : 'Sin asignar',
                    'area_destino_nombre' => $tarjeta->area ? $tarjeta->area->area : 'Sin área'
                ];
            });
        });
        return response()->json(['recepciones' => $datos]);
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

    public function derivar($recepcion_id, $area_id)
    {
        Log::info('Método derivar iniciado', ['recepcion_id' => $recepcion_id, 'area_id' => $area_id, 'user_id' => auth()->user()->id]);
        $recepcion = Recepcion::find($recepcion_id);
        if (!$recepcion) {
            return response()->json(['success' => false, 'message' => 'No se encontró la recepción solicitada'], 404);
        }
        $area = Area::find($area_id);
        if (!$area) {
            return response()->json(['success' => false, 'message' => 'No se encontró el área solicitada'], 404);
        }
        try {
            //Validando el número de atención
            $role_id = Role::where('name', 'Supervisor')->first()->id;
            $derivada = Recepcion::where('atencion_id', $recepcion->atencion_id)->where('role_id', $role_id)->first();
            if ($derivada) {
                return response()->json(['success' => false, 'message' => 'La solicitud con número de atención ' . $derivada->atencion_id . ' ya ha sido derivada a ' . $derivada->usuarioDestino->name . ' en el área ' . $derivada->area->area]);
            }
            //Seleccionando el supervisor
            $supervisores = User::role('Supervisor')->whereHas('oficina', function ($query) use ($area) {
                $query->where('area_id', $area->id);
            })->get();
            if ($supervisores->isEmpty()) {
                Log::warning('No se encontró supervisor para área', ['area_id' => $area->id]);
                return response()->json(['success' => false, 'message' => 'No hay supervisor disponible para esta área'], 422);
            }
            $supervisor = $supervisores->random();
            //Derivando la solicitud
            DB::beginTransaction();
            try {
                $new_recepcion = new Recepcion(); //Creando solicitud - Copia Supervisor
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
                $recepcion->activo = true; //Validar solicitud y actualizar estado - Copia Recepcionista
                $recepcion->estado_id = Estado::where('estado', 'En progreso')->first()->id;
                $recepcion->save();
                DB::commit(); //Finalizando la transacción
                Log::info('Método derivar finalizado', ['recepcion_id' => $recepcion_id, 'area_id' => $area_id, 'user_id' => auth()->user()->id]);
                return response()->json(['success' => true, 'message' => 'La solicitud "' . $recepcion->atencion_id . '" ha sido derivada a ' . $recepcion->usuarioDestino->name . ' del area ' . $recepcion->area->area]);
            } catch (\Exception $e) {
                Log::error('Error al derivar', ['recepcion_id' => $recepcion_id, 'area_id' => $area_id, 'user_id' => auth()->user()->id, 'error' => $e->getMessage()]);

                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Ocurrió un error al derivar la solicitud:' . $e->getMessage()]);
            }
        } catch (\Exception $e) {
            Log::error('Error al derivar', ['recepcion_id' => $recepcion_id, 'area_id' => $area_id, 'user_id' => auth()->user()->id, 'error' => $e->getMessage()]);

            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al derivar: ' . $e->getMessage()], 500);
        }
    }

    public function asignar($recepcion_id, $equipo_id)
    {
        Log::info('Método asignar iniciado', ['recepcion_id' => $recepcion_id, 'equipo_id' => $equipo_id, 'user_id' => auth()->user()->id]);
        $recepcion = Recepcion::find($recepcion_id);
        if (!$recepcion) {
            return response()->json(['success' => false, 'message' => 'No se encontró la recepción solicitada'], 404);
        }
        $equipo = Equipo::find($equipo_id);
        if (!$equipo) {
            return response()->json(['success' => false, 'message' => 'No se encontró el equipo solicitado'], 404);
        }
        //Validando el número de atención
        $role_id = Role::where('name', 'Gestor')->first()->id;
        $asignada = Recepcion::where('atencion_id', $recepcion->atencion_id)->where('role_id', $role_id)->first();
        if ($asignada) {
            return response()->json(['success' => false, 'message' => 'La solicitud con número de atención ' . $asignada->atencion_id . ' ya ha sido asignada a ' . $asignada->usuarioDestino->name . ' en el área ' . $asignada->area->area]);
        }
        //Seleccionando el gestor
        $gestores = User::role('Gestor')->whereHas('oficina', function ($query) use ($recepcion) {
            $query->where('area_id', $recepcion->area_id);
        })->whereHas('equipos', function ($query) use ($equipo) {
            $query->where('equipos.id', $equipo->id);
        })->get();
        if ($gestores->isEmpty()) {
            Log::warning('No se encontró gestor para equipo', ['equipo_id' => $equipo->id]);
            return response()->json(['success' => false, 'message' => 'No hay gestor disponible para este equipo'], 422);
        }
        $gestor = $gestores->random();
        //Asignando la solicitud
        DB::beginTransaction();
        try {
            $new_recepcion = new Recepcion(); //Creando solicitud - Copia Gestor
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
            $recepcion->activo = true; //Validar solicitud y actualizar estado - Copia Supervisor
            $recepcion->estado_id = Estado::where('estado', 'En progreso')->first()->id;
            $recepcion->save();
            DB::commit(); //Finalizando la transacción
            //return redirect()->route('recepcion')->with('success', 'La solicitud "' . $recepcion->atencion_id . '" ha sido asignada a ' . $recepcion->usuarioDestino->name . ' del area ' . $recepcion->area->area);
            return response()->json(['success' => true, 'message' => 'La solicitud "' . $recepcion->atencion_id . '" ha sido asignada a ' . $recepcion->usuarioDestino->name . ' del area ' . $recepcion->area->area]);
        } catch (\Exception $e) {
            DB::rollBack();
            //return back()->with('error', 'Ocurrió un error al asignar la solicitud:' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Ocurrió un error al asignar la solicitud:' . $e->getMessage()]);
        }
    }

    public function delegar($recepcion_id, $user_id)
    {
        Log::info('Método delegar iniciado', ['recepcion_id' => $recepcion_id, 'user_id' => $user_id, 'auth_user_id' => auth()->user()->id]);
        $recepcion = Recepcion::find($recepcion_id);
        if (!$recepcion) {
            return response()->json(['success' => false, 'message' => 'No se encontró la recepción solicitada'], 404);
        }
        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'No se encontró el usuario solicitado'], 404);
        }
        //Validando el número de atención
        $role_id = Role::where('name', 'Operador')->first()->id;
        $delegada = Recepcion::where('atencion_id', $recepcion->atencion_id)->where('role_id', $role_id)->first();
        if ($delegada) {
            return response()->json(['success' => false, 'message' => 'La solicitud con número de atención ' . $delegada->atencion_id . ' ya ha sido delegada a ' . $delegada->usuarioDestino->name . ' en el área ' . $delegada->area->area]);
        }
        //Delegando la solicitud
        DB::beginTransaction();
        try {
            $new_recepcion = new Recepcion(); //Creando solicitud - Copia Operador
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
            $recepcion->activo = true; //Validar solicitud y actualizar estado - Copia Gestor
            $recepcion->estado_id = Estado::where('estado', 'En progreso')->first()->id;
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
            //return redirect()->route('recepcion')->with('success', 'La solicitud "' . $recepcion->atencion_id . '" ha sido delegada a ' . $user->name . ' del área ' . $recepcion->area->area);
            return response()->json(['success' => true, 'message' => 'La solicitud "' . $recepcion->atencion_id . '" ha sido delegada a ' . $user->name . ' del área ' . $recepcion->area->area]);
        } catch (\Exception $e) {
            DB::rollBack();
            //return back()->with('error', 'Ocurrió un error al delegar la solicitud:' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Ocurrió un error al delegar la solicitud:' . $e->getMessage()]);
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
        $user = auth()->user();
        $recepciones = Recepcion::where('user_id_destino', $user->id)->get();

        // Datos según el rol del usuario
        if ($user->hasRole('Recepcionista')) {
            $areas = Area::where('zona_id', $user->oficina->area->zona_id)->get();
            return view('modelos.recepcion.mis-tareas', compact('areas', 'recepciones'));
        } elseif ($user->hasRole('Supervisor')) {
            $equipos = Equipo::whereHas('usuarios.oficina', function ($query) use ($user) {
                $query->where('area_id', $user->oficina->area_id);
            })->get();
            return view('modelos.recepcion.mis-tareas', compact('equipos', 'recepciones'));
        } elseif ($user->hasRole('Gestor')) {
            $operadores = User::role('Operador')->whereHas('oficina.area', function ($query) use ($user) {
                $query->where('area_id', $user->oficina->area_id);
            })->where('activo', true)->get();
            return view('modelos.recepcion.mis-tareas', compact('operadores', 'recepciones'));
        }

        // Por defecto, sin datos adicionales
        return view('modelos.recepcion.mis-tareas', compact('recepciones'));
    }

}
