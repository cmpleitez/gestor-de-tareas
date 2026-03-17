<?php

namespace App\Services;

use App\Models\Tipo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Kit;
use App\Models\Producto;
use App\Models\OficinaStock;
use App\Models\Oficina;
use App\Services\CorrelativeIdGenerator;
use Spatie\SimpleExcel\SimpleExcelReader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportCatalogService
{
    protected $idGenerator;
    protected $oficinaId;

    public function __construct(CorrelativeIdGenerator $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    /**
     * Procesa la importación del catálogo desde un archivo Excel.
     *
     * @param string $filePath Ruta absoluta o relativa al archivo Excel.
     * @return array Resumen de la importación.
     */
    public function import(string $filePath): array
    {
        $rowsProcessed = 0;
        $errors = [];
        $oficina = Oficina::where('oficina', 'Mostro')->first(); //Parametrización: oficinas
        if (!$oficina) {
            throw new \Exception("No se encontró ninguna oficina registrada para realizar la importación.");
        }
        $this->oficinaId = $oficina->id;

        try {
            $reader = SimpleExcelReader::create($filePath);
            DB::beginTransaction();
                $reader->getRows()->each(function (array $row) use (&$rowsProcessed, &$errors) {
                    try {
                        $this->processRow($row);
                        $rowsProcessed++;
                    } catch (\Exception $e) {
                        $errors[] = "Error en fila " . ($rowsProcessed + 1) . ": " . $e->getMessage();
                        Log::error("Import Error on Row " . ($rowsProcessed + 1), [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                            'row' => $row
                        ]);
                    }
                });
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Critical Import Failure: " . $e->getMessage());
            throw $e;
        }
        return [
            'total' => $rowsProcessed,
            'errors' => $errors,
        ];
    }

    //Sub-proceso una fila por vez
    private function processRow(array $row)
    {
        $tipo = Tipo::where('tipo', $row['tipo'])->first(); // 1. Tipo
        if (!$tipo) {
            $tipo = new Tipo();
            $tipo->id = $this->idGenerator->generate('Tipo');
            $tipo->tipo = $row['tipo'];
            $tipo->save();
        }
        $marca = Marca::where('marca', $row['marca'])->first(); // 2. Marca
        if (!$marca) {
            $marca = new Marca();
            $marca->id = $this->idGenerator->generate('Marca');
            $marca->marca = $row['marca'];
            $marca->save();
        }
        $modelo = Modelo::where('modelo', $row['modelo'])->where('marca_id', $marca->id)->first(); // 3. Modelo
        if (!$modelo) {
            $modelo = new Modelo();
            $modelo->id = $this->idGenerator->generate('Modelo');
            $modelo->modelo = $row['modelo'];
            $modelo->marca_id = $marca->id;
            $modelo->save();
        }
        $kit = Kit::where('kit', $row['kit'])->first(); // 4. Kit
        if (!$kit) {
            $kit = new Kit();
            $kit->id = $this->idGenerator->generate('Kit');
            $kit->kit = $row['kit'];
            $kit->save();
        }
        $producto = Producto::where('producto', $row['producto'])->first(); // 5. Producto (Identificado por nombre, ID autogenerado si es nuevo)
        if (!$producto) {
            $producto = new Producto();
            $producto->id = $this->idGenerator->generate('Producto');
        }
        $producto->producto = $row['producto'];
        $producto->codigo   = $row['producto_codigo'] ?? null;
        $producto->precio   = $row['producto_precio'] ?? 0;
        $producto->modelo_id = $modelo->id;
        $producto->tipo_id   = $tipo->id;
        $producto->save();
        $kitProductoData = DB::table('kit_producto') // 6. Relación Kit-Producto (Tabla Pivot)
            ->where('kit_id', $kit->id)
            ->where('producto_id', $producto->id)
            ->first();
        if ($kitProductoData) {
            $kitProductoId = $kitProductoData->id;
            DB::table('kit_producto')
                ->where('id', $kitProductoId)
                ->update([
                    'unidades' => $row['kit_producto_unidades'] ?? 1,
                    'updated_at' => now(),
                ]);
        } else {
            $kitProductoId = $this->idGenerator->generate('KitProducto');
            DB::table('kit_producto')->insert([
                'id' => $kitProductoId,
                'kit_id' => $kit->id,
                'producto_id' => $producto->id,
                'unidades' => $row['kit_producto_unidades'] ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        if (!empty($row['stock_unidades'])) { // 7. Stock en Bodega (stock_id = 2, oficina_id dinámico)
            $stock = OficinaStock::where('oficina_id', $this->oficinaId)
                ->where('stock_id', 2)
                ->where('producto_id', $producto->id)
                ->first();

            if (!$stock) {
                $stock = new OficinaStock();
                $stock->oficina_id = $this->oficinaId;
                $stock->stock_id = 2;
                $stock->producto_id = $producto->id;
            }
            $stock->unidades = (int)$row['stock_unidades'];
            $stock->save();
        }
        if (!empty($row['equivalente_producto_codigo'])) { // 8. Equivalentes (Búsqueda por código de producto)
            $productoEq = Producto::where('codigo', $row['equivalente_producto_codigo'])->first();
            if ($productoEq) {
                DB::table('equivalentes')->updateOrInsert(
                    [
                        'kit_producto_id' => $kitProductoId, // ( El kit_producto_id ya lo tenemos del paso 6 (pertenece al producto principal de la fila) )
                        'producto_id'     => $productoEq->id,
                        'kit_id'          => $kit->id,
                    ],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            }
        }
        $totalKit = DB::table('kit_producto') // 9. Calcular y actualizar el precio total del Kit
            ->join('productos', 'kit_producto.producto_id', '=', 'productos.id')
            ->where('kit_producto.kit_id', $kit->id)
            ->selectRaw('SUM(kit_producto.unidades * productos.precio) as total')
            ->value('total');
        $kit->precio = $totalKit ?? 0;
        $kit->save();
    }
}
