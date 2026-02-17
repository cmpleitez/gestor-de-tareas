<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use App\Models\Producto;
use App\Models\Modelo;
use App\Models\Tipo;
use App\Services\CorrelativeIdGenerator;
use App\Http\Requests\ProductoStoreRequest;
use App\Http\Requests\ProductoUpdateRequest;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{

    public function index()
    {
        $productos = Producto::all();
        return view('modelos.producto.index', compact('productos'));
    }

    public function create()
    {
        $modelos = Modelo::where('activo', true)->get();
        $tipos = Tipo::where('activo', true)->get();
        return view('modelos.producto.create', compact('modelos', 'tipos'));
    }

    public function store(ProductoStoreRequest $request)
    {
        $generator = new CorrelativeIdGenerator();
        $id        = $generator->generate('Producto');
        $producto     = new Producto();
        $producto->fill($request->validated());
        $producto->id = $id;
        $producto->save();
        return redirect()->route('producto')->with('success', 'Producto creado correctamente');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Producto $producto)
    {
        $modelos = Modelo::where('activo', true)->get();
        $tipos = Tipo::where('activo', true)->get();
        return view('modelos.producto.edit', compact('producto', 'modelos', 'tipos'));
    }

    public function update(ProductoUpdateRequest $request, Producto $producto)
    {
        $producto->fill($request->validated());
        $producto->save();
        return redirect()->route('producto')->with('success', 'Producto actualizado correctamente');
    }

    public function destroy(Producto $producto)
    {
        if ($producto->movimientos()->exists()) {
            $firstMovimiento = $producto->movimientos()->select('movimiento')->first();
            return back()->with('error', 'El producto no puede ser eliminado porque está asignado a el movimiento: ' . ($firstMovimiento->movimiento ?? ''));
        }

        $oficinaStock = $producto->oficinaStock()->exists();
        if ($oficinaStock) {
            $firstOficinaStock = $producto->oficinaStock()->first();
            return back()->with('error', 'El producto no puede ser eliminado porque está asignado a la oficina: ' . ($firstOficinaStock->oficina->oficina ?? ''));
        }
        $atencionDetalles = $producto->atencionDetalles()->exists();
        if ($atencionDetalles) {
            return back()->with('error', 'El producto no puede ser eliminado porque tiene historial de ordenes de compra');
        }
        if ($producto->kits()->count() > 0) {
            $firstKit = $producto->kits()->first();
            return back()->with('error', 'El producto no puede ser eliminado porque está asignado a el Kit: ' . ($firstKit->kit ?? ''));
        }
        try {
            $producto->delete();
        } catch (\Exception $e) {
            Log::error('Log:: Ocurrió un error cuando se intentaba eliminar el producto: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Ocurrió un error cuando se intentaba eliminar el producto.');
        }
        return redirect()->route('producto')->with('success', 'El producto "' . $producto->producto . '" ha sido eliminado correctamente');
    }

    public function activate(Producto $producto)
    {
        $producto->activo = ! $producto->activo;
        $producto->save();
        return redirect()->route('producto')->with('success', 'El producto "' . $producto->producto . '" ha sido ' . ($producto->activo ? 'activado' : 'desactivado') . ' correctamente');
    }

}
