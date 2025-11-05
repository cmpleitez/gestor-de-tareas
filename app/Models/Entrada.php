<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';
    protected $casts     = [
        'activo' => 'boolean',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function stockOrigen()
    {
        return $this->belongsTo(Stock::class, 'stock_origen_id');
    }

    public function stockDestino()
    {
        return $this->belongsTo(Stock::class, 'stock_destino_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class);
    }

}
