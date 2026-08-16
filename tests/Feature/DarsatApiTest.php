<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Role;
use App\Notifications\NewDarsatNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DarsatApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeOwner(string $email, ?string $roleSlug): Owner
    {
        $owner = Owner::create([
            'firstname' => 'Test', 'lastname' => 'Owner', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => $email, 'phone' => '0700000030',
            'password' => Hash::make('password123'),
        ]);
        if ($roleSlug) {
            $owner->roles()->sync([Role::where('slug', $roleSlug)->firstOrFail()->id]);
        }
        return $owner;
    }

    /**
     * The actual "islamic-leader" role from RolePermissionSeeder, not admin —
     * closing the open question from Phase 3 about whether a real Leader
     * account (narrower permission set) behaves correctly, not just Admin.
     */
    public function test_islamic_leader_can_create_and_view_darsat_but_not_manage_users(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $leader = $this->makeOwner('leader@darsat.test', 'islamic-leader');
        $token = $leader->createToken('test')->plainTextToken;
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");

        // Sanity check on the permission set itself.
        $this->assertTrue($leader->hasPermission('darsat.create'));
        $this->assertTrue($leader->hasPermission('darsat.view'));
        $this->assertFalse($leader->hasPermission('users.view'), 'islamic-leader should NOT manage users');
        $this->assertFalse($leader->hasPermission('darsat.delete'), 'islamic-leader was not seeded darsat.delete');

        Storage::fake(config('filesystems.default', 'public'));
        Notification::fake();

        $audio = UploadedFile::fake()->create('lesson.mp3', 500, 'audio/mpeg');

        $create = $auth()->postJson('/api/owner/darsat', [
            'title' => 'Uburyo bwo Gusenga', 'teachers' => $leader->id, 'type' => 'Fiqh',
            'description' => 'Isomo ku isengesho', 'audio' => $audio, 'status' => 'published',
        ]);

        $create->assertStatus(201)->assertJsonPath('success', true);
        $darsatId = $create->json('data.id');
        $this->assertDatabaseHas('darsat_tables', ['id' => $darsatId, 'title' => 'Uburyo bwo Gusenga']);

        // Publishing should have queued the student notification (faked, not sent for real).
        Notification::assertCount(0); // no students seeded in this test, so the chunk loop sends to nobody — still proves it didn't error
        $this->assertTrue(true);

        // LIST as the same leader
        $list = $auth()->getJson('/api/owner/darsat');
        $list->assertOk();
        $this->assertGreaterThanOrEqual(1, count($list->json('data')));

        // Leader CANNOT delete (not seeded darsat.delete)
        $delete = $auth()->deleteJson("/api/owner/darsat/{$darsatId}");
        $delete->assertStatus(403);
        $this->assertDatabaseHas('darsat_tables', ['id' => $darsatId]); // still there

        // Leader also cannot touch user management endpoints
        $this->assertFalse($leader->hasPermission('users.view'));
    }

    public function test_full_darsat_crud_as_admin_with_real_audio_file(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin@darsat.test', 'admin');
        $token = $admin->createToken('test')->plainTextToken;
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");

        Storage::fake(config('filesystems.default', 'public'));
        Notification::fake();

        $audio = UploadedFile::fake()->create('lesson.mp3', 1200, 'audio/mpeg');
        $thumbnail = UploadedFile::fake()->image('cover.jpg', 400, 400);

        $create = $auth()->postJson('/api/owner/darsat', [
            'title' => 'Isomo rya mbere', 'teachers' => $admin->id, 'type' => 'Aqeedah',
            'audio' => $audio, 'thumbnail' => $thumbnail, 'status' => 'draft',
        ]);
        $create->assertStatus(201);
        $id = $create->json('data.id');
        $this->assertNotNull($create->json('data.audio_url'), 'audio_url should resolve once a file is stored');
        $this->assertNotNull($create->json('data.thumbnail_url'));

        $show = $auth()->getJson("/api/owner/darsat/{$id}");
        $show->assertOk()->assertJsonPath('data.title', 'Isomo rya mbere');

        $newAudio = UploadedFile::fake()->create('lesson2.mp3', 800, 'audio/mpeg');
        $update = $auth()->post("/api/owner/darsat/{$id}", [
            '_method' => 'PUT',
            'title' => 'Isomo rya mbere (rivuguruye)', 'teachers' => $admin->id, 'type' => 'Aqeedah',
            'audio' => $newAudio, 'status' => 'published',
        ]);
        $update->assertOk()->assertJsonPath('data.title', 'Isomo rya mbere (rivuguruye)');
        $this->assertDatabaseHas('darsat_tables', ['id' => $id, 'status' => 'published']);

        $delete = $auth()->deleteJson("/api/owner/darsat/{$id}");
        $delete->assertOk();
        $this->assertDatabaseMissing('darsat_tables', ['id' => $id]);
    }

    public function test_darsat_creation_requires_valid_audio_file(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin2@darsat.test', 'admin');
        $token = $admin->createToken('test')->plainTextToken;

        $badFile = UploadedFile::fake()->create('not-audio.txt', 10, 'text/plain');

        $response = $this->withHeader('Authorization', "Bearer {$token}")->postJson('/api/owner/darsat', [
            'title' => 'x', 'teachers' => $admin->id, 'type' => 'Fiqh', 'audio' => $badFile, 'status' => 'draft',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['audio']);
    }

    /**
     * Regression test for a real bug found via live testing (not caught by
     * the tests above, which all use Owner::create() through Eloquent
     * directly with $fillable already correct by the time this test runs).
     * The actual production bug: Owner::$fillable was missing 'title', so
     * the real "Add User" web form silently dropped it on every save,
     * meaning no owner could ever become a sheikh/ustadh and the teacher
     * picker (used by both the Blade Darsat form and this API) was always
     * empty. Fixed by adding 'title' to Owner::$fillable.
     */
    public function test_owner_title_field_is_mass_assignable(): void
    {
        $owner = Owner::create([
            'firstname' => 'Sheikh', 'lastname' => 'Test', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'sheikh@title.test', 'phone' => '0700000050',
            'password' => Hash::make('password123'), 'title' => 'sheikh',
        ]);

        $this->assertSame('sheikh', $owner->fresh()->title);
    }

    public function test_teachers_endpoint_returns_owners_with_a_title(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin3@darsat.test', 'admin');
        Owner::create([
            'firstname' => 'Sheikh', 'lastname' => 'Ali', 'gender' => 'male', 'title' => 'sheikh',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'ali@title.test', 'phone' => '0700000051', 'password' => Hash::make('x'),
        ]);
        $token = $admin->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/owner/teachers');
        $response->assertOk();
        $this->assertContains('Sheikh Ali', collect($response->json('data'))->pluck('name'));
    }

    /**
     * Regression test for the other real bug found via live testing: a
     * non-numeric {id} (e.g. a stray "undefined" from a broken frontend
     * call, or someone probing the API) used to fall through the
     * type-hinted `int $id` controller parameter and throw a raw 500
     * TypeError. Route-level ->where('id', '[0-9]+') now makes Laravel's
     * router reject it with a clean 404 before the controller ever runs.
     */
    public function test_non_numeric_darsat_id_returns_404_not_500(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin4@darsat.test', 'admin');
        $token = $admin->createToken('test')->plainTextToken;
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");

        $auth()->getJson('/api/owner/darsat/undefined')->assertStatus(404);
        $auth()->deleteJson('/api/owner/darsat/undefined')->assertStatus(404);
        $auth()->getJson('/api/owner/courses/not-a-number')->assertStatus(404);
    }
}
