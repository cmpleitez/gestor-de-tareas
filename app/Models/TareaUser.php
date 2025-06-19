<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TareaUser extends Model
{
    use HasFactory;
    protected $table = 'tarea_user';

    public function incidencias()
    {
        return $this->hasMany(Incidencia::class);
    }
}
