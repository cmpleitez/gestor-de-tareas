<?php
namespace App\Http\Controllers;
use App\Http\Requests\ModeloStoreRequest;
use App\Http\Requests\ModeloUpdateRequest;
use App\Models\Marca;
use App\Models\Modelo;
use App\Services\CorrelativeIdGenerator;
use Illuminate\Support\Facades\Log;

class ModeloController extends Controller
{
    public function index()
    {
        $modelos = Modelo::orderBy('id', 'desc')->paginate(10);
        return view('modelos.modelo.index', compact('modelos'));
    }

    public function create()
    {
        $marcas = Marca::where('activo', true)->get();
        return view('modelos.modelo.create', compact('marcas'));
    }

    public function store(ModeloStoreRequest $request)
    {
        $generator = new CorrelativeIdGenerator();
        $id        = $generator->generate('Modelo');
        $modelo     = new Modelo();
        $modelo->fill($request->validated());
        $modelo->id = $id;
        $modelo->save();
        return redirect()->route('modelo')->with('success', 'Modelo creado correctamente');
    }

    public function show(Modelo $modelo)
    {
        //
    }

    public function edit(Modelo $modelo)
    {
        $marcas = Marca::where('activo', true)->get();
        return view('modelos.modelo.edit', compact('modelo', 'marcas'));
    }

    public function update(ModeloUpdateRequest $request, Modelo $modelo)
    {
        $modelo->update($request->validated());
        return redirect()->route('modelo')->with('success', 'Modelo actualizado correctamente');
    }

    public function destroy(Modelo $modelo)
    {
        if ($modelo->productos()->exists()) {
            $firstProducto = $modelo->productos()->select('producto')->first();
            return back()->with('error', 'El modelo no puede ser eliminado porque está asignado a el producto: ' . ($firstProducto->producto ?? ''));
        }
        try {
            $modelo->delete();
        } catch (\Exception $e) {
            Log::error('Log:: Ocurrió un error cuando se intentaba eliminar el modelo: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error cuando se intentaba eliminar el modelo.');
        }
        return redirect()->route('modelo')->with('success', 'El modelo "' . $modelo->modelo . '" ha sido eliminado correctamente');
    }

    public function activate(Modelo $modelo)
    {
        $modelo->activo = ! $modelo->activo;
        $modelo->save();
        return redirect()->route('modelo')->with('success', 'El modelo "' . $modelo->modelo . '" ha sido ' . ($modelo->activo ? 'activado' : 'desactivado') . ' correctamente');
    }
}
