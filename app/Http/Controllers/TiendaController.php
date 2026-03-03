<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use Exception;
use App\Services\GestionService;


use App\Models\Stock;
use App\Models\OficinaStock;
use App\Models\Movimiento;
use App\Services\KeyMaker;
use App\Models\Producto;
use App\Models\Kit;
use App\Models\Recepcion;
use App\Models\User;
use App\Models\Atencion;
use App\Models\Estado;
use App\Models\Role;
use App\Models\Solicitud;
use App\Models\Orden;
use App\Models\Detalle;
use App\Models\Parametro;
use App\Models\Equipo;
use App\Services\KeyRipper;







use Illuminate\Support\Facades\Log;





class TiendaController extends Controller
{
    public function index()
    {
        $oficinaId = auth()->user()->oficina_id;
        $stockBodegaId = Stock::where('stock', 'Bodega')->first()->id;

        $kits = Kit::where('activo', true)
        ->with(['productos.oficinaStock' => function ($query) use ($oficinaId, $stockBodegaId) {
            $query->where('oficina_id', $oficinaId)
                  ->where('stock_id', $stockBodegaId);
        }])
        ->get();
        foreach ($kits as $kit) {
            $kit->disponible = true;
            foreach ($kit->productos as $producto) {
                $stock = $producto->oficinaStock->first();
                if (!$stock || $stock->unidades <= 0) {
                    $kit->disponible = false;
                    break;
                }
            }
        }
        return view('modelos.kit.tienda', compact('kits'));
    }

    public function carritoIndex()
    {
        $atencion = Atencion::where('activo', false)
        ->where('oficina_id', auth()->user()->oficina_id)
        ->whereHas('recepciones', function ($query) {
            $query->where('origen_user_id', auth()->user()->id);
        })
        ->with([
            'ordenes.kit',
            'ordenes.detalle' => function ($query) {
                $query->orderBy('created_at');
            },
            'ordenes.detalle.producto.kitProductos.equivalentes.producto' // Equivalentes
        ])
        ->get();
        $atencion_id_ripped = null;
        if (!$atencion->isEmpty()) {
            $atencion_id_ripped = KeyRipper::rip($atencion->first()->id); //cuando es nuevo no tiene id, no hay productos en el carrito
        }
        return view('modelos.kit.carrito', [
            'atencion' => $atencion,
            'atencion_id_ripped' => $atencion_id_ripped
        ]);
    }

    public function carritoEnviar(Request $request)
    {
        try {
            DB::beginTransaction();
            //ORDEN DE COMPRA
            $atencionId = null;
            $cart = $request->input('cart');
            if (isset($cart['ordenes'])) {
                foreach ($cart['ordenes'] as $ordenData) {
                    $ordenId = $ordenData['orden_id'];
                    $atencionId = $ordenData['atencion_id'];
                    $unidades = $ordenData['unidades'];
                    if ($unidades < 1) {
                        throw new Exception("Las unidades deben ser mayores a 0.");
                    }
                    $orden = Orden::with(['kit', 'detalle' => function ($query) {
                        $query->orderBy('producto_id');
                    }])->find($ordenId);
                    if ($orden) {
                        $orden->unidades = $unidades;
                        $orden->save();
                        if (isset($ordenData['detalles']) && is_array($ordenData['detalles'])) {
                            $nuevosProductos = array_values($ordenData['detalles']);
                            $productosIds = []; // Array para validar productos duplicados
                            foreach ($orden->detalle as $index => $detalle) {
                                if (isset($nuevosProductos[$index]['producto_id'])) {
                                    $productoId = $nuevosProductos[$index]['producto_id'];
                                    $esValido = DB::table('kit_producto') // Validar que el producto pertenezca al kit o sea equivalente
                                        ->where('kit_id', $orden->kit_id)
                                        ->where('producto_id', $productoId)
                                        ->exists();
                                    if (!$esValido) {
                                        $esValido = DB::table('equivalentes')
                                            ->where('kit_id', $orden->kit_id)
                                            ->where('producto_id', $productoId)
                                            ->exists();
                                    }
                                    if (!$esValido) {
                                        throw new Exception("El producto seleccionado no es válido para este kit.");
                                    }
                                    if (in_array($productoId, $productosIds)) { // Validar que no haya productos duplicados
                                        throw new Exception("Hay productos repetidos en el kit ".mb_strtoupper($orden->kit->kit).". Por favor revise su selección.");
                                    }
                                    $productosIds[] = $productoId;
                                }
                            }
                            $cambios = []; // FASE 2: Recolección
                            foreach ($orden->detalle as $index => $detalle) {
                                if (isset($nuevosProductos[$index]['producto_id'])) {
                                    $nuevoProductoId = $nuevosProductos[$index]['producto_id'];
                                    if ($detalle->producto_id != $nuevoProductoId) {
                                        $cambios[] = [
                                            'detalle_anterior' => $detalle,
                                            'nuevo_producto_id' => $nuevoProductoId
                                        ];
                                    }
                                }
                            }
                            foreach ($cambios as $cambio) { // FASE 3: Eliminación
                                DB::table('detalles')
                                    ->where('orden_id', $orden->id)
                                    ->where('kit_id', $orden->kit_id)
                                    ->where('producto_id', $cambio['detalle_anterior']->producto_id)
                                    ->delete();
                            }
                            foreach ($cambios as $cambio) { // FASE 4: Inserción
                                $detalleAnterior = $cambio['detalle_anterior'];
                                DB::table('detalles')->insert([
                                    'orden_id' => $detalleAnterior->orden_id,
                                    'producto_id' => $cambio['nuevo_producto_id'],
                                    'producto_id_original' => $detalleAnterior->producto_id_original ?? $detalleAnterior->producto_id, // Preservar el original
                                    'kit_id' => $detalleAnterior->kit_id,
                                    'unidades' => $detalleAnterior->unidades,
                                    'precio' => $detalleAnterior->precio,
                                    'created_at' => $detalleAnterior->created_at,
                                    'updated_at' => now()
                                ]);
                            }
                        }
                    }
                }
            }
            //ACTIVACIÓN DE LA ORDEN DE COMPRAS Y LA COPIA DEL RECEPTOR
            if ($atencionId) {
                $atencion = Atencion::find($atencionId);
                if ($atencion) {
                    $atencion->activo = true;
                    $atencion->estado_id = Estado::where('estado', 'Recibida')->first()->id;
                    $atencion->save();
                    $recepcion = Recepcion::where('atencion_id', $atencion->id) //Activar la copia del receptor
                    ->where('user_destino_role_id', Role::where('name','receptor')->first()->id)
                    ->first(); 
                    if ($recepcion) {
                        $recepcion->activo = true;
                        $recepcion->validada_origen = true;
                        $recepcion->estado_id = Estado::where('estado', 'Recibida')->first()->id;
                        $recepcion->save();
                    }
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Su orden ha sido recibida por nuestros gestores']);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Log:: [Usuario: ' . auth()->user()->name . '] Error en carritoEnviar: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Ocurrió un error al procesar la orden.'], 500);
        }
    }

    public function retirarOrden(Orden $orden)
    {
        try {
            DB::beginTransaction();
            $atencion = Atencion::with('ordenes')->find($orden->atencion_id);
            $autorizado = $atencion && $atencion->recepciones()->where('origen_user_id', auth()->user()->id)->exists();
            if(!$autorizado){
                if ($atencion && $atencion->ordenes->count() === 1) {
                    return response()->json(['success' => false, 'message' => 'No se autoriza eliminar el último item de la última orden, ya que se eliminaría completamente la solicitud del cliente']);
                }
            }
            $orden->detalle()->delete();
            $orden->delete();
            if ($atencion && $atencion->ordenes()->count() === 0) {
                foreach ($atencion->recepciones as $recepcion) {
                    $recepcion->actividades()->delete();
                }
                $atencion->recepciones()->delete();
                $atencion->delete();
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Kit retirado del carrito correctamente', 'orden_vacia' => true]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Log:: [Usuario: " . auth()->user()->name . "] Error al retirar orden {$orden->id}: " . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Ocurrió un error al procesar la solicitud.'], 500);
        }
    }
    
    public function agregarOrden(Request $request, Kit $orden)
    {
        try {
            DB::beginTransaction(); //Lectura
                $atencion_nueva = false;
                $orden->load('productos');
                $user = auth()->user();
                $atencion = Atencion::where('oficina_id', $user->oficina_id) //Verificando si ya existe el número de atención
                    ->where('activo', false)
                    ->whereHas('recepciones', function ($query) {
                        $query->where('origen_user_id', auth()->user()->id);
                    })
                    ->where('estado_id', Estado::where('estado', 'En carrito')->first()->id)
                    ->first();
                if (!$atencion) {
                    $atencion_nueva = true;
                    $receptores = User::whereHas('roles', function ($query) { //Seleccionando el receptor
                        $query->where('name', 'receptor');
                    })->whereHas('oficina', function ($query) use ($user) {
                        $query->where('id', $user->oficina_id);
                    })->get();
                    if ($receptores->isEmpty()) {
                        Log::warning('No hay receptores disponibles', ['oficina_id' => $user->oficina_id, 'user_id' => $user->id]);
                        DB::rollBack();
                        $message = 'El sistema está fuera de servicio';
                        return $request->ajax() ? response()->json(['success' => false, 'message' => $message, 'type' => 'error']) : back()->with('error', $message);
                    }
                    $receptor = $receptores->random();
                    $atencion             = new Atencion(); //Creando número de atención
                    $atencion->id         = (new KeyMaker())->generate('Atencion', Solicitud::where('solicitud', 'Orden de compra')->first()->id);
                    $atencion->oficina_id = auth()->user()->oficina_id;
                    $atencion->estado_id  = Estado::where('estado', 'En carrito')->first()->id;
                    $atencion->avance     = 0.00;
                    $atencion->activo     = false;
                    $atencion->save();
                    $recepcion                  = new Recepcion(); //Creando la copia de la orden de compra para el <Receptor>
                    $recepcion->id              = (new KeyMaker())->generate('Recepcion', Solicitud::where('solicitud', 'Orden de compra')->first()->id);
                    $recepcion->atencion_id     = $atencion->id;
                    $recepcion->solicitud_id    = Solicitud::where('solicitud', 'Orden de compra')->first()->id;
                    $recepcion->origen_user_id  = auth()->user()->id;
                    $recepcion->destino_user_id = $receptor->id;
                    $recepcion->user_destino_role_id = Role::where('name', 'receptor')->first()->id;
                    $recepcion->estado_id       = Estado::where('estado', 'En carrito')->first()->id;
                    $recepcion->activo          = false;
                    $recepcion->save();
                }
                if(!$atencion_nueva) { // Verificar si el kit ya está en el carrito
                    $ordenExistente = Orden::where('atencion_id', $atencion->id) 
                        ->where('kit_id', $orden->id)
                        ->first();
                    if ($ordenExistente) {
                        DB::rollBack();
                        $message = 'El kit ya se encuentra en el carrito';
                        return $request->ajax() ? response()->json(['success' => false, 'message' => $message, 'type' => 'info']) : back()->with('info', $message);
                    }
                }
                $nuevaOrden = new Orden(); //Agregando Kit (Orden de compra)
                $nuevaOrden->id = (new KeyMaker())->generate('Orden', Solicitud::where('solicitud', 'Orden de compra')->first()->id);
                $nuevaOrden->atencion_id = $atencion->id;
                $nuevaOrden->kit_id = $orden->id;
                $nuevaOrden->unidades = 1;
                $nuevaOrden->precio = $orden->precio;
                $nuevaOrden->save();
                foreach ($orden->productos as $producto) { //Agregando detalle del kit (Detalle de la orden)
                    $detalle = new Detalle();
                    $detalle->orden_id = $nuevaOrden->id;
                    $detalle->producto_id = $producto->id;
                    $detalle->producto_id_original = $producto->id; // Preservar el producto original del kit
                    $detalle->kit_id = $orden->id;
                    $detalle->unidades = $producto->pivot->unidades;
                    $detalle->precio = $producto->precio;
                    $detalle->save();
                }
            DB::commit();
            $message = 'Kit agregado a la tienda correctamente';
            return $request->ajax() ? response()->json(['success' => true, 'message' => $message, 'type' => 'success']) : back()->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Log:: [Usuario: ' . auth()->user()->name . '] Ocurrió un error cuando se intentaba agregar el kit a la tienda: ' . $e->getMessage(), ['exception' => $e]);
            $message = 'Ocurrió un error cuando se intentaba agregar el kit a la tienda.';
            return $request->ajax() ? response()->json(['success' => false, 'message' => $message, 'type' => 'error']) : back()->with('error', $message);
        }
    }

    public function retirarItem(Request $request)
    {
        $orden = Orden::with('atencion.ordenes')->find($request->orden_id);
        $autorizado = $orden && $orden->atencion && $orden->atencion->recepciones()->where('origen_user_id', auth()->user()->id)->exists();
        if (!$autorizado) {
            if ($orden && $orden->detalle()->count() === 1 && $orden->atencion->ordenes->count() === 1) {
                return response()->json([
                    'success' => false, 
                    'message' => 'No puedes vaciar el carrito completamente. Ya que provocarías una inconsistencia, la solicitud quedaría vacía, sin posibilidades de ser resuelta, por tanto quedaría permanentemente en los tableros de trabajo.'
                ], 422);
            }
        }
        try {
            DB::beginTransaction();
            $eliminado = DB::table('detalles')
                ->where('orden_id', $request->orden_id)
                ->where('kit_id', $request->kit_id)
                ->where('producto_id', $request->producto_id)
                ->delete();
            if ($eliminado) {
                $orden = Orden::with('detalle')->find($request->orden_id);
                $ordenVacia = false;
                $nuevoPrecio = 0;
                $nuevoSubtotal = 0;
                if ($orden) {
                    if ($orden->detalle->count() === 0) {
                        $orden->delete();
                        $ordenVacia = true;
                    } else {
                        $nuevoPrecio = $orden->detalle->sum(function ($det) { // Recalcular el precio de la orden sumando los precios de los detalles
                            return $det->precio; // Asumiendo que el precio en detalle es unitario y las unidades son del producto en el kit
                        });
                        $orden->precio = $nuevoPrecio;
                        $orden->save();
                        $nuevoSubtotal = $nuevoPrecio * $orden->unidades;
                    }
                }
                DB::commit();
                return response()->json([
                    'success' => true, 
                    'message' => 'Se retiró el item',
                    'orden_vacia' => $ordenVacia,
                    'nuevo_precio' => $nuevoPrecio,
                    'nuevo_subtotal' => $nuevoSubtotal
                ]);
            }
            throw new Exception('No se encontró el producto especificado');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Log:: [Usuario: ' . auth()->user()->name . '] Ocurrió un error en retirarItem: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Ocurrió un error al intentar retirar el ítem.'], 500);
        }
    }

    public function getStocksProducto(int $productoId)
    {
        $producto = Producto::with(['oficinaStock' => function ($query) {
            $query->where('oficina_id', auth()->user()->oficina_id)
                ->whereHas('stock', function ($q) {
                    $q->whereNot('stock', 'Proveedor');
                })
                ->with('stock');
        }])->find($productoId);
        if (!$producto) {
            return response()->json([
                'message' => 'Producto no encontrado',
                'stocks'  => [],
            ], 404);
        }
        $stocks = $producto->oficinaStock->map(function ($registro) {
            return [
                'id'   => optional($registro->stock)->id,
                'nombre'   => optional($registro->stock)->stock,
                'unidades' => (int) $registro->unidades
            ];
        });
        return response()->json([
            'stocks' => $stocks,
        ]);
    }

    public function kitCantidad()
    {
        $kitsUnicos = Orden::whereNotNull('kit_id')
            ->whereHas('atencion', function ($query1) {
                $query1->whereHas('recepciones', function ($query2) {
                    $query2->where('origen_user_id', auth()->user()->id)
                        ->where('activo', false);
                })->where('oficina_id', auth()->user()->oficina_id)
                    ->where('activo', false);
            })
            ->pluck('kit_id')
            ->unique()
            ->count();
        return response()->json(['cantidad' => $kitsUnicos]);
    }

    public function getKitProductos(Request $request)
    {
        $kitId = $request->input('kit_id');
        $oficinaId = auth()->user()->oficina_id;
        $stockBodegaId = Stock::where('stock', 'Bodega')->first()->id;

        $kit = Kit::with(['productos.oficinaStock' => function ($query) use ($oficinaId, $stockBodegaId) {
            $query->where('oficina_id', $oficinaId)
                  ->where('stock_id', $stockBodegaId);
        }])->find($kitId);
        if (!$kit) {
            return response()->json(['error' => 'Kit no encontrado'], 404);
        }
        return response()->json([
            'success' => true,
            'kit' => $kit
        ]);
    }

    public function consultarAvance(Request $request, GestionService $gestionService)
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
            $usuariosParticipantes = $gestionService->obtenerUsuariosParticipantes($tarjetas->pluck('atencion_id')->unique()); //Obtener usuarios participantes
            $resultado             = $tarjetas->map(function ($tarjeta) use ($usuariosParticipantes, $gestionService, $user) {
                $traza = $gestionService->obtenerTraza($tarjeta);
                
                // Si es cliente, priorizamos el estado global de la atención para asegurar sincronización con el tablero de Resueltas
                $estadoId = ($user->mainRole && $user->mainRole->name === 'cliente') 
                    ? (optional($tarjeta->atencion)->estado_id ?? $tarjeta->estado_id)
                    : $tarjeta->estado_id;

                return [
                    'atencion_id' => $tarjeta->atencion_id,
                    'avance'      => optional($tarjeta->atencion)->avance ?? 0, 
                    'estado_id'   => $estadoId,
                    'traza'       => $traza,
                    'recepciones' => $usuariosParticipantes->get($tarjeta->atencion_id, collect()),
                ];
            });
            return response()->json($resultado);
        } catch (\Exception $e) {
            Log::error('Log:: [Usuario: ' . auth()->user()->name . '] Error en consultarAvance: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Ocurrió un error al consultar el avance.'], 500);
        }
    }

    public function solicitudes(GestionService $gestionService)
    {
        try {
            //VALIDACIÓN
            $equipos = collect();
            $operadores = collect();
            $user = auth()->user();
            if ($user->mainRole->name != 'cliente') {
                $equipos = Equipo::where('oficina_id', $user->oficina_id)->get();
                if ($equipos->isEmpty()) {
                    return back()->with('warning', 'No hay equipos de trabajo disponibles para asignar las solicitudes');
                }
                $operadores = User::whereHas('roles', function ($query) {
                    $query->where('name', 'operador');
                })->whereHas('oficina', function ($query) use ($user) {
                    $query->where('id', $user->oficina_id);
                })->where('activo', true)->get();
                if ($operadores->isEmpty()) {
                    return back()->with('warning', 'No hay operadores disponibles para asignar las solicitudes');
                }
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
            $usuariosParticipantes = $gestionService->obtenerUsuariosParticipantes($atencionIds); //Obtener usuarios participantes
            $tarjetas = $recepciones->map(function ($tarjeta) use ($usuariosParticipantes, $gestionService) {
                $usuariosParticipantesAtencion = $usuariosParticipantes->get($tarjeta->atencion_id, collect());
                $traza = $gestionService->obtenerTraza($tarjeta);
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
            Log::error('Log:: [Usuario: ' . auth()->user()->name . '] Ocurrió un error cuando se intentaba obtener las tarjetas: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error al cargar las tarjetas.');
        }
    }



}


