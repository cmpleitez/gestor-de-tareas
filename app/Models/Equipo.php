<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    public $incrementing = false;
    protected $keyType   = 'int';
    protected $fillable  = ['equipo', 'oficina_id'];
    protected $casts     = [
        'activo' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class);
    }
}
