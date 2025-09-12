<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Recepcion extends Model
{
    use HasFactory;
    protected $table    = 'recepciones';
    protected $fillable = ['solicitud_id', 'detalles', 'observacion'];

    protected $casts = [
        'activo' => 'boolean',
    ];

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

    public function usuarioOrigen()
    {
        return $this->belongsTo(User::class, 'user_id_origen');
    }

    public function usuarioDestino()
    {
        return $this->belongsTo(User::class, 'user_id_destino');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function atencion()
    {
        return $this->belongsTo(Atencion::class);
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class);
    }
}
