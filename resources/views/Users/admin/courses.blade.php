@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <div class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
                <i class="fa-solid fa-diagram-project"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-primary-dark dark:text-light">Amasomo Agenda (Learning Paths)</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Total: <strong>{{ $courses->total() }}</strong></p>
            </div>
        </div>

        <button onclick="document.getElementById('createCourseModal').classList.remove('hidden')"
            class="flex items-center gap-2 px-5 py-3 text-sm font-semibold text-white shadow-md rounded-xl bg-primary">
            <i class="fa-solid fa-plus"></i> Ongeraho Isomo Rigenda
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 mb-5 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @forelse($courses as $course)
        <div class="overflow-hidden bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">

            <div class="relative flex items-center justify-center h-36 bg-gray-100 dark:bg-dark">
                @if($course->thumbnailUrl())
                    <img src="{{ $course->thumbnailUrl() }}" class="object-cover w-full h-full">
                @else
                    <i class="text-4xl text-gray-300 fa-solid fa-diagram-project"></i>
                @endif
                <span class="absolute px-2 py-0.5 text-[11px] font-bold text-white rounded-full top-3 left-3 {{ $course->status === 'published' ? 'bg-primary' : 'bg-gray-500' }}">
                    {{ strtoupper($course->status) }}
                </span>
            </div>

            <div class="p-5">
                <h2 class="mb-1 text-lg font-bold text-primary-dark dark:text-light">{{ $course->title }}</h2>
                <p class="mb-3 text-xs text-gray-400">{{ $course->lessons_count }} amasomo · {{ $course->enrollments_count }} biyandikishije</p>

                @if($course->description)
                    <p class="mb-4 text-sm text-gray-500 line-clamp-2">{{ $course->description }}</p>
                @endif

                <div class="flex items-center gap-2 pt-3 border-t dark:border-gray-700">
                    <a href="{{ route('owner.courses.builder', $course->id) }}"
                       class="flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl bg-primary/10 text-primary-dark">
                        <i class="fa-solid fa-list-ol"></i> Amasomo
                    </a>
                    <button onclick="openEditCourseModal({{ $course->id }})"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-yellow-600 rounded-xl bg-yellow-50">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <form action="{{ route('owner.courses.destroy', $course->id) }}" method="POST"
                          onsubmit="return confirm('Uremeza gusiba iri somo rigenda?')">
                        @csrf @method('DELETE')
                        <button class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-red-600 bg-red-50 rounded-xl">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div id="course-data-{{ $course->id }}" class="hidden"
                 data-title="{{ $course->title }}"
                 data-description="{{ $course->description }}"
                 data-status="{{ $course->status }}"
                 data-action="{{ route('owner.courses.update', $course->id) }}"></div>

        </div>
        @empty
        <div class="p-10 text-center text-gray-400 bg-white col-span-full rounded-3xl dark:bg-darker">
            Nta masomo agenda arahaboneka. Kanda "Ongeraho" hejuru.
        </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $courses->links() }}</div>

</div>

<!-- CREATE -->
<div id="createCourseModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Isomo Rigenda Rishya</h3>
            <button onclick="document.getElementById('createCourseModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="{{ route('owner.courses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 gap-4 p-6">
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Umutwe</label>
                    <input type="text" name="title" required placeholder="e.g. Islamic Basics"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ibisobanuro</label>
                    <textarea name="description" rows="3"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ifoto</label>
                    <input type="file" name="thumbnail" accept="image/*"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('createCourseModal').classList.add('hidden')"
                    class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Bika</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT -->
<div id="editCourseModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Hindura Isomo Rigenda</h3>
            <button onclick="document.getElementById('editCourseModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="editCourseForm" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 gap-4 p-6">
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Umutwe</label>
                    <input type="text" name="title" id="edit_course_title" required
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ibisobanuro</label>
                    <textarea name="description" id="edit_course_description" rows="3"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Guhindura Ifoto (si ngombwa)</label>
                    <input type="file" name="thumbnail" accept="image/*"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" id="edit_course_status" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('editCourseModal').classList.add('hidden')"
                    class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Bika Impinduka</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditCourseModal(id) {
    const box = document.getElementById('course-data-' + id);
    document.getElementById('edit_course_title').value = box.dataset.title;
    document.getElementById('edit_course_description').value = box.dataset.description;
    document.getElementById('edit_course_status').value = box.dataset.status;
    document.getElementById('editCourseForm').action = box.dataset.action;
    document.getElementById('editCourseModal').classList.remove('hidden');
}
</script>

@endsection
