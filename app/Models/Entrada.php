<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';
    protected $casts     = [
        'activo' => 'boolean',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class);
    }

    public function tipoEntrada()
    {
        return $this->belongsTo(TipoEntrada::class);
    }

}
