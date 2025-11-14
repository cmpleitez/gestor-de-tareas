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
        return $this->belongsTo(User::class);
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class);
    }

    public function origenStock()
    {
        return $this->belongsTo(Stock::class, 'origen_stock_id', 'id');
    }

    public function destinoStock()
    {
        return $this->belongsTo(Stock::class, 'destino_stock_id', 'id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

}
