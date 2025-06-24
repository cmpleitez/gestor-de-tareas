<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    public function oficinas()
    {
        return $this->hasMany(Oficina::class);
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }
}
