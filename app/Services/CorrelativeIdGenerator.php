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
        $maxId = $modelClass::query()->lockForUpdate()->max('id'); // Lectura con bloqueo: dentro de una transacción serializa generadores concurrentes y evita IDs duplicados
        return ($maxId ?? 0) + 1;
    }
}
