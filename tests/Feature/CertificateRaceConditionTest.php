<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CertificateRaceConditionTest extends TestCase
{
    use RefreshDatabase;

    private function makeStudent(string $email): User
    {
        return User::create([
            'user_code' => 'STD-'.strtoupper(substr(md5($email), 0, 6)),
            'firstname' => 'Test', 'lastname' => 'Student',
            'email' => $email, 'phone' => '0788'.substr(md5($email), 0, 6),
            'password' => Hash::make('x'),
        ]);
    }

    public function test_two_concurrent_certificate_number_generations_collide(): void
    {
        $studentA = $this->makeStudent('a@cert-race.test');
        $studentB = $this->makeStudent('b@cert-race.test');

        $numberA = Certificate::generateCertificateNumber();
        $numberB = Certificate::generateCertificateNumber();

        $this->assertSame($numberA, $numberB, 'Both requests computed the identical number — this is the race.');

        Certificate::create([
            'certificate_number' => $numberA, 'verification_code' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => $studentA->id, 'title' => 'Cert A', 'issued_at' => now(),
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Certificate::create([
            'certificate_number' => $numberB, 'verification_code' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => $studentB->id, 'title' => 'Cert B', 'issued_at' => now(),
        ]);
    }

    /**
     * The fix: CertificateService::issue() wraps generation + insert in
     * one DB transaction with a locked count read (lockForUpdate), so
     * two real concurrent requests serialize at the database level
     * rather than racing in PHP. Genuine parallel-connection testing
     * isn't available in a single PHPUnit process, so this confirms the
     * service produces distinct, valid numbers across repeated calls
     * (correct baseline behavior) — the locking strategy itself follows
     * standard practice for MySQL/Postgres, the actual production
     * database driver, even though it can't be fully exercised here.
     */
    public function test_certificate_service_issues_distinct_numbers_across_repeated_calls(): void
    {
        $service = app(\App\Services\CertificateService::class);
        $students = collect(range(1, 5))->map(fn ($i) => $this->makeStudent("student{$i}@cert-service.test"));

        $certificates = $students->map(fn ($s) => $service->issue($s, 'Test Certificate'));

        $numbers = $certificates->pluck('certificate_number');
        $this->assertCount(5, $numbers->unique(), 'All 5 certificates must have distinct numbers.');
    }

    public function test_issuing_for_the_same_course_twice_does_not_duplicate(): void
    {
        $service = app(\App\Services\CertificateService::class);
        $student = $this->makeStudent('dup@cert-service.test');
        $course = \App\Models\Course::create(['title' => 'Test Course', 'slug' => 'test-course-'.uniqid(), 'status' => 'published']);

        $first = $service->issueForCourseCompletion($student, $course->id, $course->title);
        $second = $service->issueForCourseCompletion($student, $course->id, $course->title);

        $this->assertSame($first->id, $second->id, 'A second completion of the same course must return the existing certificate, not create a duplicate.');
        $this->assertSame(1, Certificate::where('user_id', $student->id)->where('course_id', $course->id)->count());
    }
}
