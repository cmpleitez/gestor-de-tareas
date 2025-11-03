<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    public $incrementing = false;
    protected $keyType   = 'int';
    protected $fillable  = ['parametro', 'valor'];
    protected $casts = [
        'activo' => 'boolean',
    ];
}
