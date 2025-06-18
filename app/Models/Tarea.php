<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;

    protected $fillable = ['tarea'];

    public function solicitudes_asociadas()
    {
        return $this->belongsToMany(Solicitud::class);
    }

    public function conceptos()
    {
        return $this->hasMany(SolicitudTarea::class);
    }

}
