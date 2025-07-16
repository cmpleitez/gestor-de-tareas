<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Actividad extends Model
{
    use HasFactory;
    protected $table = 'actividades';
    
    protected $casts = [
        'activo' => 'boolean',
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function tarea()
    {
        return $this->belongsTo(Tarea::class);
    }

    public function recepcion()
    {
        return $this->belongsTo(Recepcion::class);
    }

    public function incidencias()
    {
        return $this->hasMany(Incidencia::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
