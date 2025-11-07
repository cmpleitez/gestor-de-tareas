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

    public function oficina()
    {
        return $this->belongsTo(Oficina::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function oficinaStock()
    {
        return $this->belongsTo(OficinaStock::class, 'oficina_stock_id');
    }

}
