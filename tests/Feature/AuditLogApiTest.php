<?php

namespace Tests\Feature;

use App\Models\DarsatTable;
use App\Models\Owner;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuditLogApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_audit_logs_reflecting_a_real_action(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = Owner::create([
            'firstname' => 'Admin', 'lastname' => 'User', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'admin@auditlog.test', 'phone' => '0700000240',
            'password' => Hash::make('password123'),
        ]);
        $admin->roles()->sync([Role::where('slug', 'admin')->firstOrFail()->id]);
        $token = $admin->createToken('test')->plainTextToken;

        DarsatTable::create(['title' => 'Real Dars', 'teachers' => $admin->id, 'type' => 'Fiqh', 'audio' => 'x.mp3', 'status' => 'published']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/owner/audit-logs');
        $response->assertOk();

        $this->assertGreaterThanOrEqual(1, $response->json('meta.total'));
        $entry = collect($response->json('data'))->firstWhere('entity_label', 'Real Dars');
        $this->assertNotNull($entry);
        $this->assertSame('created', $entry['action']);
    }

    public function test_teacher_role_cannot_view_audit_logs(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $teacher = Owner::create([
            'firstname' => 'Teacher', 'lastname' => 'User', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'teacher@auditlog.test', 'phone' => '0700000241',
            'password' => Hash::make('password123'),
        ]);
        $teacher->roles()->sync([Role::where('slug', 'teacher')->firstOrFail()->id]);
        $token = $teacher->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/owner/audit-logs')
            ->assertStatus(403);
    }
}
