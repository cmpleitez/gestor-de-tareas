<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    public $incrementing = false;
    protected $keyType   = 'int';
    public function recepciones()
    {
        return $this->hasMany(Recepcion::class);
    }

    public function atenciones()
    {
        return $this->hasMany(Atencion::class);
    }
}
