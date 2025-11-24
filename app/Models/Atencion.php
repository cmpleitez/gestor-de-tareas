<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Atencion extends Model
{
    protected $table = 'atenciones';
    public $incrementing = false;
    protected $keyType   = 'string';
    protected $casts = [
        'reserva' => 'boolean',
        'activo'  => 'boolean',
    ];

    public function recepciones()
    {
        return $this->hasMany(Recepcion::class);
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

}