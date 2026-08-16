@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <div class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
                <i class="fa-solid fa-pen-nib"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-primary-dark dark:text-light">Inyandiko</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Total: <strong>{{ $inyandiko->total() }}</strong></p>
            </div>
        </div>

        <button onclick="document.getElementById('createInyModal').classList.remove('hidden')"
            class="flex items-center gap-2 px-5 py-3 text-sm font-semibold text-white shadow-md rounded-xl bg-primary">
            <i class="fa-solid fa-plus"></i> Andika Inyandiko
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 mb-5 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">

        @forelse($inyandiko as $item)
        <div class="overflow-hidden bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">

            <div class="relative flex items-center justify-center h-36 bg-gray-100 dark:bg-dark">
                @if($item->image)
                    <img src="{{ asset('uploads/inyandiko/'.$item->image) }}" class="object-cover w-full h-full" alt="{{ $item->title }}">
                @else
                    <i class="text-4xl text-gray-300 fa-solid fa-pen-nib"></i>
                @endif

                <span class="absolute px-3 py-1 text-xs font-bold text-white rounded-full top-3 left-3 {{ $item->status === 'published' ? 'bg-primary' : 'bg-gray-500' }}">
                    {{ strtoupper($item->status) }}
                </span>
            </div>

            <div class="p-5">
                @if($item->category)
                    <span class="inline-block px-2 py-0.5 mb-2 text-[11px] font-bold rounded-full bg-primary/10 text-primary-dark">{{ $item->category }}</span>
                @endif

                <h2 class="mb-1 text-lg font-bold text-primary-dark dark:text-light">{{ $item->title }}</h2>

                @if($item->author)
                    <p class="mb-2 text-sm text-gray-500">{{ $item->author }}</p>
                @endif

                @if($item->summary)
                    <p class="mb-4 text-sm text-gray-500 line-clamp-2">{{ $item->summary }}</p>
                @endif

                <div class="flex items-center justify-between pt-3 border-t dark:border-gray-700">
                    <button onclick="openEditInyModal({{ $item->id }})"
                        class="flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl bg-primary/10 text-primary-dark">
                        <i class="fa-solid fa-pen"></i> Hindura
                    </button>

                    <form action="{{ route('owner.inyandiko.destroy', $item->id) }}" method="POST" class="inline"
                          onsubmit="return confirm('Uremeza gusiba iyi nyandiko?')">
                        @csrf @method('DELETE')
                        <button class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-red-600 bg-red-50 rounded-xl">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div id="iny-data-{{ $item->id }}" class="hidden"
                 data-title="{{ $item->title }}"
                 data-category="{{ $item->category }}"
                 data-author="{{ $item->author }}"
                 data-summary="{{ $item->summary }}"
                 data-content="{{ $item->content }}"
                 data-status="{{ $item->status }}"
                 data-action="{{ route('owner.inyandiko.update', $item->id) }}"></div>

        </div>
        @empty
        <div class="p-10 text-center text-gray-400 bg-white col-span-full rounded-3xl dark:bg-darker">
            <i class="mb-3 text-5xl fa-solid fa-pen-nib"></i>
            <p>Nta nyandiko zirahaboneka. Kanda "Andika Inyandiko" hejuru kugira ngo utangire.</p>
        </div>
        @endforelse

    </div>

    <div class="mt-6">{{ $inyandiko->links() }}</div>

</div>

<!-- CREATE MODAL -->
<div id="createInyModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-2xl overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Inyandiko Nshya</h3>
            <button onclick="document.getElementById('createInyModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('owner.inyandiko.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 gap-4 p-6 overflow-y-auto md:grid-cols-2 max-h-[70vh]">

                <div class="md:col-span-2">
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Umutwe</label>
                    <input type="text" name="title" required value="{{ old('title') }}"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                    @error('title')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Icyiciro (Category)</label>
                    <input type="text" name="category" placeholder="e.g. Imam, Sheikh, Scholar"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Uwanditse / Umwanditsi</label>
                    <input type="text" name="author"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>

                <div class="md:col-span-2">
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Incamake (summary shown on cards)</label>
                    <textarea name="summary" rows="2" maxlength="500"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ubutumwa bwuzuye (full content)</label>
                    <textarea name="content" rows="6"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ifoto</label>
                    <input type="file" name="image" accept="image/*"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Dosiye (PDF/Word, si ngombwa)</label>
                    <input type="file" name="file" accept=".pdf,.doc,.docx"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>

                <div class="md:col-span-2">
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Uko ihagaze</label>
                    <select name="status" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="published">Publish ubu</option>
                        <option value="draft">Bika nka Draft</option>
                    </select>
                </div>

            </div>

            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('createInyModal').classList.add('hidden')"
                    class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl dark:bg-gray-700 dark:text-gray-200">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Bika</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT MODAL -->
<div id="editInyModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-2xl overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Hindura Inyandiko</h3>
            <button onclick="document.getElementById('editInyModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editInyForm" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 gap-4 p-6 overflow-y-auto md:grid-cols-2 max-h-[70vh]">

                <div class="md:col-span-2">
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Umutwe</label>
                    <input type="text" name="title" id="edit_iny_title" required
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Icyiciro</label>
                    <input type="text" name="category" id="edit_iny_category"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Uwanditse</label>
                    <input type="text" name="author" id="edit_iny_author"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>

                <div class="md:col-span-2">
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Incamake</label>
                    <textarea name="summary" id="edit_iny_summary" rows="2" maxlength="500"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ubutumwa bwuzuye</label>
                    <textarea name="content" id="edit_iny_content" rows="6"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Guhindura Ifoto (si ngombwa)</label>
                    <input type="file" name="image" accept="image/*"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Guhindura Dosiye (si ngombwa)</label>
                    <input type="file" name="file" accept=".pdf,.doc,.docx"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>

                <div class="md:col-span-2">
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Uko ihagaze</label>
                    <select name="status" id="edit_iny_status" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="published">Publish</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>

            </div>

            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('editInyModal').classList.add('hidden')"
                    class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl dark:bg-gray-700 dark:text-gray-200">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Bika Impinduka</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditInyModal(id) {
    const box = document.getElementById('iny-data-' + id);
    document.getElementById('edit_iny_title').value = box.dataset.title;
    document.getElementById('edit_iny_category').value = box.dataset.category;
    document.getElementById('edit_iny_author').value = box.dataset.author;
    document.getElementById('edit_iny_summary').value = box.dataset.summary;
    document.getElementById('edit_iny_content').value = box.dataset.content;
    document.getElementById('edit_iny_status').value = box.dataset.status;
    document.getElementById('editInyForm').action = box.dataset.action;
    document.getElementById('editInyModal').classList.remove('hidden');
}
</script>

@endsection
