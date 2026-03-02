<?php

namespace App\Http\Controllers;

use App\Models\Parametro;
use Illuminate\Http\Request;

class ParametroController extends Controller
{
    public function index()
    {
        $parametros = Parametro::all();
        return view('modelos.parametro.index', compact('parametros'));
    }

    public function edit(Parametro $parametro)
    {
        return view('modelos.parametro.edit', compact('parametro'));
    }

    public function update(Request $request, Parametro $parametro)
    {
        $validatedData = $request->validate([
            'parametro'     => 'required|string|min:3|max:255|unique:parametros,parametro,' . $parametro->id,
            'valor'         => 'required|string|min:1|max:255',
            'unidad_medida' => 'required|string|min:3|max:255',
        ]);
        $parametro->parametro     = $validatedData['parametro']; 
        $parametro->valor         = $validatedData['valor'];     
        $parametro->unidad_medida = $validatedData['unidad_medida']; 
        $parametro->save();
        return redirect()->route('parametro')->with('success', 'Parámetro actualizado correctamente');
    }

    public function activate(Parametro $parametro)
    {
        $parametro->activo = ! $parametro->activo;
        $parametro->save();
        return redirect()->route('parametro')->with('success', 'Parámetro actualizado correctamente');
    }
}
