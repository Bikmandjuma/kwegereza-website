<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Visit;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use App\Models\GuestVisit;
use Illuminate\Support\Facades\Cache;
use App\Models\Owner;
use App\Models\DarsatTable;
use App\Models\Book;
use App\Models\Amatangazo;
use App\Models\Inyandiko;
use App\Models\Course;


class GuestController extends Controller{

    public function home(Request $request)
    {
        $guestId = $request->cookie('guest_visit_id');

        if (!$guestId) {
            $guestId = Str::uuid()->toString();
            Cookie::queue('guest_visit_id', $guestId, 60 * 24 * 30);
        }

        // ONLY AFTER cookie exists
        $this->trackVisit($request, $guestId);

        $today = now()->toDateString();

        $todayVisit = Visit::where('date', $today)->value('count') ?? 0;
        $totalVisit = Visit::sum('count');

        $onlineUsers = GuestVisit::where('last_visit_at', '>=', now()->subMinutes(5))->count();

        return view('Guest.ahabanza', compact(
            'todayVisit',
            'totalVisit',
            'onlineUsers'
        ));
    }

    public function books()
    {
        if (!\App\Models\FeatureFlag::enabled('public_books')) {
            return view('Guest.feature-disabled', ['message' => "Ibitabo ntabwo bihari ubu. Garuka vuba."]);
        }

        $books = Book::published()->latest()->get();
        return view('Guest.ibitabo', compact('books'));
    }

    public function news()
    {
        $amatangazo = Amatangazo::published()
            ->latest('published_at')
            ->get();

        return view('Guest.amatangazo', compact('amatangazo'));
    }

    public function inyandiko_zabamenyi()
    {
        $inyandiko = Inyandiko::published()->latest('published_at')->get();

        return view('Guest.inyandiko-zabamenyi', compact('inyandiko'));
    }

    public function inyandikoShow($slug)
    {
        $item = Inyandiko::published()->where('slug', $slug)->firstOrFail();

        return view('Guest.inyandiko-show', compact('item'));
    }

    // public function teachers(){
    //     return view('Guest.abasheikh');
    // }

    public function teachers()
    {
        $teachers = Owner::whereIn('title', ['sheikh', 'ustadh'])
            ->withCount(['darsat' => fn($q) => $q->published()])
            ->orderBy('firstname')
            ->get();

        return view('Guest.abasheikh', compact('teachers'));
    }


    public function teacherDarsa($id)
    {
        $teacher = Owner::withCount(['darsat' => fn($q) => $q->published()])->findOrFail($id);

        $darsat = DarsatTable::where('teachers', $id)
            ->published()
            ->latest()
            ->get();

        $types = DarsatTable::where('teachers', $id)
            ->published()
            ->distinct()
            ->pluck('type');

        return view('Guest.inyigisho_zabarimu', compact(
            'teacher',
            'darsat',
            'types'
        ));
    }

    public function courses()
    {
        $courses = Course::published()->withCount('lessons')->latest('published_at')->get();

        return view('Guest.courses', compact('courses'));
    }

    public function courseShow($slug)
    {
        $course = Course::published()->with('lessons.darsat', 'lessons.quizzes')->where('slug', $slug)->firstOrFail();

        return view('Guest.course-show', compact('course'));
    }

    public function twandikire(){
        if (!\App\Models\FeatureFlag::enabled('live_chat')) {
            return view('Guest.feature-disabled', ['message' => "Ubu buryo bwo kuvugana ntabwo buhari ubu. Ushobora kudusanga kuri (+250) 723061482."]);
        }

        return view('Guest.twandikire');
    }

    public function trackVisit(Request $request, $guestId)
    {
        $today = now()->toDateString();

        $key = "visit_{$guestId}_{$today}";

        if (cache()->has($key)) {
            return;
        }

        $visit = Visit::firstOrCreate(
            ['date' => $today],
            ['count' => 0]
        );

        $visit->increment('count');

        cache()->put($key, true, 600);
    }

    public function ping(Request $request)
    {
        $guestId = $request->cookie('guest_visit_id');

        if (!$guestId) return response()->noContent();

        GuestVisit::updateOrCreate(
            ['guest_id' => $guestId],
            [
                'ip' => $request->ip(),
                'user_agent' => substr($request->userAgent(), 0, 255),
                'last_visit_at' => now(),
            ]
        );

        return response()->json(['ok' => true]);
    }

    public function liveVisits()
    {
        $today = now()->toDateString();

        $visit = Visit::where('date', $today)->first();

        $todayCount = $visit ? $visit->count : 0;

        $totalVisit = Visit::sum('count');

        $online = Cache::remember('online_users_count', 5, function () {
            return GuestVisit::where('last_visit_at', '>=', now()->subMinutes(5))->count();
        });

        return response()->json([
            'today' => $this->shortNumber($todayCount),
            'total' => $this->shortNumber($totalVisit),
            'online' => $online,
        ]);
    }

    /* SHORT FORMAT: K / M / B */
    private function shortNumber($num)
    {
        if ($num >= 1000000000) {
            return round($num / 1000000000, 1) . 'B';
        }

        if ($num >= 1000000) {
            return round($num / 1000000, 2) . 'M';
        }

        if ($num >= 1000) {
            return round($num / 1000, 1) . 'k';
        }

        return $num;
    }
    

}

