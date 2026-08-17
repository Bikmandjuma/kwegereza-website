@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('owner.courses') }}" class="text-gray-400 hover:text-primary-dark">
            <i class="text-xl fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-primary-dark dark:text-light">{{ $course->title }}</h1>
            <p class="text-sm text-gray-500">Tondekanya amasomo y'iri somo rigenda</p>
        </div>
        <button onclick="document.getElementById('addLessonModal').classList.remove('hidden')"
            class="flex items-center gap-2 px-5 py-3 ml-auto text-sm font-semibold text-white shadow-md rounded-xl bg-primary">
            <i class="fa-solid fa-plus"></i> Ongeraho Isomo
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 mb-5 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif

    <div id="lessonList" class="space-y-3">
        @forelse($course->lessons as $lesson)
        <div class="flex items-center gap-4 p-4 bg-white shadow rounded-2xl dark:bg-darker" data-lesson-id="{{ $lesson->id }}">
            <span class="cursor-move drag-handle text-gray-300"><i class="fa-solid fa-grip-vertical"></i></span>

            <div class="flex items-center justify-center w-8 h-8 text-xs font-bold text-white rounded-full bg-primary">
                {{ $loop->iteration }}
            </div>

            <div class="flex-1">
                <p class="font-semibold text-primary-dark dark:text-light">
                    {{ $lesson->title }}
                    @if(!$lesson->is_required)
                        <span class="px-2 py-0.5 text-[10px] font-bold bg-gray-200 rounded-full">OPTIONAL</span>
                    @endif
                </p>
                @if($lesson->darsat)
                    <p class="text-xs text-gray-400"><i class="fa-solid fa-music"></i> Bihuza n'isomo: {{ $lesson->darsat->title }}</p>
                @endif
            </div>

            <button onclick='openEditLessonModal(@json($lesson))' class="px-3 py-2 text-yellow-600 rounded-lg bg-yellow-50">
                <i class="fa-solid fa-pen"></i>
            </button>
            <form action="{{ route('owner.courses.lessons.destroy', $lesson->id) }}" method="POST"
                  onsubmit="return confirm('Gukuraho iri somo?')">
                @csrf @method('DELETE')
                <button class="px-3 py-2 text-red-600 rounded-lg bg-red-50"><i class="fa-solid fa-trash"></i></button>
            </form>
        </div>
        @empty
        <div class="p-10 text-center text-gray-400 bg-white rounded-3xl dark:bg-darker">
            Nta masomo arahaboneka muri iri somo rigenda. Kanda "Ongeraho Isomo" hejuru.
        </div>
        @endforelse
    </div>

</div>

<!-- ADD LESSON -->
<div id="addLessonModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Ongeraho Isomo</h3>
            <button onclick="document.getElementById('addLessonModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="{{ route('owner.courses.lessons.store', $course->id) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-4 p-6 overflow-y-auto max-h-[65vh]">
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Umutwe</label>
                    <input type="text" name="title" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ibisobanuro</label>
                    <textarea name="description" rows="2" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Huza n'isomo ryabayeho (optional)</label>
                    <select name="darsat_id" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="">-- Nta na kimwe (andika ubutumwa hasi) --</option>
                        @foreach($darsatOptions as $d)
                            <option value="{{ $d->id }}">{{ $d->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ubutumwa (niba nta somo ryahuzwe)</label>
                    <textarea name="content" rows="3" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
                </div>
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                    <input type="checkbox" name="is_required" value="1" checked class="rounded text-primary">
                    Iri somo ni ngombwa kugira ngo isomo rigenda ryuzuzwe
                </label>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('addLessonModal').classList.add('hidden')" class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Bika</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT LESSON -->
<div id="editLessonModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Hindura Isomo</h3>
            <button onclick="document.getElementById('editLessonModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="editLessonForm" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 gap-4 p-6 overflow-y-auto max-h-[65vh]">
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Umutwe</label>
                    <input type="text" name="title" id="edit_lesson_title" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ibisobanuro</label>
                    <textarea name="description" id="edit_lesson_description" rows="2" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Huza n'isomo ryabayeho</label>
                    <select name="darsat_id" id="edit_lesson_darsat" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="">-- Nta na kimwe --</option>
                        @foreach($darsatOptions as $d)
                            <option value="{{ $d->id }}">{{ $d->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ubutumwa</label>
                    <textarea name="content" id="edit_lesson_content" rows="3" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
                </div>
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                    <input type="checkbox" name="is_required" id="edit_lesson_required" value="1" class="rounded text-primary">
                    Iri somo ni ngombwa
                </label>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('editLessonModal').classList.add('hidden')" class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Bika Impinduka</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditLessonModal(lesson) {
    document.getElementById('edit_lesson_title').value = lesson.title;
    document.getElementById('edit_lesson_description').value = lesson.description || '';
    document.getElementById('edit_lesson_darsat').value = lesson.darsat_id || '';
    document.getElementById('edit_lesson_content').value = lesson.content || '';
    document.getElementById('edit_lesson_required').checked = !!lesson.is_required;
    document.getElementById('editLessonForm').action = `/owner/courses/lessons/${lesson.id}`;
    document.getElementById('editLessonModal').classList.remove('hidden');
}

// Simple drag-to-reorder using the native HTML5 drag API — no external
// library needed for a plain vertical list like this.
const lessonList = document.getElementById('lessonList');
let dragEl = null;

lessonList.querySelectorAll('[data-lesson-id]').forEach(item => {
    item.setAttribute('draggable', 'true');

    item.addEventListener('dragstart', () => { dragEl = item; item.style.opacity = '0.4'; });
    item.addEventListener('dragend', () => {
        item.style.opacity = '1';
        const order = [...lessonList.querySelectorAll('[data-lesson-id]')].map(el => el.dataset.lessonId);

        fetch("{{ route('owner.courses.lessons.reorder', $course->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ order }),
        });
    });

    item.addEventListener('dragover', (e) => {
        e.preventDefault();
        const bounding = item.getBoundingClientRect();
        const offset = e.clientY - bounding.top;
        if (offset > bounding.height / 2) {
            item.parentNode.insertBefore(dragEl, item.nextSibling);
        } else {
            item.parentNode.insertBefore(dragEl, item);
        }
    });
});
</script>

@endsection
