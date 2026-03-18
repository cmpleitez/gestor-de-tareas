<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    public $incrementing = false;
    protected $keyType   = 'int';
    protected $fillable  = ['valor', 'unidad_medida'];
    protected $casts = [
        'activo' => 'boolean',
    ];
}
