<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WebBookControllerRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_still_manage_books_through_the_web_panel(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = Role::where('slug', 'admin')->firstOrFail();
        $owner = Owner::create([
            'firstname' => 'Web', 'lastname' => 'Admin', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'webadmin@books.test', 'phone' => '0700000080',
            'password' => Hash::make('password123'),
        ]);
        $owner->roles()->sync([$admin->id]);

        Storage::fake(config('filesystems.default', 'public'));
        $pdf = UploadedFile::fake()->create('book.pdf', 500, 'application/pdf');

        $create = $this->actingAs($owner, 'owner')->post('/owner/books/store', [
            'title' => 'Web Panel Book', 'author' => 'x', 'status' => 'published', 'book' => $pdf,
        ]);
        $create->assertRedirect();
        $this->assertDatabaseHas('books', ['title' => 'Web Panel Book']);

        $book = \App\Models\Book::where('title', 'Web Panel Book')->firstOrFail();

        $update = $this->actingAs($owner, 'owner')->put("/owner/books/{$book->id}", [
            'title' => 'Web Panel Book (Edited)', 'status' => 'draft',
        ]);
        $update->assertRedirect();
        $this->assertDatabaseHas('books', ['id' => $book->id, 'status' => 'draft']);

        $delete = $this->actingAs($owner, 'owner')->delete("/owner/books/{$book->id}");
        $delete->assertRedirect();
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }
}
