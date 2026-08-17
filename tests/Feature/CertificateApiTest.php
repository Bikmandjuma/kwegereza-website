<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CertificateApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_issue_and_list_certificates(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = Owner::create([
            'firstname' => 'Admin', 'lastname' => 'User', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'admin@cert.test', 'phone' => '0700000250',
            'password' => Hash::make('password123'),
        ]);
        $admin->roles()->sync([Role::where('slug', 'admin')->firstOrFail()->id]);
        $token = $admin->createToken('test')->plainTextToken;

        $student = User::create([
            'user_code' => 'STD-CERT01', 'firstname' => 'Aisha', 'lastname' => 'Uwimana',
            'email' => 'aisha@cert.test', 'phone' => '0788333444', 'password' => Hash::make('x'),
        ]);

        $create = $this->withHeader('Authorization', "Bearer {$token}")->postJson('/api/owner/certificates', [
            'user_id' => $student->id, 'title' => 'Certificate of Excellence',
        ]);
        $create->assertStatus(201)->assertJsonPath('data.title', 'Certificate of Excellence');
        $this->assertNotNull($create->json('data.certificate_number'));

        $list = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/owner/certificates');
        $list->assertOk();
        $this->assertSame(1, $list->json('meta.total'));
    }

    public function test_teacher_role_cannot_issue_certificates(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $teacher = Owner::create([
            'firstname' => 'Teacher', 'lastname' => 'User', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'teacher@cert.test', 'phone' => '0700000251',
            'password' => Hash::make('password123'),
        ]);
        $teacher->roles()->sync([Role::where('slug', 'teacher')->firstOrFail()->id]);
        $token = $teacher->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/owner/certificates', ['user_id' => 1, 'title' => 'x'])
            ->assertStatus(403);
    }
}
