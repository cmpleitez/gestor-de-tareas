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
                'origen_stock_id'  => 'required|different:destino_stock_id|exists:stocks,id',
                'destino_stock_id' => 'required|different:origen_stock_id|exists:stocks,id',
                'producto_id'      => 'required|exists:productos,id',
                'unidades'         => 'required|numeric|min:1',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', 'Error en la validación: ' . $e->getMessage());
        }
        //agregar validación de unidades para que cuadren los stocks

        if ($validated->origen_stock_id==1 && $validated->destino_stock_id==5) { //Validando cuando sean compras o devoluciones sobre compras
            $origenStockUnidades = 0;
            $destinoStockUnidades = $validated->unidades;
        } else if ($validated->origen_stock_id==5 && $validated->destino_stock_id==1) {
            $origenStockUnidades = $validated->unidades;
            $destinoStockUnidades = 0;
        } else { //Validando cuando sean movimientos entre stocks
            $origenStockUnidades = $validated->unidades;
            $destinoStockUnidades = $validated->unidades;
        }

        //PROCESOS
        //try {
            //DB::beginTransaction();
                $origenStockName = Stock::findOrfail($validated->origen_stock_id)->stock; //Nombre del stock de origen
            
                $oficinaStockOrigen = OficinaStock::where('oficina_id', auth()->user()->oficina_id) //Origen
                ->where('stock_id', $validated->origen_stock_id)
                ->where('producto_id', $validated->producto_id)
                ->with('stock')
                ->first();
                if (!$oficinaStockOrigen) {
                    $oficinaStockOrigen = new OficinaStock();
                    $oficinaStockOrigen->oficina_id = auth()->user()->oficina_id;
                    $oficinaStockOrigen->stock_id = $validated->destino_stock_id;
                    $oficinaStockOrigen->producto_id = $validated->producto_id;
                    $oficinaStockOrigen->unidades = $validated->unidades; //agregar excepcion: origen->1|5; destino->5|1
                    $oficinaStockOrigen->save();
                } else {
                    $oficinaStockOrigen->unidades -= $validated->unidades; //agregar excepcion: origen->1|5; destino->5|1
                    $oficinaStockOrigen->save();
                }

                $oficinaStockDestino = OficinaStock::where('oficina_id', auth()->user()->oficina_id) //Destino
                ->where('stock_id', $validated->destino_stock_id)
                ->where('producto_id', $validated->producto_id)
                ->with('stock')
                ->first();
                if (!$oficinaStockDestino) {
                    $oficinaStockDestino = new OficinaStock();
                    $oficinaStockDestino->oficina_id = auth()->user()->oficina_id;
                    $oficinaStockDestino->stock_id = $validated->destino_stock_id;
                    $oficinaStockDestino->producto_id = $validated->producto_id;
                    $oficinaStockDestino->unidades = $validated->unidades; //agregar excepcion: origen->5|1; destino->1|5
                    $oficinaStockDestino->save();
                } else {
                    $oficinaStockDestino->unidades += $validated->unidades; //agregar excepcion: origen->5|1; destino->1|5
                    $oficinaStockDestino->save();
                }

                $movimiento = new Movimiento(); //Movimiento
                $movimiento->id = app(KeyMaker::class)->generate('Movimiento', $oficinaStockOrigen->stock_id);
                $movimiento->user_id = auth()->id();
                $movimiento->oficina_id = auth()->user()->oficina_id;
                $movimiento->origen_stock_id = $validated->origen_stock_id;
                $movimiento->destino_stock_id = $oficinaStockOrigen->stock_id;
                $movimiento->producto_id = $oficinaStockOrigen->producto_id;
                $movimiento->movimiento = $origenStockName . ' -> ' . $oficinaStockOrigen->stock->stock;
                $movimiento->unidades = $oficinaStockOrigen->unidades;
                $movimiento->save();
            //DB::commit();
            return back()->with('success', 'Movimiento registrado correctamente');
/*
        } catch (QueryException $e) {
            DB::rollBack();
            return back()->with('error', 'Error en la consulta: ' . $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error en la transacción: ' . $e->getMessage());
        }
*/        
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
