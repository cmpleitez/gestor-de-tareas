<?php
namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Atencion;
use App\Models\Equipo;
use App\Models\Estado;
use App\Models\Parametro;
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

    public function parametros()
    {
        $parametros = Parametro::All();
        return view('modelos.parametro.index', compact('parametros'));
    }

    public function parametrosEdit(Parametro $parametro)
    {
        return view('modelos.parametro.edit', compact('parametro'));
    }

    public function parametrosUpdate(Request $request, Parametro $parametro)
    {
        $validatedData = $request->validate([
            'parametro'     => 'required|string|min:3|max:255|unique:parametros,parametro,' . $parametro->id,
            'valor'         => 'required|string|min:1|max:255',
            'unidad_medida' => 'required|string|min:3|max:255',
        ]);
        $parametro->parametro     = $validatedData['parametro']; 
        $parametro->valor         = $validatedData['valor'];     
        $parametro->unidad_medida = $validatedData['unidad_medida']; 
        $parametro->save(); 
        return redirect()->route('recepcion.parametros')->with('success', 'Parámetro actualizado correctamente');
    }

    public function parametrosActivate(Parametro $parametro)
    {
        $parametro->activo = ! $parametro->activo; // Guardado por unidad, no masivo
        $parametro->save();
        return redirect()->route('recepcion.parametros')->with('success', 'Parámetro actualizado correctamente');
    }

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

    private function obtenerTraza($tarjeta)
    {
        $actividades = Actividad::whereHas('recepcion', function ($query) use ($tarjeta) {
            $query->where('atencion_id', $tarjeta->atencion_id)
                ->where('user_destino_role_id', Role::where('name', 'Operador')->first()->id);
        })
            ->with(['tarea', 'estado'])
            ->get()
            ->sortByDesc('updated_at');
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
        return $traza;
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
                    $query->where('origen_user_id', $user->id);
                } else {
                    $query->where('destino_user_id', $user->id);
                }
            })
            ->with(['solicitud.tareas', 'usuarioDestino', 'usuarioOrigen', 'atencion.oficina', 'atencion.estado', 'role', 'actividades.tarea'])
            ->whereHas('atencion.oficina', function ($query) use ($user) {
                $query->where('id', $user->oficina_id);
            })
            ->where('estado_id', '<>', Estado::where('estado', 'Resuelta')->first()->id)
            ->where('activo', true)
            ->orderBy('atencion_id', 'asc')
            ->take(10)
            ->get();
            $atencionIds = $recepciones->pluck('atencion_id')->unique();
            $usuariosParticipantes = $this->obtenerUsuariosParticipantes($atencionIds); //Obtener usuarios participantes
            $tarjetas              = $recepciones->map(function ($tarjeta) use ($usuariosParticipantes) {
                $usuariosParticipantesAtencion = $usuariosParticipantes->get($tarjeta->atencion_id, collect());
                $traza                         = $this->obtenerTraza($tarjeta);
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
            $recibidas                = $tarjetas->where('estado_id', Estado::where('estado', 'Recibida')->first()->id)->sortBy('created_at')->values()->toArray();
            $progreso                 = $tarjetas->where('estado_id', Estado::where('estado', 'En progreso')->first()->id)->sortBy('created_at')->values()->toArray();
            $resueltas                = $tarjetas->where('estado_id', Estado::where('estado', 'Resuelta')->first()->id)->sortBy('created_at')->values()->toArray();
            $parametro                = Parametro::where('parametro', 'Frecuencia de refresco')->first();
            $frecuencia_actualizacion = $parametro ? $parametro->valor : 30; // Valor por defecto: 30 segundos
            $data                     = [
                'recibidas'                => $recibidas,
                'progreso'                 => $progreso,
                'resueltas'                => $resueltas,
                'equipos'                  => $equipos,
                'operadores'               => $operadores,
                'frecuencia_actualizacion' => $frecuencia_actualizacion,
            ];
            return view('modelos.recepcion.solicitudes', $data);
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error cuando se intentaba obtener las tarjetas:' . $e->getMessage());
        }
    }

    public function nuevasRecibidas(Request $request)
    {
        try {
            //PROCESO
            $user                    = auth()->user();
            $atencionesIdsEnFrontend = $request->input('atencion_ids', []);
            $recepciones             = Recepcion::where(function ($query) use ($user) {
                if ($user->mainRole->name == 'Cliente') {
                    $query->where('origen_user_id', $user->id);
                } else {
                    $query->where('destino_user_id', $user->id);
                }
            })
                ->with(['solicitud.tareas', 'usuarioDestino', 'usuarioOrigen', 'atencion.oficina', 'atencion.estado', 'role', 'actividades.tarea'])
                ->whereHas('atencion.oficina', function ($query) use ($user) {
                    $query->where('id', $user->oficina_id);
                })
                ->where('estado_id', Estado::where('estado', 'Recibida')->first()->id)
                ->where('activo', true)
                ->orderBy('atencion_id')
                ->take(5)
                ->get();
            $atencionIds = $recepciones->pluck('atencion_id')->unique();
            $usuariosParticipantes = $this->obtenerUsuariosParticipantes($atencionIds); //Obtener usuarios participantes
            if (! empty($atencionesIdsEnFrontend)) {
                $recepciones = $recepciones->whereNotIn('atencion_id', $atencionesIdsEnFrontend);
            }
            $nuevas = $recepciones->map(function ($tarjeta) use ($usuariosParticipantes) {
                $usuariosParticipantesAtencion = $usuariosParticipantes->get($tarjeta->atencion_id, collect());
                return [
                    'recepcion_id'        => $tarjeta->id,
                    'atencion_id'         => $tarjeta->atencion_id,
                    'titulo'              => $tarjeta->solicitud->solicitud ?? '',
                    'detalle'             => $tarjeta->detalle,
                    'traza'               => "Recibida",
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

    public function asignar(Recepcion $recepcion, Equipo $equipo)
    {
        $operadores = User::whereHas('equipos', function ($q1) use ($equipo) {
            $q1->where('equipo_id', $equipo->id);
        })->whereHas('mainRole', function ($q1) {
            $q1->where('name', 'Operador');
        })->with('equipos')->where('activo', true)->get();
        if ($operadores->isEmpty()) {
            return response()->json(['warning' => true, 'message' => 'No hay operadores disponibles para asignar la solicitud'], 422);
        }
        try {
            //VALIDACIÓN
            $operador = $operadores->random();
            //PROCESO
            $estado_en_progreso_id = Estado::where('estado', 'En progreso')->first()->id;
            DB::beginTransaction();
            $new_recepcion                  = new Recepcion();
            $new_recepcion->id              = (new KeyMaker())->generate('Recepcion', $recepcion->solicitud_id);
            $new_recepcion->atencion_id     = $recepcion->atencion_id;
            $new_recepcion->solicitud_id    = $recepcion->solicitud_id;
            $new_recepcion->user_destino_role_id         = Role::where('name', 'Operador')->first()->id;
            $new_recepcion->origen_user_id  = auth()->user()->id;
            $new_recepcion->destino_user_id = $operador->id;
            $new_recepcion->estado_id       = Estado::where('estado', 'Recibida')->first()->id;
            $new_recepcion->save();
            $recepcion->activo    = true; //Validar solicitud y actualizar estado - Copia Operador
            $recepcion->estado_id = $estado_en_progreso_id;
            $atencion_id          = $recepcion->atencion_id;
            $recepcion->save();
            //RESULTADO
            DB::commit();
            $traza = $this->obtenerTraza($recepcion); // Obtener la traza actualizada
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

    public function consultarAvance(Request $request)
    {
        try {
            $user        = auth()->user();
            $tarjetasIds = $request->input('atencion_ids', []);   //Tarjetas en el frontend
            if (! is_array($tarjetasIds) || empty($tarjetasIds)) { //Validación: si no hay tarjetas en el frontendya no se ejecuta el proceso
                return response()->json([]);
            }
            $tarjetas = Recepcion::with(['usuarioOrigen', 'usuarioDestino', 'atencion', 'actividades.tarea']) //Consulta de las tarjetas recopiladas
                ->whereIn('atencion_id', $tarjetasIds)
                ->where(function ($query) use ($user) {
                    if ($user->mainRole && $user->mainRole->name === 'Cliente') {
                        $query->where('origen_user_id', $user->id);
                    } else {
                        $query->where('destino_user_id', $user->id);
                    }
                })
                ->select('atencion_id', 'estado_id', 'origen_user_id', 'destino_user_id')
                ->get();
            $usuariosParticipantes = $this->obtenerUsuariosParticipantes($tarjetas->pluck('atencion_id')->unique()); //Obtener usuarios participantes
            $resultado             = $tarjetas->map(function ($tarjeta) use ($usuariosParticipantes) {
                $traza = $this->obtenerTraza($tarjeta);
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
            $estado_en_progreso_id = Estado::where('estado', 'En progreso')->first()->id;
            foreach ($recepcion->solicitud->tareas as $tarea) {
                $actividad                      = new Actividad();
                $actividad->id                  = (new KeyMaker())->generate('Actividad', $recepcion->solicitud_id);
                $actividad->recepcion_id        = $recepcion->id;
                $actividad->tarea_id            = $tarea->id;
                $actividad->user_destino_role_id= Role::where('name', 'Operador')->first()->id;
                $actividad->origen_user_id      = auth()->user()->id;
                $actividad->destino_user_id     = $recepcion->destino_user_id;
                $actividad->estado_id           = Estado::where('estado', 'En progreso')->first()->id;
                if ($tarea->id == 1) { //La primer tarea se resuelve en automático
                    $actividad->estado_id = Estado::where('estado', 'Resuelta')->first()->id;
                } else {
                    $actividad->estado_id = $estado_en_progreso_id;
                }
                $actividad->save();
            }
            $recepcion->activo    = true; //Actualizar estado de la recepción y establecer la recepción como orden compra válida
            $recepcion->estado_id = $estado_en_progreso_id;
            $recepcion->save();
            $atencion            = $recepcion->atencion; //Actualizar estado de la atención
            $atencion->estado_id = $estado_en_progreso_id;
            $atencion->save();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'El despacho de la solicitud "' . (new KeyRipper())->rip($atencion->id) . '" ha sido iniciado',
                'traza'   => $this->obtenerTraza($recepcion), // Obtener la traza actualizada
            ]);
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
            //LECTURA
            $actividad             = Actividad::find($actividad_id);
            $atencion              = $actividad->recepcion->atencion;
            $recepcion             = $actividad->recepcion->load('role');
            $recepciones           = Recepcion::with('role')->where('atencion_id', $atencion->id)->get();
            $nuevoEstado           = $request->input('estado');
            $estado_resuelta_id    = Estado::where('estado', 'Resuelta')->first()->id;
            $estado_en_progreso_id = Estado::where('estado', 'En progreso')->first()->id;
                                               //PROCESO
            if ($nuevoEstado === 'Resuelta') { //Actualizar actividad
                $actividad->estado_id = Estado::where('estado', 'Resuelta')->first()->id;
            } elseif ($nuevoEstado === 'En progreso') {
                $actividad->estado_id = Estado::where('estado', 'En progreso')->first()->id;
            } else {
                return response()->json(['success' => false, 'message' => 'Estado no válido'], 422);
            }
            $actividad->save();
            $total_actividades     = Actividad::where('recepcion_id', $recepcion->id)->count();
            $actividades_resueltas = Actividad::where('recepcion_id', $recepcion->id)
                ->where('estado_id', Estado::where('estado', 'Resuelta')->first()->id)
                ->count();
            $procentaje_progreso = $total_actividades > 0
                ? round(($actividades_resueltas / $total_actividades) * 100, 2)
                : 0;
            $recepcion->estado_id = $estado_en_progreso_id; //Actualizar recepción
            $recepcion->save();
            $atencion = $actividad->recepcion->atencion; //Actualizar atención
            if ($atencion) {
                $atencion->avance    = $procentaje_progreso;
                $atencion->estado_id = $estado_en_progreso_id;
                $atencion->save();
            }
            $todas_resueltas       = ($actividades_resueltas === $total_actividades); // Verificar si todas las tareas están resueltas
            $solicitud_actualizada = false;
            if ($todas_resueltas && $nuevoEstado === 'Resuelta') {
                foreach ($recepciones as $recepcion) { // Actualizar recepciones
                    $recepcion->estado_id = $estado_resuelta_id;
                    $recepcion->save();
                }
                $solicitud_actualizada = true;
                if ($atencion) { //Actualizar atención
                    $atencion->estado_id = $estado_resuelta_id;
                    $atencion->save();
                }
            }
            $traza = $this->obtenerTraza($recepcion);
            DB::commit();
            //RESULTADO
            return response()->json([
                'success'               => true,
                'message'               => 'Estado de la tarea actualizado correctamente',
                'recepcion_id'          => $recepcion->id,
                'atencion_id'           => $atencion->id,
                'traza'                 => $traza,
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
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la tarea: ' . $e->getMessage(),
            ], 500);
        }
    }

}
