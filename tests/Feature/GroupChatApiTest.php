<?php

namespace Tests\Feature;

use App\Models\GroupMessage;
use App\Models\Owner;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GroupChatApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeOwner(string $email, ?string $roleSlug): Owner
    {
        $owner = Owner::create([
            'firstname' => 'Test', 'lastname' => 'Owner', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => $email, 'phone' => '0700000130',
            'password' => Hash::make('password123'),
        ]);
        if ($roleSlug) {
            $owner->roles()->sync([Role::where('slug', $roleSlug)->firstOrFail()->id]);
        }
        return $owner;
    }

    private function makeStudent(string $email, string $gender): User
    {
        return User::create([
            'user_code' => 'STD-'.strtoupper(substr(md5($email), 0, 6)),
            'firstname' => 'Test', 'lastname' => 'Student', 'gender' => $gender,
            'email' => $email, 'phone' => '0788'.substr(md5($email), 0, 6),
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_islamic_leader_can_post_and_read_leaders_group(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $leader = $this->makeOwner('leader@groups.test', 'islamic-leader');
        $token = $leader->createToken('test')->plainTextToken;
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");

        $send = $auth()->postJson('/api/owner/group-chat/leaders', ['message' => 'Muraho bavandimwe']);
        $send->assertStatus(201)->assertJsonPath('data.sender_type', 'owner');

        $list = $auth()->getJson('/api/owner/group-chat/leaders');
        $list->assertOk();
        $this->assertCount(1, $list->json('data'));
    }

    public function test_teacher_role_cannot_access_leaders_group(): void
    {
        // teacher was not seeded group_chat.leaders — confirms this is a
        // genuinely separate permission from chat.*/darsat.*, not implied.
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $teacher = $this->makeOwner('teacher@groups.test', 'teacher');
        $token = $teacher->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/owner/group-chat/leaders')
            ->assertStatus(403);
    }

    public function test_male_student_is_placed_in_male_group_and_female_in_female_group(): void
    {
        $male = $this->makeStudent('male@groups.test', 'male');
        $female = $this->makeStudent('female@groups.test', 'female');

        $this->actingAs($male, 'student')->postJson('/student/group-chat', ['message' => 'Hi from male group'])
            ->assertStatus(201);
        $this->actingAs($female, 'student')->postJson('/student/group-chat', ['message' => 'Hi from female group'])
            ->assertStatus(201);

        $this->assertDatabaseHas('group_messages', ['group' => GroupMessage::GROUP_MALE_STUDENTS, 'sender_id' => $male->id]);
        $this->assertDatabaseHas('group_messages', ['group' => GroupMessage::GROUP_FEMALE_STUDENTS, 'sender_id' => $female->id]);
    }

    public function test_male_student_only_sees_male_group_messages_not_female(): void
    {
        $male = $this->makeStudent('male2@groups.test', 'male');
        $female = $this->makeStudent('female2@groups.test', 'female');

        $this->actingAs($female, 'student')->postJson('/student/group-chat', ['message' => 'Female only message']);
        $this->actingAs($male, 'student')->postJson('/student/group-chat', ['message' => 'Male only message']);

        $response = $this->actingAs($male, 'student')->getJson('/student/group-chat');
        $response->assertOk()->assertJsonPath('group', GroupMessage::GROUP_MALE_STUDENTS);

        $messages = collect($response->json('data'))->pluck('message');
        $this->assertTrue($messages->contains('Male only message'));
        $this->assertFalse($messages->contains('Female only message'));
    }

    public function test_broadcast_failure_does_not_prevent_a_group_message_from_saving(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $leader = $this->makeOwner('leader2@groups.test', 'islamic-leader');
        $token = $leader->createToken('test')->plainTextToken;

        config(['broadcasting.default' => 'pusher']);
        config(['broadcasting.connections.pusher.options.host' => '127.0.0.1']);
        config(['broadcasting.connections.pusher.options.port' => 1]); // nothing listens here
        config(['broadcasting.connections.pusher.key' => 'test-key']);
        config(['broadcasting.connections.pusher.secret' => 'test-secret']);
        config(['broadcasting.connections.pusher.app_id' => 'test-app']);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/owner/group-chat/leaders', ['message' => 'Should still save']);

        $response->assertStatus(201); // NOT a 500
        $this->assertDatabaseHas('group_messages', ['message' => 'Should still save']);
    }
}
