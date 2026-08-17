<?php

namespace Tests\Feature;

use App\Models\Amatangazo;
use App\Models\Owner;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AnnouncementsApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeOwner(string $email, ?string $roleSlug): Owner
    {
        $owner = Owner::create([
            'firstname' => 'Test', 'lastname' => 'Owner', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => $email, 'phone' => '0700000090',
            'password' => Hash::make('password123'),
        ]);
        if ($roleSlug) {
            $owner->roles()->sync([Role::where('slug', $roleSlug)->firstOrFail()->id]);
        }
        return $owner;
    }

    public function test_full_announcement_crud_as_admin(): void
    {
        Notification::fake();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin@amatangazo.test', 'admin');
        $token = $admin->createToken('test')->plainTextToken;
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");

        $create = $auth()->postJson('/api/owner/amatangazo', [
            'title' => 'Amasomo azaba ejo', 'status' => 'upcoming', 'presenter' => 'Sheikh Ali',
        ]);
        $create->assertStatus(201)->assertJsonPath('data.is_published', true);
        $id = $create->json('data.id');

        $list = $auth()->getJson('/api/owner/amatangazo');
        $list->assertOk();
        $this->assertSame(1, $list->json('meta.total'));

        $show = $auth()->getJson("/api/owner/amatangazo/{$id}");
        $show->assertOk()->assertJsonPath('data.title', 'Amasomo azaba ejo');

        $delete = $auth()->deleteJson("/api/owner/amatangazo/{$id}");
        $delete->assertOk();
        $this->assertDatabaseMissing('amatangazos', ['id' => $id]);
    }

    public function test_toggle_publish(): void
    {
        Notification::fake();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin2@amatangazo.test', 'admin');
        $token = $admin->createToken('test')->plainTextToken;
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");

        $announcement = Amatangazo::create([
            'title' => 'x', 'status' => 'live', 'is_published' => true,
            'published_at' => now(), 'created_by' => $admin->id,
        ]);

        $toggle1 = $auth()->patchJson("/api/owner/amatangazo/{$announcement->id}/toggle");
        $toggle1->assertOk()->assertJsonPath('data.is_published', false);

        $toggle2 = $auth()->patchJson("/api/owner/amatangazo/{$announcement->id}/toggle");
        $toggle2->assertOk()->assertJsonPath('data.is_published', true);
    }

    /**
     * Regression test for a real, confirmed bug found while extracting
     * AnnouncementService: the Blade edit form (#editModal in
     * amatangazo.blade.php) has NO is_published field at all, but the
     * pre-extraction updateAmatangazo() defaulted a missing field to
     * `true` via $request->boolean('is_published', true) — meaning every
     * single edit silently re-published a hidden announcement, with no
     * way for an admin editing a typo to avoid it. Fixed: is_published is
     * now only changed when the request actually sends it.
     */
    public function test_editing_an_announcement_does_not_silently_republish_it(): void
    {
        Notification::fake();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin3@amatangazo.test', 'admin');
        $token = $admin->createToken('test')->plainTextToken;

        $announcement = Amatangazo::create([
            'title' => 'Original', 'status' => 'done', 'is_published' => false,
            'published_at' => now(), 'created_by' => $admin->id,
        ]);
        $this->assertFalse($announcement->is_published);

        // Edit WITHOUT sending is_published at all — exactly what the real
        // edit form does today.
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->post("/api/owner/amatangazo/{$announcement->id}", [
                '_method' => 'PUT', 'title' => 'Fixed a typo', 'status' => 'done',
            ]);

        $response->assertOk()->assertJsonPath('data.is_published', false);
        $this->assertFalse($announcement->fresh()->is_published, 'A hidden announcement must stay hidden after an edit that never mentions publish state.');
    }

    public function test_moderator_cannot_manage_announcements(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $moderator = $this->makeOwner('mod@amatangazo.test', 'moderator');
        $token = $moderator->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/owner/amatangazo', ['title' => 'x', 'status' => 'live'])
            ->assertStatus(403);
    }
}
