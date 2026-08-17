<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OwnerManagementApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(string $email, string $roleSlug = 'admin'): Owner
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $owner = Owner::create([
            'firstname' => 'Admin', 'lastname' => 'User', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => $email, 'phone' => '0700000210',
            'password' => Hash::make('password123'),
        ]);
        $owner->roles()->sync([Role::where('slug', $roleSlug)->firstOrFail()->id]);
        return $owner;
    }

    public function test_admin_can_create_list_update_and_delete_a_staff_account(): void
    {
        $admin = $this->makeAdmin('admin@staff.test');
        $token = $admin->createToken('test')->plainTextToken;
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");

        $create = $auth()->postJson('/api/owner/staff', [
            'firstname' => 'New', 'lastname' => 'Teacher', 'gender' => 'male',
            'phone' => '0788111333', 'dob' => '1992-05-05', 'email' => 'new.teacher@staff.test',
            'role' => 'teacher', 'title' => 'ustadh', 'password' => 'password123',
            'role_slugs' => ['teacher'],
        ]);
        $create->assertStatus(201)->assertJsonPath('data.firstname', 'New');
        $newId = $create->json('data.id');

        $list = $auth()->getJson('/api/owner/staff');
        $list->assertOk();
        $this->assertSame(2, $list->json('meta.total'));

        $update = $auth()->postJson("/api/owner/staff/{$newId}", [
            'firstname' => 'Updated', 'lastname' => 'Teacher', 'gender' => 'male',
            'phone' => '0788111333', 'dob' => '1992-05-05', 'email' => 'new.teacher@staff.test',
            'role' => 'teacher', 'title' => 'ustadh',
        ]);
        $update->assertOk()->assertJsonPath('data.firstname', 'Updated');

        $delete = $auth()->deleteJson("/api/owner/staff/{$newId}");
        $delete->assertOk();
        $this->assertDatabaseMissing('owners', ['id' => $newId]);
    }

    public function test_teacher_role_cannot_access_staff_management(): void
    {
        $teacher = $this->makeAdmin('teacher@staff.test', 'teacher');
        $token = $teacher->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/owner/staff')
            ->assertStatus(403);
    }

    public function test_an_admin_cannot_delete_their_own_account(): void
    {
        $admin = $this->makeAdmin('admin2@staff.test');
        $token = $admin->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson("/api/owner/staff/{$admin->id}");

        $response->assertStatus(422);
        $this->assertDatabaseHas('owners', ['id' => $admin->id]);
    }

    public function test_staff_avatar_upload_respects_the_configured_disk(): void
    {
        Storage::fake('public');
        $admin = $this->makeAdmin('admin3@staff.test');
        $token = $admin->createToken('test')->plainTextToken;

        $file = UploadedFile::fake()->image('teacher.jpg');
        $response = $this->withHeader('Authorization', "Bearer {$token}")->post('/api/owner/staff', [
            'firstname' => 'Photo', 'lastname' => 'Teacher', 'gender' => 'female',
            'phone' => '0788222444', 'dob' => '1992-05-05', 'email' => 'photo.teacher@staff.test',
            'role' => 'teacher', 'title' => 'ustadha', 'password' => 'password123',
            'image' => $file,
        ]);

        $response->assertStatus(201);
        $this->assertNotNull($response->json('data.image_url'));
    }
}
