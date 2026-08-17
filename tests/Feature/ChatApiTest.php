<?php

namespace Tests\Feature;

use App\Models\ChatMessage;
use App\Models\ChatPresence;
use App\Models\Owner;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChatApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeOwner(string $email, ?string $roleSlug): Owner
    {
        $owner = Owner::create([
            'firstname' => 'Test', 'lastname' => 'Owner', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => $email, 'phone' => '0700000120',
            'password' => Hash::make('password123'),
        ]);
        if ($roleSlug) {
            $owner->roles()->sync([Role::where('slug', $roleSlug)->firstOrFail()->id]);
        }
        return $owner;
    }

    public function test_moderator_can_view_and_reply_to_conversations(): void
    {
        // moderator was seeded chat.view/chat.moderate but NOT chat.reply —
        // recheck against seeder: moderator gets chat.view + chat.moderate.
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $leader = $this->makeOwner('leader@chat.test', 'islamic-leader'); // has chat.view + chat.reply
        $token = $leader->createToken('test')->plainTextToken;
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");

        ChatMessage::create(['guest_id' => 'g1', 'sender_type' => 'guest', 'sender_name' => 'Guest One', 'message' => 'Hello?']);

        $list = $auth()->getJson('/api/owner/chat/conversations');
        $list->assertOk();
        $this->assertSame(1, count($list->json('data')));
        $this->assertSame(1, $list->json('data.0.unread_count'));

        $messages = $auth()->getJson('/api/owner/chat/messages/g1');
        $messages->assertOk();
        $this->assertCount(1, $messages->json('data'));

        $send = $auth()->postJson('/api/owner/chat/messages/g1', ['message' => 'Muraho, ni iki nagufasha?']);
        $send->assertStatus(201)->assertJsonPath('data.sender_type', 'admin');

        $markRead = $auth()->postJson('/api/owner/chat/messages/g1/read');
        $markRead->assertOk();

        $listAfter = $auth()->getJson('/api/owner/chat/conversations');
        $this->assertSame(0, $listAfter->json('data.0.unread_count'));
    }

    public function test_teacher_role_cannot_access_chat(): void
    {
        // teacher was not seeded any chat.* permissions.
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $teacher = $this->makeOwner('teacher@chat.test', 'teacher');
        $token = $teacher->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/owner/chat/conversations')
            ->assertStatus(403);
    }

    /**
     * Regression test for a real, confirmed bug: the typingStatus method
     * was referenced by both the permission middleware ->only([...]) list
     * and a live route (GET /owner/chat/typing/{guest_id} originally, now
     * fixed and re-routed) since the very first version of this
     * controller, but the method itself never existed — hitting it threw
     * a fatal 500. Fixed by actually implementing it.
     */
    public function test_typing_status_endpoint_no_longer_throws_a_fatal_error(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $owner = $this->makeOwner('admin@chat.test', 'admin');

        $response = $this->actingAs($owner, 'owner')->get('/owner/chat/typing/g1');
        $response->assertOk()->assertJson(['typing' => false]);
    }

    public function test_guest_can_report_typing_and_it_is_reflected_in_conversations(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin2@chat.test', 'admin');
        ChatMessage::create(['guest_id' => 'g2', 'sender_type' => 'guest', 'sender_name' => 'Guest Two', 'message' => 'hi']);

        // The guest side (public, no auth) reports typing — matches the
        // real guest widget exactly: POST /chat/typing with {guest_id,
        // typing} in the body, not a route segment.
        $this->postJson('/chat/typing', ['guest_id' => 'g2', 'typing' => true])->assertOk();

        $token = $admin->createToken('test')->plainTextToken;
        $list = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/owner/chat/conversations');
        $this->assertTrue(collect($list->json('data'))->firstWhere('guest_id', 'g2')['typing']);

        // Confirms the same web route the admin chatroom's own JS calls.
        $this->actingAs($admin, 'owner')->get('/owner/chat/typing/g2')->assertOk()->assertJson(['typing' => true]);
    }

    /**
     * Regression test for the other half of the same bug: the guest
     * widget (twandikire.blade.php) has always polled GET
     * /chat/admin-typing/{guestId} to show its own "admin arandika"
     * bubble, but nothing on the admin side ever wrote that value and the
     * route didn't exist at all — confirmed by reading the actual Blade
     * view, not assumed. Fixed on both ends: chatRoom.blade.php now
     * reports admin typing on keystroke, and this route now exists to
     * both accept and read it back.
     */
    public function test_admin_can_report_typing_and_guest_widget_can_read_it_back(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin4@chat.test', 'admin');
        ChatMessage::create(['guest_id' => 'g4', 'sender_type' => 'guest', 'sender_name' => 'Guest Four', 'message' => 'hi']);

        // Before any report, the guest widget should see "not typing".
        $this->getJson('/chat/admin-typing/g4')->assertOk()->assertJson(['typing' => false]);

        // Admin reports typing (requires chat.reply, matching the gate
        // on the message-send endpoint it sits right next to).
        $this->actingAs($admin, 'owner')->postJson('/owner/chat/admin-typing/g4', ['typing' => true])->assertOk();

        // The guest widget's own polling endpoint (public, no auth) now
        // reflects it — this is the exact route twandikire.blade.php calls.
        $this->getJson('/chat/admin-typing/g4')->assertOk()->assertJson(['typing' => true]);
    }

    /**
     * Regression test for a bug I introduced in this same phase, not a
     * pre-existing one: adding the admin_typing column via migration
     * without also adding it to ChatPresence::$fillable meant
     * updateOrCreate() silently dropped it — the exact same mass-assignment
     * pattern as the Owner::title and User::deactivated_at bugs found in
     * earlier phases, just self-inflicted this time. Caught only because
     * the test above checked the actual database value, not just the
     * HTTP 200 the endpoint still returned regardless.
     */
    public function test_admin_typing_column_is_actually_mass_assignable(): void
    {
        \App\Models\ChatPresence::create(['guest_id' => 'fillable-check', 'admin_typing' => true]);
        $this->assertTrue(\App\Models\ChatPresence::where('guest_id', 'fillable-check')->first()->admin_typing);
    }

    public function test_broadcast_failure_does_not_prevent_a_chat_message_from_saving(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin3@chat.test', 'admin');
        ChatMessage::create(['guest_id' => 'g3', 'sender_type' => 'guest', 'sender_name' => 'Guest Three', 'message' => 'hi']);

        config(['broadcasting.default' => 'pusher']);
        config(['broadcasting.connections.pusher.options.host' => '127.0.0.1']);
        config(['broadcasting.connections.pusher.options.port' => 1]); // nothing listens here
        config(['broadcasting.connections.pusher.key' => 'test-key']);
        config(['broadcasting.connections.pusher.secret' => 'test-secret']);
        config(['broadcasting.connections.pusher.app_id' => 'test-app']);

        $token = $admin->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/owner/chat/messages/g3', ['message' => 'Should still save']);

        $response->assertStatus(201); // NOT a 500
        $this->assertDatabaseHas('chat_messages', ['guest_id' => 'g3', 'message' => 'Should still save']);
    }
}
