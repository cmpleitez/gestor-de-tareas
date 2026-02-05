<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detalle extends Model
{
    protected $primaryKey = ['orden_id', 'kit_id', 'producto_id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $casts = [
        'activo' => 'boolean',
        'stock_fisico_existencias' => 'boolean',
    ];

    protected $fillable = [
        'orden_id',
        'kit_id',
        'producto_id',
        'producto_id_original',
        'unidades',
        'precio',
        'stock_fisico_existencias',
    ];

    public function orden()
    {
        return $this->belongsTo(Orden::class);
    }

    public function kit()
    {
        return $this->belongsTo(Kit::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function productoOriginal()
    {
        return $this->belongsTo(Producto::class, 'producto_id_original');
    }

    protected function setKeysForSaveQuery($query)
    {
        return $query->where('orden_id', $this->orden_id)
        ->where('kit_id', $this->kit_id)
        ->where('producto_id', $this->producto_id);
    }

}
