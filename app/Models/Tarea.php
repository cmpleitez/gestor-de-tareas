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

}
