<?php
namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StockRevisadoNotification;

use App\Models\Actividad;
use App\Models\Equipo;
use App\Models\Estado;
use App\Models\Parametro;
use App\Models\Recepcion;
use App\Models\Solicitud;
use App\Models\User;
use App\Models\Detalle;
use App\Models\Orden;
use App\Models\Atencion;
use App\Services\KeyMaker;
use App\Services\KeyRipper;
use Spatie\Permission\Models\Role;

class RecepcionController extends Controller
{
    public function solicitudes()
    {
        try {
            //VALIDACIÓN
            $equipos = Equipo::where('oficina_id', auth()->user()->oficina_id)->get();
            if ($equipos->isEmpty()) {
                return back()->with('warning', 'No hay equipos de trabajo disponibles para asignar las solicitudes');
            }
            $operadores = User::whereHas('roles', function ($query) {
                $query->where('name', 'Operador');
            })->whereHas('oficina', function ($query) {
                $query->where('id', auth()->user()->oficina_id);
            })->where('activo', true)->get();
            if ($operadores->isEmpty()) {
                return back()->with('warning', 'No hay operadores disponibles para asignar las solicitudes');
            }
            $solicitudes = Solicitud::has('tareas')->get();
            if ($solicitudes->isEmpty()) {
                return back()->with('warning', 'Las solicitudes no tienen tareas asociadas');
            }
            //PROCESO
            $user = auth()->user();
            $recepciones = Recepcion::where(function ($query) use ($user) {
                if ($user->mainRole->name == 'cliente') {
                    $query->where('origen_user_id', $user->id);
                } else {
                    $query->where('destino_user_id', $user->id);
                }
            })
            ->with(['solicitud.tareas', 'usuarioDestino', 'usuarioOrigen', 'atencion.oficina', 'atencion.estado', 'role', 'actividades.tarea'])
            ->whereHas('atencion.oficina', function ($query) use ($user) {
                $query->where('id', $user->oficina_id);
            })
            ->where('activo', true)
            ->orderBy('atencion_id', 'desc')
            ->take(15)
            ->get();
            $atencionIds = $recepciones->pluck('atencion_id')->unique();
            $usuariosParticipantes = $this->obtenerUsuariosParticipantes($atencionIds); //Obtener usuarios participantes
            $tarjetas = $recepciones->map(function ($tarjeta) use ($usuariosParticipantes) {
                $usuariosParticipantesAtencion = $usuariosParticipantes->get($tarjeta->atencion_id, collect());
                $traza = $this->obtenerTraza($tarjeta);
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
            $frecuencia_actualizacion = $parametro ? $parametro->valor : 1; // Valor por defecto: 1 minuto
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
                if ($user->mainRole->name == 'cliente') {
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
            //PROCESO
            $operador = $operadores->random(); //Seleccion del operador
            $usuario = Auth()->user();
            $estado_en_progreso_id = Estado::where('estado', 'En progreso')->first()->id;
            $atencion = $recepcion->atencion()->first();
            DB::beginTransaction();
                if ($usuario->mainRole->name=='receptor') { //El Receptor crea una copia de la solicitud y la asigna al Operador
                    $new_recepcion = new Recepcion(); 
                    $new_recepcion->id = (new KeyMaker())->generate('Recepcion', $recepcion->solicitud_id);
                    $new_recepcion->atencion_id = $atencion->id;
                    $new_recepcion->solicitud_id = $recepcion->solicitud_id;
                    $new_recepcion->origen_user_id = $usuario->id;
                    $new_recepcion->destino_user_id = $operador->id;
                    $new_recepcion->user_destino_role_id = Role::where('name', 'Operador')->first()->id;
                    $new_recepcion->estado_id = Estado::where('estado', 'Recibida')->first()->id;
                    $new_recepcion->save();
                    $recepcion->estado_id = $estado_en_progreso_id; //Validando de <copia receptor> y cambiando estado local
                    $recepcion->save();
                    foreach ($recepcion->solicitud->tareas as $tarea) { //Autoasignación de tareas
                        $coincide = $usuario->tareas()->where('tareas.id', $tarea->id)->first();
                        if($coincide) {
                            $actividad                      = new Actividad();
                            $actividad->id                  = (new KeyMaker())->generate('Actividad', $recepcion->solicitud_id);
                            $actividad->recepcion_id        = $recepcion->id;
                            $actividad->tarea_id            = $tarea->id;
                            $actividad->estado_id           = $estado_en_progreso_id;
                            $actividad->save();
                        }
                    }
                } elseIf($usuario->mainRole->name=='operador') {
                    $recepcion->estado_id = $estado_en_progreso_id; //Validación de <copia operador>
                    $recepcion->save();
                    foreach ($recepcion->solicitud->tareas as $tarea) { //Autoasignación de tareas
                        $coincide = $usuario->tareas()->where('tareas.id', $tarea->id)->first();
                        if($coincide) {
                            $actividad                      = new Actividad();
                            $actividad->id                  = (new KeyMaker())->generate('Actividad', $recepcion->solicitud_id);
                            $actividad->recepcion_id        = $recepcion->id;
                            $actividad->tarea_id            = $tarea->id;
                            $actividad->estado_id           = $estado_en_progreso_id;
                            $actividad->save();
                        }
                    }
                }
                $atencion->estado_id = $estado_en_progreso_id; //Cambiando estado global
                $atencion->save();
            //RESULTADO
            DB::commit();
            $traza = $this->obtenerTraza($recepcion); // Obtener la traza actualizada
            return response()->json([
                'success' => true,
                'message' => 'La solicitud "' . (new KeyRipper())->rip($atencion->id) . '" ha sido asignada al operador',
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
                    if ($user->mainRole && $user->mainRole->name === 'cliente') {
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
                    'avance'      => optional($tarjeta->atencion)->avance ?? 0, 
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
                    'actividad_id'        => $actividad->id,
                    'actividad_id_ripped' => KeyRipper::rip($actividad->id),
                ];
            });
            return response()->json(['tareas' => $tareas]);
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al obtener las tareas: ' . $e->getMessage());
        }
    }

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
        $parametro->activo = ! $parametro->activo; 
        $parametro->save();
        return redirect()->route('recepcion.parametros')->with('success', 'Parámetro actualizado correctamente');
    }


    public function revisarStock(Request $request)
    {
        try {
            // LECTURA DE DATOS
            $atencion_id = $request->input('atencion_id');
            $recepcion_id = $request->input('recepcion_id');
            $orden = $request->input('lote_stock', []);
            // VALIDACIÓN
            if (empty($atencion_id) || empty($orden)) { //Ordenes e items no vacíos
                return response()->json([
                    'success' => false,
                    'message' => 'Información incompleta para la validación del lote'
                ], 422);
            }
            $detallesRequeridos = Detalle::whereHas('orden', function($q) use ($atencion_id) { //Confirmaciones
                $q->where('atencion_id', $atencion_id);
            })->get(['orden_id', 'kit_id', 'producto_id']);
            $clavesRequeridas = $detallesRequeridos->map(function($item) {
                return "{$item->orden_id}-{$item->kit_id}-{$item->producto_id}";
            })->toArray();
            $clavesRecibidas = [];
            foreach ($orden as $item) {
                 if (isset($item['orden_id'], $item['kit_id'], $item['producto_id'])) {
                     $clavesRecibidas[] = "{$item['orden_id']}-{$item['kit_id']}-{$item['producto_id']}";
                 }
            }
            $faltantes = array_diff($clavesRequeridas, $clavesRecibidas);
            if (!empty($faltantes)) {
                Log::warning("Log:: Intento de confirmación incompleta para atención $atencion_id. Faltan items.");
                return response()->json([
                    'success' => false,
                    'message' => 'Error: El lote enviado no contiene todos los ítems de la solicitud.'
                ], 422);
            }
            foreach ($orden as $item) {
                if (!isset($item['stock_fisico_existencias']) || !in_array($item['stock_fisico_existencias'], ['1', '0'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error: Hay ítems sin una confirmación de stock válida.'
                    ], 422);
                }
            }
            //PROCESO
            DB::beginTransaction();
                $itemsValidados = [];
                foreach ($orden as $item) {
                    Detalle::where('orden_id', $item['orden_id'])
                        ->where('kit_id', $item['kit_id'])
                        ->where('producto_id', $item['producto_id'])
                        ->update(['stock_fisico_existencias' => $item['stock_fisico_existencias']]);
                    $itemsValidados[] = [
                        'orden_id' => $item['orden_id'],
                        'kit_id' => $item['kit_id'],
                        'producto_id' => $item['producto_id'],
                        'stock_existencias' => $item['stock_fisico_existencias']
                    ];
                }
                $recepcion = Recepcion::find($recepcion_id); // Validar copia operador
                if ($recepcion) {
                    $recepcion->validada_origen = true;
                    $recepcion->validada_destino = true;
                    $recepcion->save();
                }
            $this->reportarTarea('Stock revisado', $recepcion_id, $atencion_id); //Reportar tarea
            DB::commit();
            try {
                if ($recepcion) {
                    $oficina_id = auth()->user()->oficina_id;
                    $receptores = User::where('oficina_id', $oficina_id)
                        ->whereHas('roles', function($q) {
                            $q->where('name', 'receptor');
                        })
                        ->get();
                    if ($receptores->isNotEmpty()) {
                        Notification::send($receptores, new StockRevisadoNotification($recepcion, $itemsValidados));
                    }
                }
            } catch (\Exception $e) {
                Log::error("Log:: Error al enviar notificación StockRevisadoNotification: " . $e->getMessage());
            }
            //RESULTADO
            return response()->json([
                'success' => true,
                'message' => 'Stock revisado correctamente',
                'items_validados' => $itemsValidados
            ]);
        } catch (\Exception $e) {
            Log::error("Log:: error en confirmarStock (batch): " . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al validar el lote de stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function corregirOrden(Request $request)
    {
        try {
            // LECTURA
            $atencion_id = $request->input('atencion_id');
            $recepcion_id = $request->input('recepcion_id'); // Añadido recepcion_id
            $ordenes_recibidas = $request->input('ordenes', []);
            // VALIDACIÓN
            if (empty($atencion_id) || empty($recepcion_id) || empty($ordenes_recibidas)) { 
                return response()->json([
                    'success' => false,
                    'message' => 'Información incompleta para la corrección'
                ], 422);
            }
            // PROCESAMIENTO
            $productos_cambiados = [];
            DB::beginTransaction();
                foreach ($ordenes_recibidas as $ordenData) {
                    $orden_id = $ordenData['orden_id'];
                    $unidades = $ordenData['unidades'];
                    $detalles = $ordenData['detalles'] ?? [];
                    Orden::where('id', $orden_id)->update(['unidades' => $unidades]);
                    foreach ($detalles as $detalleData) {
                        $kit_id = $detalleData['kit_id'];
                        $producto_id_original = $detalleData['producto_id_original'];
                        $producto_id_nuevo = $detalleData['producto_id_nuevo'];
                        if ($producto_id_original != $producto_id_nuevo) {
                            Detalle::where('orden_id', $orden_id)
                                ->where('kit_id', $kit_id)
                                ->where('producto_id', $producto_id_original)
                                ->update([
                                    'producto_id' => $producto_id_nuevo,
                                    'stock_fisico_existencias' => null
                                ]);
                            $productos_cambiados[] = [ //Registrando productos que cambiaron de la orden
                                'orden_id' => $orden_id,
                                'kit_id' => $kit_id,
                                'producto_id' => $producto_id_nuevo
                            ];
                        } else {
                            // Si no cambió el producto, igual reseteamos existencias según requerimiento
                            Detalle::where('orden_id', $orden_id)
                                ->where('kit_id', $kit_id)
                                ->where('producto_id', $producto_id_original)
                                ->update([
                                    'stock_fisico_existencias' => null
                                ]);
                        }
                    }
                }

                // REVERTIR TAREA "Stock revisado" (Y opcionalmente "Orden Revisada" si se requiere, pero enfocado en Stock)
                $estado_en_progreso_id = Estado::where('estado', 'En progreso')->first()->id;
                $actividadStock = Actividad::whereHas('recepcion', function($q) use ($atencion_id) {
                        $q->where('atencion_id', $atencion_id);
                    })
                    ->whereHas('tarea', function($q) {
                        $q->where('tarea', 'Stock revisado');
                    })
                    ->first();

                if ($actividadStock) {
                    $actividadStock->estado_id = $estado_en_progreso_id;
                    $actividadStock->save();
                    Log::info("Log:: Tarea 'Stock revisado' revertida a 'En progreso' para atención $atencion_id");

                    // Forzar actualización de progreso de la atención (restando esta tarea)
                    $total_actividades = Actividad::whereHas('recepcion', function($query) use ($atencion_id) {
                        $query->where('atencion_id', $atencion_id);
                    })->count();
                    
                    $actividades_resueltas = Actividad::whereHas('recepcion', function($query) use ($atencion_id) {
                        $query->where('atencion_id', $atencion_id);
                    })
                    ->where('estado_id', Estado::where('estado', 'Resuelta')->first()->id)
                    ->count();

                    $porcentaje_avance = $total_actividades > 0 
                        ? round(($actividades_resueltas / $total_actividades) * 100, 2) 
                        : 0;

                    $atencion = Atencion::find($atencion_id);
                    if ($atencion) {
                        $atencion->avance = $porcentaje_avance;
                        // Si retrocede del 100%, bajamos el estado a "En progreso" si estaba resuelta
                        if ($porcentaje_avance < 100) {
                            $atencion->estado_id = $estado_en_progreso_id;
                            Recepcion::where('atencion_id', $atencion_id)->update(['estado_id' => $estado_en_progreso_id]);
                        }
                        $atencion->save();
                    }
                }
            DB::commit();
            //RESULTADO
            return response()->json([
                'success' => true,
                'message' => 'La orden ha sido corregida',
                'productos_cambiados' => $productos_cambiados,
                'count' => count($ordenes_recibidas)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Log:: error en corregirOrden: " . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al corregir la orden: ' . $e->getMessage()
            ], 500);
        }
    }

    public function revisarOrden(Request $request)
    {
        try {
            // LECTURA
            $atencion_id = $request->input('atencion_id');
            $recepcion_id = $request->input('recepcion_id');
            $ordenes_recibidas = $request->input('ordenes', []);
            // VALIDACIÓN
            if (empty($atencion_id) || empty($ordenes_recibidas)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Información incompleta para la revisión'
                ], 422);
            }
            // PROCESAMIENTO
            $detalles = Detalle::whereHas('orden', function($q) use ($atencion_id) {
                $q->where('atencion_id', $atencion_id);
            })->get();
            if ($detalles->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron productos asociados a esta solicitud para revisar.'
                ], 422);
            }
            foreach ($detalles as $detalle) {
                if ($detalle->stock_fisico_existencias === null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Hay productos pendientes de revisión física. Por favor, espere a que el operador complete la revisión.'
                    ], 422);
                }
                
                if ($detalle->stock_fisico_existencias == "0") {
                    return response()->json([
                        'success' => false,
                        'message' => "El producto {$detalle->producto_id} - {$detalle->producto->producto} no tiene existencias físicas. No se puede revisar la orden."
                    ], 422);
                }
            }
            DB::beginTransaction();
                $recepcion = Recepcion::find($recepcion_id);
                if ($recepcion) {
                    $recepcion->validada_destino = true;
                    $recepcion->save();
                }
                $this->reportarTarea('Orden Revisada', $recepcion_id, $atencion_id); //Reportar tarea
            DB::commit();
            $recepcion->load('atencion.ordenes.detalle.kit', 'atencion.ordenes.detalle.producto');
            $recepcion->usuarioOrigen->notify(new \App\Notifications\OrdenRevisadaNotification($recepcion));
            //RESULTADO
            return response()->json([
                'success' => true,
                'message' => 'Orden revisada correctamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Log:: error en revisarOrden: " . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error al revisar la orden: ' . $e->getMessage()
            ], 500);
        }
    }

    public function confirmarPago(Request $request)
    {
        $recepcion_id = $request->input('recepcion_id');
        $atencion_id = $request->input('atencion_id');
        $this->reportarTarea('Pago efectuado', $recepcion_id, $atencion_id);
        return response()->json([
            'success' => true,
            'message' => 'Pago confirmado correctamente'
        ]);
    }

    public function descargarStock(Request $request)
    {
        DB::beginTransaction();
        try {
            //Descargando Stock
            $recepcion_id = $request->input('recepcion_id');
            $atencion_id = $request->input('atencion_id');
            $oficina_id = auth()->user()->oficina_id;
            $stockBodegaId = \App\Models\Stock::where('stock', 'Bodega')->value('id');
            if (!$stockBodegaId) {
                throw new \Exception("No se encontró el stock 'Bodega' configurado en el sistema.");
            }
            $recepcion = Recepcion::with(['atencion.ordenes.detalle'])->find($recepcion_id);
            if (!$recepcion || !$recepcion->atencion) {
                throw new \Exception("No se pudo localizar la atención asociada a la recepción.");
            }
            foreach ($recepcion->atencion->ordenes as $orden) {
                $cantidadKits = $orden->unidades;
                foreach ($orden->detalle as $detalle) {
                    $productoId = $detalle->producto_id;
                    $unidadesPorKit = $detalle->unidades;
                    $totalDescontar = $cantidadKits * $unidadesPorKit;
                    $stockItem = \App\Models\OficinaStock::where('oficina_id', $oficina_id)
                        ->where('producto_id', $productoId)
                        ->where('stock_id', $stockBodegaId)
                        ->first();
                    if (!$stockItem) {
                        throw new \Exception("No existe registro de stock en Bodega para el producto con ID {$productoId} en esta oficina.");
                    }
                    if ($stockItem->unidades < $totalDescontar) {
                        throw new \Exception("Stock insuficiente en Bodega para el producto con ID {$productoId}. Requerido: {$totalDescontar}, Disponible: {$stockItem->unidades}");
                    }
                    $stockItem->decrement('unidades', $totalDescontar);
                }
            }
            // Reportando Tarea
            $this->reportarTarea('Stock descargado', $recepcion_id, $atencion_id);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Stock descargado del inventario de Bodega correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al descargar stock: ' . $e->getMessage()]);
        }
    }

    public function efectuarEntrega(Request $request)
    {
        $recepcion_id = $request->input('recepcion_id');
        $atencion_id = $request->input('atencion_id');
        $this->reportarTarea('Entrega efectuada', $recepcion_id, $atencion_id);
        return response()->json([
            'success' => true,
            'message' => 'Entrega efectuada correctamente'
        ]);
    }

    private function reportarTarea($nombre_tarea, $recepcion_id, $atencion_id)
    {
        $estado_resuelta_id = Estado::where('estado', 'Resuelta')->first()->id;
        $actividad = Actividad::where('recepcion_id', $recepcion_id)
            ->whereHas('tarea', function($q) use ($nombre_tarea) {
                $q->where('tarea', $nombre_tarea);
            })
            ->first();
        if ($actividad) {
            $actividad->estado_id = $estado_resuelta_id;
            $actividad->save();
            $total_actividades_globales = Actividad::whereHas('recepcion', function($query) use ($atencion_id) {
                $query->where('atencion_id', $atencion_id);
            })->count();
            $actividades_resueltas_globales = Actividad::whereHas('recepcion', function($query) use ($atencion_id) {
                $query->where('atencion_id', $atencion_id);
            })
            ->where('estado_id', $estado_resuelta_id)
            ->count();
            $porcentaje_avance = $total_actividades_globales > 0 
                ? round(($actividades_resueltas_globales / $total_actividades_globales) * 100, 2) 
                : 0;
            $atencion = Atencion::find($atencion_id);
            if ($atencion) {
                $atencion->avance = $porcentaje_avance;
                $atencion->save();
            }
            if ($porcentaje_avance >= 100) {
                Recepcion::where('atencion_id', $atencion_id)
                    ->update(['estado_id' => $estado_resuelta_id]);
                $atencion->estado_id = $estado_resuelta_id;
                $atencion->save();
            }
        }
    }

    private function obtenerTraza($tarjeta)
    {
        $estado_resuelta_id = Estado::where('estado', 'Resuelta')->first()->id;
        $actividades = Actividad::whereHas('recepcion', function ($query) use ($tarjeta) {
            $query->where('atencion_id', $tarjeta->atencion_id);
        })
        ->with(['tarea', 'estado'])
        ->get()
        ->sortByDesc('updated_at');
        if ($actividades->isEmpty()) {
            $traza = 'Solicitada';
        } else {
            $todasResueltas = $actividades->every(function ($actividad) use ($estado_resuelta_id) {
                return $actividad->estado_id == $estado_resuelta_id;
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

    public function ordenCompra(Request $request)
    {
        try {
            $recepcion = Recepcion::with(['atencion.ordenes.kit', 'usuarioOrigen'])
                ->findOrFail($request->input('recepcion_id'));
            $ordenes = $recepcion->atencion->ordenes->map(function ($orden) {
                return [
                    'kit'      => $orden->kit->kit,
                    'unidades' => $orden->unidades,
                    'precio'   => $orden->precio,
                ];
            });
            return response()->json([
                'success'    => true,
                'cliente'    => $recepcion->usuarioOrigen->name,
                'atencion_id_ripped' => KeyRipper::rip($recepcion->atencion_id),
                'ordenes'    => $ordenes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la orden de compra: ' . $e->getMessage()
            ], 500);
        }
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




}

