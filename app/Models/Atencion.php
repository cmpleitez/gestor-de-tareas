<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atencion extends Model
{
    use HasFactory;
    protected $table = 'atenciones';

    public function recepciones()
    {
        return $this->hasMany(Recepcion::class);
    }
}
