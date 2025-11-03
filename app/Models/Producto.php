<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    public $incrementing = false;
    protected $keyType   = 'int';
    protected $fillable  = ['producto', 'precio', 'modelo_id'];

    protected $casts = [
        'accesorio' => 'boolean',
        'activo'    => 'boolean',
    ];

    public function tipo()
    {
        return $this->belongsTo(Tipo::class);
    }

    public function modelo()
    {
        return $this->belongsTo(Modelo::class);
    }

}
