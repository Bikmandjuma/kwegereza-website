<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuditLogNoiseTest extends TestCase
{
    use RefreshDatabase;

    public function test_routine_last_active_at_updates_do_not_flood_the_audit_log(): void
    {
        $student = User::create([
            'user_code' => 'STD-AUDIT01', 'firstname' => 'Aisha', 'lastname' => 'Uwimana',
            'email' => 'aisha@audit.test', 'phone' => '0788555000', 'password' => Hash::make('password123'),
        ]);

        $this->assertSame(1, AuditLog::count(), 'Only the creation itself should be logged so far.');

        // Simulate 5 ordinary page loads spread a few seconds apart (real
        // page loads, not instantaneous) — each one runs UpdateLastActive,
        // which calls $student->update(['last_active_at' => now()]).
        // Advancing the clock matters: without it, fast back-to-back test
        // requests can land within the same wall-clock second, and
        // Eloquent's dirty-checking (correctly) sees no actual change to
        // a second-precision column and skips firing 'updated' — which
        // would make this test pass for the wrong reason and hide the
        // real flood risk that shows up once genuine time elapses.
        for ($i = 0; $i < 5; $i++) {
            \Illuminate\Support\Carbon::setTestNow(now()->addSeconds(10));
            $this->actingAs($student, 'student')->get('/student/dashboard');
        }
        \Illuminate\Support\Carbon::setTestNow();

        $totalLogs = AuditLog::count();

        $this->assertLessThanOrEqual(
            1,
            $totalLogs - 1, // subtract the one creation-log from before
            "Expected routine last_active_at-only updates to NOT flood the audit log, but found {$totalLogs} total entries after 5 ordinary page loads."
        );
    }

    public function test_a_genuine_field_change_is_still_logged_normally(): void
    {
        $student = User::create([
            'user_code' => 'STD-AUDIT02', 'firstname' => 'Aisha', 'lastname' => 'Uwimana',
            'email' => 'aisha2@audit.test', 'phone' => '0788555001', 'password' => Hash::make('password123'),
        ]);

        $student->update(['firstname' => 'Renamed']);

        $this->assertDatabaseHas('audit_logs', [
            'entity_type' => User::class, 'entity_id' => $student->id, 'action' => 'updated',
        ]);
        $latest = AuditLog::where('entity_id', $student->id)->where('action', 'updated')->latest()->first();
        $this->assertArrayHasKey('firstname', $latest->after);
        $this->assertSame('Renamed', $latest->after['firstname']);
    }

    public function test_last_active_at_changing_alongside_a_real_field_is_still_captured(): void
    {
        // A trivial field changing ALONGSIDE a meaningful one must not be
        // silently dropped from the log entry — only a change consisting
        // ENTIRELY of trivial fields should be skipped.
        $student = User::create([
            'user_code' => 'STD-AUDIT03', 'firstname' => 'Aisha', 'lastname' => 'Uwimana',
            'email' => 'aisha3@audit.test', 'phone' => '0788555002', 'password' => Hash::make('password123'),
        ]);

        $student->update(['firstname' => 'BothChanged', 'last_active_at' => now()]);

        $latest = AuditLog::where('entity_id', $student->id)->where('action', 'updated')->latest()->first();
        $this->assertNotNull($latest);
        $this->assertArrayHasKey('firstname', $latest->after);
        $this->assertArrayHasKey('last_active_at', $latest->after);
    }

    /**
     * Course and Quiz were confirmed missing from the observed-models
     * list in AppServiceProvider — every sibling content type
     * (Amatangazo, Inyandiko, Book, DarsatTable) was audited, but these
     * two, arguably the most substantial content-management features in
     * the app, had zero audit trail at all.
     */
    public function test_course_and_quiz_creation_are_now_audited(): void
    {
        \App\Models\Course::create(['title' => 'Audited Course', 'slug' => 'audited-course-'.uniqid(), 'status' => 'draft']);
        \App\Models\Quiz::create(['title' => 'Audited Quiz', 'passing_percentage' => 50, 'status' => 'draft']);

        $this->assertDatabaseHas('audit_logs', ['entity_type' => \App\Models\Course::class, 'action' => 'created']);
        $this->assertDatabaseHas('audit_logs', ['entity_type' => \App\Models\Quiz::class, 'action' => 'created']);
    }
}
