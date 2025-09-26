<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType   = 'int';

    protected $fillable = ['tarea'];

    public function solicitudes()
    {
        return $this->belongsToMany(Solicitud::class);
    }

    public function usuariosOrigen()
    {
        return $this->belongsToMany(User::class, 'actividades', 'user_id_origen');
    }

    public function usuariosDestino()
    {
        return $this->belongsToMany(User::class, 'actividades', 'user_id_destino');
    }
}
