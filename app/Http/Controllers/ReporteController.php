<?php

namespace App\Http\Controllers;

use App\Support\SecurityReport;

class ReporteController extends Controller
{
    public function indicadoresSeguridad()
    {
        return view('reportes.indicadores-seguridad', ['security' => SecurityReport::data()]); // Solo lectura
    }
}
