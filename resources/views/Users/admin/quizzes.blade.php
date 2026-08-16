@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <div class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-primary-dark dark:text-light">Ibizamini (Quizzes)</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Total: <strong>{{ $quizzes->total() }}</strong></p>
            </div>
        </div>

        <button onclick="document.getElementById('createQuizModal').classList.remove('hidden')"
            class="flex items-center gap-2 px-5 py-3 text-sm font-semibold text-white shadow-md rounded-xl bg-primary">
            <i class="fa-solid fa-plus"></i> Ongeraho Ikizamini
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 mb-5 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @forelse($quizzes as $quiz)
        <div class="p-5 bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">
            <div class="flex items-start justify-between mb-2">
                <span class="px-2 py-0.5 text-[11px] font-bold rounded-full {{ $quiz->status === 'published' ? 'bg-primary/10 text-primary-dark' : 'bg-gray-200 text-gray-500' }}">
                    {{ strtoupper($quiz->status) }}
                </span>
                <span class="text-xs text-gray-400">{{ $quiz->passing_percentage }}% to pass</span>
            </div>

            <h2 class="mb-1 text-lg font-bold text-primary-dark dark:text-light">{{ $quiz->title }}</h2>
            <p class="mb-1 text-xs text-gray-400">{{ $quiz->questions_count }} ibibazo · {{ $quiz->attempts_count }} abagerageje</p>
            @if($quiz->starts_at)
                <p class="mb-3 text-xs font-semibold" style="color:{{ $quiz->hasStarted() ? '#058e48' : '#994c1d' }}">
                    <i class="fa-solid fa-calendar-clock"></i>
                    {{ $quiz->hasStarted() ? 'Cyatangiye: ' : 'Kizatangira: ' }}{{ $quiz->starts_at->copy()->setTimezone('Africa/Kigali')->format('M j, Y g:i A') }}
                    @if($quiz->duration_minutes) · {{ $quiz->duration_minutes }} min @endif
                </p>
            @elseif($quiz->duration_minutes)
                <p class="mb-3 text-xs text-gray-400"><i class="fa-solid fa-hourglass-half"></i> {{ $quiz->duration_minutes }} min</p>
            @else
                <p class="mb-3"></p>
            @endif

            <div class="flex items-center gap-2 pt-3 border-t dark:border-gray-700">
                <a href="{{ route('owner.quizzes.builder', $quiz->id) }}"
                   class="flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl bg-primary/10 text-primary-dark">
                    <i class="fa-solid fa-list-ol"></i> Ibibazo
                </a>
                <button onclick="openEditQuizModal({{ $quiz->id }})" class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-yellow-600 rounded-xl bg-yellow-50">
                    <i class="fa-solid fa-pen"></i>
                </button>
                <form action="{{ route('owner.quizzes.destroy', $quiz->id) }}" method="POST" onsubmit="return confirm('Gusiba iki kizamini?')">
                    @csrf @method('DELETE')
                    <button class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-red-600 bg-red-50 rounded-xl"><i class="fa-solid fa-trash"></i></button>
                </form>
            </div>

            <div id="quiz-data-{{ $quiz->id }}" class="hidden"
                 data-title="{{ $quiz->title }}"
                 data-description="{{ $quiz->description }}"
                 data-passing="{{ $quiz->passing_percentage }}"
                 data-status="{{ $quiz->status }}"
                 data-starts="{{ $quiz->starts_at?->copy()->setTimezone('Africa/Kigali')->format('Y-m-d\TH:i') }}"
                 data-duration="{{ $quiz->duration_minutes }}"
                 data-action="{{ route('owner.quizzes.update', $quiz->id) }}"></div>
        </div>
        @empty
        <div class="p-10 text-center text-gray-400 bg-white col-span-full rounded-3xl dark:bg-darker">
            Nta bizamini birahaboneka. Kanda "Ongeraho Ikizamini" hejuru.
        </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $quizzes->links() }}</div>

</div>

<!-- CREATE -->
<div id="createQuizModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Ikizamini Gishya</h3>
            <button onclick="document.getElementById('createQuizModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="{{ route('owner.quizzes.store') }}" method="POST">
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
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Bihuze na (optional)</label>
                    <select name="attach_type" id="attachType" onchange="kiuToggleAttachOptions()" class="w-full px-4 py-3 mb-2 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="none">Nta na kimwe (standalone)</option>
                        <option value="course_lesson">Isomo ryo mu masomo agenda</option>
                        <option value="darsat">Isomo rya Darsat</option>
                    </select>
                    <select id="attachCourseLesson" class="hidden w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="">-- Hitamo isomo --</option>
                        @foreach($courseLessons as $cl)
                            <option value="{{ $cl->id }}">{{ $cl->course->title }} — {{ $cl->title }}</option>
                        @endforeach
                    </select>
                    <select id="attachDarsat" class="hidden w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="">-- Hitamo isomo --</option>
                        @foreach($darsatLessons as $d)
                            <option value="{{ $d->id }}">{{ $d->title }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="attach_id" id="attachIdHidden">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Igipimo cyo gutsinda (%)</label>
                    <input type="number" name="passing_percentage" value="70" min="1" max="100" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Kizatangira ryari? (optional)</label>
                        <input type="datetime-local" name="starts_at" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <p class="mt-1 text-xs text-gray-400">Nta na kimwe = gihari ako kanya nyuma yo gushyirwaho.</p>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Igihe cyose (iminota, optional)</label>
                        <input type="number" name="duration_minutes" min="1" placeholder="e.g. 30" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <p class="mt-1 text-xs text-gray-400">Nta na kimwe = nta gihe kigenwe.</p>
                    </div>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('createQuizModal').classList.add('hidden')" class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Bika</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT -->
<div id="editQuizModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Hindura Ikizamini</h3>
            <button onclick="document.getElementById('editQuizModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="editQuizForm" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 gap-4 p-6">
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Umutwe</label>
                    <input type="text" name="title" id="edit_quiz_title" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ibisobanuro</label>
                    <textarea name="description" id="edit_quiz_description" rows="2" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Igipimo cyo gutsinda (%)</label>
                    <input type="number" name="passing_percentage" id="edit_quiz_passing" min="1" max="100" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Kizatangira ryari? (optional)</label>
                        <input type="datetime-local" name="starts_at" id="edit_quiz_starts_at" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Igihe cyose (iminota, optional)</label>
                        <input type="number" name="duration_minutes" id="edit_quiz_duration" min="1" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                    </div>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" id="edit_quiz_status" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('editQuizModal').classList.add('hidden')" class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Bika Impinduka</button>
            </div>
        </form>
    </div>
</div>

<script>
function kiuToggleAttachOptions() {
    const type = document.getElementById('attachType').value;
    const courseSel = document.getElementById('attachCourseLesson');
    const darsatSel = document.getElementById('attachDarsat');
    const hidden = document.getElementById('attachIdHidden');

    courseSel.classList.toggle('hidden', type !== 'course_lesson');
    darsatSel.classList.toggle('hidden', type !== 'darsat');

    const sync = () => {
        hidden.value = type === 'course_lesson' ? courseSel.value : (type === 'darsat' ? darsatSel.value : '');
    };
    courseSel.onchange = sync;
    darsatSel.onchange = sync;
    sync();
}

function openEditQuizModal(id) {
    const box = document.getElementById('quiz-data-' + id);
    document.getElementById('edit_quiz_title').value = box.dataset.title;
    document.getElementById('edit_quiz_description').value = box.dataset.description;
    document.getElementById('edit_quiz_passing').value = box.dataset.passing;
    document.getElementById('edit_quiz_status').value = box.dataset.status;
    document.getElementById('edit_quiz_starts_at').value = box.dataset.starts || '';
    document.getElementById('edit_quiz_duration').value = box.dataset.duration || '';
    document.getElementById('editQuizForm').action = box.dataset.action;
    document.getElementById('editQuizModal').classList.remove('hidden');
}
</script>

@endsection
