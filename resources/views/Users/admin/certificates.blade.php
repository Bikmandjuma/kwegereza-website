@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <div class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
                <i class="fa-solid fa-award"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-primary-dark dark:text-light">Ibyemezo (Certificates)</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Total: <strong>{{ $certificates->total() }}</strong></p>
            </div>
        </div>

        <button onclick="document.getElementById('issueCertModal').classList.remove('hidden')"
            class="flex items-center gap-2 px-5 py-3 text-sm font-semibold text-white shadow-md rounded-xl bg-primary">
            <i class="fa-solid fa-plus"></i> Tanga Icyemezo
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 mb-5 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif

    <div class="overflow-hidden bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-dark dark:text-gray-400">
                <tr>
                    <th class="px-5 py-3">Certificate ID</th>
                    <th class="px-5 py-3">Umunyeshuri</th>
                    <th class="px-5 py-3">Porogaramu</th>
                    <th class="px-5 py-3">Yatanzwe</th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-gray-700">
                @forelse($certificates as $cert)
                <tr>
                    <td class="px-5 py-3"><code class="px-2 py-1 text-xs bg-gray-100 rounded dark:bg-dark">{{ $cert->certificate_number }}</code></td>
                    <td class="px-5 py-3 font-semibold text-primary-dark dark:text-light">{{ $cert->user->firstname }} {{ $cert->user->lastname }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $cert->course->title ?? $cert->title }}</td>
                    <td class="px-5 py-3 text-gray-400">{{ $cert->issued_at->format('M j, Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="p-10 text-center text-gray-400">Nta byemezo birahaboneka.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $certificates->links() }}</div>

</div>

<!-- MANUAL ISSUE -->
<div id="issueCertModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Tanga Icyemezo mu Ntoki</h3>
            <button onclick="document.getElementById('issueCertModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="{{ route('owner.certificates.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-4 p-6">
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Umunyeshuri</label>
                    <select name="user_id" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="">-- Hitamo --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->firstname }} {{ $u->lastname }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Isomo Rigenda (optional)</label>
                    <select name="course_id" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="">-- Nta na kimwe --</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Umutwe w'Icyemezo</label>
                    <input type="text" name="title" required placeholder="e.g. Certificate of Completion — Islamic Basics"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('issueCertModal').classList.add('hidden')" class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Tanga</button>
            </div>
        </form>
    </div>
</div>

@endsection
