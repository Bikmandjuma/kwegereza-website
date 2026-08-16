<?php

namespace Tests\Feature;

use App\Models\Inyandiko;
use App\Models\Owner;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CommentsEnabledTest extends TestCase
{
    use RefreshDatabase;

    public function test_comments_enabled_is_actually_mass_assignable(): void
    {
        $item = Inyandiko::create([
            'title' => 'x', 'slug' => 'x-'.uniqid(), 'status' => 'draft', 'comments_enabled' => false,
        ]);

        $this->assertFalse($item->fresh()->comments_enabled);
    }

    public function test_admin_can_disable_comments_on_an_article_via_the_update_endpoint(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = Owner::create([
            'firstname' => 'Test', 'lastname' => 'Owner', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'admin@comments.test', 'phone' => '0700000190',
            'password' => Hash::make('password123'),
        ]);
        $admin->roles()->sync([Role::where('slug', 'admin')->firstOrFail()->id]);

        $item = Inyandiko::create(['title' => 'x', 'slug' => 'x-'.uniqid(), 'status' => 'published', 'comments_enabled' => true]);

        $response = $this->actingAs($admin, 'owner')->put("/owner/inyandiko_zabamenyi/{$item->id}", [
            'title' => 'x', 'status' => 'published', 'comments_enabled' => '0',
        ]);

        $response->assertRedirect();
        $this->assertFalse((bool) $item->fresh()->comments_enabled);
    }

    public function test_omitting_the_field_on_update_preserves_the_existing_value_rather_than_flipping_it(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = Owner::create([
            'firstname' => 'Test', 'lastname' => 'Owner', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'admin2@comments.test', 'phone' => '0700000191',
            'password' => Hash::make('password123'),
        ]);
        $admin->roles()->sync([Role::where('slug', 'admin')->firstOrFail()->id]);

        $item = Inyandiko::create(['title' => 'x', 'slug' => 'x-'.uniqid(), 'status' => 'published', 'comments_enabled' => true]);

        $this->actingAs($admin, 'owner')->put("/owner/inyandiko_zabamenyi/{$item->id}", [
            'title' => 'Updated title', 'status' => 'published',
        ]);

        $this->assertTrue((bool) $item->fresh()->comments_enabled);
    }
}
