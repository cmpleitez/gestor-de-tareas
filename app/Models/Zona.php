<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    use HasFactory;


    public function distrito()
    {
        return $this->belongsTo(Distrito::class);
    }

    public function oficinas()
    {
        return $this->hasMany(Oficina::class);
    }
}
