<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    public $incrementing = false;
    protected $keyType   = 'int';
    protected $casts = [
        'activo' => 'boolean',
    ];

    public function entradas()
    {
        return $this->hasMany(Entrada::class);
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

}
