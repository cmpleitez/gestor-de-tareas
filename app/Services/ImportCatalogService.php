<?php

namespace App\Services;

use App\Models\Tipo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Kit;
use App\Models\Producto;
use App\Models\KitProducto;
use App\Models\Equivalente;
use App\Models\OficinaStock;
use App\Services\CorrelativeIdGenerator;
use Spatie\SimpleExcel\SimpleExcelReader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportCatalogService
{
    protected $idGenerator;

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

    /**
     * Procesa una única fila del Excel.
     */
    private function processRow(array $row)
    {
        // 1. Tipo
        $tipo = Tipo::where('tipo', $row['tipo'])->first();
        if (!$tipo) {
            $tipo = new Tipo();
            $tipo->id = $this->idGenerator->generate('Tipo');
            $tipo->tipo = $row['tipo'];
            $tipo->save();
        }

        // 2. Marca
        $marca = Marca::where('marca', $row['marca'])->first();
        if (!$marca) {
            $marca = new Marca();
            $marca->id = $this->idGenerator->generate('Marca');
            $marca->marca = $row['marca'];
            $marca->save();
        }

        // 3. Modelo
        $modelo = Modelo::where('modelo', $row['modelo'])->where('marca_id', $marca->id)->first();
        if (!$modelo) {
            $modelo = new Modelo();
            $modelo->id = $this->idGenerator->generate('Modelo');
            $modelo->modelo = $row['modelo'];
            $modelo->marca_id = $marca->id;
            $modelo->save();
        }

        // 4. Kit
        $kit = Kit::where('kit', $row['kit'])->first();
        if (!$kit) {
            $kit = new Kit();
            $kit->id = $this->idGenerator->generate('Kit');
            $kit->kit = $row['kit'];
            $kit->save();
        }

        // 5. Producto
        $productoId = !empty($row['id']) ? (int)$row['id'] : null;
        
        if ($productoId) {
            $producto = Producto::find($productoId);
            if (!$producto) {
                $producto = new Producto();
                $producto->id = $productoId;
            }
        } else {
            $producto = Producto::where('producto', $row['producto'])->first();
            if (!$producto) {
                $producto = new Producto();
                $producto->id = $this->idGenerator->generate('Producto');
            }
        }

        $producto->producto = $row['producto'];
        $producto->codigo   = $row['producto_codigo'] ?? null;
        $producto->precio   = $row['producto_precio'] ?? 0;
        $producto->modelo_id = $modelo->id;
        $producto->tipo_id   = $tipo->id;
        $producto->save();

        // 6. Relación Kit-Producto (Tabla Pivot)
        $kitProductoData = DB::table('kit_producto')
            ->where('kit_id', $kit->id)
            ->where('producto_id', $producto->id)
            ->first();

        if ($kitProductoData) {
            DB::table('kit_producto')
                ->where('id', $kitProductoData->id)
                ->update([
                    'unidades' => $row['kit_unidades'] ?? 1,
                    'updated_at' => now(),
                ]);
            $kitProductoId = $kitProductoData->id;
        } else {
            $kitProductoId = $this->idGenerator->generate('KitProducto');
            DB::table('kit_producto')->insert([
                'id' => $kitProductoId,
                'kit_id' => $kit->id,
                'producto_id' => $producto->id,
                'unidades' => $row['kit_unidades'] ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 7. Stock en Bodega (stock_id = 2, oficina_id = 1)
        if (!empty($row['stock_unidades'])) {
            $stock = OficinaStock::where('oficina_id', 1)
                ->where('stock_id', 2)
                ->where('producto_id', $producto->id)
                ->first();

            if (!$stock) {
                $stock = new OficinaStock();
                $stock->oficina_id = 1;
                $stock->stock_id = 2;
                $stock->producto_id = $producto->id;
            }
            $stock->unidades = (int)$row['stock_unidades'];
            $stock->save();
        }

        // 8. Equivalentes
        if (!empty($row['equivalente_producto_codigo'])) {
            $productoEq = Producto::where('codigo', $row['equivalente_producto_codigo'])->first();
            
            if ($productoEq) {
                DB::table('equivalentes')->updateOrInsert(
                    [
                        'kit_producto_id' => $kitProductoId,
                        'producto_id'     => $productoEq->id,
                        'kit_id'          => $kit->id,
                    ],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            }
        }
    }
}
