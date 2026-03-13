<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\Detalle;
use App\Models\OficinaStock;
use Illuminate\Support\Facades\Auth;

class StockService
{
    /**
     * Valida si existe stock suficiente en la bodega de la oficina para las órdenes recibidas.
     *
     * @param array $ordenes_recibidas
     * @param int|null $oficina_id
     * @return array|bool Retorna un array con 'fallos' si no hay stock, o true si todo está correcto.
     */
    public function validarDisponibilidad(array $ordenes_recibidas, $oficina_id = null)
    {
        $oficina_id = $oficina_id ?? Auth::user()->oficina_id;
        $stockBodega = Stock::where('stock', 'Bodega')->first();
        if (!$stockBodega) {
            return [
                'error' => true,
                'message' => 'No se encontró el stock "Bodega"',
                'status' => 500
            ];
        }
        $stockBodegaId = $stockBodega->id;
        $demandaTotal = [];
        foreach ($ordenes_recibidas as $ordenData) {
            $orden_id = $ordenData['orden_id'];
            $unidadesKit = (int) $ordenData['unidades'];
            $detallesRecibidos = $ordenData['detalles'] ?? [];
            if (empty($detallesRecibidos)) {
                $detallesRecibidos = Detalle::where('orden_id', $orden_id)->get();
            }
            foreach ($detallesRecibidos as $detalleData) {
                $productoId = $detalleData['producto_id_nuevo'] ?? $detalleData['producto_id'];
                $unidadesPorItem = Detalle::where('orden_id', $orden_id)
                    ->where('producto_id', $detalleData['producto_id_original'] ?? $detalleData['producto_id'])
                    ->value('unidades') ?? 0;
                
                $demandaTotal[$productoId] = ($demandaTotal[$productoId] ?? 0) + ($unidadesKit * $unidadesPorItem);
            }
        }
        $fallos = [];
        foreach ($demandaTotal as $productoId => $cantidadRequerida) {
            $oficinaStock = OficinaStock::where('oficina_id', $oficina_id)
                ->where('stock_id', $stockBodegaId)
                ->where('producto_id', $productoId)
                ->first();
            $disponible = $oficinaStock ? $oficinaStock->unidades : 0;
            if ($cantidadRequerida > $disponible) {
                $fallos[] = [
                    'producto_id' => $productoId,
                    'requerida' => $cantidadRequerida,
                    'disponible' => $disponible
                ];
            }
        }
        if (!empty($fallos)) {
            return [
                'error' => true,
                'fallos' => $fallos,
                'message' => 'Se encontraron peticiones que rebasan el stock actual',
                'status' => 422
            ];
        }
        return true;
    }
}
