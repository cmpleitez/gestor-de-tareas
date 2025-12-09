<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Recepcion extends Model
{
    public $incrementing = false;
    protected $keyType   = 'string';
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
        return $this->belongsTo(Role::class, 'user_destino_role_id');
    }

    public function usuarioOrigen()
    {
        return $this->belongsTo(User::class, 'origen_user_id');
    }

    public function usuarioDestino()
    {
        return $this->belongsTo(User::class, 'destino_user_id');
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
