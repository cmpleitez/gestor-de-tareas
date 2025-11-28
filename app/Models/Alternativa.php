<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Alternativa extends Model
{
    protected function setKeysForSaveQuery($query)
    {
        return $query->where('kit_id', $this->kit_id)
        ->where('producto_id', $this->producto_id);
    }

    public function kit() {
        return $this->belongsTo(Kit::class);
    }
    public function producto() {
        return $this->belongsTo(Producto::class);
    }

}