@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <div class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-primary-dark dark:text-light">Ibikorwa (Events)</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Total: <strong>{{ $events->total() }}</strong></p>
            </div>
        </div>

        <button onclick="document.getElementById('createEventModal').classList.remove('hidden')"
            class="flex items-center gap-2 px-5 py-3 text-sm font-semibold text-white shadow-md rounded-xl bg-primary">
            <i class="fa-solid fa-plus"></i> Ongeraho Igikorwa
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 mb-5 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @forelse($events as $event)
        <div class="overflow-hidden bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">

            <div class="relative flex items-center justify-center h-36 bg-gray-100 dark:bg-dark">
                @if($event->imageUrl())
                    <img src="{{ $event->imageUrl() }}" class="object-cover w-full h-full">
                @else
                    <i class="text-4xl text-gray-300 fa-solid fa-calendar-days"></i>
                @endif
                <span class="absolute px-2 py-0.5 text-[11px] font-bold text-white rounded-full top-3 left-3
                    {{ $event->status === 'published' ? 'bg-primary' : ($event->status === 'cancelled' ? 'bg-red-600' : 'bg-gray-500') }}">
                    {{ strtoupper($event->status) }}
                </span>
            </div>

            <div class="p-5">
                <h2 class="mb-1 text-lg font-bold text-primary-dark dark:text-light">{{ $event->title }}</h2>
                <p class="mb-1 text-xs text-gray-400"><i class="fa-solid fa-clock"></i> {{ $event->startsAtLocal()->format('M j, Y — g:i A') }}</p>
                @if($event->location)
                    <p class="mb-3 text-xs text-gray-400"><i class="fa-solid fa-location-dot"></i> {{ $event->location }}</p>
                @endif
                <p class="mb-4 text-xs font-semibold" style="color:#058e48">
                    {{ $event->registrations_count }} biyandikishije
                    @if($event->capacity) / {{ $event->capacity }} @endif
                </p>

                <div class="flex items-center gap-2 pt-3 border-t dark:border-gray-700">
                    <button onclick="openEditEventModal({{ $event->id }})" class="flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl bg-primary/10 text-primary-dark">
                        <i class="fa-solid fa-pen"></i> Hindura
                    </button>
                    <form action="{{ route('owner.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Gusiba iki gikorwa?')">
                        @csrf @method('DELETE')
                        <button class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-red-600 bg-red-50 rounded-xl">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div id="event-data-{{ $event->id }}" class="hidden"
                 data-title="{{ $event->title }}"
                 data-description="{{ $event->description }}"
                 data-location="{{ $event->location }}"
                 data-starts="{{ $event->startsAtLocal()->format('Y-m-d\TH:i') }}"
                 data-ends="{{ $event->endsAtLocal()?->format('Y-m-d\TH:i') }}"
                 data-capacity="{{ $event->capacity }}"
                 data-status="{{ $event->status }}"
                 data-action="{{ route('owner.events.update', $event->id) }}"></div>

        </div>
        @empty
        <div class="p-10 text-center text-gray-400 bg-white col-span-full rounded-3xl dark:bg-darker">
            Nta bikorwa birahaboneka. Kanda "Ongeraho Igikorwa" hejuru.
        </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $events->links() }}</div>

</div>

<!-- CREATE -->
<div id="createEventModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Igikorwa Gishya</h3>
            <button onclick="document.getElementById('createEventModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="{{ route('owner.events.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 gap-4 p-6 overflow-y-auto max-h-[65vh]">
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Umutwe</label>
                    <input type="text" name="title" required placeholder="e.g. Igikorwa cya Ramadan"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ibisobanuro</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Aho biba</label>
                    <input type="text" name="location" placeholder="e.g. Musigiti wa Al-Jamia"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Itangira</label>
                        <input type="datetime-local" name="starts_at" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Irangira (optional)</label>
                        <input type="datetime-local" name="ends_at" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                    </div>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Umubare w'abantu bakwiyandikisha (optional)</label>
                    <input type="number" name="capacity" min="1" placeholder="Nta na kimwe = ntagipimo"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ifoto</label>
                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('createEventModal').classList.add('hidden')" class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Bika</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT -->
<div id="editEventModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Hindura Igikorwa</h3>
            <button onclick="document.getElementById('editEventModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="editEventForm" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 gap-4 p-6 overflow-y-auto max-h-[65vh]">
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Umutwe</label>
                    <input type="text" name="title" id="edit_event_title" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ibisobanuro</label>
                    <textarea name="description" id="edit_event_description" rows="3" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Aho biba</label>
                    <input type="text" name="location" id="edit_event_location" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Itangira</label>
                        <input type="datetime-local" name="starts_at" id="edit_event_starts" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Irangira</label>
                        <input type="datetime-local" name="ends_at" id="edit_event_ends" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                    </div>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Umubare w'abantu (optional)</label>
                    <input type="number" name="capacity" id="edit_event_capacity" min="1" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Guhindura Ifoto (optional)</label>
                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" id="edit_event_status" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('editEventModal').classList.add('hidden')" class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Bika Impinduka</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditEventModal(id) {
    const box = document.getElementById('event-data-' + id);
    document.getElementById('edit_event_title').value = box.dataset.title;
    document.getElementById('edit_event_description').value = box.dataset.description;
    document.getElementById('edit_event_location').value = box.dataset.location;
    document.getElementById('edit_event_starts').value = box.dataset.starts;
    document.getElementById('edit_event_ends').value = box.dataset.ends || '';
    document.getElementById('edit_event_capacity').value = box.dataset.capacity || '';
    document.getElementById('edit_event_status').value = box.dataset.status;
    document.getElementById('editEventForm').action = box.dataset.action;
    document.getElementById('editEventModal').classList.remove('hidden');
}
</script>

@endsection
