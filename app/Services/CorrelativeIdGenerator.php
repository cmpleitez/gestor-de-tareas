<?php
namespace App\Services;

class CorrelativeIdGenerator
{
    public function generate(string $model): int
    {
        $modelClass = "App\\Models\\{$model}"; // Crear instancia del modelo dinámicamente
        if (! class_exists($modelClass)) {
            throw new \Exception("Modelo {$model} no existe");
        }
        $maxId = $modelClass::max('id'); // Buscar el máximo ID existente
        if ($maxId) {                    // Generar nuevo ID correlativo
            return $maxId + 1;
        }
        return 1; // Primer ID
    }
}
