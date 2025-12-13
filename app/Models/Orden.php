<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    public $table = 'ordenes';
    public $incrementing = false;
    protected $keyType   = 'string';
    protected $casts = [
        'activo' => 'boolean',
    ];
    
    public function atencion()
    {
        return $this->belongsTo(Atencion::class);
    }
    
    public function detalle()
    {
        return $this->hasMany(Detalle::class);
    }

    public function kit()
    {
        return $this->belongsTo(Kit::class);
    }

}
