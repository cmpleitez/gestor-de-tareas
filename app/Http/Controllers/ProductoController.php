<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  
use Illuminate\Database\QueryException;
use Exception;

use App\Models\Producto;    
use App\Models\Stock;
use App\Models\OficinaStock;
use App\Models\Movimiento;
use App\Services\KeyMaker;

use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{

    public function index()
    {
        return view('modelos.producto.index');
    }

    public function createMovimiento()
    {
        $stocks    = Stock::where('activo', true)->get();
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
        //PROCESOS
        try {
            DB::beginTransaction();
                $oficinaStockOrigen = OficinaStock::where('oficina_id', auth()->user()->oficina_id) //Stock origen
                ->where('stock_id', $validated['origen_stock_id'])
                ->where('producto_id', $validated['producto_id'])
                ->with('stock')
                ->first();
                if (!$oficinaStockOrigen) {
                    $oficinaStockOrigen = new OficinaStock();
                    $oficinaStockOrigen->oficina_id = auth()->user()->oficina_id;
                    $oficinaStockOrigen->stock_id = $validated['origen_stock_id'];
                    $oficinaStockOrigen->producto_id = $validated['producto_id'];
                    $oficinaStockOrigen->unidades = $origenStockUnidades;
                    $oficinaStockOrigen->save();
                } else {
                    $oficinaStockOrigen->unidades -= $origenStockUnidades;
                    $oficinaStockOrigen->save();
                }
                $oficinaStockDestino = OficinaStock::where('oficina_id', auth()->user()->oficina_id) //Stock destino
                ->where('stock_id', $validated['destino_stock_id'])
                ->where('producto_id', $validated['producto_id'])
                ->with('stock')
                ->first();
                if (!$oficinaStockDestino) {
                    $oficinaStockDestino = new OficinaStock();
                    $oficinaStockDestino->oficina_id = auth()->user()->oficina_id;
                    $oficinaStockDestino->stock_id = $validated['destino_stock_id'];
                    $oficinaStockDestino->producto_id = $validated['producto_id'];
                    $oficinaStockDestino->unidades = $destinoStockUnidades;
                    $oficinaStockDestino->save();
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
                $movimiento->movimiento = $oficinaStockOrigen->stock->stock . ' -> ' . $oficinaStockDestino->stock->stock;
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
}
