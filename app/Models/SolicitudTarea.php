<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudTarea extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_tareas';

    public function user_tarea()
    {
        return $this->hasMany(UserTarea::class, 'solicitud_tarea_id');
    }
}
