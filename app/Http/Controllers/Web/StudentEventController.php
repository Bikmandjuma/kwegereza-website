<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentEventController extends Controller
{
    public function register($slug)
    {
        $event = Event::published()->where('slug', $slug)->firstOrFail();
        $user = Auth::guard('student')->user();

        if ($event->isFull()) {
            return back()->with('error', 'Iki gikorwa cyuzuye. Ntibishoboka kongera kwiyandikisha.');
        }

        EventRegistration::firstOrCreate(
            ['event_id' => $event->id, 'user_id' => $user->id],
            ['registered_at' => now()]
        );

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
