<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory;
    public function recepciones()
    {
        return $this->hasMany(Recepcion::class);
    }

    public function atenciones()
    {
        return $this->hasMany(Atencion::class);
    }
}
