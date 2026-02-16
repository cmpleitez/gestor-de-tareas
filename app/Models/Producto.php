<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = ['id', 'producto', 'codigo', 'precio', 'modelo_id', 'tipo_id'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function tipo()
    {
        return $this->belongsTo(Tipo::class);
    }

    public function modelo()
    {
        return $this->belongsTo(Modelo::class);
    }

    public function kits()
    {
        return $this->belongsToMany(Kit::class)->withPivot('unidades');
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }

    public function oficinaStock()
    {
        return $this->hasMany(OficinaStock::class, 'producto_id', 'id');
    }

    public function kitProductos()
    {
        return $this->hasMany(KitProducto::class, 'producto_id', 'id');
    }

}
