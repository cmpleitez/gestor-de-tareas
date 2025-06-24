<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Actividad extends Model
{
    use HasFactory;
    protected $table = 'actividades';
    public function incidencias()
    {
        return $this->hasMany(Incidencia::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
