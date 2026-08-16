<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject{
    
    protected $table='users';
    protected $guarded = array();
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable=[
        'id',
        'user_code',
        'user_name',
        'provider_name',
        'provider_id',
        'provider_token',
        'firstname',
        'lastname',
        'email',
        'image',
        'gender',
        'phone',
        'birthdate',
        'password',
        'last_active_at',
        'two_factor_secret',
        'two_factor_enabled',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'profile_visible',
        'deactivated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */


    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at'        => 'datetime',
        'password'                 => 'hashed',
        'last_active_at'           => 'datetime',
        'two_factor_confirmed_at'  => 'datetime',
        'deactivated_at'           => 'datetime',
    ];

     public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Learning progress (web/student side — see DarsatProgressController)
     */
    public function darsatProgress()
    {
        return $this->hasMany(DarsatProgress::class, 'user_id');
    }

    public function completedLessonsCount(): int
    {
        return $this->darsatProgress()->where('status', 'completed')->count();
    }

    public function inProgressLessonsCount(): int
    {
        return $this->darsatProgress()->where('status', 'in_progress')->count();
    }

    public function imageUrl(): string
    {
        if (!$this->image || $this->image === 'user.png') {
            return asset('images/users/user.png');
        }

        return \App\Support\FileUrl::resolve($this->image, 'images/users', 'images/users')
            ?? asset('images/users/user.png');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'user_id');
    }
}
