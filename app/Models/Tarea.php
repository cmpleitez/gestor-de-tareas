<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    public $incrementing = false;
    protected $keyType   = 'int';
    protected $fillable = ['tarea'];
    protected $casts = [
        'activo' => 'boolean',
    ];

    public function usuariosOrigen()
    {
        return $this->belongsToMany(User::class, 'actividades', 'user_id_origen');
    }

    public function usuariosDestino()
    {
        return $this->belongsToMany(User::class, 'actividades', 'user_id_destino');
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class);
    }

}
