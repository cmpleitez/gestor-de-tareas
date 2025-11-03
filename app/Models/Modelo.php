<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    public $incrementing = false;
    protected $keyType   = 'int';
    protected $fillable  = ['marca_id', 'modelo'];
    protected $casts     = [
        'activo' => 'boolean',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }
}
