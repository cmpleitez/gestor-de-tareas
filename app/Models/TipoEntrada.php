<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TipoEntrada extends Model
{
    public $incrementing = false;
    protected $keyType   = 'int';
    protected $fillable = ['tipo_entrada'];
    protected $casts = [
        'activo' => 'boolean',
    ];

    public function entradas()
    {
        return $this->hasMany(Entrada::class);
    }

}
