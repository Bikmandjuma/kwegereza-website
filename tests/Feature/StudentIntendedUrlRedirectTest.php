<?php

namespace Tests\Feature;

use App\Models\LiveClass;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentIntendedUrlRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_logged_out_student_clicking_a_live_class_link_lands_on_the_class_after_login(): void
    {
        $host = Owner::create([
            'firstname' => 'Sheikh', 'lastname' => 'Ahmad', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1980-01-01', 'is_verified' => 0,
            'email' => 'host@intended.test', 'phone' => '0700000290', 'password' => Hash::make('x'),
        ]);
        $liveClass = LiveClass::create(['title' => 'Tajwiid', 'host_id' => $host->id, 'status' => 'live']);
        $student = User::create([
            'user_code' => 'STD-INTEND', 'firstname' => 'Aisha', 'lastname' => 'Uwimana',
            'email' => 'aisha@intended.test', 'phone' => '0788444555', 'password' => Hash::make('password123'),
        ]);

        $classUrl = "/student/live-classes/{$liveClass->id}";
        $response = $this->get($classUrl);
        $response->assertRedirect(route('student.login'));

        $login = $this->post('/student/login', ['username' => 'aisha@intended.test', 'password' => 'password123']);
        $login->assertRedirect($classUrl);
    }

    public function test_a_student_navigating_to_login_directly_still_lands_on_the_dashboard(): void
    {
        $student = User::create([
            'user_code' => 'STD-NORMAL', 'firstname' => 'Jean', 'lastname' => 'Bosco',
            'email' => 'jean@intended.test', 'phone' => '0788555666', 'password' => Hash::make('password123'),
        ]);

        $login = $this->post('/student/login', ['username' => 'jean@intended.test', 'password' => 'password123']);
        $login->assertRedirect(route('student.dashboard'));
    }
}
