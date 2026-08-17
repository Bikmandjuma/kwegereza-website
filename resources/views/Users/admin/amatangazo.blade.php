@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-center lg:justify-between">

        <div class="flex items-center gap-4">

            <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
                <i class="fa-solid fa-bullhorn"></i>
            </div>

            <div>
                <h1 class="text-2xl font-bold text-primary-dark dark:text-light">
                    Amatangazo
                </h1>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Total: <strong>{{ $amatangazo->total() }}</strong>
                </p>
            </div>

        </div>

        <button
            onclick="document.getElementById('createModal').classList.remove('hidden')"
            class="flex items-center gap-2 px-5 py-3 text-sm font-semibold text-white shadow-md rounded-xl bg-primary"
        >
            <i class="fa-solid fa-plus"></i>
            Shyiraho Itangazo
        </button>

    </div>

    @if(session('success'))
        <div class="p-4 mb-5 font-medium text-green-700 bg-green-100 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <!-- LIST -->
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">

        @forelse($amatangazo as $item)

        <div class="overflow-hidden bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">

            <div class="relative flex items-center justify-center h-40 bg-gray-100 dark:bg-dark">
                @if($item->image)
                    <img src="{{ asset('uploads/amatangazo/'.$item->image) }}" class="object-cover w-full h-full" alt="{{ $item->title }}">
                @else
                    <i class="text-5xl text-gray-300 fa-solid fa-bullhorn"></i>
                @endif

                <span class="absolute px-3 py-1 text-xs font-bold rounded-full top-3 left-3
                    {{ $item->status === 'live' ? 'bg-red-600 text-white' : ($item->status === 'upcoming' ? 'bg-amber-500 text-white' : 'bg-primary text-white') }}">
                    {{ strtoupper($item->status) }}
                </span>

                @if(!$item->is_published)
                    <span class="absolute px-3 py-1 text-xs font-bold text-white bg-gray-500 rounded-full top-3 right-3">
                        HIDDEN
                    </span>
                @endif
            </div>

            <div class="p-5">
                <h2 class="mb-1 text-lg font-bold text-primary-dark dark:text-light">{{ $item->title }}</h2>

                @if($item->presenter)
                    <p class="mb-2 text-sm text-gray-500">{{ $item->presenter }}</p>
                @endif

                @if($item->description)
                    <p class="mb-4 text-sm text-gray-500 line-clamp-2">{{ $item->description }}</p>
                @endif

                <div class="flex items-center justify-between pt-3 border-t dark:border-gray-700">

                    <button
                        onclick="openEditModal({{ $item->id }})"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl bg-primary/10 text-primary-dark"
                    >
                        <i class="fa-solid fa-pen"></i> Hindura
                    </button>

                    <form action="{{ route('owner.amatangazo.toggle', $item->id) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <button class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 rounded-xl dark:bg-dark dark:text-gray-300">
                            <i class="fa-solid {{ $item->is_published ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                        </button>
                    </form>

                    <form action="{{ route('owner.amatangazo.destroy', $item->id) }}" method="POST" class="inline"
                          onsubmit="return confirm('Uremeza gusiba iri tangazo?')">
                        @csrf @method('DELETE')
                        <button class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-red-600 bg-red-50 rounded-xl">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>

                </div>
            </div>

            <!-- hidden data for edit modal -->
            <div id="data-{{ $item->id }}" class="hidden"
                 data-title="{{ $item->title }}"
                 data-description="{{ $item->description }}"
                 data-presenter="{{ $item->presenter }}"
                 data-status="{{ $item->status }}"
                 data-action="{{ route('owner.amatangazo.update', $item->id) }}"
                 data-publish="{{ $item->is_published ? 1 : 0 }}"></div>

        </div>

        @empty
        <div class="p-10 text-center text-gray-400 bg-white col-span-full rounded-3xl dark:bg-darker">
            <i class="mb-3 text-5xl fa-solid fa-bullhorn"></i>
            <p>Nta matangazo arahaboneka. Kanda "Shyiraho Itangazo" hejuru kugira ngo utangire.</p>
        </div>
        @endforelse

    </div>

    <div class="mt-6">
        {{ $amatangazo->links() }}
    </div>

</div>

<!-- CREATE MODAL -->
<div id="createModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">

        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Itangazo Rishya</h3>
            <button onclick="document.getElementById('createModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('owner.amatangazo.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 gap-4 p-6 overflow-y-auto max-h-[70vh]">

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Umutwe (Title)</label>
                    <input type="text" name="title" required value="{{ old('title') }}"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                    @error('title')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Uwigisha (Presenter)</label>
                    <input type="text" name="presenter" value="{{ old('presenter') }}" placeholder="Sheikh ..."
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ibisobanuro</label>
                    <textarea name="description" rows="3"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Uko rihagaze</label>
                    <select name="status" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="upcoming">Asigaye (Upcoming)</option>
                        <option value="live">Live</option>
                        <option value="done">Ryarangiye (Done)</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ifoto</label>
                    <input type="file" name="image" accept="image/*"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                    @error('image')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                    <input type="checkbox" name="is_published" value="1" checked class="rounded text-primary">
                    Erekana ku rubuga (Publish immediately)
                </label>

            </div>

            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')"
                    class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl dark:bg-gray-700 dark:text-gray-200">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Bika</button>
            </div>

        </form>
    </div>
</div>

<!-- EDIT MODAL -->
<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">

        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Hindura Itangazo</h3>
            <button onclick="document.getElementById('editModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-4 p-6 overflow-y-auto max-h-[70vh]">

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Umutwe (Title)</label>
                    <input type="text" name="title" id="edit_title" required
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Uwigisha (Presenter)</label>
                    <input type="text" name="presenter" id="edit_presenter"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ibisobanuro</label>
                    <textarea name="description" id="edit_description" rows="3"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Uko rihagaze</label>
                    <select name="status" id="edit_status" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="upcoming">Asigaye (Upcoming)</option>
                        <option value="live">Live</option>
                        <option value="done">Ryarangiye (Done)</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Guhindura Ifoto (bidasabwa)</label>
                    <input type="file" name="image" accept="image/*"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>

            </div>

            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')"
                    class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl dark:bg-gray-700 dark:text-gray-200">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Bika Impinduka</button>
            </div>

        </form>
    </div>
</div>

<script>
function openEditModal(id) {
    const box = document.getElementById('data-' + id);
    document.getElementById('edit_title').value = box.dataset.title;
    document.getElementById('edit_presenter').value = box.dataset.presenter;
    document.getElementById('edit_description').value = box.dataset.description;
    document.getElementById('edit_status').value = box.dataset.status;
    document.getElementById('editForm').action = box.dataset.action;
    document.getElementById('editModal').classList.remove('hidden');
}
</script>

@endsection
