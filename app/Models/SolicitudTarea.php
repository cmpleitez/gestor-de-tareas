<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudTarea extends Model
{
    use HasFactory;
    protected $table = 'solicitud_tarea';

    public function tarea()
    {
        return $this->belongsTo(Tarea::class);
    }

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }
}
