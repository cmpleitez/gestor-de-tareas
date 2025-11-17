<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Producto;
use App\Models\Modelo;
use App\Models\Tipo;
use App\Services\CorrelativeIdGenerator;
use App\Http\Requests\ProductoStoreRequest;

class ProductoController extends Controller
{

    public function index()
    {
        $productos = Producto::all();
        return view('modelos.producto.index', compact('productos'));
    }

    public function create()
    {
        $modelos = Modelo::where('activo', true)->get();
        $tipos = Tipo::where('activo', true)->get();
        return view('modelos.producto.create', compact('modelos', 'tipos'));
    }

    public function store(ProductoStoreRequest $request)
    {
        $generator = new CorrelativeIdGenerator();
        $id        = $generator->generate('Producto');
        $producto     = new Producto();
        $producto->fill($request->validated());
        $producto->id = $id;
        $producto->save();
        return redirect()->route('producto')->with('success', 'Producto creado correctamente');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Producto $producto)
    {
        $modelos = Modelo::all();
        $tipos = Tipo::all();
        return view('modelos.producto.edit', compact('producto', 'modelos', 'tipos'));
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
