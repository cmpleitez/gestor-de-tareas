<?php

namespace App\Services;

class KeyRipper
{
    public static function rip($id): string
    {
        // Validación de entrada
        if (empty($id)) {
            return '0000';
        }
        
        $id = (string)$id;
        
        // Asegurar que tenga al menos 8 dígitos
        if (strlen($id) < 8) {
            $id = str_pad($id, 8, '0', STR_PAD_LEFT);
        }
        
        // Extraer los últimos 8 dígitos
        $last8 = substr($id, -8);
        
        // Dividir en dos partes: primeros 3 dígitos y últimos 5 dígitos
        $solicitudPart = substr($last8, 0, 3);  // Primeros 3 dígitos
        $correlativoPart = substr($last8, 3, 5); // Últimos 5 dígitos
        
        // Extraer los últimos 2 dígitos de cada parte
        $tipoSolicitud = substr($solicitudPart, -2);
        $correlativo = substr($correlativoPart, -2);
        
        // Concatenar como texto
        $result = $tipoSolicitud . $correlativo;
        
        // Excepción: si el resultado es "0000", devolver "10000"
        if ($result === '0000') {
            return '10000';
        }
        
        // Retornar el resultado de 4 dígitos
        return $result;
    }
} 