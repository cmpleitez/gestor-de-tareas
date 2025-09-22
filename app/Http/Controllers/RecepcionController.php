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
    private function obtenerUsuariosParticipantes($atencionIds)
    {
        $usuariosDestino = Recepcion::with(['usuarioDestino', 'role']) // Consulta separada para usuarios destino
            ->whereIn('atencion_id', $atencionIds)
            ->get()
            ->map(function ($recepcion) {
                return [
                    'recepcion_id'        => $recepcion->id,
                    'atencion_id'         => $recepcion->atencion_id,
                    'name'                => $recepcion->usuarioDestino->name,
                    'profile_photo_url'   => $recepcion->usuarioDestino->profile_photo_url,
                    'recepcion_role_name' => $recepcion->role->name,
                    'tipo'                => 'destino',
                ];
            });

        $usuariosOrigen = Recepcion::with(['usuarioOrigen', 'role']) // Consulta separada para usuarios origen
            ->whereIn('atencion_id', $atencionIds)
            ->whereHas('role', function ($query) {
                $query->where('name', 'Receptor');
            })
            ->get()
            ->map(function ($recepcion) {
                return [
                    'recepcion_id'        => $recepcion->id,
                    'atencion_id'         => $recepcion->atencion_id,
                    'name'                => $recepcion->usuarioOrigen->name,
                    'profile_photo_url'   => $recepcion->usuarioOrigen->profile_photo_url,
                    'recepcion_role_name' => $recepcion->usuarioOrigen->mainRole->name,
                    'tipo'                => 'origen',
                ];
            });
        return $usuariosDestino->merge($usuariosOrigen) // Combinar y agrupar por atencion_id
            ->groupBy('atencion_id')
            ->map(function ($grupo) {
                return $grupo->unique(function ($usuario) {
                    return $usuario['recepcion_id'] . '_' . $usuario['tipo'];
                })->values();
            });
    }

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
                if ($user->mainRole->name == 'Cliente') {
                    $query->where('user_id_origen', $user->id);
                } else {
                    $query->where('user_id_destino', $user->id);
                }
            })
                ->with(['solicitud.tareas', 'usuarioDestino', 'usuarioOrigen', 'atencion.oficina', 'atencion.estado', 'role', 'actividades.tarea'])
                ->whereHas('atencion.oficina', function ($query) use ($user) {
                    $query->where('id', $user->oficina_id);
                })
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            //dd($recepciones);

            $atencionIds           = $recepciones->pluck('atencion_id')->unique();
            $usuariosParticipantes = $this->obtenerUsuariosParticipantes($atencionIds); //Obtener usuarios participantes
            $tarjetas              = $recepciones->map(function ($tarjeta) use ($usuariosParticipantes) {
                $usuariosParticipantesAtencion = $usuariosParticipantes->get($tarjeta->atencion_id, collect());
                $actividades                   = $tarjeta->actividades->sortByDesc('created_at');

                //inicio de la funcion para obtener la traza: debe convertirse en una funcion
                if ($actividades->isEmpty()) {
                    $traza = 'Recibida';
                } else {
                    $todasResueltas = $actividades->every(function ($actividad) {
                        return $actividad->estado_id == 3;
                    });

                    if ($todasResueltas) {
                        $traza = 'Resuelta';
                    } else {
                        $ultimaActividad = $actividades->first();
                        $traza           = $ultimaActividad->tarea->tarea;
                    }
                }
                return [
                    'atencion_id'         => $tarjeta->atencion_id,
                    'created_at'          => $tarjeta->created_at->toISOString(),
                    'detalle'             => $tarjeta->detalle,
                    'traza'               => $traza,
                    'estado_id'           => $tarjeta->estado->id,
                    'fecha_relativa'      => Carbon::parse($tarjeta->created_at)->diffForHumans(),
                    'porcentaje_progreso' => $tarjeta->atencion->avance,
                    'recepcion_id'        => $tarjeta->id,
                    'recepcion_id_ripped' => KeyRipper::rip($tarjeta->id),
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
            //PROCESO
            $user                   = auth()->user();
            $recepcionIdsExistentes = $request->input('recepcion_ids', []);
            $queryBase              = Recepcion::where(function ($query) use ($user) {
                if ($user->mainRole->name == 'Cliente') {
                    $query->where('user_id_origen', $user->id);
                } else {
                    $query->where('user_id_destino', $user->id);
                }
            })
                ->where('estado_id', 1)
                ->with(['solicitud.tareas', 'usuarioDestino', 'usuarioOrigen', 'atencion.oficina', 'atencion.estado', 'role', 'actividades.tarea'])
                ->whereHas('atencion.oficina', function ($query) use ($user) {
                    $query->where('id', $user->oficina_id);
                })
                ->orderBy('created_at', 'desc')
                ->take(5);
            $recepcionesBase       = $queryBase->get();
            $atencionIds           = $recepcionesBase->pluck('atencion_id')->unique();
            $usuariosParticipantes = $this->obtenerUsuariosParticipantes($atencionIds); //Obtener usuarios participantes
            $recepciones           = $recepcionesBase;                                  //Filtrar las recepciones que ya fueron mostradas
            if (! empty($recepcionIdsExistentes)) {
                $recepciones = $recepciones->whereNotIn('id', $recepcionIdsExistentes);
            }
            $nuevas = $recepciones->map(function ($tarjeta) use ($usuariosParticipantes) {
                $usuariosParticipantesAtencion = $usuariosParticipantes->get($tarjeta->atencion_id, collect());
                $actividad                     = $tarjeta->actividades// Obtener la última actividad realizada
                    ->sortByDesc('created_at')
                    ->first();
                $traza = $actividad ? $actividad->tarea->tarea : 'En revisión';
                return [
                    'recepcion_id'        => $tarjeta->id,
                    'atencion_id'         => $tarjeta->atencion_id,
                    'titulo'              => $tarjeta->solicitud->solicitud ?? '',
                    'detalle'             => $tarjeta->detalle,
                    'traza'               => $traza,
                    'estado_id'           => $tarjeta->estado->id,
                    'users'               => $usuariosParticipantesAtencion,
                    'role_name'           => $tarjeta->role->name,
                    'porcentaje_progreso' => optional($tarjeta->atencion)->avance ?? 0,
                    'recepcion_id_ripped' => KeyRipper::rip($tarjeta->id),
                    'atencion_id_ripped'  => KeyRipper::rip($tarjeta->atencion_id),
                    'fecha_relativa'      => Carbon::parse($tarjeta->created_at)->diffForHumans(),
                    'created_at'          => $tarjeta->created_at,
                ];
            });
            return response()->json($nuevas);
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al obtener las nuevas solicitudes recibidas: ' . $e->getMessage());
        }
    }

    public function consultarAvance(Request $request)
    {
        try {
            $user        = auth()->user();
            $tarjetasIds = $request->input('atencion_ids', []);   //Recopilación de tarjetas del frontend
            if (! is_array($tarjetasIds) || empty($tarjetasIds)) { //Validación: si no hay tarjetas ya no se ejecuta el proceso
                return response()->json([]);
            }
            $tarjetas = Recepcion::with(['usuarioOrigen', 'usuarioDestino', 'atencion', 'actividades.tarea']) //Consulta de las tarjetas recopiladas
                ->whereIn('atencion_id', $tarjetasIds)
                ->where(function ($query) use ($user) {
                    if ($user->mainRole && $user->mainRole->name === 'Cliente') {
                        $query->where('user_id_origen', $user->id);
                    } else {
                        $query->where('user_id_destino', $user->id);
                    }
                })
                ->select('atencion_id', 'estado_id', 'user_id_origen', 'user_id_destino')
                ->get();
            $usuariosParticipantes = $this->obtenerUsuariosParticipantes($tarjetas->pluck('atencion_id')->unique()); //Obtener usuarios participantes
            $resultado             = $tarjetas->map(function ($tarjeta) use ($usuariosParticipantes) {
                // Obtener la última actividad realizada
                $actividad = $tarjeta->actividades
                    ->sortByDesc('created_at')
                    ->first();
                $traza = $actividad ? $actividad->tarea->tarea : 'Recibida';

                return [
                    'atencion_id' => $tarjeta->atencion_id,
                    'avance'      => optional($tarjeta->atencion)->avance ?? 0, // Acceder al avance de la atención relacionada
                    'estado_id'   => $tarjeta->estado_id,
                    'traza'       => $traza,
                    'recepciones' => $usuariosParticipantes->get($tarjeta->atencion_id, collect()),
                ];
            });
            return response()->json($resultado);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al obtener el avance del tablero: ' . $e->getMessage());
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
            $recepcion->load('actividades.tarea'); // Obtener la traza actualizada
            $actividad = $recepcion->actividades->sortByDesc('created_at')->first();
            $traza     = $actividad ? $actividad->tarea->tarea : 'En revisión';
            return response()->json([
                'success' => true,
                'message' => 'La solicitud "' . (new KeyRipper())->rip($atencion_id) . '" ha sido asignada al operador ' . $operador->name,
                'traza'   => $traza,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Ocurrió un error al asignar la solicitud:' . $e->getMessage()]);
        }
    }

    public function iniciarTareas(string $recepcion_id)
    {
                                                     //VALIDANDO
        $recepcion = Recepcion::find($recepcion_id); //Id de recepcion
        if (! $recepcion) {
            return response()->json(['success' => false, 'message' => 'No se encontró la recepción solicitada'], 404);
        }
        if ($recepcion->solicitud->tareas->count() == 0) { //Tareas asignadas
            return response()->json(['success' => false, 'message' => 'La solicitud no tiene tareas asignadas']);
        }
        //PROCESO
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
                if ($tarea->id == 1) { //La primer tarea se resuelve en automático
                    $actividad->estado_id = Estado::where('estado', 'Resuelta')->first()->id;
                } else {
                    $actividad->estado_id = Estado::where('estado', 'En progreso')->first()->id;
                }
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

}
