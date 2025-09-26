<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType   = 'int';

    protected $fillable = ['equipo', 'oficina_id'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class);
    }
}
