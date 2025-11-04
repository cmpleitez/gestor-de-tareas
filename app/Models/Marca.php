<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    public $incrementing = false;
    protected $keyType   = 'int';
    protected $fillable  = ['marca'];
    protected $casts = [
        'activo' => 'boolean',
    ];

    public function modelos()
    {
        return $this->hasMany(Modelo::class);
    }
}
