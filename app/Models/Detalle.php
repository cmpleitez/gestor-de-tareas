<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detalle extends Model
{
    protected $primaryKey = ['orden_id', 'producto_id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $casts = [
        'activo' => 'boolean',
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

    protected function setKeysForSaveQuery($query)
    {
        return $query->where('orden_id', $this->orden_id)
        ->where('kit_id', $this->kit_id)
        ->where('producto_id', $this->producto_id);
    }

}
