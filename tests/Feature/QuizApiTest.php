<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class QuizApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeOwner(string $email, ?string $roleSlug): Owner
    {
        $owner = Owner::create([
            'firstname' => 'Test', 'lastname' => 'Owner', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => $email, 'phone' => '0700000100',
            'password' => Hash::make('password123'),
        ]);
        if ($roleSlug) {
            $owner->roles()->sync([Role::where('slug', $roleSlug)->firstOrFail()->id]);
        }
        return $owner;
    }

    public function test_full_quiz_crud_as_teacher(): void
    {
        Notification::fake();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        // teacher has quizzes.view/create/update seeded, matching darsat/courses.
        $teacher = $this->makeOwner('teacher@quiz.test', 'teacher');
        $token = $teacher->createToken('test')->plainTextToken;
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");

        $create = $auth()->postJson('/api/owner/quizzes', [
            'title' => 'Ikizamini cya Fiqh', 'passing_percentage' => 60, 'status' => 'published',
            'duration_minutes' => 15,
        ]);
        $create->assertStatus(201)->assertJsonPath('data.title', 'Ikizamini cya Fiqh');
        $quizId = $create->json('data.id');

        $list = $auth()->getJson('/api/owner/quizzes');
        $list->assertOk();
        $this->assertSame(1, $list->json('meta.total'));

        $show = $auth()->getJson("/api/owner/quizzes/{$quizId}");
        $show->assertOk()->assertJsonPath('data.questions_count', null); // withCount not loaded on findWithQuestions path

        $update = $auth()->post("/api/owner/quizzes/{$quizId}", [
            '_method' => 'PUT', 'title' => 'Ikizamini cyahinduwe', 'passing_percentage' => 70, 'status' => 'draft',
        ]);
        $update->assertOk()->assertJsonPath('data.title', 'Ikizamini cyahinduwe');
    }

    public function test_teacher_cannot_delete_quiz(): void
    {
        // teacher was seeded quizzes.view/create/update but NOT quizzes.delete.
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $teacher = $this->makeOwner('teacher2@quiz.test', 'teacher');
        $token = $teacher->createToken('test')->plainTextToken;
        $quiz = Quiz::create(['title' => 'x', 'passing_percentage' => 50, 'status' => 'draft']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->deleteJson("/api/owner/quizzes/{$quiz->id}");
        $response->assertStatus(403);
        $this->assertDatabaseHas('quizzes', ['id' => $quiz->id]);
    }

    public function test_full_question_lifecycle_multiple_choice(): void
    {
        Notification::fake();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $teacher = $this->makeOwner('teacher3@quiz.test', 'teacher');
        $token = $teacher->createToken('test')->plainTextToken;
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");

        $quiz = Quiz::create(['title' => 'x', 'passing_percentage' => 50, 'status' => 'draft']);

        $create = $auth()->postJson("/api/owner/quizzes/{$quiz->id}/questions", [
            'question' => 'What is 2+2?', 'type' => 'multiple_choice', 'points' => 10,
            'answers' => ['3', '4', '5'], 'correct' => 1,
        ]);
        $create->assertStatus(201);
        $this->assertCount(3, $create->json('data.answers'));
        $this->assertTrue(collect($create->json('data.answers'))->firstWhere('answer_text', '4')['is_correct']);
        $questionId = $create->json('data.id');

        $update = $auth()->post("/api/owner/quizzes/questions/{$questionId}", [
            '_method' => 'PUT', 'question' => 'What is 2+2? (updated)', 'points' => 20,
        ]);
        $update->assertOk()->assertJsonPath('data.points', 20);
    }

    /**
     * Deleting is its own test method (rather than continuing in the one
     * above with a second token) because Laravel's auth guard caches the
     * first resolved user for the lifetime of a test's app container —
     * switching Sanctum tokens mid-method doesn't force re-resolution. This
     * bit me in Phase 2's logout test too; splitting into a fresh test
     * method is the reliable fix, not a workaround.
     */
    public function test_admin_can_delete_a_question(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin-delete-q@quiz.test', 'admin');
        $token = $admin->createToken('test')->plainTextToken;
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");

        $quiz = Quiz::create(['title' => 'x', 'passing_percentage' => 50, 'status' => 'draft']);
        $question = QuizQuestion::create(['quiz_id' => $quiz->id, 'question' => 'x', 'type' => 'true_false', 'points' => 5, 'order' => 1]);

        $delete = $auth()->deleteJson("/api/owner/quizzes/questions/{$question->id}");
        $delete->assertOk();
        $this->assertDatabaseMissing('quiz_questions', ['id' => $question->id]);
    }

    public function test_true_false_question_creates_two_answers(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $teacher = $this->makeOwner('teacher4@quiz.test', 'teacher');
        $token = $teacher->createToken('test')->plainTextToken;
        $quiz = Quiz::create(['title' => 'x', 'passing_percentage' => 50, 'status' => 'draft']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")->postJson("/api/owner/quizzes/{$quiz->id}/questions", [
            'question' => 'The Earth is round.', 'type' => 'true_false', 'points' => 5, 'correct' => 'true',
        ]);

        $response->assertStatus(201);
        $answers = $response->json('data.answers');
        $this->assertCount(2, $answers);
        $this->assertTrue(collect($answers)->firstWhere('answer_text', 'True')['is_correct']);
        $this->assertFalse(collect($answers)->firstWhere('answer_text', 'False')['is_correct']);
    }

    public function test_moderator_cannot_manage_quizzes(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $moderator = $this->makeOwner('mod@quiz.test', 'moderator');
        $token = $moderator->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/owner/quizzes', ['title' => 'x', 'passing_percentage' => 50, 'status' => 'draft'])
            ->assertStatus(403);
    }
}
