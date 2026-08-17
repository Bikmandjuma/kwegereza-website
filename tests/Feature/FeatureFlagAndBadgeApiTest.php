<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\FeatureFlag;
use App\Models\Owner;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FeatureFlagAndBadgeApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(string $email): Owner
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $owner = Owner::create([
            'firstname' => 'Admin', 'lastname' => 'User', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => $email, 'phone' => '0700000260',
            'password' => Hash::make('password123'),
        ]);
        $owner->roles()->sync([Role::where('slug', 'admin')->firstOrFail()->id]);
        return $owner;
    }

    public function test_admin_can_toggle_a_feature_flag(): void
    {
        $admin = $this->makeAdmin('admin@flags.test');
        $token = $admin->createToken('test')->plainTextToken;
        $flag = FeatureFlag::create(['key' => 'guest_chat', 'label' => 'Guest Chat', 'is_enabled' => true]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->postJson("/api/owner/feature-flags/{$flag->id}/toggle");
        $response->assertOk()->assertJsonPath('data.is_enabled', false);
        $this->assertFalse($flag->fresh()->is_enabled);
    }

    public function test_teacher_role_cannot_manage_feature_flags(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $teacher = Owner::create([
            'firstname' => 'Teacher', 'lastname' => 'User', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'teacher@flags.test', 'phone' => '0700000261',
            'password' => Hash::make('password123'),
        ]);
        $teacher->roles()->sync([Role::where('slug', 'teacher')->firstOrFail()->id]);
        $token = $teacher->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/owner/feature-flags')
            ->assertStatus(403);
    }

    public function test_admin_can_create_update_and_delete_a_badge(): void
    {
        $admin = $this->makeAdmin('admin2@flags.test');
        $token = $admin->createToken('test')->plainTextToken;
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");

        $create = $auth()->postJson('/api/owner/badges', [
            'name' => '7 Day Streak', 'criteria_type' => 'streak_days', 'criteria_value' => 7,
        ]);
        $create->assertStatus(201);
        $badgeId = $create->json('data.id');

        $update = $auth()->putJson("/api/owner/badges/{$badgeId}", [
            'name' => '14 Day Streak', 'criteria_type' => 'streak_days', 'criteria_value' => 14,
        ]);
        $update->assertOk()->assertJsonPath('data.criteria_value', 14);

        $delete = $auth()->deleteJson("/api/owner/badges/{$badgeId}");
        $delete->assertOk();
        $this->assertDatabaseMissing('badges', ['id' => $badgeId]);
    }
}
