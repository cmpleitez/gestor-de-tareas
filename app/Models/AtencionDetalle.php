<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtencionDetalle extends Model
{
    public function atencion()
    {
        return $this->belongsTo(Atencion::class);
    }
    public function kit()
    {
        return $this->belongsTo(Kit::class);
    }
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
