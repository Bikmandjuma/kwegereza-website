<?php

namespace Tests\Feature;

use App\Models\Owner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OwnerProfileApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeOwner(string $email = 'owner@profile.test'): Owner
    {
        return Owner::create([
            'firstname' => 'Test', 'lastname' => 'Owner', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => $email, 'phone' => '0700000170',
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_owner_can_view_and_update_their_own_profile(): void
    {
        $owner = $this->makeOwner();
        $token = $owner->createToken('test')->plainTextToken;
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");

        $show = $auth()->getJson('/api/owner/profile');
        $show->assertOk()->assertJsonPath('data.firstname', 'Test');

        $update = $auth()->putJson('/api/owner/profile', [
            'firstname' => 'Updated', 'lastname' => 'Name', 'phone' => '0788111222', 'bio' => 'Ndi umwarimu.',
        ]);
        $update->assertOk()->assertJsonPath('data.firstname', 'Updated');
        $this->assertDatabaseHas('owners', ['id' => $owner->id, 'firstname' => 'Updated', 'bio' => 'Ndi umwarimu.']);
    }

    public function test_owner_can_change_password_with_correct_current_password(): void
    {
        $owner = $this->makeOwner('owner2@profile.test');
        $token = $owner->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")->postJson('/api/owner/profile/password', [
            'current_password' => 'password123', 'new_password' => 'newpassword456', 'new_password_confirmation' => 'newpassword456',
        ]);

        $response->assertOk();
        $this->assertTrue(Hash::check('newpassword456', $owner->fresh()->password));
    }

    public function test_wrong_current_password_is_rejected(): void
    {
        $owner = $this->makeOwner('owner3@profile.test');
        $token = $owner->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")->postJson('/api/owner/profile/password', [
            'current_password' => 'totally-wrong', 'new_password' => 'newpassword456', 'new_password_confirmation' => 'newpassword456',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('current_password');
        $this->assertTrue(Hash::check('password123', $owner->fresh()->password), 'Password must NOT have changed.');
    }

    public function test_owner_can_upload_an_avatar(): void
    {
        Storage::fake('public');
        $owner = $this->makeOwner('owner4@profile.test');
        $token = $owner->createToken('test')->plainTextToken;

        $file = UploadedFile::fake()->image('avatar.jpg');
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->post('/api/owner/profile/avatar', ['avatar' => $file]);

        $response->assertOk();
        $newImage = $owner->fresh()->image;
        $this->assertNotSame('user.png', $newImage);
        Storage::disk('public')->assertExists('avatars/'.$newImage);
    }

    /**
     * Regression test for a bug I introduced in the Profile phase itself:
     * the avatar upload originally hardcoded Storage::disk('public')
     * directly, rather than respecting FILESYSTEM_DISK like every other
     * upload in the app (Darsat audio, book PDFs, Inyandiko images, all
     * via HandlesFileUploads) — meaning avatars specifically would still
     * be lost on Railway/any ephemeral host even after correctly
     * configuring S3 for everything else. Proves the upload actually
     * goes wherever config('filesystems.default') points, not a
     * hardcoded disk name.
     */
    public function test_avatar_upload_respects_the_configured_filesystem_disk_not_a_hardcoded_one(): void
    {
        config(['filesystems.default' => 'local']);
        Storage::fake('local');
        Storage::fake('public'); // if this receives the file instead, the bug has regressed

        $owner = $this->makeOwner('owner6@profile.test');
        $token = $owner->createToken('test')->plainTextToken;

        $file = UploadedFile::fake()->image('avatar.jpg');
        $this->withHeader('Authorization', "Bearer {$token}")
            ->post('/api/owner/profile/avatar', ['avatar' => $file])
            ->assertOk();

        $newImage = $owner->fresh()->image;
        Storage::disk('local')->assertExists('avatars/'.$newImage);
        Storage::disk('public')->assertMissing('avatars/'.$newImage);
    }

    public function test_a_non_image_file_is_rejected_as_an_avatar(): void
    {
        Storage::fake('public');
        $owner = $this->makeOwner('owner5@profile.test');
        $token = $owner->createToken('test')->plainTextToken;

        $file = UploadedFile::fake()->create('document.pdf', 100);
        $this->withHeader('Authorization', "Bearer {$token}")
            ->withHeader('Accept', 'application/json')
            ->post('/api/owner/profile/avatar', ['avatar' => $file])
            ->assertStatus(422);
    }

    public function test_an_unauthenticated_request_cannot_view_a_profile(): void
    {
        $this->getJson('/api/owner/profile')->assertStatus(401);
    }
}
