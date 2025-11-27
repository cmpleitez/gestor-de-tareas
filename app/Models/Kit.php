<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Kit extends Model
{
    public $incrementing = false;
    protected $keyType   = 'int';
    protected $fillable = ['kit', 'precio', 'descargas', 'image_path'];
    protected $casts = [
        'activo' => 'boolean',
    ];

    public function productos()
    {
        return $this->belongsToMany(Producto::class)->withPivot('unidades');
    }

    public function atencionDetalles()
    {
        return $this->hasMany(AtencionDetalle::class);
    }
}
