<?php

namespace Tests\Feature;

use App\Models\Amatangazo;
use App\Models\Owner;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class WebAmatangazoControllerRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_still_manage_announcements_through_the_web_panel(): void
    {
        Notification::fake();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = Role::where('slug', 'admin')->firstOrFail();
        $owner = Owner::create([
            'firstname' => 'Web', 'lastname' => 'Admin', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'webadmin@amatangazo.test', 'phone' => '0700000091',
            'password' => Hash::make('password123'),
        ]);
        $owner->roles()->sync([$admin->id]);

        $create = $this->actingAs($owner, 'owner')->post('/owner/amatangazo', [
            'title' => 'Web Panel Announcement', 'status' => 'upcoming',
        ]);
        $create->assertRedirect();
        $this->assertDatabaseHas('amatangazos', ['title' => 'Web Panel Announcement']);

        $announcement = Amatangazo::where('title', 'Web Panel Announcement')->firstOrFail();
        $this->assertTrue($announcement->is_published, 'Create should still default to published, matching prior behavior.');

        // Toggle it hidden, exactly like clicking the eye icon.
        $this->actingAs($owner, 'owner')->patch("/owner/amatangazo/{$announcement->id}/toggle");
        $this->assertFalse($announcement->fresh()->is_published);

        // Now edit it via the real edit form's field set (NO is_published,
        // matching the actual #editModal in amatangazo.blade.php) — this is
        // exactly the scenario that used to silently re-publish it.
        $update = $this->actingAs($owner, 'owner')->put("/owner/amatangazo/{$announcement->id}", [
            'title' => 'Web Panel Announcement (Edited)', 'status' => 'upcoming', 'presenter' => 'x',
        ]);
        $update->assertRedirect();
        $this->assertFalse($announcement->fresh()->is_published, 'Editing through the real web form must not silently republish a hidden announcement.');

        $delete = $this->actingAs($owner, 'owner')->delete("/owner/amatangazo/{$announcement->id}");
        $delete->assertRedirect();
        $this->assertDatabaseMissing('amatangazos', ['id' => $announcement->id]);
    }
}
