<?php
namespace App\Http\Controllers;

use App\Http\Requests\EquipoStoreRequest;
use App\Http\Requests\EquipoUpdateRequest;
use App\Models\Equipo;
use App\Models\Oficina;
use App\Services\CorrelativeIdGenerator;
use Illuminate\Support\Facades\Log;

class EquipoController extends Controller
{
    public function index()
    {
        $equipos = Equipo::orderBy('id', 'desc')->paginate(15);
        return view('modelos.equipo.index', compact('equipos'));
    }

    public function create()
    {
        $oficinas = Oficina::where('activo', true)->orderBy('oficina')->get();
        return view('modelos.equipo.create', compact('oficinas'));
    }

    public function store(EquipoStoreRequest $request)
    {
        $generator = new CorrelativeIdGenerator();
        $id        = $generator->generate('Equipo');
        $equipo    = new Equipo();
        $equipo->fill($request->validated());
        $equipo->id = $id;
        $equipo->save();
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

    public function destroy(Equipo $equipo)
    {
        if ($equipo->users()->exists()) {
            return back()->with('error', 'El equipo no puede ser eliminado porque tiene usuarios asignados.');
        }
        try {
            $equipo->delete();
        } catch (\Exception $e) {
            Log::error('Log:: Ocurrió un error cuando se intentaba eliminar el equipo: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error cuando se intentaba eliminar el equipo.');
        }
        return redirect()->route('equipo')->with('success', 'El equipo "' . $equipo->equipo . '" ha sido eliminado correctamente');
    }

    public function activate(Equipo $equipo)
    {
        $equipo->activo = ! $equipo->activo;
        $equipo->save();
        return redirect()->route('equipo')->with('success', 'El equipo "' . $equipo->equipo . '" ha sido ' . ($equipo->activo ? 'activado' : 'desactivado') . ' correctamente');
    }
}
