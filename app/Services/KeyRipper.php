<?php

namespace App\Services;

class KeyRipper
{
    public static function rip($id): string
    {
        $id = (string)$id;
        // Tomar los últimos 8 dígitos
        $last8 = substr($id, -8);
        // Correlativo: últimos 3 dígitos
        $correlativo = ltrim(substr($last8, 5, 3), '0');
        // tipoSolicitud: dígitos 2 y 3 (índices 1 y 2)
        $tipoSolicitud = ltrim(substr($last8, 1, 2), '0');
        // Unir ambos como texto
        $result = $tipoSolicitud . $correlativo;
        // Limitar a 5 dígitos, rellenar con ceros a la izquierda si es necesario
        $result = str_pad(substr($result, 0, 5), 5, '0', STR_PAD_LEFT);
        return $result;
    }
} 