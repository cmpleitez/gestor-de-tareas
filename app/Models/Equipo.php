<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    use HasFactory;

    protected $fillable = ['equipo'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class);
    }
}
