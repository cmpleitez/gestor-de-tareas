<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OficinaStock extends Model
{
    protected $table = 'oficina_stock';
    public $incrementing = false;

    protected function setKeysForSaveQuery($query)
    {
        return $query->where('oficina_id', $this->oficina_id)
        ->where('stock_id', $this->stock_id)
        ->where('producto_id', $this->producto_id);
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

}
