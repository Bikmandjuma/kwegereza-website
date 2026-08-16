<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Role;
use App\Models\User;
use App\Notifications\NewSupportTicketNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OwnerNotificationsApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeOwner(string $email, ?string $roleSlug): Owner
    {
        $owner = Owner::create([
            'firstname' => 'Test', 'lastname' => 'Owner', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => $email, 'phone' => '0700000110',
            'password' => Hash::make('password123'),
        ]);
        if ($roleSlug) {
            $owner->roles()->sync([Role::where('slug', $roleSlug)->firstOrFail()->id]);
        }
        return $owner;
    }

    private function makeStudent(): User
    {
        return User::create([
            'user_code' => 'STD-NT01', 'firstname' => 'Aisha', 'lastname' => 'Uwimana',
            'email' => 'aisha@notif.test', 'phone' => '0788222333', 'password' => Hash::make('password123'),
        ]);
    }

    public function test_creating_a_support_ticket_notifies_owners_with_support_view(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin@notif.test', 'admin'); // has support.view via array_diff
        $teacher = $this->makeOwner('teacher@notif.test', 'teacher'); // does NOT have support.view
        $student = $this->makeStudent();

        $response = $this->actingAs($student, 'student')->post('/student/support', [
            'subject' => 'Sinshobora kwinjira', 'category' => 'technical', 'message' => 'Ntibikora neza.',
        ]);
        $response->assertRedirect();

        $this->assertSame(1, $admin->fresh()->unreadNotifications()->count(), 'admin (has support.view) should be notified.');
        $this->assertSame(0, $teacher->fresh()->unreadNotifications()->count(), 'teacher (no support.view) should NOT be notified.');
    }

    public function test_owner_can_list_and_read_their_own_notifications(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin2@notif.test', 'admin');
        $student = $this->makeStudent();

        $this->actingAs($student, 'student')->post('/student/support', [
            'subject' => 'Ikibazo', 'category' => 'account', 'message' => 'x',
        ]);

        $token = $admin->createToken('test')->plainTextToken;
        $auth = fn () => $this->withHeader('Authorization', "Bearer {$token}");

        $unread = $auth()->getJson('/api/owner/notifications/unread-count');
        $unread->assertOk()->assertJsonPath('data.count', 1);

        $list = $auth()->getJson('/api/owner/notifications');
        $list->assertOk();
        $this->assertSame(1, $list->json('meta.total'));
        $this->assertFalse($list->json('data.0.is_read'));
        $notificationId = $list->json('data.0.id');

        $markRead = $auth()->postJson("/api/owner/notifications/{$notificationId}/read");
        $markRead->assertOk();

        $unreadAfter = $auth()->getJson('/api/owner/notifications/unread-count');
        $unreadAfter->assertOk()->assertJsonPath('data.count', 0);
    }

    public function test_mark_all_read(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin3@notif.test', 'admin');
        $student = $this->makeStudent();

        // Two real (undelivered-fake) support tickets → two real database notifications.
        $this->actingAs($student, 'student')->post('/student/support', ['subject' => 'A', 'category' => 'other', 'message' => 'x']);
        $this->actingAs($student, 'student')->post('/student/support', ['subject' => 'B', 'category' => 'other', 'message' => 'y']);

        $this->assertSame(2, $admin->fresh()->unreadNotifications()->count());

        $token = $admin->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', "Bearer {$token}")->postJson('/api/owner/notifications/read-all');
        $response->assertOk()->assertJsonPath('success', true);

        $this->assertSame(0, $admin->fresh()->unreadNotifications()->count());
    }

    /**
     * Regression test for a real bug found while building this phase: with
     * BROADCAST_DRIVER=pusher pointing at an actually unreachable host (a
     * broadcast/WebSocket server outage, or simply not running), the
     * connection exception thrown by the 'broadcast' notification channel
     * propagated all the way up and turned ticket creation into a 500 —
     * a core feature failing because an unrelated realtime enhancement was
     * temporarily down. Fixed by wrapping the notification dispatch so a
     * broadcast failure is logged, not fatal.
     */
    public function test_ticket_creation_survives_a_broadcast_delivery_failure(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $admin = $this->makeOwner('admin4@notif.test', 'admin');
        $student = $this->makeStudent();

        config(['broadcasting.default' => 'pusher']);
        config(['broadcasting.connections.pusher.options.host' => '127.0.0.1']);
        config(['broadcasting.connections.pusher.options.port' => 1]); // nothing listens here
        config(['broadcasting.connections.pusher.key' => 'test-key']);
        config(['broadcasting.connections.pusher.secret' => 'test-secret']);
        config(['broadcasting.connections.pusher.app_id' => 'test-app']);

        $response = $this->actingAs($student, 'student')->post('/student/support', [
            'subject' => 'Should survive a broadcast outage', 'category' => 'technical', 'message' => 'x',
        ]);

        $response->assertRedirect(); // NOT a 500
        $this->assertDatabaseHas('support_tickets', ['subject' => 'Should survive a broadcast outage']);
        $this->assertSame(1, $admin->fresh()->unreadNotifications()->count(), 'The database notification must still be written even though the broadcast channel failed.');
    }
}
