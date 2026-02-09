<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Exception;

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
use App\Services\KeyRipper;






use Illuminate\Support\Facades\Log;





class TiendaController extends Controller
{
    public function index()
    {
        $kits = Kit::where('activo', true)
        ->with('productos')
        ->get();
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
        return view('modelos.kit.carrito', [
            'atencion' => collect([$atencion]),
            'atencion_id_ripped' => $atencion_id_ripped,
            'recepcion_id' => $request->recepcion_id
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
                    ->where('user_destino_role_id', Role::where('name','Receptor')->first()->id)
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
            return response()->json(['success' => true, 'message' => 'Orden recibida. Redireccionando a la Tienda']);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error en carritoEnviar: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Error al procesar la orden: ' . $e->getMessage()], 500);
        }
    }

    public function retirarOrden(Orden $orden)
    {
        try {
            DB::beginTransaction();
            $atencion = Atencion::with('ordenes')->find($orden->atencion_id);
            if ($atencion && $atencion->ordenes->count() === 1) {
                return response()->json(['success' => false, 'message' => 'No se autoriza dejar vacío el carrito porque crearía una inconsistencia: la solicitud quedaría en el tablero de trabajo de manera permanente']);
            }
            $orden->detalle()->delete();
            $orden->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Kit retirado del carrito correctamente', 'orden_vacia' => true]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error al retirar orden {$orden->id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al procesar la solicitud: ' . $e->getMessage()], 500);
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
                    ->where('estado_id', Estado::where('estado', 'Solicitada')->first()->id)
                    ->first();
                if (!$atencion) {
                    $atencion_nueva = true;
                    $receptores = User::whereHas('roles', function ($query) { //Seleccionando el receptor
                        $query->where('name', 'Receptor');
                    })->whereHas('oficina', function ($query) use ($user) {
                        $query->where('id', $user->oficina_id);
                    })->get();
                    if ($receptores->isEmpty()) {
                        
                        Log::warning('No hay receptores disponibles', ['oficina_id' => $user->oficina_id, 'user_id' => $user->id]);
                        
                        DB::rollBack();
                        $message = 'No hay personal <Receptor> disponible para atender la solicitud';
                        return $request->ajax() ? response()->json(['success' => false, 'message' => $message, 'type' => 'error']) : back()->with('error', $message);
                    }
                    $receptor = $receptores->random();
                    $atencion             = new Atencion(); //Creando número de atención
                    $atencion->id         = (new KeyMaker())->generate('Atencion', Solicitud::where('solicitud', 'Orden de compra')->first()->id);
                    $atencion->oficina_id = auth()->user()->oficina_id;
                    $atencion->estado_id  = Estado::where('estado', 'Solicitada')->first()->id;
                    $atencion->avance     = 0.00;
                    $atencion->activo     = false;
                    $atencion->save();
                    $recepcion                  = new Recepcion(); //Creando la copia de la orden de compra para el <Receptor>
                    $recepcion->id              = (new KeyMaker())->generate('Recepcion', Solicitud::where('solicitud', 'Orden de compra')->first()->id);
                    $recepcion->atencion_id     = $atencion->id;
                    $recepcion->solicitud_id    = Solicitud::where('solicitud', 'Orden de compra')->first()->id;
                    $recepcion->origen_user_id  = auth()->user()->id;
                    $recepcion->destino_user_id = $receptor->id;
                    $recepcion->user_destino_role_id = Role::where('name', 'Receptor')->first()->id;
                    $recepcion->estado_id       = Estado::where('estado', 'Solicitada')->first()->id;
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
            $message = 'Ocurrió un error cuando se intentaba agregar el kit a la tienda: ' . $e->getMessage();
            return $request->ajax() ? response()->json(['success' => false, 'message' => $message, 'type' => 'error']) : back()->with('error', $message);
        }
    }

    public function retirarItem(Request $request)
    {
        if (auth()->user()->mainRole->name !== 'cliente') {
            $orden = Orden::with('atencion.ordenes')->find($request->orden_id);
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
                $orden = Orden::find($request->orden_id);
                $ordenVacia = false;
                if ($orden && $orden->detalle()->count() === 0) {
                    $orden->delete();
                    $ordenVacia = true;
                }
                
                DB::commit();
                return response()->json([
                    'success' => true, 
                    'message' => 'Se retiró el item',
                    'orden_vacia' => $ordenVacia
                ]);
            }
            throw new Exception('No se encontró el producto especificado');
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
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
            return back()->with('error', 'Error en la validación: ' . $e->getMessage());
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
            $movimiento->save();
            DB::commit();
            return back()->with('success', 'Movimiento ' . $movimiento->movimiento . ' efectuado correctamente');
        } catch (QueryException $e) {
            DB::rollBack();
            return back()->with('error', 'Error en la consulta: ' . $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error en la transacción: ' . $e->getMessage());
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
        $kit = Kit::with('productos')->find($kitId);
        if (!$kit) {
            return response()->json(['error' => 'Kit no encontrado'], 404);
        }
        return response()->json([
            'success' => true,
            'kit' => $kit
        ]);
    }
}
