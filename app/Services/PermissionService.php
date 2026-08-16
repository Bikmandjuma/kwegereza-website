<?php

namespace App\Services;

use App\Models\Permission;
use Illuminate\Support\Str;

class PermissionService
{
    public function paginate(int $perPage = 20)
    {
        return Permission::withCount('roles')->orderBy('group')->orderBy('name')->paginate($perPage);
    }

    public function grouped()
    {
        return Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');
    }

    public function create(string $name, string $slug, ?string $group): Permission
    {
        return Permission::create([
            'name'  => $name,
            'slug'  => $slug,
            'group' => $group ?: Str::before($slug, '.'),
        ]);
    }

    public function update(Permission $permission, string $name, string $slug, ?string $group): Permission
    {
        $permission->update([
            'name'  => $name,
            'slug'  => $slug,
            'group' => $group ?: Str::before($slug, '.'),
        ]);

        return $permission;
    }

    public function delete(Permission $permission): void
    {
        $permission->delete();
    }
}
