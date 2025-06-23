<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = ['solicitud'];

    public function tareas()
    {
        return $this->belongsToMany(Tarea::class);
    }

    public function usuario_origen()
    {
        return $this->belongsToMany(User::class, 'recepciones', 'solicitud_id', 'user_id_origen');
    }

    public function usuario_destino()
    {
        return $this->belongsToMany(User::class, 'recepciones', 'solicitud_id', 'user_id_destino');
    }

}
