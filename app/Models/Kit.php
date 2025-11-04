<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kit extends Model
{
    use HasFactory;

    public function productos()
    {
        return $this->belongsToMany(Producto::class);
    }

    public function atencionDetalles()
    {
        return $this->hasMany(AtencionDetalle::class);
    }
}
