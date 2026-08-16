<?php

namespace Tests\Feature;

use App\Models\LiveClass;
use App\Models\LiveClassParticipant;
use App\Models\Owner;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LiveClassApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeOwner(string $email, ?string $roleSlug): Owner
    {
        $owner = Owner::create([
            'firstname' => 'Test', 'lastname' => 'Owner', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => $email, 'phone' => '0700000160',
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

    public function test_teacher_can_create_start_and_end_a_class(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $teacher = $this->makeOwner('teacher@live.test', 'teacher');
        $token = $teacher->createToken('test')->plainTextToken;
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");

        $create = $auth()->postJson('/api/owner/live-classes', ['title' => 'Isomo ku Isengesho']);
        $create->assertStatus(201)->assertJsonPath('data.status', 'scheduled');
        $id = $create->json('data.id');

        $start = $auth()->postJson("/api/owner/live-classes/{$id}/start");
        $start->assertOk()->assertJsonPath('data.status', 'live');

        // Starting the class should auto-join the host, unmuted (spec: the
        // host needs to be heard from the start).
        $this->assertDatabaseHas('live_class_participants', [
            'live_class_id' => $id, 'participant_type' => 'owner', 'participant_id' => $teacher->id,
            'role' => 'host', 'is_muted' => false,
        ]);

        $end = $auth()->postJson("/api/owner/live-classes/{$id}/end");
        $end->assertOk()->assertJsonPath('data.status', 'ended');
    }

    public function test_moderator_role_cannot_create_a_class(): void
    {
        // moderator was not seeded live_class.create.
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $moderator = $this->makeOwner('mod@live.test', 'moderator');
        $token = $moderator->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/owner/live-classes', ['title' => 'x'])
            ->assertStatus(403);
    }

    public function test_a_student_joins_muted_by_default(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $teacher = $this->makeOwner('teacher2@live.test', 'teacher');
        $token = $teacher->createToken('test')->plainTextToken;
        $class = LiveClass::create(['title' => 'x', 'host_id' => $teacher->id, 'status' => 'live', 'started_at' => now()]);

        $student = $this->makeStudent('aisha@live.test');
        $join = $this->actingAs($student, 'student')->postJson("/student/live-classes/{$class->id}/join");

        $join->assertOk()->assertJsonPath('data.is_muted', true);
    }

    public function test_raising_and_approving_a_hand_promotes_to_speaker_and_unmutes(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $teacher = $this->makeOwner('teacher3@live.test', 'teacher');
        $token = $teacher->createToken('test')->plainTextToken;
        $class = LiveClass::create(['title' => 'x', 'host_id' => $teacher->id, 'status' => 'live', 'started_at' => now()]);

        $student = $this->makeStudent('aisha2@live.test');
        $this->actingAs($student, 'student')->postJson("/student/live-classes/{$class->id}/join");
        $this->actingAs($student, 'student')->postJson("/student/live-classes/{$class->id}/raise-hand")->assertOk();

        $participant = LiveClassParticipant::where('live_class_id', $class->id)->where('participant_id', $student->id)->firstOrFail();
        $this->assertTrue($participant->fresh()->hand_raised);

        $approve = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/owner/live-classes/{$class->id}/participants/{$participant->id}/approve-hand");
        $approve->assertOk();

        $fresh = $participant->fresh();
        $this->assertSame('speaker', $fresh->role);
        $this->assertFalse($fresh->is_muted);
        $this->assertFalse($fresh->hand_raised);
    }

    public function test_mute_everyone_does_not_mute_the_host(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $teacher = $this->makeOwner('teacher4@live.test', 'teacher');
        $token = $teacher->createToken('test')->plainTextToken;
        $class = LiveClass::create(['title' => 'x', 'host_id' => $teacher->id, 'status' => 'live', 'started_at' => now()]);
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");

        $auth()->postJson("/api/owner/live-classes/{$class->id}/start");

        $s1 = $this->makeStudent('aisha3@live.test');
        $s2 = $this->makeStudent('aisha4@live.test');
        $this->actingAs($s1, 'student')->postJson("/student/live-classes/{$class->id}/join");
        $this->actingAs($s2, 'student')->postJson("/student/live-classes/{$class->id}/join");

        $auth()->postJson("/api/owner/live-classes/{$class->id}/mute-everyone")->assertOk();

        $this->assertDatabaseHas('live_class_participants', ['live_class_id' => $class->id, 'participant_type' => 'owner', 'participant_id' => $teacher->id, 'is_muted' => false]);
        $this->assertDatabaseHas('live_class_participants', ['live_class_id' => $class->id, 'participant_type' => 'student', 'participant_id' => $s1->id, 'is_muted' => true]);
        $this->assertDatabaseHas('live_class_participants', ['live_class_id' => $class->id, 'participant_type' => 'student', 'participant_id' => $s2->id, 'is_muted' => true]);
    }

    public function test_a_teacher_cannot_moderate_another_teachers_class(): void
    {
        // Object-level check flagged as an open question back in Phase 1:
        // live_class.manage lets a teacher run classes, but not someone
        // else's specific class.
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $hostTeacher = $this->makeOwner('host@live.test', 'teacher');
        $otherTeacher = $this->makeOwner('other@live.test', 'teacher');
        $class = LiveClass::create(['title' => 'x', 'host_id' => $hostTeacher->id, 'status' => 'live', 'started_at' => now()]);

        $token = $otherTeacher->createToken('test')->plainTextToken;
        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/owner/live-classes/{$class->id}/mute-everyone")
            ->assertStatus(403);
    }

    public function test_removed_participant_can_no_longer_be_counted_as_active(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $teacher = $this->makeOwner('teacher5@live.test', 'teacher');
        $token = $teacher->createToken('test')->plainTextToken;
        $class = LiveClass::create(['title' => 'x', 'host_id' => $teacher->id, 'status' => 'live', 'started_at' => now()]);
        $this->withHeader('Authorization', "Bearer {$token}")->postJson("/api/owner/live-classes/{$class->id}/start");

        $student = $this->makeStudent('aisha5@live.test');
        $this->actingAs($student, 'student')->postJson("/student/live-classes/{$class->id}/join");
        $participant = LiveClassParticipant::where('live_class_id', $class->id)->where('participant_id', $student->id)->firstOrFail();

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/owner/live-classes/{$class->id}/participants/{$participant->id}/remove")
            ->assertOk();

        $this->assertNotNull($participant->fresh()->left_at);
        $active = $this->withHeader('Authorization', "Bearer {$token}")->getJson("/api/owner/live-classes/{$class->id}/participants");
        $this->assertCount(1, $active->json('data')); // only the host remains
    }

    public function test_broadcast_failure_does_not_prevent_joining_a_class(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $teacher = $this->makeOwner('teacher6@live.test', 'teacher');
        $class = LiveClass::create(['title' => 'x', 'host_id' => $teacher->id, 'status' => 'live', 'started_at' => now()]);

        config(['broadcasting.default' => 'pusher']);
        config(['broadcasting.connections.pusher.options.host' => '127.0.0.1']);
        config(['broadcasting.connections.pusher.options.port' => 1]);
        config(['broadcasting.connections.pusher.key' => 'test-key']);
        config(['broadcasting.connections.pusher.secret' => 'test-secret']);
        config(['broadcasting.connections.pusher.app_id' => 'test-app']);

        $student = $this->makeStudent('aisha6@live.test');
        $response = $this->actingAs($student, 'student')->postJson("/student/live-classes/{$class->id}/join");

        $response->assertOk(); // NOT a 500
        $this->assertDatabaseHas('live_class_participants', ['live_class_id' => $class->id, 'participant_id' => $student->id]);
    }
}
