<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Recepcion extends Model
{
    use HasFactory;
    protected $table = 'recepciones';
    protected $fillable = ['solicitud_id', 'detalles', 'observacion'];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

}
