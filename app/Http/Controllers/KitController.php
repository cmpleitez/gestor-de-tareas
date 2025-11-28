<?php

namespace App\Http\Controllers;

use App\Http\Requests\KitStoreRequest;
use App\Http\Requests\KitUpdateRequest;
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
        $kit->load('productos.alternativas');
        return view('modelos.kit.edit', compact('kit'));
    }

    public function update(KitUpdateRequest $request, Kit $kit)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated(); // Guardar Kit
            $productos = $data['producto'] ?? [];
            unset($data['producto']);
            $kit->update($data);
            $productosSync = []; // Guardar productos
            foreach ($productos as $productoId => $productoData) {
                if (isset($productoData['unidades'])) {
                    $productosSync[$productoId] = ['unidades' => $productoData['unidades']];
                }
            }
            $kit->productos()->sync($productosSync);

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

    public function actualizarProductos(Kit $kit, Request $request)
    {
        try {
            $nombre_automatico = Parametro::findOrFail(2)->valor; // Nombre automático
            if ($nombre_automatico == '1') {
                $nombre_creado = $this->sugerirNombreKit($kit, $request);
                if ($nombre_creado) {
                    $existe = \App\Models\Kit::where('kit', $nombre_creado)
                        ->where('id', '<>', $kit->id)
                        ->exists();
                    if ($existe) {
                        return redirect()->back()->with('info', 'El nombre sugerido para el kit ya existe, por favor revise los productos seleccionados.');
                    }
                }
                if ($nombre_creado) {
                    $kit->kit = $nombre_creado;
                    $kit->save();
                }
            }
            $kit->productos()->sync($request->productos);

            return redirect()->route('kit')->with('success', 'Kit actualizado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('info', 'Ocurrió un error al intentar actualizar el Kit: '.$e->getMessage());
        }
    }

    public function sugerirNombreKit(Kit $kit, Request $request)
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
}
