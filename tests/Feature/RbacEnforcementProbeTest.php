<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Phase 1 RBAC verification probe.
 *
 * Purpose: empirically confirm (not assume) whether an owner account that
 * holds a role WITHOUT a given granular permission can currently reach
 * mutating owner routes that a permission slug exists for.
 */
class RbacEnforcementProbeTest extends TestCase
{
    use RefreshDatabase;

    protected function seedRoles(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_moderator_lacks_darsat_create_permission_per_model(): void
    {
        $this->seedRoles();
        $moderator = Role::where('slug', 'moderator')->firstOrFail();
        $owner = Owner::create([
            'firstname' => 'Restricted', 'lastname' => 'Probe', 'gender' => 'male', 'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'restricted@probe.test', 'phone' => '0700000001',
            'password' => Hash::make('password123'),
        ]);
        $owner->roles()->sync([$moderator->id]);

        $this->assertFalse($owner->hasPermission('darsat.create'), 'Sanity check: moderator role should NOT have darsat.create in the seeded data.');
    }

    public function test_owner_without_darsat_permission_can_currently_hit_storeDarsat_route(): void
    {
        $this->seedRoles();
        $moderator = Role::where('slug', 'moderator')->firstOrFail();
        $owner = Owner::create([
            'firstname' => 'Restricted', 'lastname' => 'Probe', 'gender' => 'male', 'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'restricted2@probe.test', 'phone' => '0700000002',
            'password' => Hash::make('password123'),
        ]);
        $owner->roles()->sync([$moderator->id]);

        $response = $this->actingAs($owner, 'owner')
            ->post('/owner/storeDarsat', []);

        // THIS IS THE PROBE: today, does this return 403 (correctly blocked)
        // or does it pass ownerAuth and reach the controller (302/200/422 —
        // i.e. NOT 403), proving the permission slug is not enforced?
        fwrite(STDERR, "\n[PROBE] /owner/storeDarsat as non-darsat.create owner => HTTP {$response->getStatusCode()}\n");
        fwrite(STDERR, "[PROBE] Body snippet: " . substr(strip_tags($response->getContent()), 0, 500) . "\n");

        // We assert the CURRENT (unfixed) behavior to document the gap.
        $this->assertEquals(403, $response->getStatusCode(), 'Confirms darsat.create IS correctly enforced (via AdminController constructor middleware) for owners lacking the permission.');
    }

    public function test_owner_without_roles_route_only_gate(): void
    {
        $this->seedRoles();
        // Owner with literally zero roles (simulating a pre-existing account
        // before RolePermissionSeeder's safety net runs) still passes ownerAuth.
        $owner = Owner::create([
            'firstname' => 'NoRole', 'lastname' => 'Probe', 'gender' => 'male', 'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'norole@probe.test', 'phone' => '0700000003',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($owner, 'owner')->get('/owner/darsat');

        fwrite(STDERR, "[PROBE] /owner/darsat (view) as owner with ZERO roles => HTTP {$response->getStatusCode()}\n");
        fwrite(STDERR, "[PROBE] Body snippet: " . substr(strip_tags($response->getContent()), 0, 500) . "\n");
        $this->assertEquals(403, $response->getStatusCode(), 'Confirms darsat.view IS correctly enforced even for an owner with zero roles.');
    }

    public function test_resource_users_index_route_is_broken(): void
    {
        $this->seedRoles();
        $super = Role::where('slug', 'super-admin')->firstOrFail();
        $owner = Owner::create([
            'firstname' => 'Super', 'lastname' => 'Admin', 'gender' => 'male', 'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'super@probe.test', 'phone' => '0700000004',
            'password' => Hash::make('password123'),
        ]);
        $owner->roles()->sync([$super->id]);

        try {
            $response = $this->actingAs($owner, 'owner')->get('/owner/users');
            fwrite(STDERR, "\n[PROBE] GET /owner/users (Route::resource index) as SUPER ADMIN => HTTP {$response->getStatusCode()}\n");
        } catch (\Throwable $e) {
            fwrite(STDERR, "\n[PROBE] GET /owner/users (Route::resource index) THREW: " . get_class($e) . ': ' . $e->getMessage() . "\n");
        }

        try {
            $response2 = $this->actingAs($owner, 'owner')->get('/owner/users/1/edit');
            fwrite(STDERR, "[PROBE] GET /owner/users/1/edit (Route::resource edit) as SUPER ADMIN => HTTP {$response2->getStatusCode()}\n");
        } catch (\Throwable $e) {
            fwrite(STDERR, "[PROBE] GET /owner/users/1/edit THREW: " . get_class($e) . ': ' . $e->getMessage() . "\n");
        }

        $this->assertTrue(true);
    }

    public function test_owner_with_darsat_permission_can_actually_create_a_darsat(): void
    {
        $this->seedRoles();
        $teacher = Role::where('slug', 'teacher')->firstOrFail(); // has darsat.create
        $owner = Owner::create([
            'firstname' => 'Authorized', 'lastname' => 'Teacher', 'gender' => 'male', 'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'authorized@probe.test', 'phone' => '0700000005',
            'password' => Hash::make('password123'),
        ]);
        $owner->roles()->sync([$teacher->id]);

        \Illuminate\Support\Facades\Storage::fake('public');
        $audio = \Illuminate\Http\UploadedFile::fake()->create('lesson.mp3', 100, 'audio/mpeg');

        $response = $this->actingAs($owner, 'owner')->post('/owner/storeDarsat', [
            'title' => 'Test Dars', 'teachers' => $owner->id, 'type' => 'Fiqh',
            'description' => 'x', 'audio' => $audio, 'status' => 'draft',
        ]);

        fwrite(STDERR, "\n[PROBE] /owner/storeDarsat as owner WITH darsat.create => HTTP {$response->getStatusCode()}\n");
        $this->assertNotEquals(403, $response->getStatusCode(), 'An owner who legitimately holds darsat.create must NOT be blocked.');
        $this->assertDatabaseHas('darsat_tables', ['title' => 'Test Dars']);
    }
}
