<?php
namespace App\Http\Controllers;

use App\Http\Requests\KitStoreRequest;
use App\Http\Requests\KitUpdateRequest;
use App\Models\Equivalente;
use App\Models\Kit;
use App\Models\Parametro;
use App\Models\Producto;
use App\Services\CorrelativeIdGenerator;
use App\Services\ImageWeightStabilizer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

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
        try {
            DB::beginTransaction();
            $generator = new CorrelativeIdGenerator; // Registrar Kit
            $id = $generator->generate('Kit');
            $kit = new Kit;
            $kit->fill($request->validated());
            $kit->id = $id;
            $kit->save();
            if (isset($request->image_path) && $request->image_path->isValid()) { // Procesar imagen de perfil si existe
                $imageStabilizer = new ImageWeightStabilizer;
                $imageStabilizer->stabilize(
                    $request->image_path,
                    storage_path('app/public/kit-images'),
                    'Kit',
                    $kit->id
                );
            }
            DB::commit();
            return redirect()->route('kit')->with('success', 'Kit creado correctamente');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Ocurrió un error cuando se intentaba registrar el Kit: '.$e->getMessage());
        }
    }

    public function edit(Kit $kit)
    {
        $kit->load(['productos.kitProductos.equivalentes' => function ($query) use ($kit) {
            $query->where('kit_id', $kit->id);
        }]);
        $productos = Producto::where('activo', true)->get();
        return view('modelos.kit.edit', compact('kit', 'productos'));
    }

    public function update(KitUpdateRequest $request, Kit $kit)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated(); // Actualizar Kit
            $productos = $data['producto'] ?? [];
            unset($data['producto']);
            $kit->update($data);
            if (isset($request->image_path) && $request->image_path->isValid()) {
                $imageStabilizer = new ImageWeightStabilizer;
                $imageStabilizer->stabilize(
                    $request->image_path,
                    storage_path('app/public/kit-images'),
                    'Kit',
                    $kit->id
                );
            }
            $this->sincronizarProductosConIds($kit, $productos);
            DB::commit();
            return redirect()->route('kit')->with('success', 'Kit creado correctamente');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Ocurrió un error cuando se intentaba registrar el Kit: '.$e->getMessage());
        }
        return redirect()->route('kit')->with('success', 'Kit actualizado correctamente');
    }

    public function asignarProductos(Kit $kit)
    {
        $kit->load('productos');
        $productos = Producto::where('activo', true)
            ->with(['modelo', 'tipo'])
            ->orderByRaw('EXISTS(SELECT 1 FROM kit_producto WHERE kit_producto.kit_id = ? AND kit_producto.producto_id = productos.id) DESC', [$kit->id])
            ->get();
        $kitProductosIds = $kit->productos->pluck('id')->toArray();
        $productosChunks = $productos->chunk(2);
        return view('modelos.kit.asignar-productos', compact('kit', 'productos', 'kitProductosIds', 'productosChunks'));
    }

    public function sincronizarProductos(Kit $kit, Request $request)
    {
        try {
            DB::beginTransaction();
            $nombre_automatico = Parametro::findOrFail(2)->valor; // Nombre automático del Kit
            if ($nombre_automatico == '1') {
                $nombre_creado = $this->sugerirNombreKit($kit, $request);
                if ($nombre_creado) {
                    $existe = Kit::where('kit', $nombre_creado)
                        ->where('id', '<>', $kit->id)
                        ->exists();
                    if ($existe) {
                        DB::rollback();
                        return redirect()->back()->with('info', 'El nombre sugerido para el kit ya existe, por favor revise los productos seleccionados.');
                    }
                }
                if ($nombre_creado) {
                    $kit->kit = $nombre_creado;
                    $kit->save();
                }
            }
            $this->sincronizarProductosConIds($kit, $request->productos ?? []); // Sincronizar productos del Kit
            DB::commit();
            return redirect()->route('kit')->with('success', 'Kit actualizado correctamente');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('info', 'Ocurrió un error al intentar actualizar el Kit: '.$e->getMessage());
        }
    }

    public function storeEquivalente(Kit $kit, Request $request)
    {

        \Log::info(['ID llave entidad' =>$request->kit_producto_id, 'ID producto' => $request->producto_id, 'ID kit' => $kit->id]);

        $request->validate([
            'producto_id' => 'required|exists:productos,id',
        ]);
        try {
            $equivalente = Equivalente::where('kit_id', $kit->id) //aqui hay que analizar una matematica para ver como deberian funcionar su kit_producto_id y su llave principal en kit_producto
                ->where('kit_producto_id', $request->kit_producto_id)
                ->where('producto_id', $request->producto_id)
                ->first();
            if ($equivalente) {
                return redirect()->back()->with('warning', 'Este producto ya está asignado como equivalente');
            }
            DB::beginTransaction();
                $equivalente = new Equivalente;
                $equivalente->kit_id = $kit->id; // Usar $kit->id del route model binding, no $request->kit_id
                $equivalente->kit_producto_id = $request->kit_producto_id;
                $equivalente->producto_id = $request->producto_id;
                $equivalente->save();
            DB::commit();


            \Log::info(['ID llave entidad' =>$equivalente->kit_producto_id, 'ID producto' => $equivalente->producto_id, 'ID kit' => $equivalente->kit_id]);
            
            return redirect()->back()->with('success', 'El producto se agregó como equivalente');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Ocurrió un error cuando se intentaba actualizar el producto equivalente: '.$e->getMessage());
        }
    }

    public function destroyEquivalente(Kit $kit, Equivalente $equivalente)
    {
        try {
            $equivalente->delete();
            return redirect()->route('kit.asignar-productos', $kit->id)->with('success', 'El producto se elimino correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocurrió un error cuando se intentaba eliminar el producto equivalente: '.$e->getMessage());
        }
    }

    public function destroy(Kit $kit)
    {
        if ($kit->productos()->exists()) {
            $firstProducto = $kit->productos()->select('producto')->first();
            return back()->with('error', 'El kit no puede ser eliminado porque está asignado al producto: '.($firstProducto->producto ?? ''));
        }
        if ($kit->atencionDetalles()->exists()) {
            $firstAtencionDetalle = $kit->atencionDetalles()->first();
            return back()->with('error', 'El kit no puede ser eliminado tiene historial de ordenes de compra');
        }
        try {
            $kit->delete();
            return redirect()->route('kit')->with('success', 'Kit eliminado correctamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error cuando se intentaba eliminar el kit: '.$e->getMessage());
        }
    }

    public function activate(Kit $kit)
    {
        $kit->activo = ! $kit->activo;
        $kit->save();
        return redirect()->route('kit')->with('success', 'El kit "'.$kit->kit.'" ha sido '.($kit->activo ? 'activado' : 'desactivado').' correctamente');
    }


    private function sincronizarProductosConIds(Kit $kit, array $productosNuevos)
    {
        $productosActuales = DB::table('kit_producto') // Obtener productos actuales del kit
            ->where('kit_id', $kit->id)
            ->pluck('producto_id')
            ->toArray();
        $productosNuevosIds = [];
        foreach ($productosNuevos as $productoId) {
            if (is_numeric($productoId)) {
                $productosNuevosIds[] = (int) $productoId;
            }
        }
        $productosNuevosIds = array_unique($productosNuevosIds);
        $productosAAgregar = array_diff($productosNuevosIds, $productosActuales);
        $productosAEliminar = array_diff($productosActuales, $productosNuevosIds);
        $generator = new CorrelativeIdGenerator;
        foreach ($productosAAgregar as $productoId) { // Agregar productos nuevos
            if (! Producto::find($productoId)) {
                continue;
            }
            $kitProductoId = $generator->generate('KitProducto');
            $unidades = 1;
            DB::table('kit_producto')->insert([
                'id' => $kitProductoId,
                'kit_id' => $kit->id,
                'producto_id' => $productoId,
                'unidades' => $unidades,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        if (! empty($productosAEliminar)) { // Eliminar productos que ya no están
            DB::table('kit_producto')
                ->where('kit_id', $kit->id)
                ->whereIn('producto_id', $productosAEliminar)
                ->delete();
        }
    }

    private function sugerirNombreKit(Kit $kit, Request $request)
    {
        if (! $request->has('productos') || empty($request->productos)) { // Validar que haya productos en la solicitud
            return false;
        }
        $productos = Producto::whereIn('id', $request->productos)->pluck('producto')->toArray(); // Producto promedio
        $palabras_productos = [];
        foreach ($productos as $producto) {
            $palabras = explode(' ', $producto);
            $palabras_productos = array_merge($palabras_productos, $palabras);
        }
        $palabras_filtradas = array_filter($palabras_productos, function ($palabra) {
            return strlen($palabra) > 4;
        });
        $conteo_palabras = array_count_values($palabras_filtradas);
        $producto_promedio = ! empty($conteo_palabras) ? array_search(max($conteo_palabras), $conteo_palabras) : '';

        $modelos = Producto::whereIn('id', $request->productos)->with('modelo')->get()->pluck('modelo.modelo')->toArray(); // Modelo promedio
        $palabras_modelos = [];
        foreach ($modelos as $modelo) {
            $palabras = explode(' ', $modelo);
            $palabras_modelos = array_merge($palabras_modelos, $palabras);
        }
        $palabras_filtradas = array_filter($palabras_modelos, function ($palabra) {
            return strlen($palabra) > 4;
        });
        $conteo_palabras = array_count_values($palabras_filtradas);
        $modelo_promedio = ! empty($conteo_palabras) ? array_search(max($conteo_palabras), $conteo_palabras) : '';
        if (empty($producto_promedio) && empty($modelo_promedio)) { // Validar que al menos uno de los promedios no esté vacío para generar un nombre válido
            return false;
        }
        $nuevo_nombre = ''; // Construir el nombre solo con las partes que tienen valor
        if (! empty($producto_promedio) && ! empty($modelo_promedio)) {
            $nuevo_nombre = $producto_promedio.' de '.$modelo_promedio;
        } elseif (! empty($producto_promedio)) {
            $nuevo_nombre = $producto_promedio;
        } elseif (! empty($modelo_promedio)) {
            $nuevo_nombre = $modelo_promedio;
        }
        if (! empty($nuevo_nombre) && mb_strlen($nuevo_nombre) > 0) { // Verificar que el $nuevo_nombre comience con inicial mayuscula, si comienza con minuscula se la cambia a mayuscula
            $nuevo_nombre = mb_strtoupper(mb_substr($nuevo_nombre, 0, 1)).mb_substr($nuevo_nombre, 1);
        }
        if (empty(trim($nuevo_nombre))) { // Validar que el nombre generado no sea solo espacios o esté vacío
            return false;
        }
        return $nuevo_nombre;
    }

}
