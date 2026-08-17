<?php

namespace Tests\Feature;

use App\Models\AccountDeletionRequest;
use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\Inyandiko;
use App\Models\Owner;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RemainingAdminFeaturesApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(string $email): Owner
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $owner = Owner::create([
            'firstname' => 'Admin', 'lastname' => 'User', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => $email, 'phone' => '0700000280',
            'password' => Hash::make('password123'),
        ]);
        $owner->roles()->sync([Role::where('slug', 'admin')->firstOrFail()->id]);
        return $owner;
    }

    private function makeStudent(string $email): User
    {
        return User::create([
            'user_code' => 'STD-'.strtoupper(substr(md5($email), 0, 6)),
            'firstname' => 'Test', 'lastname' => 'Student',
            'email' => $email, 'phone' => '0788'.substr(md5($email), 0, 6),
            'password' => Hash::make('x'),
        ]);
    }

    public function test_admin_can_view_and_moderate_a_reported_comment(): void
    {
        $admin = $this->makeAdmin('admin@moderation.test');
        $token = $admin->createToken('test')->plainTextToken;
        $student = $this->makeStudent('student@moderation.test');
        $article = Inyandiko::create(['title' => 'x', 'slug' => 'x-'.uniqid(), 'status' => 'published']);

        $comment = Comment::create(['commentable_type' => Inyandiko::class, 'commentable_id' => $article->id, 'user_id' => $student->id, 'content' => 'Bad comment', 'status' => 'approved']);
        CommentReport::create(['comment_id' => $comment->id, 'reported_by' => $student->id, 'reason' => 'spam', 'status' => 'pending']);

        $list = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/owner/comment-moderation');
        $list->assertOk();
        $this->assertCount(1, $list->json('data.reported'));

        $hide = $this->withHeader('Authorization', "Bearer {$token}")->postJson("/api/owner/comment-moderation/{$comment->id}/hide");
        $hide->assertOk();
        $this->assertSame('hidden', $comment->fresh()->status);
        $this->assertSame('resolved', CommentReport::where('comment_id', $comment->id)->first()->status);
    }

    public function test_teacher_role_cannot_moderate_comments(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $teacher = Owner::create([
            'firstname' => 'Teacher', 'lastname' => 'User', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'teacher@moderation.test', 'phone' => '0700000281',
            'password' => Hash::make('password123'),
        ]);
        $teacher->roles()->sync([Role::where('slug', 'teacher')->firstOrFail()->id]);
        $token = $teacher->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/owner/comment-moderation')->assertStatus(403);
    }

    public function test_admin_can_verify_and_unverify_a_teacher(): void
    {
        $admin = $this->makeAdmin('admin@verify.test');
        $token = $admin->createToken('test')->plainTextToken;
        $teacher = Owner::create([
            'firstname' => 'Sheikh', 'lastname' => 'Ahmad', 'gender' => 'male', 'title' => 'sheikh',
            'image' => 'user.png', 'dob' => '1980-01-01', 'is_verified' => 0,
            'email' => 'sheikh@verify.test', 'phone' => '0700000282', 'password' => Hash::make('x'),
        ]);

        $verify = $this->withHeader('Authorization', "Bearer {$token}")->postJson("/api/owner/teacher-verification/{$teacher->id}/verify");
        $verify->assertOk();
        $this->assertTrue($teacher->fresh()->is_verified);

        $unverify = $this->withHeader('Authorization', "Bearer {$token}")->postJson("/api/owner/teacher-verification/{$teacher->id}/unverify");
        $unverify->assertOk();
        $this->assertFalse($teacher->fresh()->is_verified);
    }

    public function test_admin_can_view_system_health(): void
    {
        $admin = $this->makeAdmin('admin@monitor.test');
        $token = $admin->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/owner/system-monitor');
        $response->assertOk();
        $this->assertSame('ok', $response->json('data.checks.database.status'));
        $this->assertSame('ok', $response->json('data.checks.storage.status'));
    }

    public function test_teacher_role_cannot_view_system_health(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $teacher = Owner::create([
            'firstname' => 'Teacher', 'lastname' => 'User', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'teacher@monitor.test', 'phone' => '0700000283',
            'password' => Hash::make('password123'),
        ]);
        $teacher->roles()->sync([Role::where('slug', 'teacher')->firstOrFail()->id]);
        $token = $teacher->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/owner/system-monitor')->assertStatus(403);
    }

    public function test_admin_can_approve_an_account_deletion_request_which_deactivates_the_student(): void
    {
        $admin = $this->makeAdmin('admin@deletion.test');
        $token = $admin->createToken('test')->plainTextToken;
        $student = $this->makeStudent('todelete@deletion.test');
        $deletionRequest = AccountDeletionRequest::create(['user_id' => $student->id, 'status' => 'pending']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->postJson("/api/owner/account-deletions/{$deletionRequest->id}/approve");
        $response->assertOk();

        $this->assertSame('approved', $deletionRequest->fresh()->status);
        $this->assertNotNull($student->fresh()->deactivated_at);
    }

    public function test_admin_can_reject_an_account_deletion_request(): void
    {
        $admin = $this->makeAdmin('admin2@deletion.test');
        $token = $admin->createToken('test')->plainTextToken;
        $student = $this->makeStudent('reject@deletion.test');
        $deletionRequest = AccountDeletionRequest::create(['user_id' => $student->id, 'status' => 'pending']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->postJson("/api/owner/account-deletions/{$deletionRequest->id}/reject");
        $response->assertOk();

        $this->assertSame('rejected', $deletionRequest->fresh()->status);
        $this->assertNull($student->fresh()->deactivated_at, 'Rejecting must NOT deactivate the account.');
    }

    /**
     * Backups: list/delete only. createBackup() uses MySQL-specific SQL
     * (SHOW TABLES / SHOW CREATE TABLE), which isn't exercisable against
     * the sqlite test database used here — that path is honestly left
     * unverified in this environment rather than faked.
     */
    public function test_admin_can_list_and_delete_backups(): void
    {
        Storage::fake('local');
        $admin = $this->makeAdmin('admin@backups.test');
        $token = $admin->createToken('test')->plainTextToken;

        Storage::disk('local')->put('backups/backup-2026-01-01_120000.sql', '-- test');

        $list = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/owner/backups');
        $list->assertOk();
        $this->assertCount(1, $list->json('data'));

        $delete = $this->withHeader('Authorization', "Bearer {$token}")->deleteJson('/api/owner/backups/backup-2026-01-01_120000.sql');
        $delete->assertOk();
        Storage::disk('local')->assertMissing('backups/backup-2026-01-01_120000.sql');
    }

    /**
     * The route parameter regex constraint itself blocks this before it
     * ever reaches application logic — Laravel resolves it as no
     * matching route (404), which is at least as safe as a 422 handled
     * inside the controller, just enforced one layer earlier.
     */
    public function test_path_traversal_attempt_in_backup_filename_is_rejected(): void
    {
        Storage::fake('local');
        $admin = $this->makeAdmin('admin2@backups.test');
        $token = $admin->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson('/api/owner/backups/'.urlencode('../../.env'));

        $response->assertStatus(404);
    }
}
