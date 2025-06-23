<?php

namespace App\Services;

use App\Models\Recepcion;

class IdGenerator {
    public function generate(): string {
        $maxCorrelativo = Recepcion::max('id');
        $maxCorrelativo = $maxCorrelativo ? substr($maxCorrelativo, -6) : null;
        $correlativo = $maxCorrelativo ? str_pad($maxCorrelativo + 1, 6, '0', STR_PAD_LEFT) : '000001';
        $anio_actual = date('Y');
        $id = $anio_actual . $correlativo;
        return $id;
    }
} 