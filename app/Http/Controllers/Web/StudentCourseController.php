<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseLesson;
use App\Models\CourseLessonCompletion;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentCourseController extends Controller
{
    public function __construct(private GamificationService $gamification)
    {
    }
    public function myCourses()
    {
        $user = Auth::guard('student')->user();

        $enrollments = CourseEnrollment::with('course')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('Users.User.courses', compact('enrollments'));
    }

    public function enroll($slug)
    {
        $course = Course::published()->where('slug', $slug)->firstOrFail();
        $user = Auth::guard('student')->user();

        CourseEnrollment::firstOrCreate(
            ['course_id' => $course->id, 'user_id' => $user->id],
            ['enrolled_at' => now()]
        );

        return redirect()->route('guest.course.show', $course->slug)->with('success', 'Wiyandikishije muri iri somo ryagenda.');
    }

    public function completeLesson(Request $request, $lessonId)
    {
        $lesson = CourseLesson::findOrFail($lessonId);
        $user = Auth::guard('student')->user();

        CourseLessonCompletion::firstOrCreate(
            ['user_id' => $user->id, 'course_lesson_id' => $lesson->id],
            ['completed_at' => now()]
        );

        // If every required lesson is now done, mark the enrollment complete.
        $course = $lesson->course;
        $percent = $course->completionPercentFor($user->id);

        if ($percent >= 100) {
            CourseEnrollment::where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->update(['completed_at' => now()]);

            Certificate::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                ['title' => 'Certificate of Completion — ' . $course->title]
            );
        }

        $gamification = $this->gamification->recordActivityAndCheckBadges($user);

        if ($request->wantsJson()) {
            return response()->json([
                'ok'        => true,
                'percent'   => $percent,
                'newBadges' => $gamification['newlyEarned']->map(fn($b) => ['name' => $b->name, 'icon' => $b->icon])->values(),
                'streak'    => $gamification['streak']->current_streak,
            ]);
        }

        return back()->with('success', 'Isomo ryarangiye!');
    }
}
