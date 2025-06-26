<?php

namespace App\Services;

use App\Models\Recepcion;

class AtencionIdGenerator {
    public function generate(int $user_id_origen, int $solicitud_id): string {
        $anio_actual = date('Y'); // 4 dígitos
        $user_id_padded = str_pad($user_id_origen, 5, '0', STR_PAD_LEFT); // 5 dígitos
        $solicitud_id_padded = str_pad($solicitud_id, 3, '0', STR_PAD_LEFT); // 3 dígitos
        
        // Buscar el máximo correlativo para la misma combinación de año, user_id y solicitud_id
        $patron_busqueda = $anio_actual . $user_id_padded . $solicitud_id_padded;
        $maxCorrelativo = Recepcion::whereNotNull('atencion_id')
            ->where('atencion_id', 'LIKE', $patron_busqueda . '%')
            ->max('atencion_id');
        
        if ($maxCorrelativo) {
            // Extraer los últimos 2 dígitos (correlativo)
            $correlativo_actual = (int)substr($maxCorrelativo, -2);
            $nuevo_correlativo = str_pad($correlativo_actual + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $nuevo_correlativo = '01'; // Primer correlativo
        }
        
        $id = $anio_actual . $user_id_padded . $solicitud_id_padded . $nuevo_correlativo;
        return $id;
    }
} 