<?php
namespace App\Services;

class KeyRipper
{
    public static function rip($id): string
    {
        if (empty($id)) { // Validación de entrada
            return '000';
        }
        $id = (string) $id;
        if (strlen($id) >= 12) { // Si tiene 12 dígitos o más, ignorar los primeros 4 (año) y tomar los siguientes 8
            $id = substr($id, 4, 8); // Ignorar año, tomar 8 dígitos siguientes
        } elseif (strlen($id) < 8) {
            $id = str_pad($id, 8, '0', STR_PAD_LEFT); // Si tiene menos de 8 dígitos, rellenar con ceros
        }
        $last2 = substr($id, -2); // Tomar los últimos 2 dígitos
        if ($last2 === '00') { // Si es "00", devolver "100"
            return '100';
        }
        return $last2; // Retornar los últimos 2 dígitos
    }
}
