<?php

namespace App\Services;

use App\Models\Recepcion;

class AtencionIdGenerator {
    public function generate(int $solicitud_id): string {
        $anio_actual = date('Y'); // 4 dígitos
        $solicitud_id_padded = str_pad($solicitud_id, 3, '0', STR_PAD_LEFT); // 3 dígitos
        $patron_busqueda = $anio_actual . $solicitud_id_padded; // Buscar el máximo correlativo para la misma combinación de año y solicitud_id
        $maxCorrelativo = Recepcion::whereNotNull('atencion_id')
            ->where('atencion_id', 'LIKE', $patron_busqueda . '%')
            ->max('atencion_id');
        if ($maxCorrelativo) {
            $correlativo_actual = (int)substr($maxCorrelativo, -7); // Extraer los últimos 7 dígitos (correlativo)
            $nuevo_correlativo = str_pad($correlativo_actual + 1, 7, '0', STR_PAD_LEFT);
        } else {
            $nuevo_correlativo = '0000001'; // Primer correlativo
        }
        
        $id = $anio_actual . $solicitud_id_padded . $nuevo_correlativo;
        return $id;
    }
} 