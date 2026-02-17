<?php
namespace App\Http\Controllers;
use App\Models\Tipo;
use App\Http\Requests\TipoStoreRequest;
use App\Http\Requests\TipoUpdateRequest;
use App\Services\CorrelativeIdGenerator;
use Illuminate\Support\Facades\Log;

class TipoController extends Controller
{
    public function index()
    {
        $tipos = Tipo::orderBy('id', 'desc')->paginate(10);
        return view('modelos.tipo.index', compact('tipos'));
    }

    public function create()
    {
        return view('modelos.tipo.create');
    }

    public function store(TipoStoreRequest $request)
    {
        $generator = new CorrelativeIdGenerator();
        $id        = $generator->generate('Tipo');
        $tipo     = new Tipo();
        $tipo->fill($request->validated());
        $tipo->id = $id;
        $tipo->save();
        return redirect()->route('tipo')->with('success', 'Tipo creado correctamente');
    }

    public function show(Tipo $tipo)
    {
        return view('modelos.tipo.show', compact('tipo'));
    }

    public function edit(Tipo $tipo)
    {
        return view('modelos.tipo.edit', compact('tipo'));
    }

    public function update(TipoUpdateRequest $request, Tipo $tipo)
    {
        $tipo->update($request->validated());
        return redirect()->route('tipo')->with('success', 'Tipo actualizado correctamente');
    }

    public function destroy(Tipo $tipo)
    {
        if ($tipo->productos()->exists()) {
            $firstProducto = $tipo->productos()->select('producto')->first();
            return back()->with('error', 'El tipo no puede ser eliminado porque está asignado a el producto: ' . ($firstProducto->producto ?? ''));
        }
        try {
            $tipo->delete();
        } catch (\Exception $e) {
            Log::error('Log:: [Usuario: ' . auth()->user()->name . '] Ocurrió un error cuando se intentaba eliminar el tipo: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error cuando se intentaba eliminar el tipo.');
        }
        return redirect()->route('tipo')->with('success', 'El tipo "' . $tipo->tipo . '" ha sido eliminado correctamente');
    }

    public function activate(Tipo $tipo)
    {
        $tipo->activo = !$tipo->activo;
        $tipo->save();
        return redirect()->route('tipo')->with('success', 'Tipo ' . ($tipo->activo ? 'activado' : 'desactivado') . ' correctamente');
    }

}

