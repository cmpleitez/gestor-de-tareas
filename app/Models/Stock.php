<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;



class Stock extends Model
{
    public $incrementing = false;
    protected $keyType   = 'int';
    protected $casts = [
        'activo' => 'boolean',
    ];

    public function oficinas()
    {
        return $this->belongsToMany(Oficina::class)->withPivot('unidades', 'producto_id');
    }

}
