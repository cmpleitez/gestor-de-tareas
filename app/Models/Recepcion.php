<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recepcion extends Model
{
    use HasFactory;
    protected $table = 'recepciones';
    protected $fillable = ['id', 'user_id_origen', 'user_id_destino', 'solicitud_id', 'oficina_id', 'detalles', 'observacion'];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class);
    }
}
