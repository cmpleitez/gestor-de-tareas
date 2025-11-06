<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Entrada;
use App\Models\Producto;
use App\Models\Stock;
use Illuminate\Http\Request;

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
        $request->validate([
            'origen_stock_id'  => 'required|different:destino_stock_id|exists:stocks,id',
            'destino_stock_id' => 'required|different:origen_stock_id|exists:stocks,id',
            'producto_id'      => 'required|exists:productos,id',
            'unidades'         => 'required|numeric|min:1',
        ]);

        $stock = Stock::find($request->origen_stock_id)->entradas->count();

        dd($stock);

        if ($stock > 0) {
            //return redirect()->route('producto.entrada')->with('error', 'Stock origen no tiene unidades disponibles');
        } else {
/*             $entrada = Entrada::create([
                'stock_origen_id' => $request->origen_stock_id,
                'stock_destino_id' => $request->destino_stock_id,
                'modelo_id' => $request->modelo_id,
                'tipo_id' => $request->tipo_id,
                'producto_id' => $request->producto_id,
                'cantidad' => $request->cantidad,
            ]);
            return redirect()->route('producto.entrada')->with('success', 'Entrada creada correctamente'); */
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
