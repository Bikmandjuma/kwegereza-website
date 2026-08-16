<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Traits\HandlesFileUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class EventController extends Controller
{
    use HandlesFileUploads;

    public function index(Request $request)
    {
        $search = $request->string('search')->value() ?: null;

        $events = Event::withCount('registrations')
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->orderByDesc('starts_at')
            ->paginate(10);

        return response()->json([
            'success' => true, 'message' => null,
            'data' => collect($events->items())->map(fn ($e) => $this->present($e)),
            'meta' => ['current_page' => $events->currentPage(), 'last_page' => $events->lastPage(), 'total' => $events->total()],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $baseSlug = Str::slug($data['title']);
        $slug = $baseSlug;
        $i = 1;
        while (Event::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$i++;
        }

        $event = Event::create([
            'title' => $data['title'], 'slug' => $slug, 'description' => $data['description'] ?? null,
            'location' => $data['location'] ?? null,
            'starts_at' => $this->parseLocalDateTime($data['starts_at']),
            'ends_at' => $this->parseLocalDateTime($data['ends_at'] ?? null),
            'capacity' => $data['capacity'] ?? null,
            'image' => $request->hasFile('image') ? $this->storeUploadedFile($request->file('image'), 'events') : null,
            'status' => $data['status'], 'created_by' => $request->user()->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Igikorwa cyashyizweho.', 'data' => $this->present($event)], 201);
    }

    public function update(Request $request, int $id)
    {
        $event = Event::findOrFail($id);
        $data = $this->validated($request);

        $image = $event->image;
        if ($request->hasFile('image')) {
            $this->deleteUploadedFile($event->image, 'events');
            $image = $this->storeUploadedFile($request->file('image'), 'events');
        }

        $event->update([
            'title' => $data['title'], 'description' => $data['description'] ?? null,
            'location' => $data['location'] ?? null,
            'starts_at' => $this->parseLocalDateTime($data['starts_at']),
            'ends_at' => $this->parseLocalDateTime($data['ends_at'] ?? null),
            'capacity' => $data['capacity'] ?? null, 'image' => $image,
            'status' => $data['status'], 'updated_by' => $request->user()->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Igikorwa cyahinduwe.', 'data' => $this->present($event->fresh()->loadCount('registrations'))]);
    }

    public function destroy(int $id)
    {
        $event = Event::findOrFail($id);
        $this->deleteUploadedFile($event->image, 'events');
        $event->delete();

        return response()->json(['success' => true, 'message' => 'Igikorwa cyasibwe.', 'data' => null]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255', 'description' => 'nullable|string',
            'location' => 'nullable|string|max:255', 'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at', 'capacity' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status' => 'required|in:draft,published,cancelled',
        ]);
    }

    private function present(Event $event): array
    {
        return [
            'id' => $event->id, 'title' => $event->title, 'slug' => $event->slug,
            'description' => $event->description, 'location' => $event->location,
            'starts_at' => $event->starts_at, 'ends_at' => $event->ends_at,
            'capacity' => $event->capacity, 'status' => $event->status,
            'registrations_count' => $event->registrations_count ?? 0,
        ];
    }

    private function parseLocalDateTime(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d\TH:i', $value, 'Africa/Kigali')->setTimezone('UTC');
    }
}
