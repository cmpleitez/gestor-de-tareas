<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    public $incrementing = false;
    protected $keyType   = 'int';

    protected $fillable = [
        'name',
        'dui',
        'email',
        'password',
        'role_id',
        'profile_photo_path',
    ];
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'activo'            => 'boolean',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function defaultProfilePhotoUrl()
    {
        return asset('app-assets/images/pages/operador.png');
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class);
    }

    public function equipos()
    {
        return $this->belongsToMany(Equipo::class);
    }

    public function solicitudes()
    {
        return $this->belongsToMany(Solicitud::class);
    }

    public function solicitudesRecibidas()
    {
        return $this->belongsToMany(Solicitud::class, 'recepciones', 'user_id_origen');
    }

    public function solicitudesEnviadas()
    {
        return $this->belongsToMany(Solicitud::class, 'recepciones', 'user_id_destino');
    }

    public function mainRole()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

}
