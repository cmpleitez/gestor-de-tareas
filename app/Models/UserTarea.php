<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTarea extends Model
{
    use HasFactory;
    protected $table = 'users_tareas';

    public function user_origen()
    {
        return $this->belongsTo(User::class, 'user_id_origen');
    }
    
    public function user_destino()
    {
        return $this->belongsTo(User::class, 'user_id_destino');
    }
    
    public function solicitud_tarea()
    {
        return $this->belongsTo(SolicitudTarea::class);
    }
}
