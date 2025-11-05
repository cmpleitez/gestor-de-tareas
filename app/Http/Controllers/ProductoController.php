<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\TipoEntrada;

class ProductoController extends Controller
{

    public function index()
    {
        return view('modelos.producto.index');
    }


    public function entrada()
    {
        $tipos_entradas = TipoEntrada::all();
        $productos = Producto::where('activo', true)->with('modelo', 'tipo')->get();
        return view('modelos.producto.entrada', compact('productos', 'tipo_entradas'));
    }


    public function agregar()
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
}
