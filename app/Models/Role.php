<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'is_super'];

    protected $casts = [
        'is_super' => 'boolean',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    public function owners()
    {
        return $this->belongsToMany(Owner::class, 'owner_role');
    }

    public function hasPermission(string $slug): bool
    {
        if ($this->is_super) {
            return true;
        }

        return $this->permissions()->where('slug', $slug)->exists();
    }
}
