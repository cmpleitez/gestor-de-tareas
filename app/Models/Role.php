<?php
namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function mainUsers()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
