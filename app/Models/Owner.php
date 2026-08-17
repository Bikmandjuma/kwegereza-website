<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Owner extends Authenticatable{
    
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'owners';
    protected $guarded = [];

    protected $fillable = [
        'firstname',
        'lastname',
        'gender',
        'phone',
        'email',
        'role',
        'image',
        'dob',
        'bio',
        'credentials',
        'is_verified',
        'verified_at',
        'verified_by',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'dob' => 'date',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function verifiedBy()
    {
        return $this->belongsTo(Owner::class, 'verified_by');
    }

    public function darsat()
    {
        return $this->hasMany(DarsatTable::class, 'teachers');
    }

    public function imageUrl(): string
    {
        if (!$this->image || $this->image === 'user.png') {
            return asset('images/users/user.png');
        }

        return \App\Support\FileUrl::resolve($this->image, 'images/users', 'images/users')
            ?? asset('images/users/user.png');
    }

    /**
     * RBAC
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'owner_role');
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles()->where('slug', $slug)->exists();
    }

    public function isSuperAdmin(): bool
    {
        return $this->roles()->where('is_super', true)->exists();
    }

    public function hasPermission(string $slug): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', function ($q) use ($slug) {
                $q->where('slug', $slug);
            })
            ->exists();
    }
}