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
        $user = auth()->user()->load('area.oficina.zona');
        $areas = Area::where('zona_id', $user->area->oficina->zona_id)
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
        $user = auth()->user()->load('area');
        $equipos = Equipo::whereHas('usuarios.area', function ($query) use ($user) {
            $query->where('id', $user->area_id);
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
        $user = auth()->user()->load('area');
        $operadores = User::role('Operador')->whereHas('area', function ($query) use ($user) {
            $query->where('id', $user->area_id);
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
        //Consulta de recepciones
        $recepciones = Recepcion::where('user_id_destino', auth()->user()->id)
        ->with(['solicitud', 'estado'])->orderBy('created_at', 'desc')
        ->limit(20)->get(); //Bloque de procesamiento: 20 unidades cada vez

        //Transformando a la estructura de la tarjeta
        $recepciones = $recepciones->map(function ($tarjeta) {
            return [
                'atencion_id' => $tarjeta->atencion_id,
                'titulo' => $tarjeta->solicitud->solicitud,
                'detalle' => $tarjeta->detalle,
                'estado' => $tarjeta->estado->estado,
                'estado_id' => $tarjeta->estado->id,
                'user_foto' => $tarjeta->usuarioDestino && $tarjeta->usuarioDestino->profile_photo_url
                    ? $tarjeta->usuarioDestino->profile_photo_url
                    : asset('app-assets/images/pages/operador.png'),
                'user_name' => $tarjeta->usuarioDestino->name,
                'area' => $tarjeta->area->area,
                'role_name' => $tarjeta->role->name,
                'recepcion_id' => $tarjeta->id
            ];
        });
        return response()->json(['recepciones' => $recepciones]);
    }

    public function create()
    {
        $solicitudes = Solicitud::where('activo', true)->get();
        return view('modelos.recepcion.create', compact('solicitudes'));
    }

    public function store(Request $request)
    {
        //Seleccionando un recepcionista aleatorio de la oficina destino
        $user = auth()->user()->load('area.oficina.zona.distrito');
        $recepcionistas = User::role('Recepcionista')->whereHas('area', function ($query) use ($user) {
            $query->where('oficina_id', $user->area->oficina_id);
        })->get();
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
            $recepcion->oficina_id = $recepcionista->area->oficina_id;
            $recepcion->area_id = $user->area_id;
            $recepcion->zona_id = $user->area->oficina->zona_id;
            $recepcion->distrito_id = $user->area->oficina->zona->distrito_id;
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
        return redirect()->route('recepcion')->with('success', 'La solicitud número "' . $atencion_id . '" ha sido recibida en el area ' . $user->area->area);
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
        $recepcion = Recepcion::find($recepcion_id);
        if (!$recepcion) {
            return response()->json(['success' => false, 'message' => 'No se encontró la recepción solicitada'], 404);
        }
        $area = Area::find($area_id)->load('oficina.zona.distrito');
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
            $supervisores = User::role('Supervisor')->whereHas('area', function ($query) use ($area) {
                $query->where('id', $area->id);
            })->get();
            if ($supervisores->isEmpty()) {
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
                $new_recepcion->zona_id = $area->oficina->zona_id;
                $new_recepcion->distrito_id = $area->oficina->zona->distrito_id;
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
                return response()->json(['success' => true, 'message' => 'La solicitud "' . $recepcion->atencion_id . '" ha sido derivada a ' . $supervisor->name . ' del area ' . $area->area]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Ocurrió un error al derivar la solicitud:' . $e->getMessage()]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al derivar: ' . $e->getMessage()], 500);
        }
    }

    public function asignar($recepcion_id, $equipo_id)
    {
        //Validando
        $recepcion = Recepcion::find($recepcion_id);
        if (!$recepcion) {
            return response()->json(['success' => false, 'message' => 'No se encontró la recepción solicitada'], 404);
        }
        $equipo = Equipo::find($equipo_id);
        if (!$equipo) {
            return response()->json(['success' => false, 'message' => 'No se encontró el equipo solicitado'], 404);
        }
        $role_id = Role::where('name', 'Gestor')->first()->id;
        $asignada = Recepcion::where('atencion_id', $recepcion->atencion_id)->where('role_id', $role_id)->first();
        if ($asignada) {
            return response()->json(['success' => false, 'message' => 'La solicitud con número de atención ' . $asignada->atencion_id . ' ya ha sido asignada al '.$asignada->role->name .' '. $asignada->usuarioDestino->name . ' en el área ' . $asignada->area->area]);
        }
        //Seleccionando el gestor
        $gestores = User::role('Gestor')->whereHas('area', function ($query) use ($recepcion) {
            $query->where('id', $recepcion->area_id);
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

            Log::info('En la asignación: Se actualiza al estado "En progreso" para la solicitud "' . $recepcion->id . '"');

            $recepcion->activo = true; //Validar solicitud y actualizar estado - Copia Supervisor
            $recepcion->estado_id = Estado::where('estado', 'En progreso')->first()->id;
            $recepcion->save();
            DB::commit(); //Finalizando la transacción
            return response()->json(['success' => true, 'message' => 'La solicitud "' . $recepcion->atencion_id . '" ha sido asignada a ' . $recepcion->usuarioDestino->name . ' del area ' . $recepcion->area->area]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Ocurrió un error al asignar la solicitud:' . $e->getMessage()]);
        }
    }

    public function delegar($recepcion_id, $user_id)
    {
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
            return response()->json(['success' => false, 'message' => 'La solicitud con número de atención ' . $delegada->atencion_id . ' ya ha sido delegada al '.$delegada->role->name .' '. $delegada->usuarioDestino->name . ' en el área ' . $delegada->area->area]);
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
            $new_recepcion->save();

            Log::info('En la delegación: Se actualiza al estado "En progreso" para la solicitud "' . $recepcion->id . '"');

            $recepcion->activo = true; //Validar solicitud y actualizar estado - Copia Gestor
            $recepcion->estado_id = Estado::where('estado', 'En progreso')->first()->id;
            $recepcion->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'La solicitud "' . $recepcion->atencion_id . '" ha sido delegada a ' . $user->name . ' del área ' . $recepcion->area->area]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Ocurrió un error al delegar la solicitud:' . $e->getMessage()]);
        }
    }

    public function iniciarTareas(string $recepcion_id)
    {
        $recepcion = Recepcion::find($recepcion_id); //Validando
        if (!$recepcion) {
            return response()->json(['success' => false, 'message' => 'No se encontró la recepción solicitada'], 404);
        }
        if ($recepcion->solicitud->tareas->count() == 0) {
            return response()->json(['success' => false, 'message' => 'La solicitud no tiene tareas asignadas']);
        }
        DB::beginTransaction(); //Iniciando las tareas
        try {
            foreach ($recepcion->solicitud->tareas as $tarea) { 
                $actividad = new Actividad();
                $actividad->id = (new KeyMaker())->generate('Actividad', $recepcion->solicitud_id);
                $actividad->recepcion_id = $recepcion->id;
                $actividad->tarea_id = $tarea->id;
                $actividad->role_id = Role::where('name', 'Operador')->first()->id;
                $actividad->user_id_origen = auth()->user()->id;
                $actividad->user_id_destino = $recepcion->user_id_destino;
                $actividad->estado_id = Estado::where('estado', 'En progreso')->first()->id;
                $actividad->save();
            }
            $recepcion->activo = true; //Validar solicitud y actualizar estado - Copia Operador
            $recepcion->estado_id = Estado::where('estado', 'En progreso')->first()->id;
            $recepcion->save();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Las tareas de la solicitud "' . $recepcion->atencion_id . '" han sido iniciadas']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Ocurrió un error al iniciar las tareas:' . $e->getMessage()]);
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
        $user = auth()->user()->load('area');
        $recepciones = Recepcion::where('user_id_destino', $user->id)->get();

        // Datos según el rol del usuario
        if ($user->hasRole('Recepcionista')) {
            $user->load('area.oficina');
            $areas = Area::where('oficina_id', $user->area->oficina_id)->get();
            return view('modelos.recepcion.mis-tareas', compact('areas', 'recepciones'));
        } elseif ($user->hasRole('Supervisor')) {
            $equipos = Equipo::whereHas('usuarios.area', function ($query) use ($user) {
                $query->where('id', $user->area_id);
            })->get();
            return view('modelos.recepcion.mis-tareas', compact('equipos', 'recepciones'));
        } elseif ($user->hasRole('Gestor')) {
            $operadores = User::role('Operador')->whereHas('area', function ($query) use ($user) {
                $query->where('id', $user->area_id);
            })->where('activo', true)->get();
            return view('modelos.recepcion.mis-tareas', compact('operadores', 'recepciones'));
        }

        // Por defecto, sin datos adicionales
        return view('modelos.recepcion.mis-tareas', compact('recepciones'));
    }

}
