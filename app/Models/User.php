<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'dui',
        'email',
        'password',
        'oficina_id',
        'profile_photo_path'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function getProfilePhotoUrlAttribute()
    {
        // Si el usuario no está verificado, usar la foto por defecto de Jetstream
        if (!$this->hasVerifiedEmail()) {
            return $this->defaultProfilePhotoUrl();
        }

        // Si el usuario está verificado y tiene una foto de perfil
        if ($this->profile_photo_path && Storage::exists('public/' . $this->profile_photo_path)) {
            return asset('storage/' . $this->profile_photo_path);
        }

        // Si no tiene foto de perfil o no existe el archivo
        return $this->defaultProfilePhotoUrl();
    }

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

    public function tareas_usuario_origen()
    {
        return $this->hasMany(TareaUser::class, 'user_id_origen');
    }

    public function tareas_usuario_destino()
    {
        return $this->hasMany(TareaUser::class, 'user_id_destino');
    }

}
