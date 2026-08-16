<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Certificates phase: found a real race condition —
 * Certificate::generateCertificateNumber() read
 * whereYear('created_at',$year)->count()+1 with no lock at all. Under
 * real concurrent traffic (two students completing courses around the
 * same moment — plausible on a live platform), two requests could read
 * the identical count before either committed, both computing the same
 * certificate_number. Since that column has a genuine unique
 * constraint, the second insert didn't silently duplicate — it threw an
 * uncaught QueryException, surfacing as a raw 500 to whichever student's
 * request lost the race. Confirmed with a test that forces the collision
 * deterministically (real concurrent DB connections aren't available in
 * a single PHPUnit process) before fixing anything.
 *
 * Fixed by moving number generation inside a DB transaction with a
 * locked read (lockForUpdate) on the year's row count, so two concurrent
 * requests are serialized at the database level rather than racing in
 * PHP — the second request's transaction waits for the first to commit,
 * then reads the now-updated count and generates a genuinely different
 * number.
 */
class CertificateService
{
    public function issue(User $student, string $title, ?int $courseId = null, ?int $issuedBy = null): Certificate
    {
        return DB::transaction(function () use ($student, $title, $courseId, $issuedBy) {
            $year = now()->year;

            $sequence = Certificate::whereYear('created_at', $year)->lockForUpdate()->count() + 1;
            $certificateNumber = sprintf('KIU-%d-%06d', $year, $sequence);

            return Certificate::create([
                'certificate_number' => $certificateNumber,
                'verification_code'  => (string) Str::uuid(),
                'user_id'            => $student->id,
                'course_id'          => $courseId,
                'title'              => $title,
                'issued_by'          => $issuedBy,
                'issued_at'          => now(),
            ]);
        });
    }

    public function issueForCourseCompletion(User $student, int $courseId, string $courseTitle): Certificate
    {
        $existing = Certificate::where('user_id', $student->id)->where('course_id', $courseId)->first();
        if ($existing) {
            return $existing;
        }

        return $this->issue($student, 'Certificate of Completion — '.$courseTitle, $courseId);
    }

    public function paginate(int $perPage = 15, ?string $search = null)
    {
        return Certificate::with(['user', 'course'])
            ->when($search, fn ($q) => $q->where(function ($q2) use ($search) {
                $q2->where('title', 'like', "%{$search}%")
                   ->orWhere('certificate_number', 'like', "%{$search}%")
                   ->orWhereHas('user', fn ($q3) => $q3->where('firstname', 'like', "%{$search}%")
                       ->orWhere('lastname', 'like', "%{$search}%"));
            }))
            ->latest('issued_at')
            ->paginate($perPage);
    }
}
