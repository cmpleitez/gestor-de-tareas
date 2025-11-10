<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Stock;
use Illuminate\Http\Request;
use App\Models\OficinaStock;

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
        $request->merge([ //Limpiando máscara de entrada
            'unidades' => preg_replace('/[\s,]/', '', (string) $request->input('unidades')),
        ]);
        $validated = $request->validate([
            'origen_stock_id'  => 'required|different:destino_stock_id|exists:stocks,id',
            'destino_stock_id' => 'required|different:origen_stock_id|exists:stocks,id',
            'producto_id'      => 'required|exists:productos,id',
            'unidades'         => 'required|numeric|min:1',
        ]);

        $unidades_disponibles = OficinaStock::where('oficina_id', auth()->user()->oficina_id)->where('stock_id', $request->origen_stock_id)->where('producto_id', $request->producto_id)->first();
        if ($request->origen_stock_id == 0) { //Stock de recien comprados
            //
        } else { //Resto de stocks
            //
        }
        
/*         if ($unidades_disponibles) {
            return redirect()->route('producto.entrada')->with('success', 'Ya existen unidades');
        } else {
            
            return redirect()->route('producto.entrada')->with('warning', 'No existen unidades disponibles');
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
