<?php
namespace App\Http\Controllers;

use App\Models\Actividad;
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
use Spatie\Permission\Models\Role;

class RecepcionController extends Controller
{
    public function solicitudes()
    {
        try {
            //VALIDACIÓN
            $equipos = Equipo::where('oficina_id', auth()->user()->oficina_id)->get();
            if ($equipos->isEmpty()) {
                return back()->with('error', 'No hay equipos de trabajo disponibles para asignar las solicitudes');
            }
            $operadores = User::whereHas('roles', function ($query) {
                $query->where('name', 'Operador');
            })->whereHas('oficina', function ($query) {
                $query->where('id', auth()->user()->oficina_id);
            })->where('activo', true)->get();
            if ($operadores->isEmpty()) {
                return back()->with('error', 'No hay operadores disponibles para asignar las solicitudes');
            }
            //PROCESO
            $user        = auth()->user();
            $recepciones = Recepcion::where(function ($query) use ($user) {
                $query->where('user_id_destino', $user->id)
                    ->orWhere('user_id_origen', $user->id);
            })
                ->with(['solicitud.tareas', 'usuarioDestino', 'usuarioOrigen', 'atencion.oficina', 'atencion.estado', 'role'])
                ->whereHas('atencion.oficina', function ($query) use ($user) {
                    $query->where('id', $user->oficina_id);
                })
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            $atencionIds = $recepciones->pluck('atencion_id')->unique();

            // Consulta separada para usuarios destino
            $usuariosDestino = Recepcion::with(['usuarioDestino', 'role'])
                ->whereIn('atencion_id', $atencionIds)
                ->get()
                ->map(function ($recepcion) {
                    return [
                        'recepcion_id'        => $recepcion->id,
                        'atencion_id'         => $recepcion->atencion_id,
                        'name'                => $recepcion->usuarioDestino->name,
                        'profile_photo_url'   => $recepcion->usuarioDestino->profile_photo_url
                            ? $recepcion->usuarioDestino->profile_photo_url
                            : asset('app-assets/images/pages/operador.png'),
                        'recepcion_role_name' => $recepcion->role->name,
                        'oficina_name'        => $recepcion->atencion->oficina->oficina,
                        'tipo'                => 'destino',
                    ];
                });

            // Consulta separada para usuarios origen
            $usuariosOrigen = Recepcion::with(['usuarioOrigen', 'role'])
                ->whereIn('atencion_id', $atencionIds)
                ->get()
                ->map(function ($recepcion) {
                    return [
                        'recepcion_id'        => $recepcion->id,
                        'atencion_id'         => $recepcion->atencion_id,
                        'name'                => $recepcion->usuarioOrigen->name,
                        'profile_photo_url'   => $recepcion->usuarioOrigen->profile_photo_url
                            ? $recepcion->usuarioOrigen->profile_photo_url
                            : asset('app-assets/images/pages/operador.png'),
                        'recepcion_role_name' => $recepcion->role->name,
                        'oficina_name'        => $recepcion->atencion->oficina->oficina,
                        'tipo'                => 'origen',
                    ];
                });

            // Combinar y agrupar por atencion_id
            $usuariosParticipantes = $usuariosDestino->merge($usuariosOrigen)
                ->groupBy('atencion_id')
                ->map(function ($grupo) {
                    return $grupo->unique(function ($usuario) {
                        return $usuario['recepcion_id'] . '_' . $usuario['tipo'];
                    })->values();
                });

            return $usuariosParticipantes;

            $tarjetas = $recepciones->map(function ($tarjeta) use ($usuariosParticipantes) {
                $usuariosParticipantesAtencion = $usuariosParticipantes->get($tarjeta->atencion_id, collect());
                return [
                    'atencion_id'         => $tarjeta->atencion_id,
                    'created_at'          => $tarjeta->created_at->toISOString(),
                    'detalle'             => $tarjeta->detalle,
                    'estado'              => $tarjeta->estado->estado,
                    'estado_id'           => $tarjeta->estado->id,
                    'fecha_relativa'      => Carbon::parse($tarjeta->created_at)->diffForHumans(),
                    'porcentaje_progreso' => $tarjeta->atencion->avance,
                    'recepcion_id'        => $tarjeta->id,
                    'role_name'           => $tarjeta->role->name,
                    'atencion_id_ripped'  => KeyRipper::rip($tarjeta->atencion_id),
                    'titulo'              => $tarjeta->solicitud->solicitud,
                    'users'               => $usuariosParticipantesAtencion,
                    'user_name'           => $tarjeta->usuarioDestino->name,
                    'user_origen_name'    => $tarjeta->usuarioOrigen->name,
                    'oficina'             => $tarjeta->atencion->oficina->oficina,
                ];
            });
            $recibidas = $tarjetas->where('estado_id', 1)->sortBy('created_at')->values()->toArray();
            $progreso  = $tarjetas->where('estado_id', 2)->sortBy('created_at')->values()->toArray();
            $resueltas = $tarjetas->where('estado_id', 3)->sortBy('created_at')->values()->toArray();
            $data      = [
                'recibidas'  => $recibidas,
                'progreso'   => $progreso,
                'resueltas'  => $resueltas,
                'equipos'    => $equipos,
                'operadores' => $operadores,
            ];
            return view('modelos.recepcion.solicitudes', $data);
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error cuando se intentaba obtener las solicitudes:' . $e->getMessage());
        }
    }

    public function nuevasRecibidas(Request $request)
    {
        try {
            $recepcionIdsExistentes = $request->input('recepcion_ids', []);
            $query                  = Recepcion::where('user_id_destino', auth()->user()->id)
                ->where('estado_id', 1)
                ->with(['atencion'])
                ->orderBy('created_at', 'desc')
                ->limit(5);
            if (! empty($recepcionIdsExistentes)) {
                $query->whereNotIn('id', $recepcionIdsExistentes);
            }
            $tarjetas                   = $query->get();
            $atencionIds                = $tarjetas->pluck('atencion_id')->unique();
            $usuariosDestinoPorAtencion = Recepcion::with(['usuarioDestino', 'role'])
                ->whereIn('atencion_id', $atencionIds)
                ->get()
                ->groupBy('atencion_id')
                ->map(function ($grupo) {
                    return $grupo->map(function ($recepcion) {
                        return [
                            'name'                => $recepcion->usuarioDestino->name ?? 'Sin asignar',
                            'profile_photo_url'   => $recepcion->usuarioDestino && $recepcion->usuarioDestino->profile_photo_url
                                ? $recepcion->usuarioDestino->profile_photo_url
                                : asset('app-assets/images/pages/operador.png'),
                            'recepcion_role_name' => $recepcion->role->name ?? 'Sin rol',
                        ];
                    })->values();
                });
            $nuevas = $tarjetas->map(function ($tarjeta) use ($usuariosDestinoPorAtencion) {
                $usuariosDestino = $usuariosDestinoPorAtencion->get($tarjeta->atencion_id, collect());
                return [
                    'recepcion_id'        => $tarjeta->id,
                    'atencion_id'         => $tarjeta->atencion_id,
                    'titulo'              => $tarjeta->solicitud->solicitud ?? '',
                    'detalle'             => $tarjeta->detalle,
                    'estado'              => $tarjeta->estado->estado,
                    'estado_id'           => $tarjeta->estado->id,
                    'users'               => $usuariosDestino,
                    'role_name'           => $tarjeta->role->name,
                    'porcentaje_progreso' => optional($tarjeta->atencion)->avance ?? 0,
                    'solicitud_id_ripped' => KeyRipper::rip($tarjeta->atencion_id),
                    'fecha_relativa'      => Carbon::parse($tarjeta->fecha_hora_solicitud)->diffForHumans(),
                    'created_at'          => $tarjeta->created_at,
                ];
            });
            return response()->json($nuevas);
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al obtener las nuevas solicitudes recibidas: ' . $e->getMessage());
        }
    }

    public function equipos(Solicitud $solicitud)
    {
        try {
            $equipos = Equipo::whereHas('usuarios.oficina', function ($query) {
                $query->where('id', auth()->user()->oficina_id);
            })->whereHas('usuarios.solicitudes', function ($query) use ($solicitud) {
                $query->where('solicitudes.id', $solicitud->id);
            })->get();
            return response()->json([
                'equipos'  => $equipos,
                'unidades' => $equipos->count(),
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al obtener los equipos: ' . $e->getMessage());
        }
    }

    public function operadores(Solicitud $solicitud)
    {
        // Modelo: o3
        try {
            $operadores = User::whereHas('roles', function ($query) {
                $query->where('name', 'Operador');
            })->whereHas('oficina', function ($query) {
                $query->where('id', auth()->user()->oficina_id);
            })->whereHas('solicitudes', function ($query) use ($solicitud) {
                $query->where('solicitudes.id', $solicitud->id);
            })->get();

            $operadores_activos = User::whereHas('roles', function ($query) {
                $query->where('name', 'Operador');
            })->where('activo', true)->get();

            return response()->json([
                'operadores'         => $operadores,
                'operadores_activos' => $operadores_activos,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al obtener los operadores: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $solicitudes = Solicitud::where('activo', true)->get();
            return view('modelos.recepcion.create', compact('solicitudes'));
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al obtener las solicitudes: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            //SELECCIONANDO UN RECEPTOR
            $user      = auth()->user();
            $Receptors = User::whereHas('roles', function ($query) {
                $query->where('name', 'Receptor');
            })->whereHas('oficina', function ($query) use ($user) {
                $query->where('id', $user->oficina_id);
            })->get();
            if ($Receptors->isEmpty()) {
                return back()->with('error', 'La funcionalidad se encuentra inhabilitada, consulte con el administrador del sistema');
            }
            $Receptor = $Receptors->random();
                                                    //REGISTRANDO LA SOLICITUD
            $atencion             = new Atencion(); //Creando el número de atención
            $atencion->id         = (new KeyMaker())->generate('Atencion', $request->solicitud_id);
            $atencion->oficina_id = $user->oficina_id;
            $atencion->estado_id  = Estado::where('estado', 'Recibida')->first()->id;
            $atencion->avance     = 0.00;
            $atencion->activo     = false; //Por defecto invalidada, se valida al ejemplo: procesar el carrito originando una orden de compra válida, luego una tarea programada borra cada cieto tiempo todos los registros con condicion nula en este campo
            $atencion_id          = $atencion->id;
            $atencion->save();
            $recepcion                  = new Recepcion(); //Creando la recepción
            $recepcion->id              = (new KeyMaker())->generate('Recepcion', $request->solicitud_id);
            $recepcion->atencion_id     = $atencion_id;
            $recepcion->role_id         = Role::where('name', 'Receptor')->first()->id;
            $recepcion->solicitud_id    = $request->solicitud_id;
            $recepcion->user_id_origen  = auth()->user()->id;
            $recepcion->user_id_destino = $Receptor->id;
            $recepcion->estado_id       = Estado::where('estado', 'Recibida')->first()->id;
            $recepcion->detalle         = $request->detalle;
            $recepcion->activo          = false; //Por defecto invalidada, se valida al ejemplo: procesar el carrito originando una orden de compra válida, luego una tarea programada borra cada cieto tiempo todos los registros con condicion nula en este campo
            $recepcion->save();
            DB::commit();
            return redirect()->route('recepcion.create')->with('success', 'La solicitud número "' . KeyRipper::rip($atencion_id) . '" ha sido recibida en la oficina ' . $user->oficina->oficina);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error cuando se intentaba enviar la solicitud:' . $e->getMessage());
        }
    }

    public function asignar(Recepcion $recepcion, Equipo $equipo)
    {
        try {
            //VALIDACIÓN
            $operadores = User::whereHas('equipos', function ($q1) use ($equipo) {
                $q1->where('equipo_id', $equipo->id);
            })->whereHas('mainRole', function ($q1) {
                $q1->where('name', 'Operador');
            })->where('activo', true)->get();
            if ($operadores->isEmpty()) {
                return response()->json(['warning' => true, 'message' => 'No hay operadores disponibles para asignar la solicitud'], 422);
            }
            $operador = $operadores->random();
            //PROCESO
            DB::beginTransaction();
            $new_recepcion                  = new Recepcion();
            $new_recepcion->id              = (new KeyMaker())->generate('Recepcion', $recepcion->solicitud_id);
            $new_recepcion->atencion_id     = $recepcion->atencion_id;
            $new_recepcion->solicitud_id    = $recepcion->solicitud_id;
            $new_recepcion->role_id         = Role::where('name', 'Operador')->first()->id;
            $new_recepcion->user_id_origen  = auth()->user()->id;
            $new_recepcion->user_id_destino = $operador->id;
            $new_recepcion->estado_id       = Estado::where('estado', 'Recibida')->first()->id;
            $new_recepcion->detalle         = $recepcion->detalle;
            $new_recepcion->activo          = false;
            $new_recepcion->save();
            $recepcion->activo    = true; //Validar solicitud y actualizar estado - Copia Operador
            $recepcion->estado_id = Estado::where('estado', 'En progreso')->first()->id;
            $atencion_id          = $recepcion->atencion_id;
            $recepcion->save();
            //RESULTADO
            DB::commit();
            return response()->json(['success' => true, 'message' => 'La solicitud "' . (new KeyRipper())->rip($atencion_id) . '" ha sido asignada al operador ' . $operador->name], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Ocurrió un error al asignar la solicitud:' . $e->getMessage()]);
        }
    }

    public function iniciarTareas(string $recepcion_id)
    {
                                                     //Validando
        $recepcion = Recepcion::find($recepcion_id); //Id de recepcion
        if (! $recepcion) {
            return response()->json(['success' => false, 'message' => 'No se encontró la recepción solicitada'], 404);
        }
        if ($recepcion->solicitud->tareas->count() == 0) { //Tareas asignadas
            return response()->json(['success' => false, 'message' => 'La solicitud no tiene tareas asignadas']);
        }
        //Iniciando las tareas
        DB::beginTransaction();
        try {
            foreach ($recepcion->solicitud->tareas as $tarea) {
                $actividad                  = new Actividad();
                $actividad->id              = (new KeyMaker())->generate('Actividad', $recepcion->solicitud_id);
                $actividad->recepcion_id    = $recepcion->id;
                $actividad->tarea_id        = $tarea->id;
                $actividad->role_id         = Role::where('name', 'Operador')->first()->id;
                $actividad->user_id_origen  = auth()->user()->id;
                $actividad->user_id_destino = $recepcion->user_id_destino;
                $actividad->estado_id       = Estado::where('estado', 'En progreso')->first()->id;
                $actividad->save();
            }
            $recepcion->activo    = true; //Validar solicitud y actualizar estado - Copia Operador
            $recepcion->estado_id = Estado::where('estado', 'En progreso')->first()->id;
            $atencion_id          = $recepcion->atencion_id;
            $recepcion->save();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'El despacho de la solicitud "' . (new KeyRipper())->rip($atencion_id) . '" ha sido iniciado']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Ocurrió un error al iniciar el despacho:' . $e->getMessage()]);
        }
    }

    public function tareas($recepcion_id)
    {
        try {
            $actividades = Actividad::where('recepcion_id', $recepcion_id)
                ->with(['tarea', 'estado'])
                ->get();
            $tareas = $actividades->map(function ($actividad) {
                return [
                    'recepcion_id'        => $actividad->recepcion_id,
                    'tarea'               => $actividad->tarea->tarea,
                    'estado'              => $actividad->estado->estado,
                    'estado_id'           => $actividad->estado_id,
                    'actividad_id'        => $actividad->id,
                    'actividad_id_ripped' => KeyRipper::rip($actividad->id),
                ];
            });
            return response()->json(['tareas' => $tareas]);
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al obtener las tareas: ' . $e->getMessage());
        }
    }
    public function reportarTarea(Request $request, $actividad_id)
    {
        try {
            DB::beginTransaction();
            $actividad    = Actividad::find($actividad_id); //Validando
            $atencion_id  = $actividad->recepcion->atencion_id;
            $recepcion_id = $actividad->recepcion_id;
            if (! $actividad) {
                return response()->json(['success' => false, 'message' => 'No se encontró la tarea'], 404);
            }
            $nuevoEstado = $request->input('estado');
            if ($nuevoEstado === 'Resuelta') {
                $actividad->estado_id = Estado::where('estado', 'Resuelta')->first()->id;
            } elseif ($nuevoEstado === 'En progreso') {
                $actividad->estado_id = Estado::where('estado', 'En progreso')->first()->id;
            } else {
                return response()->json(['success' => false, 'message' => 'Estado no válido'], 422);
            }
            $actividad->save();
            $total_actividades     = Actividad::where('recepcion_id', $recepcion_id)->count();
            $actividades_resueltas = Actividad::where('recepcion_id', $recepcion_id)
                ->where('estado_id', Estado::where('estado', 'Resuelta')->first()->id)
                ->count();
            $procentaje_progreso = $total_actividades > 0
                ? round(($actividades_resueltas / $total_actividades) * 100, 2)
                : 0;
            $atencion = $actividad->recepcion->atencion; //Actualizar avance
            if ($atencion) {
                $atencion->avance = $procentaje_progreso;
                $atencion->save();
            }
            $todas_resueltas       = ($actividades_resueltas === $total_actividades); // Verificar si todas las tareas están resueltas
            $solicitud_actualizada = false;
            if ($todas_resueltas && $nuevoEstado === 'Resuelta') { // Actualizar el estado de la solicitud a "Resuelta"
                $recepciones = Recepcion::with('role')->where('atencion_id', $atencion_id)->get();
                foreach ($recepciones as $recepcion) { // Actualizar todas las copias a resuelta
                    $recepcion->estado_id = Estado::where('estado', 'Resuelta')->first()->id;
                    $recepcion->save();
                }
                $solicitud_actualizada = true;
            }
            DB::commit();
            return response()->json([
                'success'               => true,
                'message'               => 'Estado de la tarea actualizado correctamente',
                'recepcion_id'          => $recepcion_id,
                'atencion_id'           => $atencion_id,
                'progreso'              => [
                    'total_actividades'     => $total_actividades,
                    'actividades_resueltas' => $actividades_resueltas,
                    'porcentaje'            => $procentaje_progreso,
                ],
                'todas_resueltas'       => $todas_resueltas,
                'solicitud_actualizada' => $solicitud_actualizada,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar la tarea: ' . $e->getMessage());
        }
    }

    public function avanceTablero(Request $request)
    {
        try {
            $user        = auth()->user();
            $atencionIds = $request->input('atencion_ids', []);
            if (! is_array($atencionIds) || empty($atencionIds)) {
                return response()->json([]);
            }
            $estadosTablero = [1, 2, 3]; // 1: Recibida, 2: En progreso, 3: Resuelta
            $recepciones    = Recepcion::with(['usuarioDestino', 'atencion'])->where('user_id_destino', $user->id)
                ->whereIn('estado_id', $estadosTablero)
                ->whereIn('atencion_id', $atencionIds)
                ->select('atencion_id', 'estado_id')
                ->get();

            $todosPorAtencion = Recepcion::with(['usuarioDestino', 'role'])
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
                                'id'                => $recepcionItem->usuarioDestino->id ?? null,
                                'name'              => $recepcionItem->usuarioDestino->name ?? 'Sin asignar',
                                'profile_photo_url' => $profilePhotoUrl,
                            ],
                            'role'           => $recepcionItem->role,
                        ];
                    })->values();
                });

            $resultado = $recepciones->map(function ($recepcion) use ($todosPorAtencion) {
                return [
                    'atencion_id' => $recepcion->atencion_id,
                    'avance'      => optional($recepcion->atencion)->avance ?? 0,
                    'estado_id'   => $recepcion->estado_id,
                    'recepciones' => $todosPorAtencion->get($recepcion->atencion_id, collect()),
                ];
            });
            return response()->json($resultado);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al obtener el avance del tablero: ' . $e->getMessage());
        }
    }

}
