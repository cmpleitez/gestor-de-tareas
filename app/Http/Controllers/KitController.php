<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kit;
use App\Http\Requests\KitStoreRequest;
use App\Http\Requests\KitUpdateRequest;
use App\Services\CorrelativeIdGenerator;
use App\Models\Producto;

class KitController extends Controller
{
    public function index()
    {
        $kits = Kit::orderBy('id', 'desc')->paginate(10);
        return view('modelos.kit.index', compact('kits'));
    }

    public function create()
    {
        return view('modelos.kit.create');
    }

    public function store(KitStoreRequest $request)
    {
        $generator = new CorrelativeIdGenerator();
        $id        = $generator->generate('Kit');
        $kit       = new Kit();
        $kit->fill($request->validated());
        $kit->id = $id;
        $kit->save();
        return redirect()->route('kit')->with('success', 'Kit creado correctamente');    
    }

    public function edit(Kit $kit)
    {
        return view('modelos.kit.edit', compact('kit'));
    }

    public function update(KitUpdateRequest $request, Kit $kit)
    {
        $kit->update($request->validated());
        return redirect()->route('kit')->with('success', 'Kit actualizado correctamente');
    }

    public function asignarProductos(Kit $kit)
    {
        $kit->load('productos');
        $productos = Producto::where('activo', true)
            ->with(['modelo', 'tipo'])
            ->orderByRaw("EXISTS(SELECT 1 FROM kit_producto WHERE kit_producto.kit_id = ? AND kit_producto.producto_id = productos.id) DESC", [$kit->id])
            ->get();
        $kitProductosIds = $kit->productos->pluck('id')->toArray();
        $productosChunks = $productos->chunk(2);
        return view('modelos.kit.asignar-productos', compact('kit', 'productos', 'kitProductosIds', 'productosChunks'));
    }

    public function actualizarProductos(Kit $kit, Request $request)
    {
        $kit->productos()->sync($request->productos);
        return redirect()->route('kit')->with('success', 'Kit actualizado correctamente');
    }

    public function destroy(Kit $kit)
    {
        if ($kit->productos()->exists()) {
            $firstProducto = $kit->productos()->select('producto')->first();
            return back()->with('error', 'El kit no puede ser eliminado porque está asignado al producto: ' . ($firstProducto->producto ?? ''));
        }
        if ($kit->atencionDetalles()->exists()) {
            $firstAtencionDetalle = $kit->atencionDetalles()->first();
            return back()->with('error', 'El kit no puede ser eliminado tiene historial de ordenes de compra');
        }
        try {
            $kit->delete();
            return redirect()->route('kit')->with('success', 'Kit eliminado correctamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error cuando se intentaba eliminar el kit: ' . $e->getMessage());
        }
    }

    public function activate(Kit $kit)
    {
        $kit->activo = ! $kit->activo;
        $kit->save();
        return redirect()->route('kit')->with('success', 'El kit "' . $kit->kit . '" ha sido ' . ($kit->activo ? 'activado' : 'desactivado') . ' correctamente');
    }
}
