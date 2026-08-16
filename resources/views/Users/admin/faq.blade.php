@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <div class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
                <i class="fa-solid fa-comment-dots"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-primary-dark dark:text-light">Guest Chat — FAQ</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Total: <strong>{{ $questions->total() }}</strong></p>
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('owner.faq.analytics') }}"
               class="flex items-center gap-2 px-5 py-3 text-sm font-semibold rounded-xl bg-primary/10 text-primary-dark">
                <i class="fa-solid fa-chart-line"></i> Analytics
            </a>
            <button onclick="document.getElementById('createFaqModal').classList.remove('hidden')"
                class="flex items-center gap-2 px-5 py-3 text-sm font-semibold text-white shadow-md rounded-xl bg-primary">
                <i class="fa-solid fa-plus"></i> Ongeraho
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 mb-5 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif

    <div class="grid gap-5 md:grid-cols-2">

        @forelse($questions as $q)
        <div class="p-5 bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">
            <div class="flex items-start justify-between mb-2">
                <span class="px-2 py-0.5 text-[11px] font-bold rounded-full {{ $q->status === 'active' ? 'bg-primary/10 text-primary-dark' : 'bg-gray-200 text-gray-500' }}">
                    {{ strtoupper($q->status) }}
                </span>
                <span class="text-xs text-gray-400">Matched {{ $q->times_matched }}x</span>
            </div>

            <h3 class="mb-1 font-bold text-primary-dark dark:text-light">{{ $q->question }}</h3>
            <p class="mb-3 text-sm text-gray-500 line-clamp-3">{{ $q->answer }}</p>

            @if($q->category)
                <span class="inline-block px-2 py-0.5 mb-3 text-[11px] font-semibold rounded-full bg-gray-100 dark:bg-dark">{{ $q->category }}</span>
            @endif

            <div class="flex items-center gap-2 pt-3 border-t dark:border-gray-700">
                <button onclick="openEditFaqModal({{ $q->id }})"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl bg-primary/10 text-primary-dark">
                    <i class="fa-solid fa-pen"></i> Hindura
                </button>
                <form action="{{ route('owner.faq.destroy', $q->id) }}" method="POST"
                      onsubmit="return confirm('Uremeza gusiba iki kibazo?')">
                    @csrf @method('DELETE')
                    <button class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-red-600 bg-red-50 rounded-xl">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            </div>

            <div id="faq-data-{{ $q->id }}" class="hidden"
                 data-question="{{ $q->question }}"
                 data-answer="{{ $q->answer }}"
                 data-category="{{ $q->category }}"
                 data-keywords="{{ $q->keywords }}"
                 data-status="{{ $q->status }}"
                 data-action="{{ route('owner.faq.update', $q->id) }}"></div>
        </div>
        @empty
        <div class="p-10 text-center text-gray-400 bg-white col-span-full rounded-3xl dark:bg-darker">
            Nta bibazo/bisubizo birahaboneka. Kanda "Ongeraho" hejuru.
        </div>
        @endforelse

    </div>

    <div class="mt-6">{{ $questions->links() }}</div>

</div>

<!-- CREATE -->
<div id="createFaqModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Ikibazo n'Igisubizo Bishya</h3>
            <button onclick="document.getElementById('createFaqModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="{{ route('owner.faq.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-4 p-6 overflow-y-auto max-h-[65vh]">
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ikibazo (Question)</label>
                    <input type="text" name="question" required placeholder="e.g. Islam ni iki?"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Igisubizo (Answer)</label>
                    <textarea name="answer" rows="4" required
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Icyiciro (optional)</label>
                    <input type="text" name="category" placeholder="e.g. Ibanze"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Amagambo y'inyongera ashakishwaho (comma-separated)</label>
                    <input type="text" name="keywords" placeholder="e.g. islam, idini, imana"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('createFaqModal').classList.add('hidden')"
                    class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Bika</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT -->
<div id="editFaqModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Hindura</h3>
            <button onclick="document.getElementById('editFaqModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="editFaqForm" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 gap-4 p-6 overflow-y-auto max-h-[65vh]">
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ikibazo</label>
                    <input type="text" name="question" id="edit_faq_question" required
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Igisubizo</label>
                    <textarea name="answer" id="edit_faq_answer" rows="4" required
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Icyiciro</label>
                    <input type="text" name="category" id="edit_faq_category"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Amagambo y'inyongera</label>
                    <input type="text" name="keywords" id="edit_faq_keywords"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Uko rihagaze</label>
                    <select name="status" id="edit_faq_status" required
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('editFaqModal').classList.add('hidden')"
                    class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Bika Impinduka</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditFaqModal(id) {
    const box = document.getElementById('faq-data-' + id);
    document.getElementById('edit_faq_question').value = box.dataset.question;
    document.getElementById('edit_faq_answer').value = box.dataset.answer;
    document.getElementById('edit_faq_category').value = box.dataset.category;
    document.getElementById('edit_faq_keywords').value = box.dataset.keywords;
    document.getElementById('edit_faq_status').value = box.dataset.status;
    document.getElementById('editFaqForm').action = box.dataset.action;
    document.getElementById('editFaqModal').classList.remove('hidden');
}
</script>

@endsection
