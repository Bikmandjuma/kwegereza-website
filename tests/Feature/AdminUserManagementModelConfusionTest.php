<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * A severe, confirmed bug found while starting the Users/Admin phase:
 * AdminController's edit()/ownerEditUser(), update(), and destroy() —
 * the exact actions the owner-management admin panel uses for "Edit"
 * and "Delete" on a staff/leader/admin account — all fetched
 * User::findOrFail($id) (the STUDENT model) instead of
 * Owner::findOrFail($id), despite the listing (ViewUser) and
 * create/store correctly using Owner throughout. Confirmed by reading
 * the imports (both Owner and User are correctly imported, ruling out
 * an aliasing mixup) and the validation rules (checking uniqueness
 * against the students table, 'users', while editing an owner).
 *
 * Practical impact: whenever a student happened to share a numeric ID
 * with the owner an admin intended to edit or delete, the admin panel
 * would silently operate on that unrelated student's account instead —
 * editing their name/email/password, or deleting them outright — while
 * the actual owner record remained completely untouched.
 */
class AdminUserManagementModelConfusionTest extends TestCase
{
    use RefreshDatabase;

    private function makeSuperAdmin(): Owner
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $owner = Owner::create([
            'firstname' => 'Super', 'lastname' => 'Admin', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'super@admin.test', 'phone' => '0700000200',
            'password' => Hash::make('password123'),
        ]);
        $owner->roles()->sync([Role::where('slug', 'super-admin')->firstOrFail()->id]);
        return $owner;
    }

    public function test_editing_an_owner_must_not_touch_a_student_with_the_same_id(): void
    {
        $superAdmin = $this->makeSuperAdmin(); // consumes owner id 1

        // Bump the student sequence so the REAL student below lands on
        // the same id as targetOwner (2), rather than assuming collision.
        User::create(['user_code' => 'STD-DUMMY', 'firstname' => 'Dummy', 'lastname' => 'One', 'email' => 'dummy1@collide.test', 'phone' => '0788000299', 'password' => Hash::make('x')]);

        $student = User::create([
            'user_code' => 'STD-COLLIDE', 'firstname' => 'Innocent', 'lastname' => 'Student',
            'email' => 'student@collide.test', 'phone' => '0788000201', 'password' => Hash::make('x'),
        ]);
        $targetOwner = Owner::create([
            'firstname' => 'Target', 'lastname' => 'Owner', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'target@collide.test', 'phone' => '0700000202',
            'password' => Hash::make('password123'),
        ]);

        // This test only proves something if the IDs actually collide —
        // assert that precondition explicitly rather than silently
        // passing on a fluke of auto-increment ordering.
        $this->assertSame($student->id, $targetOwner->id, 'Test setup requires colliding IDs to expose the bug.');

        $response = $this->actingAs($superAdmin, 'owner')->put("/owner/users/{$targetOwner->id}", [
            'firstname' => 'Edited', 'lastname' => 'Name', 'gender' => 'male',
            'phone' => '0700000203', 'dob' => '1991-01-01', 'email' => 'edited@collide.test',
            'role' => 'admin', 'title' => 'admin',
        ]);

        $response->assertRedirect();

        $this->assertSame('Edited', $targetOwner->fresh()->firstname, 'The OWNER record must be the one that gets edited.');
        $this->assertSame('Innocent', $student->fresh()->firstname, 'The unrelated STUDENT with the same ID must be completely untouched.');
    }

    public function test_deleting_an_owner_must_not_delete_a_student_with_the_same_id(): void
    {
        $superAdmin = $this->makeSuperAdmin(); // consumes owner id 1

        User::create(['user_code' => 'STD-DUMMY2', 'firstname' => 'Dummy', 'lastname' => 'Two', 'email' => 'dummy2@collide.test', 'phone' => '0788000298', 'password' => Hash::make('x')]);

        $student = User::create([
            'user_code' => 'STD-COLLIDE2', 'firstname' => 'Innocent', 'lastname' => 'Student',
            'email' => 'student2@collide.test', 'phone' => '0788000204', 'password' => Hash::make('x'),
        ]);
        $targetOwner = Owner::create([
            'firstname' => 'ToDelete', 'lastname' => 'Owner', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'todelete@collide.test', 'phone' => '0700000205',
            'password' => Hash::make('password123'),
        ]);
        $this->assertSame($student->id, $targetOwner->id, 'Test setup requires colliding IDs to expose the bug.');

        $this->actingAs($superAdmin, 'owner')->delete("/owner/users/{$targetOwner->id}");

        $this->assertDatabaseMissing('owners', ['id' => $targetOwner->id]);
        $this->assertDatabaseHas('users', ['id' => $student->id]);
    }
}
