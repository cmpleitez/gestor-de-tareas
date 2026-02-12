<?php
use App\Models\Recepcion;
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$r = Recepcion::whereHas('atencion', function($q){ 
    $q->where('id', '202600100005'); 
})->first();

if($r){ 
    $r->estado_id = 3; 
    $r->save(); 
    echo "Reset order #0105 (Atencion 202600100005) to in-progress (status 3)\n";
} else {
    echo "Order #0105 not found\n";
}
