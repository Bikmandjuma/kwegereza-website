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

    // Was previously ALSO declaring `$guarded = []` alongside this
    // $fillable list — dead code today (Eloquent uses $fillable as the
    // operative restriction whenever it's set, so $guarded=[] never
    // actually took effect), but a redundant pair of settings that look
    // contradictory is exactly the kind of thing that invites the "which
    // one actually governs?" confusion behind several real bugs found
    // earlier (Owner::title itself was once missing from this very list;
    // see the Darsat phase). Removed the dead declaration so there's one
    // unambiguous source of truth.
    protected $fillable = [
        'firstname',
        'lastname',
        'gender',
        'phone',
        'email',
        'role',
        'title',
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

    /**
     * Where broadcast (realtime) notifications for this specific owner are
     * sent — Laravel calls this ON the notifiable itself, which is why it's
     * here rather than on the Notification class: only $this (the actual
     * recipient) reliably knows its own ID at broadcast time, whereas a
     * shared Notification instance sent to many owners at once has no
     * built-in way to know which recipient is "current".
     */
    public function receivesBroadcastNotificationsOn(): string
    {
        return 'private-owner.'.$this->id.'.notifications';
    }
}