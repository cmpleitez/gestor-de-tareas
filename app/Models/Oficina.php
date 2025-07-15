<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oficina extends Model
{
    use HasFactory;

    public function zona()
    {
        return $this->belongsTo(Zona::class);
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

    public function areas()
    {
        return $this->hasMany(Area::class);
    }

}
