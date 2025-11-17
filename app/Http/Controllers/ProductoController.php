<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Producto;
use App\Models\Modelo;
use App\Models\Tipo;

class ProductoController extends Controller
{

    public function index()
    {
        $productos = Producto::all();
        return view('modelos.producto.index', compact('productos'));
    }

    public function store(Request $request)
    {
        //
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
