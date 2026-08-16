@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <div class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
                <i class="fa-solid fa-medal"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-primary-dark dark:text-light">Ibimenyetso (Badges)</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Total: <strong>{{ $badges->count() }}</strong></p>
            </div>
        </div>

        <button onclick="document.getElementById('createBadgeModal').classList.remove('hidden')"
            class="flex items-center gap-2 px-5 py-3 text-sm font-semibold text-white shadow-md rounded-xl bg-primary">
            <i class="fa-solid fa-plus"></i> Ongeraho Ikimenyetso
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 mb-5 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif

    <div class="grid gap-5 md:grid-cols-3">
        @foreach($badges as $badge)
        <div class="p-5 text-center bg-white shadow-lg rounded-3xl dark:bg-darker">
            <div class="mb-2 text-4xl">{{ $badge->icon }}</div>
            <p class="font-bold text-primary-dark dark:text-light">{{ $badge->name }}</p>
            <p class="mt-1 text-xs text-gray-400">{{ $badge->description }}</p>
            <p class="mt-2 text-[11px] text-gray-400">{{ str_replace('_', ' ', $badge->criteria_type) }} &ge; {{ $badge->criteria_value }}</p>
            <p class="mt-1 text-xs font-semibold" style="color:#058e48">{{ $badge->user_badges_count }} bantu babibonye</p>

            <form action="{{ route('owner.badges.destroy', $badge->id) }}" method="POST" class="mt-3" onsubmit="return confirm('Gusiba iki kimenyetso?')">
                @csrf @method('DELETE')
                <button class="text-xs font-semibold text-red-600"><i class="fa-solid fa-trash"></i> Siba</button>
            </form>
        </div>
        @endforeach
    </div>

</div>

<!-- CREATE -->
<div id="createBadgeModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Ikimenyetso Gishya</h3>
            <button onclick="document.getElementById('createBadgeModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="{{ route('owner.badges.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-4 p-6">
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Izina</label>
                    <input type="text" name="name" required placeholder="e.g. 50 Lessons Completed"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ibisobanuro</label>
                    <input type="text" name="description"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Icon (emoji)</label>
                    <input type="text" name="icon" placeholder="🏅" maxlength="10"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ubwoko bw'igipimo</label>
                    <select name="criteria_type" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="streak_days">Iminsi ikurikirana yo kwiga (streak)</option>
                        <option value="darsat_completed">Amasomo ya Darsat yarangiye</option>
                        <option value="courses_completed">Amasomo Agenda yarangiye</option>
                        <option value="quizzes_passed">Ibizamini byatsinzwe</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Igipimo (nomero)</label>
                    <input type="number" name="criteria_value" min="1" required
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('createBadgeModal').classList.add('hidden')" class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Bika</button>
            </div>
        </form>
    </div>
</div>

@endsection
