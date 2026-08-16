<?php

namespace Tests\Feature;

use App\Models\LiveClass;
use App\Models\LiveClassParticipant;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentLiveClassPageTest extends TestCase
{
    use RefreshDatabase;

    private function makeStudent(string $email): User
    {
        return User::create([
            'user_code' => 'STD-'.strtoupper(substr(md5($email), 0, 6)),
            'firstname' => 'Test', 'lastname' => 'Student',
            'email' => $email, 'phone' => '0788'.substr(md5($email), 0, 6),
            'password' => Hash::make('password123'),
        ]);
    }

    private function makeHost(string $email): Owner
    {
        return Owner::create([
            'firstname' => 'Sheikh', 'lastname' => 'Ahmad', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1980-01-01', 'is_verified' => 0,
            'email' => $email, 'phone' => '0700000291', 'password' => Hash::make('x'),
        ]);
    }

    public function test_a_logged_in_student_can_view_the_live_class_page_when_the_class_is_live(): void
    {
        $host = $this->makeHost('host1@liveview.test');
        $student = $this->makeStudent('student1@liveview.test');
        $liveClass = LiveClass::create(['title' => 'Tafsiir', 'host_id' => $host->id, 'status' => 'live']);

        $response = $this->actingAs($student, 'student')->get("/student/live-classes/{$liveClass->id}");

        $response->assertOk();
        $response->assertSee('Tafsiir');
        $response->assertSee('Fungura ijwi');
    }

    public function test_the_page_does_not_render_join_controls_when_the_class_is_not_live(): void
    {
        $host = $this->makeHost('host2@liveview.test');
        $student = $this->makeStudent('student2@liveview.test');
        $liveClass = LiveClass::create(['title' => 'Fiqh', 'host_id' => $host->id, 'status' => 'scheduled']);

        $response = $this->actingAs($student, 'student')->get("/student/live-classes/{$liveClass->id}");

        $response->assertOk();
        $response->assertDontSee('Fungura ijwi');
        $response->assertSee('Ntabwo birimo gukorwa');
    }

    public function test_the_participants_endpoint_reflects_who_actually_joined(): void
    {
        $host = $this->makeHost('host3@liveview.test');
        $student = $this->makeStudent('student3@liveview.test');
        $liveClass = LiveClass::create(['title' => 'Hadiith', 'host_id' => $host->id, 'status' => 'live']);
        LiveClassParticipant::create([
            'live_class_id' => $liveClass->id, 'participant_type' => 'student',
            'participant_id' => $student->id, 'role' => 'listener', 'joined_at' => now(),
        ]);

        $response = $this->actingAs($student, 'student')->getJson("/student/live-classes/{$liveClass->id}/participants");

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $this->assertSame("student:{$student->id}", $response->json('data.0.key'));
    }
}
