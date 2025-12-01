<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Equivalente;

class KitProducto extends Model
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
        return $this->hasMany(Equivalente::class);
    }
}
