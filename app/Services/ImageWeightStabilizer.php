<?php
namespace App\Services;

use Exception;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageWeightStabilizer
{
    /**
     * Procesa y optimiza una imagen, guardándola y actualizando el modelo
     *
     * @param \Illuminate\Http\UploadedFile $file Archivo de imagen subido
     * @param string $destinationPath Ruta completa de destino (ej: storage_path('app/public/user-images'))
     * @param string $modelName Nombre del modelo (ej: 'User')
     * @param int $modelId ID del registro del modelo
     * @return void
     * @throws Exception
     */
    public function stabilize($file, string $destinationPath, string $modelName, int $modelId): void
    {
        // Generar nombre del archivo: {modelId}.{extension}
        $imageName = $modelId . '.' . $file->getClientOriginalExtension();

        // Procesar imagen ANTES de guardar para hacerlo más rápido
        $manager = new ImageManager(Driver::class);
        $image   = $manager->read($file->getRealPath());
        $image->scale(width: 200); // Escalar a máximo 200px de ancho (mantiene proporción)

        // Guardar imagen ya optimizada
        if (! file_exists($destinationPath)) {
            mkdir($destinationPath, 0775, true);
        }
        $fullPath = $destinationPath . '/' . $imageName;
        $image->save($fullPath, quality: 75);

        // Generar nombres dinámicos basados en el nombre del modelo
        $modelNameLower = strtolower($modelName);
        $fieldName      = 'image_path';                    // Campo estándar para todos los modelos
        $routePrefix    = $modelNameLower . '-images/';    // ej: 'user-images/'

        // Actualizar path en BD
        $modelClass = "App\\Models\\{$modelName}";
        if (! class_exists($modelClass)) {
            throw new Exception("Modelo {$modelName} no existe");
        }

        $model = $modelClass::find($modelId);
        if (! $model) {
            throw new Exception("Registro con ID {$modelId} no encontrado en el modelo {$modelName}");
        }

        // Asignar campo dinámico con ruta relativa dinámica
        $model->$fieldName = $routePrefix . $imageName;
        $model->save();
    }
}

