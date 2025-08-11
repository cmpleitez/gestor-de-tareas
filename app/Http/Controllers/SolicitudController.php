<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\Solicitud;
use App\Http\Requests\SolicitudStoreRequest;
use App\Http\Requests\SolicitudUpdateRequest;
use App\Models\Tarea;

class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = Solicitud::orderBy('id', 'desc')->paginate(15);
        return view('modelos.solicitud.index', compact('solicitudes'));
    }

    public function create()
    {
        return view('modelos.solicitud.create');
    }

    public function store(SolicitudStoreRequest $request)
    {
        Solicitud::create($request->validated());
        return redirect()->route('solicitud')->with('success', 'Solicitud creada correctamente');
    }

    public function edit(Solicitud $solicitud)
    {
        return view('modelos.solicitud.edit', compact('solicitud'));
    }

    public function update(SolicitudUpdateRequest $request, Solicitud $solicitud)
    {
        $solicitud->update($request->validated());
        return redirect()->route('solicitud')->with('success', 'Solicitud actualizada correctamente');  
    }

    public function asignarTareas(Solicitud $solicitud)
    {
        $tareas = Tarea::where('activo', true)->get();
        return view('modelos.solicitud.asignar-tareas', compact('solicitud', 'tareas'));
    }

    public function actualizarTareas(Solicitud $solicitud, Request $request)
    {
        $solicitud->tareas()->sync($request->tareas);
        return redirect()->route('solicitud')->with('success', 'Tareas actualizadas correctamente');
    }

    public function show(string $id)
    {
        //
    }

    public function destroy(Solicitud $solicitud)
    {
        if ($solicitud->usuarios()->exists()) {
            return back()->with('error', 'La solicitud "' . $solicitud->solicitud . '" no puede ser eliminada porque tiene usuarios asociados');
        }
        
        if($solicitud->tareas()->exists()) {
            return back()->with('error', 'La solicitud "' . $solicitud->solicitud . '" no puede ser eliminada porque tiene tareas asociadas');
        }
        if($solicitud->usuariosOrigenes()->exists() || $solicitud->usuariosDestinos()->exists()) {
            return back()->with('error', 'La solicitud "' . $solicitud->solicitud . '" no puede ser eliminada porque tiene transacciones asociadas');
        }
        try {
            $solicitud->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error cuando se intentaba eliminar la solicitud: ' . $e->getMessage());
        }
        return redirect()->route('solicitud')->with('success', 'La solicitud "' . $solicitud->solicitud . '" ha sido eliminada correctamente');
    }
    public function activate(Solicitud $solicitud)
    {
        $solicitud->activo = !$solicitud->activo;
        $solicitud->save();
        return redirect()->route('solicitud')->with('success', 'La solicitud "' . $solicitud->solicitud . '" ha sido ' . ($solicitud->activo ? 'activada' : 'desactivada') . ' correctamente');
    }

}
