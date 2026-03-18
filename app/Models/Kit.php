<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Detalle;

class Kit extends Model
{
    public $incrementing = false;
    protected $keyType = 'int';
    protected $fillable = ['kit', 'precio', 'descargas', 'image_path'];
    protected $casts = [
        'activo' => 'boolean',
    ];

    public function productos()
    {
        return $this->belongsToMany(Producto::class)->withPivot('unidades');
    }

    public function ordenes()
    {
        return $this->hasMany(Orden::class);
    }

    public function detalles()
    {
        return $this->hasMany(Detalle::class);
    }
}
