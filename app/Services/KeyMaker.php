<?php
namespace App\Services;

class KeyMaker
{
    public function generate(string $model, string $solicitud_id): string
    {
        $anio_actual         = date('Y');                                    // 4 dígitos
        $solicitud_id_padded = str_pad($solicitud_id, 3, '0', STR_PAD_LEFT); // 3 dígitos
        $patron_busqueda     = $anio_actual . $solicitud_id_padded;          // Buscar el máximo correlativo para la misma combinación de año y solicitud_id
        $modelClass          = "App\\Models\\{$model}";                      // Crear instancia del modelo dinámicamente
        if (! class_exists($modelClass)) {
            throw new \Exception("Modelo {$model} no existe");
        }
        $maxCorrelativo = $modelClass::whereNotNull('id')
            ->where('id', 'LIKE', $patron_busqueda . '%')
            ->max('id');
        if ($maxCorrelativo) {
            $correlativo_actual = (int) substr($maxCorrelativo, -5); // Extraer los últimos 5 dígitos (correlativo)
            $nuevo_correlativo  = str_pad($correlativo_actual + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $nuevo_correlativo = '00001'; // Primer correlativo
        }
        $id = $anio_actual . $solicitud_id_padded . $nuevo_correlativo;
        return $id;
    }
}
