<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;

    protected $fillable = ['tarea'];

    public function solicitudes()
    {
        return $this->belongsToMany(Solicitud::class);
    }

    public function usuarios_origen()
    {
        return $this->belongsToMany(User::class, 'actividades', 'user_id_origen');
    }

    public function usuarios_destino()
    {
        return $this->belongsToMany(User::class, 'actividades', 'user_id_destino');
    }
}
