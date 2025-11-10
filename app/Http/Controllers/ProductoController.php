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
use App\Models\Entrada;
use App\Services\KeyMaker;

use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{

    public function index()
    {
        return view('modelos.producto.index');
    }

    public function entrada()
    {
        $stocks    = Stock::where('activo', true)->get();
        $productos = Producto::where('activo', true)->with('modelo', 'tipo')->get();
        return view('modelos.producto.entrada', compact('productos', 'stocks'));
    }

    public function ingreso(Request $request)
    {
        //PREESTABLECIMIENTOS
        $compra = $request->origen_stock_id == 0 ? true : false;
        if ($compra) {
            $request->merge([
                'origen_stock_id' => 1,
            ]);
        }
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
            return back()->with('error', 'Error en la validación: ' . $e->getMessage());;
        }

        

        //PROCESOS
        if ($compra) { //Compra
            Log::info('Antes del try');
            try {
                Log::info('inicio del try');
                DB::beginTransaction();
                    
                    Log::info('antes de la consulta');
                    $stock = OficinaStock::where('oficina_id', auth()->user()->oficina_id)->where('stock_id', $request->origen_stock_id)->where('producto_id', $request->producto_id)->first();
                    
                    Log::info('despues de la consulta');

                    if (!$stock) {

                        Log::info('inicio de registro de nuevo stock');

                        $stock = new OficinaStock();
                        $stock->oficina_id = auth()->user()->oficina_id;
                        $stock->stock_id = $request->destino_stock_id;
                        $stock->producto_id = $request->producto_id;
                        $stock->unidades = $request->unidades;
                        $stock->save();

                        Log::info('final del registro de nuevo stock');

                    } else {
                        $stock->unidades += $request->unidades;
                        $stock->save();
                    }



                    $entrada = new Entrada();
                    $entrada->id = app(KeyMaker::class)->generate('Entrada', $stock->stock_id);
                    $entrada->user_id = auth()->id();
                    $entrada->origen_stock_id = 0;
                    $entrada->oficina_id = auth()->user()->oficina_id;
                    $entrada->destino_stock_id = $stock->stock_id;
                    $entrada->producto_id = $stock->producto_id;
                    $entrada->entrada = 'Compra';
                    $entrada->unidades = $stock->unidades;
                    $entrada->save();
                DB::commit();
                
            } catch (QueryException $e) {
                DB::rollBack();
                return back()->with('error', 'Error en la consulta: ' . $e->getMessage());
            } catch (Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Error en la consulta: ' . $e->getMessage());
            }
        } else { //Entrada
            return back()->with('success', 'Se trata de una entrada.');
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
