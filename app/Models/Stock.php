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

    public function entradas()
    {
        return $this->belongsToMany(Entrada::class)->withPivot('unidades');
    }

}
