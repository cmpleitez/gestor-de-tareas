<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Kit extends Model
{
    public $incrementing = false;
    protected $keyType   = 'int';
    protected $fillable = ['kit', 'precio', 'descargas'];
    protected $casts = [
        'activo' => 'boolean',
    ];

    public function productos()
    {
        return $this->belongsToMany(Producto::class);
    }

    public function atencionDetalles()
    {
        return $this->hasMany(AtencionDetalle::class);
    }
}
