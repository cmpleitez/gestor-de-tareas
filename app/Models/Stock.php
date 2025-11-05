<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;



class Stock extends Model
{
    public $incrementing = false;
    protected $keyType   = 'int';
    protected $casts = [
        'activo' => 'boolean',
    ];

    public function oficina()
    {
        return $this->belongsTo(Oficina::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function entradasOrigen()
    {
        return $this->hasMany(Entrada::class, 'stock_origen_id');
    }

    public function entradasDestino()
    {
        return $this->hasMany(Entrada::class, 'stock_destino_id');
    }

}
