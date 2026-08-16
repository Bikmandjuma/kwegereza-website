<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentsApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeOwner(string $email, ?string $roleSlug): Owner
    {
        $owner = Owner::create([
            'firstname' => 'Test', 'lastname' => 'Owner', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => $email, 'phone' => '0700000060',
            'password' => Hash::make('password123'),
        ]);
        if ($roleSlug) {
            $owner->roles()->sync([Role::where('slug', $roleSlug)->firstOrFail()->id]);
        }
        return $owner;
    }

    private function makeStudent(string $email, string $firstname = 'Aisha'): User
    {
        return User::create([
            'user_code' => 'STD-'.strtoupper(substr(md5($email), 0, 6)),
            'firstname' => $firstname, 'lastname' => 'Uwimana',
            'email' => $email, 'phone' => '0788'.substr(md5($email), 0, 6),
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_islamic_leader_cannot_view_students(): void
    {
        // 'islamic-leader' was not seeded students.view — confirms RBAC
        // isolation between content-management and student-management roles.
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $leader = $this->makeOwner('leader@students.test', 'islamic-leader');
        $token = $leader->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/owner/students');
        $response->assertStatus(403);
    }

    public function test_admin_can_list_search_and_view_a_student(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin@students.test', 'admin');
        $token = $admin->createToken('test')->plainTextToken;
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");

        $student = $this->makeStudent('aisha@students.test');
        $this->makeStudent('other@students.test', 'Fatima');

        $list = $auth()->getJson('/api/owner/students');
        $list->assertOk();
        $this->assertSame(2, $list->json('meta.total'));

        $search = $auth()->getJson('/api/owner/students?search=Aisha');
        $search->assertOk();
        $this->assertSame(1, $search->json('meta.total'));
        $this->assertSame('Aisha', $search->json('data.0.firstname'));

        $show = $auth()->getJson("/api/owner/students/{$student->id}");
        $show->assertOk()->assertJsonPath('data.student.firstname', 'Aisha');
        $this->assertArrayHasKey('completed_darsat_count', $show->json('data'));
        $this->assertArrayHasKey('certificates', $show->json('data'));
    }

    public function test_admin_can_block_and_unblock_a_student(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin2@students.test', 'admin');
        $token = $admin->createToken('test')->plainTextToken;
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");

        $student = $this->makeStudent('toblock@students.test');
        $this->assertNull($student->deactivated_at);

        $block = $auth()->patchJson("/api/owner/students/{$student->id}/block");
        $block->assertOk()->assertJsonPath('data.is_blocked', true);
        $this->assertNotNull($student->fresh()->deactivated_at);

        // A blocked student must actually be rejected by the student guard
        // middleware too — not just flagged in the database. Fetch a FRESH
        // instance (actingAs binds the exact object passed in; reusing the
        // stale pre-block $student would let a cached in-memory deactivated_at
        // pass without ever consulting the real per-request auth resolution).
        $loginAttempt = $this->actingAs($student->fresh(), 'student')->get('/student/dashboard');
        $loginAttempt->assertRedirect(route('student.login'));

        $unblock = $auth()->patchJson("/api/owner/students/{$student->id}/unblock");
        $unblock->assertOk()->assertJsonPath('data.is_blocked', false);
        $this->assertNull($student->fresh()->deactivated_at);
    }

    /**
     * Regression test for a serious, real bug found via this feature's live
     * testing: User::$fillable was missing 'deactivated_at' (and, it turned
     * out, 'two_factor_*' and 'profile_visible' too). This meant the block
     * feature — a security control the spec explicitly requires ("cannot
     * login... do not rely only on frontend restrictions") — has been
     * silently doing NOTHING: $student->update(['deactivated_at' => now()])
     * looked like it succeeded (no error, controller redirected with a
     * success message) but Eloquent silently dropped the non-fillable
     * attribute, so the column in the database never actually changed and
     * a "blocked" student could keep logging in normally. Fixed by adding
     * the missing columns to $fillable.
     */
    public function test_blocking_a_student_actually_persists_to_the_database(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin3@students.test', 'admin');
        $token = $admin->createToken('test')->plainTextToken;
        $student = $this->makeStudent('realcheck@students.test');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/owner/students/{$student->id}/block")
            ->assertOk();

        // The real check: read a FRESH model instance straight from the
        // database, not the in-memory object the controller already had.
        $this->assertNotNull(User::find($student->id)->deactivated_at);
    }

    public function test_moderator_cannot_block_students(): void
    {
        // moderator was seeded students.view is NOT included either — confirms
        // both view and manage are properly independent gates.
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $moderator = $this->makeOwner('mod@students.test', 'moderator');
        $token = $moderator->createToken('test')->plainTextToken;
        $student = $this->makeStudent('victim@students.test');

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson("/api/owner/students/{$student->id}/block");

        $response->assertStatus(403);
        $this->assertNull($student->fresh()->deactivated_at);
    }
}
