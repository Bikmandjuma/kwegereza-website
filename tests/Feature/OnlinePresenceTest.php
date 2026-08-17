<?php

namespace Tests\Feature;

use App\Events\StudentOnline;
use App\Models\Owner;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OnlinePresenceTest extends TestCase
{
    use RefreshDatabase;

    private function makeOwner(string $email, ?string $roleSlug): Owner
    {
        $owner = Owner::create([
            'firstname' => 'Test', 'lastname' => 'Owner', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => $email, 'phone' => '0700000140',
            'password' => Hash::make('password123'),
        ]);
        if ($roleSlug) {
            $owner->roles()->sync([Role::where('slug', $roleSlug)->firstOrFail()->id]);
        }
        return $owner;
    }

    private function makeStudent(string $email): User
    {
        return User::create([
            'user_code' => 'STD-'.strtoupper(substr(md5($email), 0, 6)),
            'firstname' => 'Aisha', 'lastname' => 'Uwimana',
            'email' => $email, 'phone' => '0788'.substr(md5($email), 0, 6),
            'password' => Hash::make('password123'),
        ]);
    }

    /**
     * The exact bug the Phase 0 audit flagged: "A user should generate a
     * 'new user online' event only when transitioning OFFLINE -> ONLINE,
     * not on every request." Confirms a brand-new session (no prior
     * last_active_at) fires exactly once, and immediate follow-up
     * requests within the same session do NOT re-fire it.
     */
    public function test_student_coming_online_fires_exactly_once_not_on_every_request(): void
    {
        Event::fake([StudentOnline::class]);
        $student = $this->makeStudent('aisha@presence.test');
        $this->assertNull($student->last_active_at);

        $this->actingAs($student, 'student')->get('/student/dashboard');
        $this->actingAs($student, 'student')->get('/student/dashboard');
        $this->actingAs($student, 'student')->get('/student/dashboard');

        Event::assertDispatchedTimes(StudentOnline::class, 1);
    }

    public function test_a_student_who_returns_after_being_away_fires_again(): void
    {
        $student = $this->makeStudent('aisha2@presence.test');
        $student->update(['last_active_at' => now()->subMinutes(10)]); // was online, now stale

        Event::fake([StudentOnline::class]);
        $this->actingAs($student, 'student')->get('/student/dashboard');

        Event::assertDispatched(StudentOnline::class, function ($event) use ($student) {
            return $event->student->id === $student->id;
        });
    }

    public function test_a_student_still_actively_browsing_does_not_refire(): void
    {
        $student = $this->makeStudent('aisha3@presence.test');
        $student->update(['last_active_at' => now()->subMinutes(1)]); // recently active

        Event::fake([StudentOnline::class]);
        $this->actingAs($student, 'student')->get('/student/dashboard');

        Event::assertNotDispatched(StudentOnline::class);
    }

    public function test_admin_can_see_online_students_count_and_list(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin@presence.test', 'admin');
        $token = $admin->createToken('test')->plainTextToken;

        $online = $this->makeStudent('online@presence.test');
        $online->update(['last_active_at' => now()]);
        $offline = $this->makeStudent('offline@presence.test');
        $offline->update(['last_active_at' => now()->subHours(2)]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/owner/students-online');
        $response->assertOk();
        $this->assertSame(1, $response->json('data.count'));
        $names = collect($response->json('data.students'))->pluck('name');
        $this->assertTrue($names->contains('Aisha Uwimana'));
        $this->assertCount(1, $response->json('data.students'));
    }

    public function test_teacher_role_cannot_see_online_students(): void
    {
        // teacher was not seeded students.view.
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $teacher = $this->makeOwner('teacher@presence.test', 'teacher');
        $token = $teacher->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/owner/students-online')
            ->assertStatus(403);
    }

    public function test_broadcast_failure_does_not_prevent_the_request_from_completing(): void
    {
        $student = $this->makeStudent('aisha4@presence.test');

        config(['broadcasting.default' => 'pusher']);
        config(['broadcasting.connections.pusher.options.host' => '127.0.0.1']);
        config(['broadcasting.connections.pusher.options.port' => 1]);
        config(['broadcasting.connections.pusher.key' => 'test-key']);
        config(['broadcasting.connections.pusher.secret' => 'test-secret']);
        config(['broadcasting.connections.pusher.app_id' => 'test-app']);

        $response = $this->actingAs($student, 'student')->get('/student/dashboard');

        $response->assertOk(); // NOT a 500
        $this->assertNotNull($student->fresh()->last_active_at); // still updated
    }
}
