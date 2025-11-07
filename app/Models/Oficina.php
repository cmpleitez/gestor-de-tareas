<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Oficina extends Model
{
    public $incrementing = false;
    protected $keyType   = 'int';
    protected $casts = [
        'activo' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function recepciones()
    {
        return $this->hasMany(Recepcion::class);
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class);
    }

    public function atenciones()
    {
        return $this->hasMany(Atencion::class);
    }

    public function stocks()
    {
        return $this->belongsToMany(Stock::class)->withPivot('unidades', 'producto_id');
    }

}
