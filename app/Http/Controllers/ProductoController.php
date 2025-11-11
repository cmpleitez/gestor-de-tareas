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
        //PROCESOS
        try {
            DB::beginTransaction();
                
                //POR CADA MOVIMIENTO HAY QUE ACTUALIZAR DOS STOCKS (ORIGEN Y DESTINO)
            
                $oficinaStock = OficinaStock::where('oficina_id', auth()->user()->oficina_id)->where('stock_id', $request->origen_stock_id)->where('producto_id', $request->producto_id)->with('stock')->first();
                
                
                $origenStockName = Stock::findOrfail($request->origen_stock_id)->stock;
                
                if (!$oficinaStock) {
                    $oficinaStock = new OficinaStock();
                    $oficinaStock->oficina_id = auth()->user()->oficina_id;
                    $oficinaStock->stock_id = $request->destino_stock_id;
                    $oficinaStock->producto_id = $request->producto_id;
                    $oficinaStock->unidades = $request->unidades;
                    $oficinaStock->save();
                } else {
                    $oficinaStock->unidades += $request->unidades;
                    $oficinaStock->save();
                }
                $movimiento = new Movimiento();
                $movimiento->id = app(KeyMaker::class)->generate('Movimiento', $oficinaStock->stock_id);
                $movimiento->user_id = auth()->id();
                $movimiento->oficina_id = auth()->user()->oficina_id;
                $movimiento->origen_stock_id = $request->origen_stock_id;
                $movimiento->destino_stock_id = $oficinaStock->stock_id;
                $movimiento->producto_id = $oficinaStock->producto_id;
                $movimiento->movimiento = $origenStockName . ' -> ' . $oficinaStock->stock->stock;
                $movimiento->unidades = $oficinaStock->unidades;
                $movimiento->save();
            DB::commit();
            return back()->with('success', 'Movimiento registrado correctamente');
        } catch (QueryException $e) {
            DB::rollBack();
            return back()->with('error', 'Error en la consulta: ' . $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error en la consulta: ' . $e->getMessage());
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
