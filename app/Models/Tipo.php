<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Tipo extends Model
{
    public $incrementing = false;
    protected $keyType   = 'int';
    protected $fillable = ['tipo'];
    protected $casts = [
        'activo' => 'boolean',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
