<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BooksApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeOwner(string $email, ?string $roleSlug): Owner
    {
        $owner = Owner::create([
            'firstname' => 'Test', 'lastname' => 'Owner', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => $email, 'phone' => '0700000070',
            'password' => Hash::make('password123'),
        ]);
        if ($roleSlug) {
            $owner->roles()->sync([Role::where('slug', $roleSlug)->firstOrFail()->id]);
        }
        return $owner;
    }

    public function test_full_book_crud_as_admin(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin@books.test', 'admin');
        $token = $admin->createToken('test')->plainTextToken;
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");

        Storage::fake(config('filesystems.default', 'public'));
        $pdf = UploadedFile::fake()->create('book.pdf', 500, 'application/pdf');

        $create = $auth()->postJson('/api/owner/books', [
            'title' => 'Uburyo bwo Gusoma Qur’an', 'author' => 'Sheikh Ali', 'category' => 'Tafsir',
            'book' => $pdf, 'status' => 'published',
        ]);
        $create->assertStatus(201);
        $id = $create->json('data.id');
        $this->assertDatabaseHas('books', ['id' => $id, 'title' => 'Uburyo bwo Gusoma Qur’an']);
        $this->assertSame(0, $create->json('data.downloads'));

        $show = $auth()->getJson("/api/owner/books/{$id}");
        $show->assertOk()->assertJsonPath('data.title', 'Uburyo bwo Gusoma Qur’an');
        $this->assertArrayHasKey('download_stats', $show->json('data'));

        $update = $auth()->post("/api/owner/books/{$id}", [
            '_method' => 'PUT', 'title' => 'Isomo ryahinduwe', 'status' => 'draft',
        ]);
        $update->assertOk()->assertJsonPath('data.title', 'Isomo ryahinduwe');

        $delete = $auth()->deleteJson("/api/owner/books/{$id}");
        $delete->assertOk();
        $this->assertDatabaseMissing('books', ['id' => $id]);
    }

    public function test_teacher_role_has_no_books_permissions(): void
    {
        // teacher was seeded darsat/courses/quizzes permissions but NOT books —
        // confirms books.* is scoped independently, not bundled with content roles.
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $teacher = $this->makeOwner('teacher@books.test', 'teacher');
        $token = $teacher->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/owner/books')
            ->assertStatus(403);
    }

    public function test_downloading_a_book_actually_logs_and_increments(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin2@books.test', 'admin');
        $token = $admin->createToken('test')->plainTextToken;

        Storage::fake(config('filesystems.default', 'public'));
        $pdf = UploadedFile::fake()->create('book.pdf', 500, 'application/pdf');
        $create = $this->withHeader('Authorization', "Bearer {$token}")->postJson('/api/owner/books', [
            'title' => 'Igitabo', 'book' => $pdf, 'status' => 'published',
        ]);
        $id = $create->json('data.id');

        // Download endpoint requires NO auth at all — matches public book browsing.
        $download1 = $this->postJson("/api/owner/books/{$id}/download");
        $download1->assertOk()->assertJsonPath('data.downloads', 1);

        $download2 = $this->postJson("/api/owner/books/{$id}/download");
        $download2->assertOk()->assertJsonPath('data.downloads', 2);

        $this->assertSame(2, \App\Models\BookDownload::where('book_id', $id)->count());
        $this->assertDatabaseHas('books', ['id' => $id, 'downloads' => 2]);
    }

    public function test_non_downloadable_book_refuses_download(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin3@books.test', 'admin');
        $token = $admin->createToken('test')->plainTextToken;

        Storage::fake(config('filesystems.default', 'public'));
        $pdf = UploadedFile::fake()->create('book.pdf', 500, 'application/pdf');
        $create = $this->withHeader('Authorization', "Bearer {$token}")->postJson('/api/owner/books', [
            'title' => 'Igitabo kidasohotse', 'book' => $pdf, 'status' => 'published', 'is_downloadable' => '0',
        ]);
        $id = $create->json('data.id');

        $response = $this->postJson("/api/owner/books/{$id}/download");
        $response->assertStatus(403);
        $this->assertSame(0, \App\Models\BookDownload::where('book_id', $id)->count());
    }

    public function test_downloading_a_draft_book_returns_404(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin4@books.test', 'admin');
        $token = $admin->createToken('test')->plainTextToken;

        Storage::fake(config('filesystems.default', 'public'));
        $pdf = UploadedFile::fake()->create('book.pdf', 500, 'application/pdf');
        $create = $this->withHeader('Authorization', "Bearer {$token}")->postJson('/api/owner/books', [
            'title' => 'Nturasohoka', 'book' => $pdf, 'status' => 'draft',
        ]);
        $id = $create->json('data.id');

        $this->postJson("/api/owner/books/{$id}/download")->assertStatus(404);
    }
}
