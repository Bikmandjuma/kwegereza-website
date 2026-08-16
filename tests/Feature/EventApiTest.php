<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Owner;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EventApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(string $email): Owner
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $owner = Owner::create([
            'firstname' => 'Admin', 'lastname' => 'User', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => $email, 'phone' => '0700000270',
            'password' => Hash::make('password123'),
        ]);
        $owner->roles()->sync([Role::where('slug', 'admin')->firstOrFail()->id]);
        return $owner;
    }

    private function makeStudent(string $email): User
    {
        return User::create([
            'user_code' => 'STD-'.strtoupper(substr(md5($email), 0, 6)),
            'firstname' => 'Test', 'lastname' => 'Student',
            'email' => $email, 'phone' => '0788'.substr(md5($email), 0, 6),
            'password' => Hash::make('x'),
        ]);
    }

    public function test_admin_can_create_update_and_delete_an_event(): void
    {
        $admin = $this->makeAdmin('admin@events.test');
        $token = $admin->createToken('test')->plainTextToken;
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");

        $create = $auth()->postJson('/api/owner/events', [
            'title' => 'Iterambere', 'starts_at' => now()->addDays(3)->format('Y-m-d\TH:i'), 'status' => 'published',
        ]);
        $create->assertStatus(201);
        $eventId = $create->json('data.id');

        $update = $auth()->postJson("/api/owner/events/{$eventId}", [
            '_method' => 'PUT', 'title' => 'Iterambere (Updated)',
            'starts_at' => now()->addDays(3)->format('Y-m-d\TH:i'), 'status' => 'published',
        ]);
        $update->assertOk()->assertJsonPath('data.title', 'Iterambere (Updated)');

        $delete = $auth()->deleteJson("/api/owner/events/{$eventId}");
        $delete->assertOk();
        $this->assertDatabaseMissing('events', ['id' => $eventId]);
    }

    public function test_registration_is_rejected_once_capacity_is_reached(): void
    {
        $event = Event::create([
            'title' => 'Small Event', 'slug' => 'small-event-'.uniqid(), 'status' => 'published',
            'starts_at' => now()->addDay(), 'capacity' => 1,
        ]);
        $studentA = $this->makeStudent('a@events.test');
        $studentB = $this->makeStudent('b@events.test');

        $this->actingAs($studentA, 'student')->post("/student/events/{$event->slug}/register")->assertRedirect();
        $this->assertSame(1, EventRegistration::where('event_id', $event->id)->count());

        $this->actingAs($studentB, 'student')->post("/student/events/{$event->slug}/register")->assertRedirect();

        $this->assertSame(1, EventRegistration::where('event_id', $event->id)->count(), 'Capacity of 1 must not be exceeded.');
        $this->assertFalse(EventRegistration::where('event_id', $event->id)->where('user_id', $studentB->id)->exists());
    }

    public function test_teacher_role_cannot_create_events(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $teacher = Owner::create([
            'firstname' => 'Teacher', 'lastname' => 'User', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'teacher@events.test', 'phone' => '0700000271',
            'password' => Hash::make('password123'),
        ]);
        $teacher->roles()->sync([Role::where('slug', 'teacher')->firstOrFail()->id]);
        $token = $teacher->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/owner/events', ['title' => 'x', 'starts_at' => now()->format('Y-m-d\TH:i'), 'status' => 'draft'])
            ->assertStatus(403);
    }
}
