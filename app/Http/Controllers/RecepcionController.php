<?php
namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StockRevisadoNotification;
use App\Notifications\OrdenValidadaNotification;
use Illuminate\Support\Facades\Validator;
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
use App\Models\Stock;
use App\Models\OficinaStock;
use App\Models\Movimiento;
use App\Models\Producto;
use App\Services\KeyMaker;
use App\Services\KeyRipper;
use Spatie\Permission\Models\Role;
use App\Services\GestionService;
use App\Services\StockService;


use App\Models\Tarea;

class RecepcionController extends Controller
{
    public function carritoEditar(Request $request)
    {
        $atencion = Atencion::find($request->atencion_id);
        $oficinaId = auth()->user()->oficina_id;
        $stockBodegaId = Stock::where('stock', 'Bodega')->first()->id;
        $atencion->load([
            'ordenes.kit',
            'ordenes.detalle' => function ($query1) {
                $query1->orderBy('created_at');
            },
            'ordenes.detalle.producto.oficinaStock' => function($query2) use ($oficinaId, $stockBodegaId){
                $query2->where('stock_id', $stockBodegaId)->where('oficina_id', $oficinaId);
            },
            'ordenes.detalle.producto.kitProductos.equivalentes.producto.oficinaStock'=> function($query3) use ($oficinaId, $stockBodegaId){
                $query3->where('stock_id', $stockBodegaId)->where('oficina_id', $oficinaId);
            }
        ]);
        $atencion_id_ripped = KeyRipper::rip($atencion->id);
        $uso_interno = (int) $request->input('uso_interno', Parametro::where('parametro', 'Uso interno')->first()->valor);
        $recepcion_id = $request->recepcion_id; // Recuperación de recepción (Seguridad para el auto-abierto del sidebar)
        if (!$recepcion_id) {
            $recepcion_id = Recepcion::where('atencion_id', $atencion->id)
                ->where('destino_user_id', auth()->id())
                ->where('activo', true)
                ->value('id');
        }
        return view('modelos.kit.carrito', [
            'atencion' => collect([$atencion]),
            'atencion_id_ripped' => $atencion_id_ripped,
            'recepcion_id' => $recepcion_id,
            'uso_interno' => $uso_interno
        ]);
    }



    public function nuevasRecibidas(Request $request, GestionService $gestionService)
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
                ->where('estado_id', \Illuminate\Support\Facades\Cache::remember('estado_recibida_id', 3600, fn() => Estado::where('estado', 'Recibida')->first()->id))
                ->where('activo', true)
                ->orderBy('atencion_id')
                ->take(5)
                ->get();
            $atencionIds = $recepciones->pluck('atencion_id')->unique();
            $usuariosParticipantes = $gestionService->obtenerUsuariosParticipantes($atencionIds); //Obtener usuarios participantes
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
            Log::error('Log:: [Usuario: ' . auth()->user()->name . '] Ocurrió un error al obtener las nuevas solicitudes recibidas: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Error al obtener nuevas solicitudes.'], 500);
        }
    }

    public function asignar(Request $request, Recepcion $recepcion, Equipo $equipo, GestionService $gestionService)
    {
        $operadores = User::whereHas('equipos', function ($q1) use ($equipo) {
            $q1->where('equipo_id', $equipo->id);
        })->whereHas('mainRole', function ($q1) {
            $q1->where('name', 'operador');
        })->with('equipos')->where('activo', true)->get();
        if ($operadores->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No hay operadores disponibles para asignar la solicitud', 'type' => 'error'], 422);
        }
        try {
            //PROCESO
            $operador              = $operadores->random(); //Seleccion del operador
            $usuario               = Auth()->user();
            $estado_en_progreso_id = Estado::where('estado', 'En progreso')->first()->id;
            $atencion              = $recepcion->atencion()->first();
            DB::beginTransaction();
                if ($usuario->mainRole->name=='receptor') { //Creando nueva copia rol
                    $new_recepcion = new Recepcion();
                    $new_recepcion->id = (new KeyMaker())->generate('Recepcion', $recepcion->solicitud_id);
                    $new_recepcion->atencion_id = $atencion->id;
                    $new_recepcion->solicitud_id = $recepcion->solicitud_id;
                    $new_recepcion->origen_user_id = $usuario->id;
                    $new_recepcion->destino_user_id = $operador->id;
                    $new_recepcion->user_destino_role_id = Role::where('name', 'operador')->first()->id;
                    $new_recepcion->estado_id = Estado::where('estado', 'Recibida')->first()->id;
                    $new_recepcion->save();
                    foreach ($recepcion->solicitud->tareas as $tarea) { //Autoasignación de tareas
                        $coincide = $usuario->tareas()->where('tareas.id', $tarea->id)->first();
                        if($coincide) {
                            $actividad             = new Actividad();
                            $actividad->id         = (new KeyMaker())->generate('Actividad', $recepcion->solicitud_id);
                            $actividad->recepcion_id = $recepcion->id;
                            $actividad->tarea_id   = $tarea->id;
                            $actividad->estado_id  = $estado_en_progreso_id;
                            $actividad->save();
                        }
                    }
                } elseIf($usuario->mainRole->name=='operador') {
                    foreach ($recepcion->solicitud->tareas as $tarea) { //Autoasignación de tareas
                        $coincide = $usuario->tareas()->where('tareas.id', $tarea->id)->first();
                        if($coincide) {
                            $actividad             = new Actividad();
                            $actividad->id         = (new KeyMaker())->generate('Actividad', $recepcion->solicitud_id);
                            $actividad->recepcion_id = $recepcion->id;
                            $actividad->tarea_id   = $tarea->id;
                            $actividad->estado_id  = $estado_en_progreso_id;
                            $actividad->save();
                        }
                    }
                }
                $recepcion->estado_id = $estado_en_progreso_id; //Cambio de estado en local (Rol actual)
                $recepcion->save();
                $atencion->estado_id = $estado_en_progreso_id; //Cambiando estado en global
                $atencion->save();
                //RESULTADO
            DB::commit();
            $traza = $gestionService->obtenerTraza($recepcion); // Obtener la traza actualizada
            return response()->json([
                'success' => true,
                'message' => 'La solicitud "' . (new KeyRipper())->rip($atencion->id) . '" ha sido asignada al operador',
                'traza'   => $traza,
                'type'    => 'success'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Log:: [Usuario: ' . auth()->user()->name . '] Ocurrió un error al asignar la solicitud: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al asignar la solicitud.',
                'type'    => 'error'
            ], 500);
        }
    }

    public function avanzarEstado(Recepcion $recepcion, GestionService $gestionService)
    {
        try {
            $atencion = $recepcion->atencion()->first();
            $estado_en_progreso_id = Estado::where('estado', 'En progreso')->first()->id;
            DB::beginTransaction();
                $recepcion->estado_id = $estado_en_progreso_id;
                $recepcion->save();
                $atencion->estado_id = $estado_en_progreso_id;
                $atencion->save();
            DB::commit();
            $traza = $gestionService->obtenerTraza($recepcion);
            return response()->json([
                'success' => true,
                'message' => 'Solicitud en Progreso',
                'traza'   => $traza,
                'type'    => 'success'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Log:: [Usuario: ' . auth()->user()->name . '] Error al avanzar estado: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Error al avanzar la solicitud de estado.',
                'type'    => 'error'
            ], 500);
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
            Log::error('Log:: [Usuario: ' . auth()->user()->name . '] Ocurrió un error al obtener los equipos: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error al obtener los equipos disponibles.');
        }
    }

    public function operadores(Solicitud $solicitud)
    {
        try {
            $operadores = User::whereHas('roles', function ($query) {
                $query->where('name', 'operador');
            })->whereHas('oficina', function ($query) {
                $query->where('id', auth()->user()->oficina_id);
            })->whereHas('solicitudes', function ($query) use ($solicitud) {
                $query->where('solicitudes.id', $solicitud->id);
            })->get();

            $operadores_activos = User::whereHas('roles', function ($query) {
                $query->where('name', 'operador');
            })->where('activo', true)->get();

            return response()->json([
                'operadores'         => $operadores,
                'operadores_activos' => $operadores_activos,
            ]);
        } catch (\Exception $e) {
            Log::error('Log:: [Usuario: ' . auth()->user()->name . '] Ocurrió un error al obtener los operadores: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error al obtener los operadores disponibles.');
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
            Log::error('Log:: [Usuario: ' . auth()->user()->name . '] Ocurrió un error al obtener las tareas: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener el listado de tareas.',
                'type'    => 'error'
            ], 500);
        }
    }

    public function confirmarStock(Request $request)
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
                    'message' => 'Información incompleta para la validación del lote',
                    'type'    => 'warning'
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
                return response()->json([
                    'success' => false,
                    'message' => 'Error: El lote enviado no contiene todos los ítems de la solicitud.',
                    'type'    => 'error'
                ], 422);
            }
            foreach ($orden as $item) {
                if (!isset($item['stock_fisico_existencias']) || !in_array($item['stock_fisico_existencias'], ['1', '0'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error: Hay ítems sin una confirmación de stock válida.',
                        'type'    => 'error'
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
                $tareaConfirmacion = Tarea::where('tarea', 'Confirmación')->first()->tarea;
                app(GestionService::class)->reportarTarea($tareaConfirmacion, $recepcion_id, $atencion_id); //Reportar tarea
            DB::commit();
            try {
                if ($recepcion) {
                    $uso_interno = Parametro::where('parametro', 'Uso interno')->first();
                    $uso_interno = $uso_interno ? $uso_interno->valor : 1;
                    if ($uso_interno == 0) { //Parametrizado: Uso interno
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
                }
            } catch (\Exception $e) {
                Log::error("Log:: [Usuario: " . auth()->user()->name . "] Error al enviar notificación: " . $e->getMessage(), ['exception' => $e]);
            }
            //RESULTADO
            return response()->json([
                'success' => true,
                'message' => ($tareaConfirmacion ?? 'Confirmación') . ' exitosa',
                'items_validados' => $itemsValidados,
                'type'    => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error("Log:: [Usuario: " . auth()->user()->name . "] Error en revisarStock: " . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false, 
                'message' => 'Ocurrió un error al validar el lote de stock.',
                'type'    => 'error'
            ], 500);
        }
    }

    public function corregirOrden(Request $request, StockService $stockService)
    {
        // LECTURA
        $atencion_id = $request->input('atencion_id');
        $recepcion_id = $request->input('recepcion_id');
        $ordenes_recibidas = $request->input('ordenes', []);
        $uso_interno = (int) $request->input('uso_interno', Parametro::where('parametro', 'Uso interno')->first()->valor);
        // VALIDACIÓN
        if (empty($atencion_id) || empty($recepcion_id) || empty($ordenes_recibidas)) { //Intento de inyección
            return response()->json([
                'success' => false,
                'message' => 'Información incompleta para la corrección'
            ], 422);
        }
        $validacionStock = $stockService->validarDisponibilidad($ordenes_recibidas); //Validación de Stock
        if ($validacionStock !== true) {
            return response()->json([
                'success' => false,
                'message' => $validacionStock['message'],
                'fallos'  => $validacionStock['fallos'] ?? [],
                'type'    => 'error'
            ], $validacionStock['status']);
        }
        try {
            // PROCESAMIENTO
            $productos_cambiados = [];
            DB::beginTransaction();
                foreach ($ordenes_recibidas as $ordenData) {
                    $orden_id = $ordenData['orden_id'];
                    $unidades = $ordenData['unidades'];
                    $detalles = $ordenData['detalles'] ?? [];
                    Orden::where('id', $orden_id)->update(['unidades' => $unidades]);
                    if ($uso_interno == 0) { //Parametrizado: Reiniciar la tarea "Confirmación" de la cual se encarga el
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
                                Detalle::where('orden_id', $orden_id) // Si no cambió el producto, igual reseteamos stock_fisico_existencias para que se vuelva a validar el stock
                                ->where('kit_id', $kit_id)
                                ->where('producto_id', $producto_id_original)
                                ->update([
                                    'stock_fisico_existencias' => null
                                ]);
                            }
                        }
                    }
                }
                if ($uso_interno == 0) { //Parametrizado: Uso interno
                    $estado_en_progreso_id = Estado::where('estado', 'En progreso')->first()->id; //Revertir estado de la tarea
                    $actividadStock = Actividad::whereHas('recepcion', function($q) use ($atencion_id) {
                        $q->where('atencion_id', $atencion_id);
                    })->whereHas('tarea', function($q) {
                        $q->where('tarea', 'Confirmación');
                    })->first();
                    if ($actividadStock) {
                        $actividadStock->estado_id = $estado_en_progreso_id;
                        $actividadStock->save();
                        $total_actividades = Actividad::whereHas('recepcion', function($query) use ($atencion_id) { // Forzar actualización de progreso de la atención (restando esta tarea)
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
                            if ($porcentaje_avance < 100) { // Si retrocede del 100%, bajamos el estado a "En progreso" si estaba resuelta
                                $atencion->estado_id = $estado_en_progreso_id;
                                Recepcion::where('atencion_id', $atencion_id)->update(['estado_id' => $estado_en_progreso_id]);
                            }
                            $atencion->save();
                        }
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
            Log::error("Log:: [Usuario: " . auth()->user()->name . "] Error en corregirOrden: " . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false, 
                'message' => 'Error al corregir la orden.'
            ], 500);
        }
    }

    public function revisarOrden(Request $request, StockService $stockService)
    {
        // LECTURA
        $atencion_id = $request->input('atencion_id');
        $recepcion_id = $request->input('recepcion_id');
        $ordenes_recibidas = $request->input('ordenes', []);
        $uso_interno = $request->input('uso_interno', Parametro::where('parametro', 'Uso interno')->first()->valor);
        // VALIDACIÓN
        if (empty($atencion_id) || empty($ordenes_recibidas)) {
            return response()->json([
                'success' => false,
                'message' => 'Información incompleta para la revisión',
                'type'    => 'warning'
            ], 422);
        }
        $validacionStock = $stockService->validarDisponibilidad($ordenes_recibidas); //Validación de Stock
        if ($validacionStock !== true) {
            return response()->json([
                'success' => false,
                'message' => $validacionStock['message'],
                'fallos'  => $validacionStock['fallos'] ?? [],
                'type'    => 'error'
            ], $validacionStock['status']);
        }
        // PROCESAMIENTO
        try {
            $detalles = Detalle::whereHas('orden', function($q) use ($atencion_id) {
                $q->where('atencion_id', $atencion_id);
            })->get();
            if ($detalles->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron productos asociados a esta solicitud para revisar.',
                    'type'    => 'warning'
                ], 422);
            }
            foreach ($detalles as $detalle) {
                if ($uso_interno == 0) { //Parametrizado: Uso interno
                    if ($detalle->stock_fisico_existencias === null) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Hay stocks pendientes de confirmación.',
                            'type'    => 'warning'
                        ], 422);
                    }
                }
                if ($detalle->stock_fisico_existencias == "0") {
                    return response()->json([
                        'success' => false,
                        'message' => "El producto {$detalle->producto_id} - {$detalle->producto->producto} no tiene existencias físicas. No se puede revisar la orden.",
                        'type'    => 'error'
                    ], 422);
                }
            }
            DB::beginTransaction();
                $recepcion = Recepcion::find($recepcion_id);
                if ($recepcion) {
                    $recepcion->validada_destino = true;
                    $recepcion->save();
                }
                $tareaRevision = Tarea::where('tarea', 'Revisión')->first()->tarea;
                app(GestionService::class)->reportarTarea($tareaRevision, $recepcion_id, $atencion_id);
            DB::commit();
            if ($uso_interno == 0) { //Parametrizado: Uso interno
                $recepcion->load('atencion.ordenes.detalle.kit', 'atencion.ordenes.detalle.producto');
                $recepcion->usuarioOrigen->notify(new OrdenValidadaNotification($recepcion));
            }
            return response()->json([
                'success' => true,
                'message' => $tareaRevision.' exitosa',
                'type'    => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Log:: [Usuario: " . auth()->user()->name . "] Ocurrió un error al validar la orden: " . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al validar la orden.',
                'type'    => 'error'
            ], 500);
        }
    }

    public function confirmarPago(Request $request)
    {
        $recepcion_id = $request->input('recepcion_id');
        $atencion_id = $request->input('atencion_id');
        $tareaPago = \App\Models\Tarea::where('tarea', 'Pago')->first()->tarea ?? 'Pago';
        app(GestionService::class)->reportarTarea($tareaPago, $recepcion_id, $atencion_id);
        return response()->json([
            'success' => true,
            'message' => ($tareaPago ?? 'Pago') . ' exitoso',
            'type'    => 'success'
        ]);
    }

    public function descargarStock(Request $request, StockService $stockService)
    {
        //LECTURA
        $recepcion_id = $request->input('recepcion_id');
        $atencion_id = $request->input('atencion_id');
        $recepcion = Recepcion::with(['atencion.ordenes.detalle'])->find($recepcion_id); 

        //VALIDACION
        if (!$recepcion || !$recepcion->atencion) { //Antifiltración
            return response()->json([
                'success' => false,
                'message' => 'No se pudo localizar la atención asociada a la recepción.',
                'type'    => 'error'
            ], 422);
        }
        $ordenes_recibidas = $recepcion->atencion->ordenes->map(function($o) {
            return [
                'orden_id' => $o->id,
                'unidades' => $o->unidades
            ];
        })->toArray();
        $validacionStock = $stockService->validarDisponibilidad($ordenes_recibidas); //Regla del stock
        if ($validacionStock !== true) {
            return response()->json([
                'success' => false,
                'message' => $validacionStock['message'],
                'fallos'  => $validacionStock['fallos'] ?? [],
                'type'    => 'error'
            ], $validacionStock['status']);
        }
        //PROCESO
        DB::beginTransaction();
        try {
            $oficina_id = auth()->user()->oficina_id; //Descargando Stock
            $stockBodegaId = Stock::where('stock', 'Bodega')->value('id');
            foreach ($recepcion->atencion->ordenes as $orden) {
                foreach ($orden->detalle as $detalle) {
                    \App\Models\OficinaStock::where([
                        'oficina_id'  => $oficina_id,
                        'producto_id' => $detalle->producto_id,
                        'stock_id'    => $stockBodegaId
                    ])->decrement('unidades', $orden->unidades * $detalle->unidades);
                }
            }
            $tareaDescarga = Tarea::where('tarea', 'Descarga')->first()->tarea; // Reportando Tarea
            app(GestionService::class)->reportarTarea($tareaDescarga, $recepcion_id, $atencion_id);
            DB::commit(); //Resultado
            return response()->json([
                'success' => true,
                'message' => ($tareaDescarga ?? 'Descarga') . ' exitosa',
                'type'    => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Log:: [Usuario: ' . auth()->user()->name . '] Error al descargar stock: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al intentar descargar el stock del inventario.',
                'type'    => 'error'
            ], 500);
        }
    }

    public function efectuarEntrega(Request $request)
    {
        $recepcion_id = $request->input('recepcion_id');
        $atencion_id = $request->input('atencion_id');
        $tareaEntrega = \App\Models\Tarea::where('tarea', 'Entrega')->first()->tarea ?? 'Entrega';
        app(GestionService::class)->reportarTarea($tareaEntrega, $recepcion_id, $atencion_id);
        return response()->json([
            'success' => true,
            'message' => ($tareaEntrega ?? 'Entrega') . ' exitosa',
            'type'    => 'success'
        ]);
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
            Log::error('Log:: [Usuario: ' . auth()->user()->name . '] Error al obtener la orden de compra: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener la información de la orden.',
                'type'    => 'error'
            ], 500);
        }
    }

    public function historialTransacciones()
    {
        $productos = Producto::where('activo', true)->orderBy('producto', 'asc')->get();
        return view('reportes.historial-transacciones', compact('productos'));
    }

    public function lecturaTransacciones(Request $request)
    {
        // VALIDACIÓN
        $validator = Validator::make($request->all(), [
            'producto_id' => 'required|integer|min:1|exists:productos,id',
            'fecha'       => 'required|date|after_or_equal:2026-01-01|before_or_equal:2035-01-01',
        ], [
            'producto_id.required'         => 'El producto es obligatorio.',
            'producto_id.integer'          => 'El producto debe ser un valor entero.',
            'producto_id.min'              => 'El producto no puede ser cero ni negativo.',
            'producto_id.exists'           => 'El producto seleccionado no existe.',
            'fecha.required'               => 'La fecha es obligatoria.',
            'fecha.date'                   => 'La fecha no tiene un formato válido.',
            'fecha.after_or_equal'         => 'La fecha debe ser igual o posterior al 01/01/2024.',
            'fecha.before_or_equal'        => 'La fecha debe ser igual o anterior al 01/01/2026.',
        ]);
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Los parámetros de búsqueda no son válidos.',
                    'errors'  => $validator->errors(),
                    'type'    => 'warning',
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $salidas = Detalle::select(['orden_id', 'unidades', 'created_at'])
        ->with(['orden.atencion'])
        ->whereHas('orden.atencion', function ($query) {
            $query->where('activo', true)
                ->where('oficina_id', auth()->user()->oficina_id)
                ->whereHas('estado', function ($q) {
                    $q->where('estado', 'Resuelta');
                });
        })
        ->where('producto_id', $request->producto_id)
        ->whereDate('created_at', '>=', $request->fecha)
        ->get();

        $stockBodegaId = Stock::where('stock', 'Bodega')->value('id');
        $entradas = Movimiento::select(['unidades', 'created_at'])
            ->where('activo', true)
            ->whereDate('created_at', '>=', $request->fecha)
            ->where('oficina_id', auth()->user()->oficina_id)
            ->where('producto_id', $request->producto_id)
            ->where('destino_stock_id', $stockBodegaId)
            ->get();

        $coleccionSalidas = $salidas->map(function ($item) {
            return (object) [
                'tipo'             => 'salida',
                'unidades'         => $item->unidades,
                'created_at'       => $item->created_at,
                'stock_resultante' => null,
            ];
        });

        $coleccionEntradas = $entradas->map(function ($item) {
            return (object) [
                'tipo'             => 'entrada',
                'unidades'         => $item->unidades,
                'created_at'       => $item->created_at,
                'stock_resultante' => null,
            ];
        });

        $transacciones = $coleccionSalidas->concat($coleccionEntradas)
            ->sortByDesc('created_at')
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Campos validados correctamente. Listo para fase 2.',
            'data_recibida' => [
                'producto_id' => $request->producto_id,
                'fecha' => $request->fecha
            ]
        ]);
    }

    public function createStock()
    {
        $stocks = Stock::where('activo', true)->get();
        $productos = Producto::where('activo', true)->with('modelo', 'tipo')->get();
        return view('modelos.producto.stock', compact('productos', 'stocks'));
    }

    public function storeStock(Request $request)
    {
        //PREESTABLECIMIENTOS
        $request->merge([ //Limpiando máscara de entrada
            'unidades' => preg_replace('/[\s,]/', '', (string) $request->input('unidades')),
        ]);
        //VALIDACIÓN
        try {
            $validated = $request->validate([
                'origen_stock_id'  => 'required|integer|different:destino_stock_id|exists:stocks,id',
                'destino_stock_id' => 'required|integer|different:origen_stock_id|exists:stocks,id',
                'producto_id'      => 'required|integer|exists:productos,id',
                'unidades'         => 'required|integer|min:1',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Log:: [Usuario: ' . auth()->user()->name . '] Error en la validación de stock: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'La información proporcionada no es válida.');
        }
        if ($validated['origen_stock_id'] == 1 && $validated['destino_stock_id'] == 5) { //Compras
            $origenStockUnidades = 0;
            $destinoStockUnidades = $validated['unidades'];
        } else if ($validated['origen_stock_id'] == 5 && $validated['destino_stock_id'] == 1) { //Devoluciones
            $origenStockUnidades = $validated['unidades'];
            $destinoStockUnidades = 0;
        } else { //Movimientos
            $origenStockUnidades = $validated['unidades'];
            $destinoStockUnidades = $validated['unidades'];
        }
        $oficinaStockOrigen = OficinaStock::where('oficina_id', auth()->user()->oficina_id) //Rebasamiento
            ->where('stock_id', $validated['origen_stock_id'])
            ->where('producto_id', $validated['producto_id'])
            ->with('stock')
            ->first();
        $oficinaStockDestino = OficinaStock::where('oficina_id', auth()->user()->oficina_id)
            ->where('stock_id', $validated['destino_stock_id'])
            ->where('producto_id', $validated['producto_id'])
            ->with('stock')
            ->first();
        $stockOrigen = Stock::find($validated['origen_stock_id']); // Cargar los stocks para obtener sus nombres
        $stockDestino = Stock::find($validated['destino_stock_id']);
        if (!$stockOrigen) { // Verificar que los stocks existan (condición de carrera)
            return back()->with('error', 'El stock de origen seleccionado ya no existe. Por favor, recarga la página.');
        }
        if (!$stockDestino) {
            return back()->with('error', 'El stock de destino seleccionado ya no existe. Por favor, recarga la página.');
        }
        if ($oficinaStockOrigen && $oficinaStockOrigen->stock && $oficinaStockOrigen->stock->id != 1 && $validated['unidades'] > $oficinaStockOrigen->unidades) {
            return back()->with(
                'error',
                'No hay suficientes unidades en ' . $oficinaStockOrigen->stock->stock .
                    '. Cantidad disponible: ' . $oficinaStockOrigen->unidades
            );
        }
        //PROCESOS
        $stock_bodega_id = Stock::where('stock', 'Bodega')->value('id');
        $stock_actual = OficinaStock::where('oficina_id', auth()->user()->oficina_id)
            ->where('stock_id', $stock_bodega_id)
            ->where('producto_id', $validated['producto_id'])
            ->value('unidades') ?? 0;
        
        

        try {
            DB::beginTransaction();
                if (!$oficinaStockOrigen) { //Stock origen
                    $oficinaStockOrigen = new OficinaStock();
                    $oficinaStockOrigen->oficina_id = auth()->user()->oficina_id;
                    $oficinaStockOrigen->stock_id = $validated['origen_stock_id'];
                    $oficinaStockOrigen->producto_id = $validated['producto_id'];
                    $oficinaStockOrigen->unidades = $origenStockUnidades;
                    $oficinaStockOrigen->save();
                    $oficinaStockOrigen->load('stock'); // Cargar la relación stock después de guardar
                } else {
                    $oficinaStockOrigen->unidades -= $origenStockUnidades;
                    $oficinaStockOrigen->save();
                }
                if (!$oficinaStockDestino) { //Stock destino
                    $oficinaStockDestino = new OficinaStock();
                    $oficinaStockDestino->oficina_id = auth()->user()->oficina_id;
                    $oficinaStockDestino->stock_id = $validated['destino_stock_id'];
                    $oficinaStockDestino->producto_id = $validated['producto_id'];
                    $oficinaStockDestino->unidades = $destinoStockUnidades;
                    $oficinaStockDestino->save();
                    $oficinaStockDestino->load('stock'); // Cargar la relación stock después de guardar
                } else {
                    $oficinaStockDestino->unidades += $destinoStockUnidades;
                    $oficinaStockDestino->save();
                }
                $orientacion = $validated['origen_stock_id'] . auth()->user()->oficina_id . $validated['destino_stock_id'];
                $movimiento = new Movimiento(); //Movimiento
                $movimiento->id = app(KeyMaker::class)->generate('Movimiento', $orientacion);
                $movimiento->user_id = auth()->id();
                $movimiento->oficina_id = auth()->user()->oficina_id;
                $movimiento->origen_stock_id = $validated['origen_stock_id'];
                $movimiento->destino_stock_id = $validated['destino_stock_id'];
                $movimiento->producto_id = $validated['producto_id'];
                $movimiento->movimiento = $stockOrigen->stock . ' -> ' . $stockDestino->stock; // Usar los stocks cargados directamente (verificados anteriormente)
                $movimiento->unidades = $validated['unidades'];
                if($stockOrigen->id == $stock_bodega_id){ //Stock resultante
                    $movimiento->stock_resultante = $oficinaStockOrigen->unidades;
                }
                if($stockDestino->id == $stock_bodega_id){
                    $movimiento->stock_resultante = $oficinaStockDestino->unidades;
                }
                $movimiento->save();
            DB::commit();
            return back()->with('success', 'Movimiento ' . $movimiento->movimiento . ' efectuado correctamente');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('Log:: [Usuario: ' . auth()->user()->name . '] Error de base de datos en storeStock: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error al procesar el movimiento en la base de datos.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Log:: [Usuario: ' . auth()->user()->name . '] Error en storeStock (transacción): ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error al efectuar el movimiento de stock.');
        }
    }
}


