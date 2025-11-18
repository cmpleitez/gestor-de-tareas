<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtencionDetalle extends Model
{
    protected $table = 'atencion_detalles';
    public $incrementing = false;

    protected function setKeysForSaveQuery($query)
    {
        return $query->where('atencion_id', $this->atencion_id)
        ->where('kit_id', $this->kit_id)
        ->where('producto_id', $this->producto_id);
    }

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
