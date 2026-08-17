<?php

namespace App\Services;

use App\Models\Owner;
use App\Models\Role;
use Illuminate\Support\Str;

class RoleService
{
    public function all()
    {
        return Role::withCount(['permissions', 'owners'])->latest()->get();
    }

    public function find(int $id): Role
    {
        return Role::with('permissions')->withCount('owners')->findOrFail($id);
    }

    public function create(string $name, ?string $description, array $permissionIds): Role
    {
        $role = Role::create([
            'name'        => $name,
            'slug'        => Str::slug($name),
            'description' => $description,
            'is_super'    => false,
        ]);

        $role->permissions()->sync($permissionIds);

        return $role;
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Role $role, string $name, ?string $description, array $permissionIds): Role
    {
        $this->guardAgainstSuperAdmin($role, 'guhindura');

        $role->update([
            'name'        => $name,
            'slug'        => Str::slug($name),
            'description' => $description,
        ]);

        $role->permissions()->sync($permissionIds);

        return $role;
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function delete(Role $role): void
    {
        $this->guardAgainstSuperAdmin($role, 'gusiba');

        $role->delete();
    }

    public function assignToOwner(Owner $owner, array $roleIds): Owner
    {
        $owner->roles()->sync($roleIds);

        return $owner->load('roles');
    }

    private function guardAgainstSuperAdmin(Role $role, string $action): void
    {
        if ($role->is_super) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'role' => ["Ntushobora {$action} uruhare rwa Super Admin."],
            ]);
        }
    }
}
