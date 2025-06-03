<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Oficina;

class Area extends Model
{
    use HasFactory;

    public function oficinas()
    {
        return $this->hasMany(Oficina::class);
    }
}
