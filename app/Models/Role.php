<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    /**
     * Relación con usuarios que tienen este rol como principal
     */
    public function mainUsers()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Obtener todos los usuarios que tienen este rol como principal
     */
    public function getMainUsersAttribute()
    {
        return $this->mainUsers()->get();
    }

    /**
     * Obtener el número de usuarios que tienen este rol como principal
     */
    public function getMainUsersCountAttribute()
    {
        return $this->mainUsers()->count();
    }
}