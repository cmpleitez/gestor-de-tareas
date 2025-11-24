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


    public function agregar(Kit $kit)
    {
        try {

return $kit;

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
            $atencion->id         = (new KeyMaker())->generate('Atencion', Solicitud::where('solicitud', 'Orden de compra')->first()->id);
            $atencion->oficina_id = auth()->user()->oficina_id;
            $atencion->estado_id  = Estado::where('estado', 'Recibida')->first()->id;
            $atencion->avance     = 0.00;
            $atencion->activo     = false; //Por defecto no validada
            $atencion->save();

            $recepcion                  = new Recepcion(); //Creando la recepción
            $recepcion->id              = (new KeyMaker())->generate('Recepcion', Solicitud::where('solicitud', 'Orden de compra')->first()->id);
            $recepcion->atencion_id     = $atencion->id;
            $recepcion->role_id         = Role::where('name', 'Receptor')->first()->id;
            $recepcion->solicitud_id    = Solicitud::where('solicitud', 'Orden de compra')->first()->id;
            $recepcion->user_id_origen  = auth()->user()->id;
            $recepcion->user_id_destino = $Receptor->id;
            $recepcion->estado_id       = Estado::where('estado', 'Recibida')->first()->id;
            $recepcion->activo          = false; //Por defecto no validada
            $recepcion->save();



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
        if ($validated['origen_stock_id']==1 && $validated['destino_stock_id']==5) { //Compras
            $origenStockUnidades = 0;
            $destinoStockUnidades = $validated['unidades'];
        } else if ($validated['origen_stock_id']==5 && $validated['destino_stock_id']==1) { //Devoluciones
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
            return back()->with('error', 
            'No hay suficientes unidades en '.$oficinaStockOrigen->stock->stock. 
            '. Cantidad disponible: '.$oficinaStockOrigen->unidades);
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
                $orientacion = $validated['origen_stock_id'].auth()->user()->oficina_id.$validated['destino_stock_id'];
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
            return back()->with('success', 'Movimiento '.$movimiento->movimiento.' efectuado correctamente');
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

}
