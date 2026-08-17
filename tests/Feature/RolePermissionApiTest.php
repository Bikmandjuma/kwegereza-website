<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RolePermissionApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(string $email = 'admin@rbac.test'): Owner
    {
        $owner = Owner::create([
            'firstname' => 'Admin', 'lastname' => 'User', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => $email, 'phone' => '0700000220',
            'password' => Hash::make('password123'),
        ]);
        $owner->roles()->sync([Role::where('slug', 'admin')->firstOrFail()->id]);
        return $owner;
    }

    /**
     * roles.delete/permissions.delete are deliberately excluded from the
     * seeded 'admin' role — only super-admin has them. Must be created
     * BEFORE any other owner in a given test: RolePermissionSeeder has
     * its own bootstrapping safety net that syncs super-admin onto
     * whichever owner is literally first in the table (so a fresh
     * system is never locked out) — calling $this->seed() after another
     * owner already exists, as my first draft of these tests did,
     * silently grants that unrelated earlier owner super-admin too.
     */
    private function makeSuperAdmin(string $email = 'superadmin@rbac.test'): Owner
    {
        $owner = Owner::create([
            'firstname' => 'Super', 'lastname' => 'Admin', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => $email, 'phone' => '0700000230',
            'password' => Hash::make('password123'),
        ]);
        $owner->roles()->sync([Role::where('slug', 'super-admin')->firstOrFail()->id]);
        return $owner;
    }

    public function test_admin_can_create_update_and_delete_a_custom_role(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeAdmin();
        $token = $admin->createToken('test')->plainTextToken;
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");

        $permIds = Permission::whereIn('slug', ['darsat.view', 'darsat.create'])->pluck('id')->toArray();

        $create = $auth()->postJson('/api/owner/roles', [
            'name' => 'Junior Teacher', 'description' => 'Limited darsat access', 'permissions' => $permIds,
        ]);
        $create->assertStatus(201)->assertJsonPath('data.name', 'Junior Teacher');
        $this->assertCount(2, $create->json('data.permissions'));
        $roleId = $create->json('data.id');

        $update = $auth()->putJson("/api/owner/roles/{$roleId}", [
            'name' => 'Junior Teacher (Updated)', 'permissions' => Permission::where('slug', 'darsat.view')->pluck('id')->toArray(),
        ]);
        $update->assertOk();
        $this->assertCount(1, $update->json('data.permissions'));
    }

    /**
     * roles.delete and permissions.delete are deliberately excluded from
     * the seeded 'admin' role (only super-admin has them) — confirmed by
     * reading RolePermissionSeeder's exclusion list rather than assumed,
     * after my first draft of this test wrongly expected a regular admin
     * to be able to delete roles.
     */
    public function test_regular_admin_cannot_delete_a_role(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $superAdmin = $this->makeSuperAdmin();
        $admin = $this->makeAdmin();
        $adminToken = $admin->createToken('test')->plainTextToken;

        $permIds = Permission::whereIn('slug', ['darsat.view'])->pluck('id')->toArray();
        $role = Role::create(['name' => 'Deletable Role', 'slug' => 'deletable-role', 'is_super' => false]);
        $role->permissions()->sync($permIds);

        $this->withHeader('Authorization', "Bearer {$adminToken}")
            ->deleteJson("/api/owner/roles/{$role->id}")
            ->assertStatus(403);
        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    /**
     * Deliberately a separate test method (not continuing in the one
     * above with a second token): Laravel's auth guard caches the first
     * resolved user for the lifetime of a test's app container —
     * switching Sanctum tokens mid-method doesn't force re-resolution.
     * This exact issue has bitten this project's tests before (Phase 2's
     * logout test, Phase 8's Quiz question-delete test); splitting into
     * a fresh test method is the reliable fix, not a workaround.
     */
    public function test_super_admin_can_delete_a_role(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $superAdmin = $this->makeSuperAdmin();
        $superAdminToken = $superAdmin->createToken('test')->plainTextToken;

        $permIds = Permission::whereIn('slug', ['darsat.view'])->pluck('id')->toArray();
        $role = Role::create(['name' => 'Deletable Role 2', 'slug' => 'deletable-role-2', 'is_super' => false]);
        $role->permissions()->sync($permIds);

        $this->withHeader('Authorization', "Bearer {$superAdminToken}")
            ->deleteJson("/api/owner/roles/{$role->id}")
            ->assertOk();
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_the_super_admin_role_cannot_be_edited_or_deleted(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $superAdmin = $this->makeSuperAdmin();
        $token = $superAdmin->createToken('test')->plainTextToken;
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");
        $superAdminRole = Role::where('slug', 'super-admin')->firstOrFail();

        $update = $auth()->putJson("/api/owner/roles/{$superAdminRole->id}", ['name' => 'Hacked Name']);
        $update->assertStatus(422);
        $this->assertSame('Super Admin', $superAdminRole->fresh()->name);

        $delete = $auth()->deleteJson("/api/owner/roles/{$superAdminRole->id}");
        $delete->assertStatus(422);
        $this->assertDatabaseHas('roles', ['id' => $superAdminRole->id]);
    }

    public function test_teacher_role_cannot_access_role_management(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $teacher = Owner::create([
            'firstname' => 'Teacher', 'lastname' => 'User', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'teacher@rbac.test', 'phone' => '0700000221',
            'password' => Hash::make('password123'),
        ]);
        $teacher->roles()->sync([Role::where('slug', 'teacher')->firstOrFail()->id]);
        $token = $teacher->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/owner/roles')
            ->assertStatus(403);
    }

    public function test_admin_can_assign_roles_to_a_staff_member(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeAdmin();
        $token = $admin->createToken('test')->plainTextToken;

        $target = Owner::create([
            'firstname' => 'Target', 'lastname' => 'Staff', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'target@rbac.test', 'phone' => '0700000222',
            'password' => Hash::make('password123'),
        ]);
        $teacherRole = Role::where('slug', 'teacher')->firstOrFail();

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson("/api/owner/staff/{$target->id}/roles", ['roles' => [$teacherRole->id]]);

        $response->assertOk();
        $this->assertTrue($target->fresh()->roles->contains('slug', 'teacher'));
    }

    public function test_admin_can_create_a_custom_permission(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $superAdmin = $this->makeSuperAdmin();
        $admin = $this->makeAdmin();
        $adminToken = $admin->createToken('test')->plainTextToken;

        $create = $this->withHeader('Authorization', "Bearer {$adminToken}")->postJson('/api/owner/permissions', [
            'name' => 'Custom Feature View', 'slug' => 'custom_feature.view',
        ]);
        $create->assertStatus(201)->assertJsonPath('data.group', 'custom_feature');
    }

    public function test_super_admin_can_delete_a_permission(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $superAdmin = $this->makeSuperAdmin();
        $superAdminToken = $superAdmin->createToken('test')->plainTextToken;

        $permission = Permission::create(['name' => 'x', 'slug' => 'custom_feature.delete_me', 'group' => 'custom_feature']);

        $delete = $this->withHeader('Authorization', "Bearer {$superAdminToken}")->deleteJson("/api/owner/permissions/{$permission->id}");
        $delete->assertOk();
        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }

    public function test_a_permission_slug_not_matching_the_group_dot_action_format_is_rejected(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeAdmin();
        $token = $admin->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")->postJson('/api/owner/permissions', [
            'name' => 'Bad Slug', 'slug' => 'not-a-valid-slug-format',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('slug');
    }
}
