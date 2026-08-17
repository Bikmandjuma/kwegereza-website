<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookDownload;
use App\Models\DarsatProgress;
use App\Models\DarsatTable;
use App\Models\Owner;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AnalyticsApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeOwner(string $email, ?string $roleSlug): Owner
    {
        $owner = Owner::create([
            'firstname' => 'Test', 'lastname' => 'Owner', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => $email, 'phone' => '0700000150',
            'password' => Hash::make('password123'),
        ]);
        if ($roleSlug) {
            $owner->roles()->sync([Role::where('slug', $roleSlug)->firstOrFail()->id]);
        }
        return $owner;
    }

    private function makeStudent(string $email): User
    {
        return User::create([
            'user_code' => 'STD-'.strtoupper(substr(md5($email), 0, 6)),
            'firstname' => 'Test', 'lastname' => 'Student',
            'email' => $email, 'phone' => '0788'.substr(md5($email), 0, 6),
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_teacher_role_cannot_access_analytics(): void
    {
        // teacher was not seeded analytics.view — only admin/super-admin get it.
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $teacher = $this->makeOwner('teacher@analytics.test', 'teacher');
        $token = $teacher->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/owner/analytics/overview')
            ->assertStatus(403);
    }

    public function test_most_played_darsat_reflects_real_progress_data(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin@analytics.test', 'admin');
        $token = $admin->createToken('test')->plainTextToken;

        $popular = DarsatTable::create(['title' => 'Popular Dars', 'teachers' => $admin->id, 'type' => 'Fiqh', 'audio' => 'x.mp3', 'status' => 'published']);
        $unpopular = DarsatTable::create(['title' => 'Unpopular Dars', 'teachers' => $admin->id, 'type' => 'Fiqh', 'audio' => 'y.mp3', 'status' => 'published']);

        $s1 = $this->makeStudent('s1@analytics.test');
        $s2 = $this->makeStudent('s2@analytics.test');
        DarsatProgress::create(['user_id' => $s1->id, 'darsat_id' => $popular->id, 'times_played' => 5]);
        DarsatProgress::create(['user_id' => $s2->id, 'darsat_id' => $popular->id, 'times_played' => 3]);
        DarsatProgress::create(['user_id' => $s1->id, 'darsat_id' => $unpopular->id, 'times_played' => 1]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/owner/analytics/overview');
        $response->assertOk();

        $topDarsat = $response->json('data.most_played_darsat.0');
        $this->assertSame('Popular Dars', $topDarsat['title']);
        $this->assertSame(8, $topDarsat['total_plays']); // 5 + 3, real sum not a fabricated number
        $this->assertSame(2, $topDarsat['unique_listeners']);
    }

    public function test_most_downloaded_books_reflects_real_download_data(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin2@analytics.test', 'admin');
        $token = $admin->createToken('test')->plainTextToken;

        $book = Book::create(['title' => 'Popular Book', 'book' => 'file.pdf', 'status' => 'published', 'is_downloadable' => true, 'downloads' => 3]);
        $s1 = $this->makeStudent('s3@analytics.test');
        BookDownload::create(['book_id' => $book->id, 'user_id' => $s1->id, 'ip_address' => '1.1.1.1']);
        BookDownload::create(['book_id' => $book->id, 'user_id' => null, 'ip_address' => '2.2.2.2']);
        BookDownload::create(['book_id' => $book->id, 'user_id' => null, 'ip_address' => '3.3.3.3']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/owner/analytics/overview');
        $response->assertOk();

        $topBook = $response->json('data.most_downloaded_books.0');
        $this->assertSame('Popular Book', $topBook['title']);
        $this->assertSame(3, $topBook['total_downloads']);
    }

    public function test_exam_participation_reflects_real_quiz_attempts(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin3@analytics.test', 'admin');
        $token = $admin->createToken('test')->plainTextToken;

        $quiz = Quiz::create(['title' => 'x', 'passing_percentage' => 50, 'status' => 'published']);
        $s1 = $this->makeStudent('s4@analytics.test');
        $s2 = $this->makeStudent('s5@analytics.test');
        $s3 = $this->makeStudent('s6@analytics.test');
        QuizAttempt::create(['quiz_id' => $quiz->id, 'user_id' => $s1->id, 'score' => 8, 'total_points' => 10, 'percentage' => 80, 'passed' => true, 'started_at' => now(), 'submitted_at' => now()]);
        QuizAttempt::create(['quiz_id' => $quiz->id, 'user_id' => $s2->id, 'score' => 3, 'total_points' => 10, 'percentage' => 30, 'passed' => false, 'started_at' => now(), 'submitted_at' => now()]);
        QuizAttempt::create(['quiz_id' => $quiz->id, 'user_id' => $s3->id, 'started_at' => now()]); // still in progress

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/owner/analytics/overview');
        $response->assertOk();

        $participation = $response->json('data.exam_participation');
        $this->assertSame(2, $participation['total_attempts']);
        $this->assertSame(1, $participation['passed']);
        $this->assertSame(1, $participation['failed']);
        $this->assertEquals(50.0, $participation['pass_rate']);
        $this->assertSame(1, $participation['in_progress']);
    }

    public function test_analytics_endpoint_never_exposes_correct_quiz_answers(): void
    {
        // Sanity check consistent with spec section 12's "do not expose
        // correct answers unnecessarily through the API" — analytics is a
        // different endpoint than quiz management, so double-checking it
        // doesn't leak question/answer data at all.
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin4@analytics.test', 'admin');
        $token = $admin->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/owner/analytics/overview');
        $this->assertArrayNotHasKey('questions', $response->json('data.exam_participation'));
        $this->assertStringNotContainsString('is_correct', $response->getContent());
    }
}
