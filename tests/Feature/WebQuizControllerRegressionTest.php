<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Quiz;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class WebQuizControllerRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_still_manage_quizzes_through_the_web_panel(): void
    {
        Notification::fake();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = Role::where('slug', 'admin')->firstOrFail();
        $owner = Owner::create([
            'firstname' => 'Web', 'lastname' => 'Admin', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'webadmin@quiz.test', 'phone' => '0700000101',
            'password' => Hash::make('password123'),
        ]);
        $owner->roles()->sync([$admin->id]);

        $create = $this->actingAs($owner, 'owner')->post('/owner/quizzes', [
            'title' => 'Web Panel Quiz', 'passing_percentage' => 60, 'status' => 'published',
        ]);
        $create->assertRedirect();
        $this->assertDatabaseHas('quizzes', ['title' => 'Web Panel Quiz']);

        $quiz = Quiz::where('title', 'Web Panel Quiz')->firstOrFail();

        $addQuestion = $this->actingAs($owner, 'owner')->post("/owner/quizzes/{$quiz->id}/questions", [
            'question' => 'Sample?', 'type' => 'true_false', 'points' => 10, 'correct' => 'true',
        ]);
        $addQuestion->assertRedirect();
        $this->assertDatabaseHas('quiz_questions', ['quiz_id' => $quiz->id]);

        $update = $this->actingAs($owner, 'owner')->put("/owner/quizzes/{$quiz->id}", [
            'title' => 'Web Panel Quiz (Edited)', 'passing_percentage' => 70, 'status' => 'draft',
        ]);
        $update->assertRedirect();
        $this->assertDatabaseHas('quizzes', ['id' => $quiz->id, 'status' => 'draft']);

        $delete = $this->actingAs($owner, 'owner')->delete("/owner/quizzes/{$quiz->id}");
        $delete->assertRedirect();
        $this->assertDatabaseMissing('quizzes', ['id' => $quiz->id]);
    }
}
