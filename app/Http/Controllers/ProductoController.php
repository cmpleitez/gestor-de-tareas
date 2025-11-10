<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Producto;
use App\Models\Stock;

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
        $compra = $request->origen_stock_id == 0 ? true : false;
        if ($compra) {
            $request->merge([
                'origen_stock_id' => 1,
            ]);
        }
        $request->merge([ //Limpiando máscara de entrada
            'unidades' => preg_replace('/[\s,]/', '', (string) $request->input('unidades')),
        ]);
        $validated = $request->validate([
            'origen_stock_id'  => 'required|different:destino_stock_id|exists:stocks,id',
            'destino_stock_id' => 'required|different:origen_stock_id|exists:stocks,id',
            'producto_id'      => 'required|exists:productos,id',
            'unidades'         => 'required|numeric|min:1',
        ]);
        if ($compra) {
            return back()->with('success', 'Se trata de una compra.');
        } else {
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
