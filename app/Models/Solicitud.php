<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = ['solicitud'];

    public function tareas_asociadas()
    {
        return $this->belongsToMany(Tarea::class);
    }

    public function conceptos()
    {
        return $this->hasMany(SolicitudTarea::class);
    }

}
