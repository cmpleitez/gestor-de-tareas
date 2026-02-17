<?php
namespace App\Http\Controllers;
use App\Http\Requests\MarcaStoreRequest;
use App\Http\Requests\MarcaUpdateRequest;
use App\Models\Marca;
use App\Services\CorrelativeIdGenerator;
use Illuminate\Support\Facades\Log;

class MarcaController extends Controller
{
    public function index()
    {
        $marcas = Marca::orderBy('id', 'desc')->paginate(10);
        return view('modelos.marca.index', compact('marcas'));
    }

    public function create()
    {
        return view('modelos.marca.create');
    }

    public function store(MarcaStoreRequest $request)
    {
        $generator = new CorrelativeIdGenerator();
        $id        = $generator->generate('Marca');
        $marca     = new Marca();
        $marca->fill($request->validated());
        $marca->id = $id;
        $marca->save();
        return redirect()->route('marca')->with('success', 'Marca creada correctamente');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Marca $marca)
    {
        return view('modelos.marca.edit', compact('marca'));
    }

    public function update(MarcaUpdateRequest $request, Marca $marca)
    {
        $marca->update($request->validated());
        return redirect()->route('marca')->with('success', 'Marca actualizada correctamente');
    }

    public function destroy(Marca $marca)
    {
        if ($marca->modelos()->exists()) {
            $firstModelo = $marca->modelos()->select('modelo')->first();
            return back()->with('error', 'La marca no puede ser eliminada porque está asignada a el modelo: ' . ($firstModelo->modelo ?? ''));
        }
        try {
            $marca->delete();
        } catch (\Exception $e) {
            Log::error('Log:: [Usuario: ' . auth()->user()->name . '] Ocurrió un error cuando se intentaba eliminar la marca: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error cuando se intentaba eliminar la marca.');
        }
        return redirect()->route('marca')->with('success', 'La marca "' . $marca->marca . '" ha sido eliminada correctamente');
    }
    
    public function activate(Marca $marca)
    {
        $marca->activo = ! $marca->activo;
        $marca->save();
        return redirect()->route('marca')->with('success', 'La marca "' . $marca->marca . '" ha sido ' . ($marca->activo ? 'activada' : 'desactivada') . ' correctamente');
    }
}
