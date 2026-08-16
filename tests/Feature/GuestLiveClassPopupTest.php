<?php

namespace Tests\Feature;

use App\Models\LiveClass;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GuestLiveClassPopupTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_live_class_endpoint_returns_null_when_nothing_is_live(): void
    {
        $response = $this->getJson('/current-live-class');

        $response->assertOk();
        $this->assertNull($response->json('data'));
    }

    public function test_current_live_class_endpoint_returns_the_real_live_class_not_hardcoded_text(): void
    {
        $host = Owner::create([
            'firstname' => 'Sheikh', 'lastname' => 'Ahmad', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1980-01-01', 'is_verified' => 0,
            'email' => 'host@popup.test', 'phone' => '0700000295', 'password' => Hash::make('x'),
        ]);
        LiveClass::create(['title' => 'Tafsiiri ya Suratu Yasin', 'host_id' => $host->id, 'status' => 'live']);

        $response = $this->getJson('/current-live-class');

        $response->assertOk();
        $this->assertSame('Tafsiiri ya Suratu Yasin', $response->json('data.title'));
        $this->assertSame('Sheikh Ahmad', $response->json('data.host_name'));
        $this->assertFalse($response->json('data.is_student_logged_in'));
    }

    public function test_current_live_class_endpoint_correctly_reports_a_logged_in_student(): void
    {
        $host = Owner::create([
            'firstname' => 'Sheikh', 'lastname' => 'Ahmad', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1980-01-01', 'is_verified' => 0,
            'email' => 'host2@popup.test', 'phone' => '0700000296', 'password' => Hash::make('x'),
        ]);
        LiveClass::create(['title' => 'Hadiith', 'host_id' => $host->id, 'status' => 'live']);
        $student = User::create([
            'user_code' => 'STD-POPUP1', 'firstname' => 'Aisha', 'lastname' => 'Uwimana',
            'email' => 'aisha@popup.test', 'phone' => '0788666777', 'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($student, 'student')->getJson('/current-live-class');

        $this->assertTrue($response->json('data.is_student_logged_in'));
    }

    public function test_a_next_param_on_login_takes_a_student_to_the_class_after_authenticating(): void
    {
        $student = User::create([
            'user_code' => 'STD-POPUP2', 'firstname' => 'Jean', 'lastname' => 'Bosco',
            'email' => 'jean@popup.test', 'phone' => '0788777888', 'password' => Hash::make('password123'),
        ]);

        $this->get('/student/login?next=/student/live-classes/5');

        $login = $this->post('/student/login', ['username' => 'jean@popup.test', 'password' => 'password123']);
        $login->assertRedirect('/student/live-classes/5');
    }

    public function test_an_external_next_url_is_rejected_not_stored(): void
    {
        $student = User::create([
            'user_code' => 'STD-POPUP3', 'firstname' => 'Marie', 'lastname' => 'Claire',
            'email' => 'marie@popup.test', 'phone' => '0788888999', 'password' => Hash::make('password123'),
        ]);

        $this->get('/student/login?next=https://evil.com/phishing');
        $login = $this->post('/student/login', ['username' => 'marie@popup.test', 'password' => 'password123']);
        $login->assertRedirect(route('student.dashboard'));

        $this->get('/student/login?next=//evil.com/phishing');
        $login2 = $this->post('/student/login', ['username' => 'marie@popup.test', 'password' => 'password123']);
        $login2->assertRedirect(route('student.dashboard'));
    }
}
