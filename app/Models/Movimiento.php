<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';
    protected $casts     = [
        'activo' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'destino_stock_id', 'stock_id');
    }

    public function oficina()
    {
        return $this->belongsTo(OficinaStock::class, 'oficina_id', 'oficina_id');
    }

    public function stockDestino()
    {
        return $this->belongsTo(OficinaStock::class, 'origen_stock_id', 'stock_id');
    }

    public function producto()
    {
        return $this->belongsTo(OficinaStock::class, 'producto_id', 'producto_id');
    }

    public function stockOrigen()
    {
        return $this->belongsTo(Stock::class, 'origen_stock_id', 'id');
    }
}
