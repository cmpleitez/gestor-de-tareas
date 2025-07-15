<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    public function oficina()
    {
        return $this->belongsTo(Oficina::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
    
}
