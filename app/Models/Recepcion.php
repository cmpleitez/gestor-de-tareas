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

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function usuario_origen()
    {
        return $this->belongsTo(User::class, 'user_id_origen');
    }

    public function usuario_destino()
    {
        return $this->belongsTo(User::class, 'user_id_destino');
    }

}
