<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Traits\HandlesFileUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class EventController extends Controller
{
    use HandlesFileUploads;

    public function __construct()
    {
        $this->middleware('permission:events.view')->only(['index']);
        $this->middleware('permission:events.create')->only(['store']);
        $this->middleware('permission:events.update')->only(['update']);
        $this->middleware('permission:events.delete')->only(['destroy']);
    }

    public function index()
    {
        $events = Event::withCount('registrations')->orderBy('starts_at', 'desc')->paginate(10);

        return view('Users.admin.events', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:255',
            'starts_at'   => 'required|date',
            'ends_at'     => 'nullable|date|after:starts_at',
            'capacity'    => 'nullable|integer|min:1',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status'      => 'required|in:draft,published,cancelled',
        ]);

        $baseSlug = Str::slug($request->title);
        $slug = $baseSlug;
        $i = 1;
        while (Event::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;
        }

        $image = $this->storeUploadedFile($request->file('image'), 'events');

        Event::create([
            'title'       => $request->title,
            'slug'        => $slug,
            'description' => $request->description,
            'location'    => $request->location,
            'starts_at'   => $this->parseLocalDateTime($request->starts_at),
            'ends_at'     => $this->parseLocalDateTime($request->ends_at),
            'capacity'    => $request->capacity,
            'image'       => $image,
            'status'      => $request->status,
            'created_by'  => auth('owner')->id(),
        ]);

        return redirect()->route('owner.events')->with('success', 'Igikorwa cyashyizweho.');
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:255',
            'starts_at'   => 'required|date',
            'ends_at'     => 'nullable|date|after:starts_at',
            'capacity'    => 'nullable|integer|min:1',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status'      => 'required|in:draft,published,cancelled',
        ]);

        $image = $event->image;

        if ($request->hasFile('image')) {
            $this->deleteUploadedFile($event->image, 'events');
            $image = $this->storeUploadedFile($request->file('image'), 'events');
        }

        $event->update([
            'title'       => $request->title,
            'description' => $request->description,
            'location'    => $request->location,
            'starts_at'   => $this->parseLocalDateTime($request->starts_at),
            'ends_at'     => $this->parseLocalDateTime($request->ends_at),
            'capacity'    => $request->capacity,
            'image'       => $image,
            'status'      => $request->status,
            'updated_by'  => auth('owner')->id(),
        ]);

        return redirect()->route('owner.events')->with('success', 'Igikorwa cyahinduwe.');
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $this->deleteUploadedFile($event->image, 'events');
        $event->delete();

        return redirect()->route('owner.events')->with('success', 'Igikorwa cyasibwe.');
    }

    /**
     * The admin's datetime-local inputs (start/end time) are captured in
     * their browser's local time — this platform runs on Kigali time in
     * practice — but the app's config('app.timezone') is UTC. Without
     * this conversion, a naive "2026-08-02T00:17" string gets stored and
     * compared as if it were already UTC, silently shifting the real
     * event time by the Kigali/UTC offset (currently +2 hours). Same root
     * cause and same fix as QuizController::parseStartsAt() — worth
     * keeping both in sync if this ever needs to change.
     */
    private function parseLocalDateTime(?string $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d\TH:i', $value, 'Africa/Kigali')->setTimezone('UTC');
    }
}
