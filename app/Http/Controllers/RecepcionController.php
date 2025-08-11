<?php
namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Area;
use App\Models\Atencion;
use App\Models\Equipo;
use App\Models\Estado;
use App\Models\Recepcion;
use App\Models\Solicitud;
use App\Models\User;
use App\Services\KeyMaker;
use App\Services\KeyRipper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class RecepcionController extends Controller
{
    public function solicitudes()
    {
        $user = auth()->user()->load('area');
        $recepciones = Recepcion::where('user_id_destino', $user->id)
            ->with(['solicitud', 'estado', 'usuarioDestino', 'area', 'role'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $atencionIds = $recepciones->pluck('atencion_id')->unique();
        $usuariosDestinoPorAtencion = Recepcion::with(['usuarioDestino', 'role', 'area'])
            ->whereIn('atencion_id', $atencionIds)
            ->get()
            ->groupBy('atencion_id')
            ->map(function ($grupo) {
                return $grupo->map(function ($recepcion) {
                    return [
                        'name' => $recepcion->usuarioDestino->name ?? 'Sin asignar',
                        'profile_photo_url' => $recepcion->usuarioDestino && $recepcion->usuarioDestino->profile_photo_url
                            ? $recepcion->usuarioDestino->profile_photo_url
                            : asset('app-assets/images/pages/operador.png'),
                        'recepcion_role_name' => $recepcion->role->name ?? 'Sin rol',
                        'area_name' => $recepcion->area->area ?? 'Sin área',
                    ];
                })->values();
            });

        $tarjetas = $recepciones->map(function ($tarjeta) use ($usuariosDestinoPorAtencion) {
            $usuariosDestino = $usuariosDestinoPorAtencion->get($tarjeta->atencion_id, collect());
            return [
                'atencion_id' => $tarjeta->atencion_id,
                'created_at' => $tarjeta->created_at->toISOString(),
                'detalle' => $tarjeta->detalle,
                'estado' => $tarjeta->estado->estado,
                'estado_id' => $tarjeta->estado->id,
                'fecha_relativa' => Carbon::parse($tarjeta->created_at)->diffForHumans(),
                'porcentaje_progreso' => $tarjeta->avance,
                'recepcion_id' => $tarjeta->id,
                'role_name' => $tarjeta->role->name,
                'solicitud_id_ripped' => KeyRipper::rip($tarjeta->atencion_id),
                'titulo' => $tarjeta->solicitud->solicitud,
                'users' => $usuariosDestino,
                'user_name' => $tarjeta->usuarioDestino->name,
                'area' => $tarjeta->area->area,
            ];
        });
        $recibidas = $tarjetas->where('estado_id', 1)->sortBy('created_at')->values()->toArray();
        $progreso = $tarjetas->where('estado_id', 2)->sortBy('created_at')->values()->toArray();
        $resueltas = $tarjetas->where('estado_id', 3)->sortBy('created_at')->values()->toArray();
        $data = [
            'recibidas' => $recibidas,
            'progreso' => $progreso,
            'resueltas' => $resueltas,
        ];
        if ($user->hasRole('Recepcionista')) {
            $user->load('area.oficina');
            $data['areas'] = Area::where('oficina_id', $user->area->oficina_id)->get();
        } elseif ($user->hasRole('Supervisor')) {
            $data['equipos'] = Equipo::whereHas('usuarios.area', function ($query) use ($user) {
                $query->where('id', $user->area_id);
            })->get();
        } elseif ($user->hasRole('Gestor')) {
            $data['operadores'] = User::whereHas('roles', function ($query) {
                $query->where('name', 'Operador');
            })->whereHas('area', function ($query) use ($user) {
                $query->where('id', $user->area_id);
            })->where('activo', true)->get();
        }
        return view('modelos.recepcion.solicitudes', $data);
    }

    public function nuevasRecibidas(Request $request)
    {
        $user = auth()->user()->load('area');
        $recepcionIdsExistentes = $request->input('recepcion_ids', []);
        $query = Recepcion::where('user_id_destino', $user->id)
            ->where('estado_id', 1)
            ->orderBy('created_at', 'desc')
            ->limit(5);
        if (!empty($recepcionIdsExistentes)) {
            $query->whereNotIn('id', $recepcionIdsExistentes);
        }
        $tarjetas = $query->get();

        $atencionIds = $tarjetas->pluck('atencion_id')->unique();
        $usuariosDestinoPorAtencion = Recepcion::with(['usuarioDestino', 'role', 'area'])
            ->whereIn('atencion_id', $atencionIds)
            ->get()
            ->groupBy('atencion_id')
            ->map(function ($grupo) {
                return $grupo->map(function ($recepcion) {
                    return [
                        'name' => $recepcion->usuarioDestino->name ?? 'Sin asignar',
                        'profile_photo_url' => $recepcion->usuarioDestino && $recepcion->usuarioDestino->profile_photo_url
                            ? $recepcion->usuarioDestino->profile_photo_url
                            : asset('app-assets/images/pages/operador.png'),
                        'recepcion_role_name' => $recepcion->role->name ?? 'Sin rol',
                        'area_name' => $recepcion->area->area ?? 'Sin área',
                    ];
                })->values();
            });

        $nuevas = $tarjetas->map(function ($tarjeta) use ($usuariosDestinoPorAtencion) {
            $usuariosDestino = $usuariosDestinoPorAtencion->get($tarjeta->atencion_id, collect());
            return [
                'recepcion_id' => $tarjeta->id,
                'atencion_id' => $tarjeta->atencion_id,
                'titulo' => $tarjeta->solicitud->solicitud ?? '',
                'detalle' => $tarjeta->detalle,
                'estado' => $tarjeta->estado->estado,
                'estado_id' => $tarjeta->estado->id,
                'users' => $usuariosDestino,
                'user_name' => $tarjeta->usuarioDestino->name,
                'area' => $tarjeta->area->area,
                'role_name' => $tarjeta->role->name,
                'porcentaje_progreso' => $tarjeta->avance,
                'solicitud_id_ripped' => KeyRipper::rip($tarjeta->atencion_id),
                'fecha_relativa' => Carbon::parse($tarjeta->fecha_hora_solicitud)->diffForHumans(),
                'created_at' => $tarjeta->created_at,
            ];
        });
        return response()->json($nuevas);
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
            'cantidad_operadores' => $areas->count(),
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
            'unidades' => $equipos->count(),
        ]);
    }

    public function operadores(Solicitud $solicitud)
    {
        $user = auth()->user()->load('area');
        $operadores = User::whereHas('roles', function ($query) {
            $query->where('name', 'Operador');
        })->whereHas('area', function ($query) use ($user) {
            $query->where('id', $user->area_id);
        })->whereHas('solicitudes', function ($query) use ($solicitud) {
            $query->where('solicitudes.id', $solicitud->id);
        })->get();
        $operadores_activos = User::whereHas('roles', function ($query) {
            $query->where('name', 'Operador');
        })->where('activo', true)->get();
        return response()->json([
            'operadores' => $operadores,
            'operadores_activos' => $operadores_activos,
        ]);
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
        $recepcionistas = User::whereHas('roles', function ($query) {
            $query->where('name', 'Recepcionista');
        })->whereHas('area', function ($query) use ($user) {
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
            return back()->with('error', 'Ocurrió un error cuando se intentaba enviar la solicitud:' . $e->getMessage())->with('toast_position', 'top-center');
        }
        DB::commit(); //Finalizando la transacción
        return redirect()->route('recepcion.create')->with('success', 'La solicitud número "' . KeyRipper::rip($atencion_id) . '" ha sido recibida en el area ' . $user->area->area)->with('toast_position', 'top-center');
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
        //Validando el número de atención
        $role_id = Role::where('name', 'Supervisor')->first()->id;
        $derivada = Recepcion::where('atencion_id', $recepcion->atencion_id)->where('role_id', $role_id)->first();
        if ($derivada) {
            return response()->json(['success' => false, 'message' => 'La solicitud con número de atención ' . $derivada->atencion_id . ' ya ha sido derivada a ' . $derivada->usuarioDestino->name . ' en el área ' . $derivada->area->area]);
        }
        //Seleccionando el supervisor
        $supervisores = User::whereHas('roles', function ($query) {
            $query->where('name', 'Supervisor');
        })->whereHas('area', function ($query) use ($area) {
            $query->where('id', $area->id);
        })->get();
        if ($supervisores->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No hay supervisor disponible para esta área'], 422);
        }
        $supervisor = $supervisores->random();
        try {
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
            return response()->json(['success' => false, 'message' => 'La solicitud con número de atención ' . $asignada->atencion_id . ' ya ha sido asignada al ' . $asignada->role->name . ' ' . $asignada->usuarioDestino->name . ' en el área ' . $asignada->area->area]);
        }
        //Seleccionando el gestor
        $gestores = User::whereHas('roles', function ($query) {
            $query->where('name', 'Gestor');
        })->whereHas('area', function ($query) use ($recepcion) {
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
    public function asignarTodas(Request $request)
    {
        // Obtener los IDs de las tarjetas desde el frontend
        $recepcionIds = $request->input('recepcion_ids', []);
        if (empty($recepcionIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No hay solicitudes recibidas para asignar',
            ]);
        }
        // Obtener las recepciones basadas en los IDs enviados desde el frontend
        $recepciones = Recepcion::whereIn('id', $recepcionIds)
            ->where('user_id_destino', auth()->user()->id)
            ->where('estado_id', 1) // Estado "Recibida"
            ->get();
        $asignacionesExitosas = 0;
        $asignacionesFallidas = 0;
        $errores = [];

        // Procesar cada recepción
        foreach ($recepciones as $recepcion) {
            try {
                // Elegir un equipo que tenga al menos un Gestor en el área de la solicitud
                $equipos = Equipo::whereHas('usuarios', function ($q) use ($recepcion) {
                    $q->where('area_id', $recepcion->area_id)
                      ->whereHas('roles', function ($r) {
                          $r->where('name', 'Gestor');
                      });
                })->get();

                if ($equipos->isEmpty()) {
                    $asignacionesFallidas++;
                    $errores[] = "Recepción ID {$recepcion->id} - Solicitud {$recepcion->atencion_id}: No hay equipos disponibles para asignar";
                    continue;
                }

                // Elegir un equipo aleatorio
                $equipoSeleccionado = $equipos->random();

                // Usar el método asignar como sub-proceso
                $response = $this->asignar($recepcion->id, $equipoSeleccionado->id);
                if ($response->getData()->success) {
                    $asignacionesExitosas++;
                    // Actualizar el estado de la recepción original a "En progreso"
                    $recepcion->estado_id = Estado::where('estado', 'En progreso')->first()->id;
                    $recepcion->save();
                } else {
                    $asignacionesFallidas++;
                    $errores[] = "Recepción ID {$recepcion->id} - Solicitud {$recepcion->atencion_id}: " . $response->getData()->message;
                }

            } catch (\Exception $e) {
                $asignacionesFallidas++;
                $errores[] = "Recepción ID {$recepcion->id} - Solicitud {$recepcion->atencion_id}: Error - " . $e->getMessage();
            }
        }
        $mensaje = "Asignación masiva efectuada. ";
        $mensaje .= "Exitosas: {$asignacionesExitosas}, ";
        $mensaje .= "Fallidas: {$asignacionesFallidas}";
        return response()->json([
            'success' => true,
            'message' => $mensaje,
            'asignaciones_exitosas' => $asignacionesExitosas,
            'asignaciones_fallidas' => $asignacionesFallidas,
            'total_procesadas' => $recepciones->count(),
            'errores' => $errores,
            'tarjetas_asignadas' => $recepciones->where('estado_id', 2)->pluck('id')->toArray()
        ]);
    }

    public function delegar($recepcion_id, $user_id)
    {
        //Validación
        $recepcion = Recepcion::find($recepcion_id);
        if (!$recepcion) {
            return response()->json(['success' => false, 'message' => 'No se encontró la recepción solicitada'], 404);
        }
        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'No se encontró el usuario solicitado'], 404);
        }
        $user->load('solicitudes');
        $operador_habilitado = $user->solicitudes->where('id', $recepcion->solicitud_id)->first(); //Validando que el operador tenga el nivel de habilidades necesario para resolver la solicitud
        if (!$operador_habilitado) {
            return response()->json(['success' => false, 'message' => 'El operador no tiene el nivel de habilidades necesario para resolver la solicitud'], 422);
        }
        $copia_operador = Recepcion::with('estado')->where('atencion_id', $recepcion->atencion_id)->whereHas('role', function ($q) {
            $q->where('name', 'Operador');
        })->first();
        $role_id = Role::where('name', 'Operador')->first()->id; //Validando el número de atención
        $delegada = Recepcion::where('atencion_id', $recepcion->atencion_id)->where('role_id', $role_id)->first();
        if ($delegada) {
            return response()->json(['success' => false, 'message' => 'La solicitud con número de atención ' . $delegada->atencion_id . ' ya ha sido delegada al ' . $delegada->role->name . ' ' . $delegada->usuarioDestino->name . ' en el área ' . $delegada->area->area]);
        }
        //Delegando la solicitud
        DB::beginTransaction();
        try {
            $new_recepcion = new Recepcion();
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

    public function delegarTodas(Request $request)
    {
        // Obtener los IDs de las tarjetas desde el frontend
        $recepcionIds = $request->input('recepcion_ids', []);

        if (empty($recepcionIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No hay solicitudes recibidas para delegar',
            ]);
        }

        // Obtener las recepciones basadas en los IDs enviados desde el frontend
        $recepciones = Recepcion::whereIn('id', $recepcionIds)
            ->where('user_id_destino', auth()->user()->id)
            ->where('estado_id', 1) // Estado "Recibida"
            ->get();
        $delegacionesExitosas = 0;
        $delegacionesFallidas = 0;
        $errores = [];
        // Procesar cada recepción
        foreach ($recepciones as $recepcion) {
            try {
                //Obtener usuarios con rol "Operador" del area de la solicitud
                $operadores = User::whereHas('roles', function ($query) {
                    $query->where('name', 'Operador');
                })->where('area_id', $recepcion->area_id)->get();
                if ($operadores->count() == 0) {
                    $delegacionesFallidas++;
                    $errores[] = "Recepción ID {$recepcion->id} - Solicitud {$recepcion->atencion_id}: No tiene usuarios calificados disponibles";
                    continue;
                }
                //Seleccionar usuario aleatorio si hay más de uno
                $operadorSeleccionado = $operadores->random();
                //Usar el método delegar como sub-proceso
                $response = $this->delegar($recepcion->id, $operadorSeleccionado->id);
                if ($response->getData()->success) {
                    $delegacionesExitosas++;
                    // Actualizar el estado de la recepción original a "En progreso"
                    $recepcion->estado_id = Estado::where('estado', 'En progreso')->first()->id;
                    $recepcion->save();
                } else {
                    $delegacionesFallidas++;
                    $errores[] = "Recepción ID {$recepcion->id} - Solicitud {$recepcion->atencion_id}: " . $response->getData()->message;
                }
            } catch (\Exception $e) {
                $delegacionesFallidas++;
                $errores[] = "Recepción ID {$recepcion->id} - Solicitud {$recepcion->atencion_id}: Error - " . $e->getMessage();
            }
        }

        // Preparar mensaje de respuesta
        $mensaje = "Delegación masiva efectuada. ";
        $mensaje .= "Exitosas: {$delegacionesExitosas}, ";
        $mensaje .= "Fallidas: {$delegacionesFallidas}";

        return response()->json([
            'success' => true,
            'message' => $mensaje,
            'delegaciones_exitosas' => $delegacionesExitosas,
            'delegaciones_fallidas' => $delegacionesFallidas,
            'total_procesadas' => $recepciones->count(),
            'errores' => $errores,
            'tarjetas_delegadas' => $recepciones->where('estado_id', 2)->pluck('id')->toArray()
        ]);
    }

    public function iniciarTareas(string $recepcion_id)
    {
        //Validando
        $recepcion = Recepcion::find($recepcion_id); //Id de recepcion
        if (!$recepcion) {
            return response()->json(['success' => false, 'message' => 'No se encontró la recepción solicitada'], 404);
        }
        if ($recepcion->solicitud->tareas->count() == 0) { //Tareas asignadas
            return response()->json(['success' => false, 'message' => 'La solicitud no tiene tareas asignadas']);
        }
        //Iniciando las tareas
        DB::beginTransaction();
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

    public function tareas($recepcion_id)
    {
        $actividades = Actividad::where('recepcion_id', $recepcion_id)
            ->with(['tarea', 'estado'])
            ->get();
        $tareas = $actividades->map(function ($actividad) {
            return [
                'recepcion_id' => $actividad->recepcion_id,
                'tarea' => $actividad->tarea->tarea,
                'estado' => $actividad->estado->estado,
                'estado_id' => $actividad->estado_id,
                'actividad_id' => $actividad->id,
                'actividad_id_ripped' => KeyRipper::rip($actividad->id),
            ];
        });
        return response()->json(['tareas' => $tareas]);
    }

    public function reportarTarea(Request $request, $actividad_id)
    {
        $actividad = Actividad::find($actividad_id); //Validando
        if (!$actividad) {
            return response()->json(['success' => false, 'message' => 'No se encontró la tarea'], 404);
        }
        $nuevoEstado = $request->input('estado');
        if ($nuevoEstado === 'Resuelta') {
            $estado = Estado::where('estado', 'Resuelta')->first();
        } elseif ($nuevoEstado === 'En progreso') {
            $estado = Estado::where('estado', 'En progreso')->first();
        } else {
            return response()->json(['success' => false, 'message' => 'Estado no válido'], 422);
        }
        $actividad->estado_id = $estado->id;
        $actividad->save();
        $recepcionActual = Recepcion::find($actividad->recepcion_id); // Obtener la recepción actual para acceder al atencion_id
        $atencionId = $recepcionActual->atencion_id;
        $recepcionId = $actividad->recepcion_id; // ID de la recepción del operador propietario
        $totalActividades = Actividad::where('recepcion_id', $recepcionId)->count();
        $actividadesResueltas = Actividad::where('recepcion_id', $recepcionId)
            ->where('estado_id', 3) // ID 3 = Resuelta según la BD
            ->count();
        $porcentajeProgreso = $totalActividades > 0
            ? round(($actividadesResueltas / $totalActividades) * 100, 2)
            : 0;
        Recepcion::where('atencion_id', $atencionId)->update(['avance' => $porcentajeProgreso]); // Actualizar el campo avance en todas las recepciones con el mismo atencion_id
        $todasResueltas = ($actividadesResueltas === $totalActividades); // Verificar si todas las tareas están resueltas
        $solicitudActualizada = false;
        if ($todasResueltas && $nuevoEstado === 'Resuelta') { // Actualizar el estado de la solicitud a "Resuelta"
            $estadoResuelta = Estado::where('estado', 'Resuelta')->first();
            $recepciones = Recepcion::with('role')->where('atencion_id', $atencionId)->get();
            foreach ($recepciones as $recepcion) { // Actualizar todas las copias a resuelta exceptuando el usuario con rol "Gestor"
                if ($recepcion->role->name !== 'Gestor') {
                    $recepcion->estado_id = $estadoResuelta->id;
                    $recepcion->save();
                }
            }
            $solicitudActualizada = true;
        }
        return response()->json([
            'success' => true,
            'message' => 'Estado de la tarea actualizado correctamente',
            'recepcion_id' => $actividad->recepcion_id,
            'atencion_id' => $atencionId,
            'progreso' => [
                'total_actividades' => $totalActividades,
                'actividades_resueltas' => $actividadesResueltas,
                'porcentaje' => $porcentajeProgreso,
            ],
            'todas_resueltas' => $todasResueltas,
            'solicitud_actualizada' => $solicitudActualizada,
        ]);
    }
    public function avanceTablero(Request $request)
    {
        $user = auth()->user();
        $atencionIds = $request->input('atencion_ids', []);
        if (!is_array($atencionIds) || empty($atencionIds)) {
            return response()->json([]);
        }
        $estadosTablero = [1, 2, 3]; // 1: Recibida, 2: En progreso, 3: Resuelta
        $recepciones = Recepcion::with('usuarioDestino')->where('user_id_destino', $user->id)
            ->whereIn('estado_id', $estadosTablero)
            ->whereIn('atencion_id', $atencionIds)
            ->select('atencion_id', 'avance', 'estado_id')
            ->get();

        $todosPorAtencion = Recepcion::with(['usuarioDestino', 'role', 'area'])
            ->whereIn('atencion_id', $recepciones->pluck('atencion_id')->unique())
            ->get()
            ->groupBy('atencion_id')
            ->map(function ($grupo) {
                return $grupo->map(function ($recepcionItem) {
                    $profilePhotoUrl = $recepcionItem->usuarioDestino && $recepcionItem->usuarioDestino->profile_photo_url
                        ? $recepcionItem->usuarioDestino->profile_photo_url
                        : asset('app-assets/images/pages/operador.png');

                    return [
                        'usuarioDestino' => [
                            'id' => $recepcionItem->usuarioDestino->id ?? null,
                            'name' => $recepcionItem->usuarioDestino->name ?? 'Sin asignar',
                            'profile_photo_url' => $profilePhotoUrl,
                        ],
                        'role' => $recepcionItem->role,
                        'area' => $recepcionItem->area,
                    ];
                })->values();
            });

        $resultado = $recepciones->map(function ($recepcion) use ($todosPorAtencion) {
            return [
                'atencion_id' => $recepcion->atencion_id,
                'avance' => $recepcion->avance,
                'estado_id' => $recepcion->estado_id,
                'recepciones' => $todosPorAtencion->get($recepcion->atencion_id, collect()),
            ];
        });

        return response()->json($resultado);
    }

}
