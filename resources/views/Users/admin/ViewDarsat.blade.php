@extends('Users.admin.cover')

@section('content')

<div class="p-6">

    <h2 class="mb-6 text-3xl font-bold">
        Darsat Library
    </h2>

    <!-- Teachers Tabs -->
    <div class="flex flex-wrap gap-3 mb-8">

        @foreach($users as $index=>$user)

            <!-- <button
                class="teacher-tab px-5 py-3 rounded-xl bg-primary text-white font-semibold"
                data-tab="teacher{{ $user->id }}"
            >
                {{ $user->firstname }} {{ $user->lastname }}
            </button> -->
            <button
                class="teacher-tab px-5 py-3 rounded-xl bg-primary text-white font-semibold"
                data-tab="teacher{{ $user->id }}"
            >
                {{ $user->firstname }} {{ $user->lastname }}

                <span class="ml-2 px-2 py-1 text-xs bg-black text-primary rounded-full">
                    {{ $user->darsat_count }}
                </span>
            </button>

        @endforeach

    </div>

    <!-- Lessons -->
    @foreach($users as $index=>$user)

        <div
            id="teacher{{ $user->id }}"
            class="teacher-content {{ $index!=0 ? 'hidden' : '' }}"
        >

            @forelse($darsat[$user->id] ?? [] as $lesson)

                <div class="p-5 mb-5 bg-white rounded-2xl shadow dark:bg-darker">

                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between ">

                        <div class="flex items-center gap-4">

                            @if($lesson->thumbnailUrl())
                                <img src="{{ $lesson->thumbnailUrl() }}" class="object-cover w-16 h-16 rounded-xl">
                            @endif

                            <div>
                                <h3 class="text-xl font-bold dark:text-gray-400">
                                    {{ $lesson->title }}
                                    <span class="px-2 py-0.5 ml-2 text-[10px] font-bold rounded-full {{ ($lesson->status ?? 'published') === 'published' ? 'bg-primary text-white' : 'bg-gray-300 text-gray-700' }}">
                                        {{ strtoupper($lesson->status ?? 'published') }}
                                    </span>
                                </h3>

                                <p class="text-gray-500">
                                    {{ $lesson->type }}
                                </p>

                                @if($lesson->description)
                                    <p class="max-w-md mt-1 text-sm text-gray-400">{{ \Illuminate\Support\Str::limit($lesson->description, 100) }}</p>
                                @endif
                            </div>

                        </div>

                        <div class="flex items-center gap-3">

                            <audio
                                id="audio{{ $lesson->id }}"
                                src="{{ $lesson->audioUrl() }}"
                            ></audio>

                            <button
                                class="play-btn px-5 py-2 text-white bg-green-600 rounded-xl"
                                data-audio="audio{{ $lesson->id }}"
                            >
                                ▶ Play
                            </button>

                            <button
                                type="button"
                                onclick="openEditDarsatModal({{ $lesson->id }})"
                                class="px-4 py-2 text-white bg-yellow-500 rounded-xl"
                            >
                                <i class="fa-solid fa-pen"></i>
                            </button>

                            <form action="{{ route('owner.destroyDarsat', $lesson->id) }}" method="POST"
                                  onsubmit="return confirm('Delete this Darsat?')">
                                @csrf @method('DELETE')
                                <button class="px-4 py-2 text-white bg-red-600 rounded-xl">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>

                        </div>

                    </div>

                    <div id="darsat-data-{{ $lesson->id }}" class="hidden"
                         data-title="{{ $lesson->title }}"
                         data-type="{{ $lesson->type }}"
                         data-teachers="{{ $lesson->teachers }}"
                         data-description="{{ $lesson->description }}"
                         data-status="{{ $lesson->status ?? 'published' }}"
                         data-action="{{ route('owner.updateDarsat', $lesson->id) }}"></div>

                </div>

            @empty

                <div class="p-8 text-center bg-white rounded-xl shadow">

                    <h4 class="text-lg font-semibold">
                        No lessons uploaded.
                    </h4>

                </div>

            @endforelse

        </div>

    @endforeach

</div>

<script>

// Teacher Tabs
const tabs=document.querySelectorAll('.teacher-tab');
const contents=document.querySelectorAll('.teacher-content');

tabs.forEach(tab=>{

    tab.addEventListener('click',()=>{

        contents.forEach(c=>c.classList.add('hidden'));

        document
            .getElementById(tab.dataset.tab)
            .classList.remove('hidden');

    });

});

// Play Button
document.querySelectorAll('.play-btn').forEach(btn=>{

    btn.addEventListener('click',()=>{

        let audio=document.getElementById(btn.dataset.audio);

        if(audio.paused){

            document.querySelectorAll('audio').forEach(a=>{

                if(a!=audio){

                    a.pause();
                    a.currentTime=0;

                }

            });

            audio.play();

            btn.innerHTML="⏸ Pause";

        }else{

            audio.pause();

            btn.innerHTML="▶ Play";

        }

        audio.onended=function(){

            btn.innerHTML="▶ Play";

        };

    });

});

</script>

<!-- EDIT DARSAT MODAL -->
<div id="editDarsatModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold">Hindura Darsat</h3>
            <button type="button" onclick="document.getElementById('editDarsatModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editDarsatForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-4 p-6 overflow-y-auto max-h-[65vh]">
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Title</label>
                    <input type="text" name="title" id="edit_darsat_title" required
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Type / Category</label>
                    <input type="text" name="type" id="edit_darsat_type" required
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Teacher</label>
                    <select name="teachers" id="edit_darsat_teachers" required
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->title }} {{ $u->firstname }} {{ $u->lastname }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Description</label>
                    <textarea name="description" id="edit_darsat_description" rows="3"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Replace Audio (optional)</label>
                    <input type="file" name="audio" accept=".mp3,.wav,.ogg,.m4a"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Replace Thumbnail (optional)</label>
                    <input type="file" name="thumbnail" accept="image/*"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" id="edit_darsat_status" required
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('editDarsatModal').classList.add('hidden')"
                    class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl">Cancel</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditDarsatModal(id) {
    const box = document.getElementById('darsat-data-' + id);
    document.getElementById('edit_darsat_title').value = box.dataset.title;
    document.getElementById('edit_darsat_type').value = box.dataset.type;
    document.getElementById('edit_darsat_teachers').value = box.dataset.teachers;
    document.getElementById('edit_darsat_description').value = box.dataset.description;
    document.getElementById('edit_darsat_status').value = box.dataset.status;
    document.getElementById('editDarsatForm').action = box.dataset.action;
    document.getElementById('editDarsatModal').classList.remove('hidden');
}
</script>

@endsection