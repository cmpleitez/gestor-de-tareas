<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Recepcion;

echo "=== Consultando primera tarea de una recepción ===\n";

// Obtener la primera recepción
$recepcion = Recepcion::first();

if (!$recepcion) {
    echo "❌ No hay recepciones en la base de datos\n";
    exit;
}

echo "✅ Recepción encontrada: {$recepcion->id}\n";
echo "   Número de atención: {$recepcion->atencion_id}\n";

// Verificar si tiene solicitud
if (!$recepcion->solicitud) {
    echo "❌ La recepción no tiene solicitud asociada\n";
    exit;
}

echo "✅ Solicitud asociada: {$recepcion->solicitud->solicitud}\n";

// Obtener las tareas de la solicitud
$tareas = $recepcion->solicitud->tareas;

if ($tareas->isEmpty()) {
    echo "❌ La solicitud no tiene tareas asociadas\n";
    exit;
}

echo "✅ Total de tareas: {$tareas->count()}\n";

// Mostrar la primera tarea
$primera_tarea = $tareas->first();
echo "\n=== PRIMERA TAREA ===\n";
echo "ID: {$primera_tarea->id}\n";
echo "Tarea: {$primera_tarea->tarea}\n";
echo "Descripción: {$primera_tarea->descripcion}\n";

// Mostrar todas las tareas
echo "\n=== TODAS LAS TAREAS ===\n";
foreach ($tareas as $index => $tarea) {
    echo ($index + 1) . ". {$tarea->tarea}\n";
}

echo "\n=== Consulta completada ===\n"; 