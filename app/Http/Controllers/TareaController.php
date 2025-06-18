<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use Illuminate\Http\Request;
use App\Http\Requests\TareaStoreRequest;
use App\Http\Requests\TareaUpdateRequest;

class TareaController extends Controller
{
    public function index()
    {
        $tareas = Tarea::all();
        return view('modelos.tarea.index', compact('tareas'));
    }

    public function create()
    {
        return view('modelos.tarea.create');
    }

    public function store(TareaStoreRequest $request)
    {
        Tarea::create($request->validated());
        return redirect()->route('tarea')->with('success', 'Tarea creada correctamente');
    }

    public function edit(Tarea $tarea)
    {
        return view('modelos.tarea.edit', compact('tarea'));
    }

    public function update(TareaUpdateRequest $request, Tarea $tarea)
    {
        $tarea->update($request->validated());
        return redirect()->route('tarea')->with('success', 'Tarea actualizada correctamente');
    }

    public function show(Tarea $tarea)
    {
        return 'show';
    }

    public function destroy(Tarea $tarea)
    {
        if ($tarea->solicitudes->count() > 0) {
            return back()->with('error', 'La tarea no puede ser eliminada porque tiene solicitudes asignadas.');
        }
        try {
            $tarea->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error cuando se intentaba eliminar la tarea: ' . $e->getMessage());
        }
        return redirect()->route('tarea')->with('success', 'La tarea "' . $tarea->tarea . '" ha sido eliminada correctamente');
    }
    public function activate(Tarea $tarea)
    {
        $tarea->activo = !$tarea->activo;
        $tarea->save();
        return redirect()->route('tarea')->with('success', 'La tarea "' . $tarea->tarea . '" ha sido ' . ($tarea->activo ? 'activada' : 'desactivada') . ' correctamente');
    }
}
