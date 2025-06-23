<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oficina extends Model
{
    use HasFactory;

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function recepciones()
    {
        return $this->hasMany(Recepcion::class);
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class);
    }
}
