<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentEventController extends Controller
{
    /**
     * Events phase: same race-condition pattern found and fixed in
     * Certificates — the previous version checked isFull() then called
     * firstOrCreate() as two separate steps, with no lock between them.
     * Two students registering for the last spot at nearly the same
     * moment could both pass the capacity check before either commits,
     * both then successfully register, silently overbooking the event.
     * Fixed by moving the capacity check and the insert inside one
     * locked transaction, so concurrent registration attempts serialize
     * at the database level instead of racing in PHP.
     */
    public function register($slug)
    {
        $event = Event::published()->where('slug', $slug)->firstOrFail();
        $user = Auth::guard('student')->user();

        $result = \Illuminate\Support\Facades\DB::transaction(function () use ($event, $user) {
            $locked = Event::where('id', $event->id)->lockForUpdate()->first();
            $currentCount = $locked->registrations()->count();

            if ($locked->capacity !== null && $currentCount >= $locked->capacity) {
                return 'full';
            }

            EventRegistration::firstOrCreate(
                ['event_id' => $locked->id, 'user_id' => $user->id],
                ['registered_at' => now()]
            );

            return 'ok';
        });

        if ($result === 'full') {
            return back()->with('error', 'Iki gikorwa cyuzuye. Ntibishoboka kongera kwiyandikisha.');
        }

        return back()->with('success', 'Wiyandikishije kuri iki gikorwa!');
    }

    public function unregister($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        $user = Auth::guard('student')->user();

        EventRegistration::where('event_id', $event->id)->where('user_id', $user->id)->delete();

        return back()->with('success', 'Wavuye kuri iki gikorwa.');
    }

    public function myEvents()
    {
        $user = Auth::guard('student')->user();

        $registrations = EventRegistration::with('event')
            ->where('user_id', $user->id)
            ->whereHas('event', fn($q) => $q->where('starts_at', '>=', now()->subDay()))
            ->get()
            ->sortBy(fn($r) => $r->event->starts_at);

        return view('Users.User.events', compact('registrations'));
    }
}
