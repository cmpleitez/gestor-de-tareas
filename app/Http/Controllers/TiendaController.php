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
use App\Models\AtencionDetalle;

class TiendaController extends Controller
{
    public function index()
    {
        $kits = Kit::where('activo', true)->get();
        return view('modelos.producto.tienda', compact('kits'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }

    public function retirar(Kit $kit)
    {
        return back()->with('error', 'La operación de retirada no está disponible en este momento');
    }

    public function agregar(Kit $kit)
    {
        $user      = auth()->user(); //Seleccionando el receptor
        $receptors = User::whereHas('roles', function ($query) {
            $query->where('name', 'Receptor');
        })->whereHas('oficina', function ($query) use ($user) {
            $query->where('id', $user->oficina_id);
        })->get();
        if ($receptors->isEmpty()) {
            return back()->with('error', 'No hay personal <Receptor> disponible para atender la solicitud');
        }
        try {
            DB::beginTransaction();
            $kit->load('productos'); //Verificando si ya existe el Kit
            $receptor = $receptors->random();
            $atencionDetalles = AtencionDetalle::where('kit_id', $kit->id) 
            ->whereHas('atencion', function ($query1) {
                $query1->whereHas('recepciones', function ($query2) {
                    $query2->where('origen_user_id', auth()->user()->id)
                    ->where('activo', false);
                })->where('oficina_id', auth()->user()->oficina_id)
                ->where('activo', false);
            })->get();
            if ($atencionDetalles->isNotEmpty()) { //Actualizando las unidades de todo el Kit
                foreach ($atencionDetalles as $atencionDetalle) {
                    $productoPivot = $kit->productos
                    ->where('id', $atencionDetalle->producto_id)
                    ->first();
                    if ($productoPivot && $productoPivot->pivot) {
                        $unidadesNuevas = (int) $productoPivot->pivot->unidades;
                        $unidadesActuales = (int) $atencionDetalle->unidades;
                        $atencionDetalle->unidades = $unidadesActuales + $unidadesNuevas;
                        $atencionDetalle->save();
                    }
                }
            } else {
                $atencion             = new Atencion(); //Creando el registro de atención al cliente
                $atencion->id         = (new KeyMaker())->generate('Atencion', Solicitud::where('solicitud', 'Orden de compra')->first()->id);
                $atencion->oficina_id = auth()->user()->oficina_id;
                $atencion->estado_id  = Estado::where('estado', 'Recibida')->first()->id;
                $atencion->avance     = 0.00;
                $atencion->activo     = false;
                $atencion->save();
                foreach ($kit->productos as $producto) { //Creando el detalle del registro de Atención
                    $atencionDetalle = new AtencionDetalle();
                    $atencionDetalle->atencion_id = $atencion->id;
                    $atencionDetalle->kit_id = $kit->id;
                    $atencionDetalle->producto_id = $producto->id;
                    $atencionDetalle->unidades = $producto->pivot->unidades;
                    $atencionDetalle->precio = $producto->precio;
                    $atencionDetalle->save();
                }
                $recepcion                  = new Recepcion(); //Creando la copia <Receptor> 
                $recepcion->id              = (new KeyMaker())->generate('Recepcion', Solicitud::where('solicitud', 'Orden de compra')->first()->id);
                $recepcion->atencion_id     = $atencion->id;
                $recepcion->solicitud_id    = Solicitud::where('solicitud', 'Orden de compra')->first()->id;
                $recepcion->origen_user_id  = auth()->user()->id;
                $recepcion->destino_user_id = $receptor->id;
                $recepcion->user_destino_role_id = Role::where('name', 'Receptor')->first()->id;
                $recepcion->estado_id       = Estado::where('estado', 'Recibida')->first()->id;
                $recepcion->activo          = false;
                $recepcion->save();
            }
            DB::commit();
            return back()->with('success', 'Kit agregado a la tienda correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error cuando se intentaba agregar el kit a la tienda: ' . $e->getMessage());
        }
    }

    public function createMovimiento()
    {
        $stocks = Stock::where('activo', true)->get();
        $productos = Producto::where('activo', true)->with('modelo', 'tipo')->get();
        return view('modelos.producto.movimiento', compact('productos', 'stocks'));
    }

    public function storeMovimiento(Request $request)
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
        // Cargar los stocks para obtener sus nombres
        $stockOrigen = Stock::find($validated['origen_stock_id']);
        $stockDestino = Stock::find($validated['destino_stock_id']);
        // Verificar que los stocks existan (condición de carrera)
        if (!$stockOrigen) {
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
                // Cargar la relación stock después de guardar
                $oficinaStockOrigen->load('stock');
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
                // Cargar la relación stock después de guardar
                $oficinaStockDestino->load('stock');
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
            // Usar los stocks cargados directamente (verificados anteriormente)
            $movimiento->movimiento = $stockOrigen->stock . ' -> ' . $stockDestino->stock;
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

    public function cantidad()
    {
        $kitsUnicos = AtencionDetalle::whereNotNull('kit_id')
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
            'productos' => $kit->productos
        ]);
    }
}
