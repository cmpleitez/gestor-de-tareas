<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use Illuminate\Http\Request;
use App\Http\Requests\EquipoStoreRequest;
use App\Http\Requests\EquipoUpdateRequest;

class EquipoController extends Controller
{
    public function index()
    {
        $equipos = Equipo::all();
        return view('modelos.equipo.index', compact('equipos'));
    }

    public function create()
    {
        return view('modelos.equipo.create');
    }

    public function store(EquipoStoreRequest $request)
    {
        $equipo = Equipo::create($request->validated());
        return redirect()->route('equipo')->with('success', 'Equipo ' . $equipo->equipo . ' creado correctamente');
    }

    public function edit(Equipo $equipo)
    {
        return view('modelos.equipo.edit', compact('equipo'));
    }

    public function update(EquipoUpdateRequest $request, Equipo $equipo)
    {
        $equipo->update($request->validated());
        return redirect()->route('equipo')->with('success', 'Equipo ' . $equipo->equipo . ' actualizado correctamente');
    }

    public function show(Equipo $equipo)
    {
        //
    }

    public function destroy(Equipo $equipo)
    {
        if ($equipo->users->count() > 0) {
            return back()->with('error', 'El equipo no puede ser eliminado porque tiene usuarios asignados.');
        }
        try {
            $equipo->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error cuando se intentaba eliminar el equipo: ' . $e->getMessage());
        }
        return redirect()->route('equipo')->with('success', 'El equipo "' . $equipo->equipo . '" ha sido eliminado correctamente');
    }

    public function activate(Equipo $equipo)
    {
        $equipo->activo = !$equipo->activo;
        $equipo->save();
        return redirect()->route('equipo')->with('success', 'El equipo "' . $equipo->equipo . '" ha sido ' . ($equipo->activo ? 'activado' : 'desactivado') . ' correctamente');
    }
}
