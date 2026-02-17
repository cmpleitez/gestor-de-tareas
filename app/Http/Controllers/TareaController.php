<?php
namespace App\Http\Controllers;

use App\Http\Requests\TareaStoreRequest;
use App\Http\Requests\TareaUpdateRequest;
use App\Models\Tarea;
use App\Services\CorrelativeIdGenerator;
use Illuminate\Support\Facades\Log;

class TareaController extends Controller
{
    public function index()
    {
        $tareas = Tarea::orderBy('id', 'desc')->paginate(10);
        return view('modelos.tarea.index', compact('tareas'));
    }

    public function create()
    {
        return view('modelos.tarea.create');
    }

    public function store(TareaStoreRequest $request)
    {
        $generator = new CorrelativeIdGenerator();
        $id        = $generator->generate('Tarea');
        $tarea     = new Tarea();
        $tarea->fill($request->validated());
        $tarea->id = $id;
        $tarea->save();
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

    public function destroy(Tarea $tarea)
    {
        if ($tarea->solicitudes()->exists()) {
            $firstSolicitud = $tarea->solicitudes()->select('solicitud')->first();
            return back()->with('error', 'La tarea no puede ser eliminada porque está asignada a la solicitud: ' . ($firstSolicitud->solicitud ?? ''));
        }
        try {
            $tarea->delete();
        } catch (\Exception $e) {
            Log::error('Log:: [Usuario: ' . auth()->user()->name . '] Ocurrió un error cuando se intentaba eliminar la tarea: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error cuando se intentaba eliminar la tarea.');
        }
        return redirect()->route('tarea')->with('success', 'La tarea "' . $tarea->tarea . '" ha sido eliminada correctamente');
    }
    public function activate(Tarea $tarea)
    {
        $tarea->activo = ! $tarea->activo;
        $tarea->save();
        return redirect()->route('tarea')->with('success', 'La tarea "' . $tarea->tarea . '" ha sido ' . ($tarea->activo ? 'activada' : 'desactivada') . ' correctamente');
    }
}
