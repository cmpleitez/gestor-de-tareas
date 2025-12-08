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
        'image_path',
        'oficina_id',
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

    /**
     * Sobrescribir el accessor de profile_photo_url para usar image_path
     */
    public function getProfilePhotoUrlAttribute()
    {
        return $this->image_path
            ? \Illuminate\Support\Facades\Storage::url($this->image_path)
            : $this->defaultProfilePhotoUrl();
    }

    /**
     * Sobrescribir el método updateProfilePhoto del trait HasProfilePhoto para usar ImageWeightStabilizer
     */
    public function updateProfilePhoto(\Illuminate\Http\UploadedFile $photo): void
    {
        $imageStabilizer = new \App\Services\ImageWeightStabilizer();
        $imageStabilizer->processProfilePhoto(
            $photo,
            storage_path('app/public/user-images'),
            'User',
            $this->id
        );
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
        return $this->belongsToMany(Solicitud::class, 'recepciones', 'origen_user_id');
    }

    public function solicitudesEnviadas()
    {
        return $this->belongsToMany(Solicitud::class, 'recepciones', 'destino_user_id');
    }

    public function mainRole()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

}
