<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\KitProducto;

class Equivalente extends Model
{
    protected function setKeysForSaveQuery($query)
    {
        return $query->where('kit_id', $this->kit_id)
        ->where('producto_id', $this->producto_id)
        ->where('kit_producto_id', $this->kit_producto_id);
    }

    public function kit() {
        return $this->belongsTo(Kit::class);
    }
    public function producto() {
        return $this->belongsTo(Producto::class);
    }
    
    public function kitProducto() {
        return $this->belongsTo(KitProducto::class, 'kit_producto_id', 'id');
    }
}
