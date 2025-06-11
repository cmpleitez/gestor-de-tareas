<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use Illuminate\Http\Request;

class EquipoController extends Controller
{
    public function index()
    {
        $equipos = Equipo::all();
        return view('modelos.equipo.index', compact('equipos'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Equipo $equipo)
    {
        //
    }

    public function edit(Equipo $equipo)
    {
        //
    }

    public function update(Request $request, Equipo $equipo)
    {
        //
    }

    public function activate(Equipo $equipo)
    {
        $equipo->activo = !$equipo->activo;
        $equipo->save();
        return redirect()->route('equipo')->with('success', 'Equipo ' . $equipo->equipo . ' ' . ($equipo->activo ? 'activado' : 'desactivado') . ' correctamente');
    }

    public function destroy(Equipo $equipo)
    {
        //
    }
}
