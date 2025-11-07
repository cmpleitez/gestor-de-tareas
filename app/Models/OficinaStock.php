<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OficinaStock extends Model
{
    protected $table = 'oficina_stock';
    public $incrementing = false;
    protected $keyType   = 'int';

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
