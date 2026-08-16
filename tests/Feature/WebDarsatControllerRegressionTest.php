<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WebDarsatControllerRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_still_manage_darsat_through_the_web_panel(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = Role::where('slug', 'admin')->firstOrFail();
        $owner = Owner::create([
            'firstname' => 'Web', 'lastname' => 'Admin', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'webadmin@darsat.test', 'phone' => '0700000040',
            'password' => Hash::make('password123'),
        ]);
        $owner->roles()->sync([$admin->id]);

        Storage::fake(config('filesystems.default', 'public'));
        Notification::fake();

        $audio = UploadedFile::fake()->create('lesson.mp3', 500, 'audio/mpeg');

        $create = $this->actingAs($owner, 'owner')->post('/owner/storeDarsat', [
            'title' => 'Web Panel Darsat', 'teachers' => $owner->id, 'type' => 'Tafsir',
            'description' => 'x', 'audio' => $audio, 'status' => 'published',
        ]);
        $create->assertRedirect();
        $this->assertDatabaseHas('darsat_tables', ['title' => 'Web Panel Darsat']);

        $darsat = \App\Models\DarsatTable::where('title', 'Web Panel Darsat')->firstOrFail();

        $update = $this->actingAs($owner, 'owner')->put("/owner/darsat/{$darsat->id}", [
            'title' => 'Web Panel Darsat (Edited)', 'teachers' => $owner->id, 'type' => 'Tafsir',
            'status' => 'draft',
        ]);
        $update->assertRedirect();
        $this->assertDatabaseHas('darsat_tables', ['id' => $darsat->id, 'status' => 'draft']);

        $delete = $this->actingAs($owner, 'owner')->delete("/owner/darsat/{$darsat->id}");
        $delete->assertRedirect();
        $this->assertDatabaseMissing('darsat_tables', ['id' => $darsat->id]);
    }
}
