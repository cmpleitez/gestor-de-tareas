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
        // Dividir en categoría (primeros 3) y correlativo (últimos 5)
        $categoria   = substr($id, 0, 3); // Primeros 3 dígitos
        $correlativo = substr($id, 3, 5); // Últimos 5 dígitos
        $categoriaLast2   = substr($categoria, -2);   // Últimos 2 de categoría
        $correlativoLast2 = substr($correlativo, -2); // Últimos 2 de correlativo
        // Si correlativo es "00", usar "100"
        if ($correlativoLast2 === '00') {
            $correlativoLast2 = '100';
        }
        // Concatenar: 2 de categoría + 2 de correlativo = 4 dígitos
        return $categoriaLast2 . $correlativoLast2;
    }
}
