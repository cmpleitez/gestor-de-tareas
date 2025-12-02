<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\Equivalente;

class KitProducto extends Pivot
{
    protected $table = 'kit_producto';
    public $incrementing = false;
    protected $keyType   = 'int';

    public function kit() {
        return $this->belongsTo(Kit::class);
    }
    
    public function producto() {
        return $this->belongsTo(Producto::class);
    }

    public function equivalentes() {
        return $this->hasMany(Equivalente::class, 'kit_producto_id', 'id');
    }
}
