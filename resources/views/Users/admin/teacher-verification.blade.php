@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <div class="flex items-center gap-4 mb-6">
        <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
            <i class="fa-solid fa-certificate"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-primary-dark dark:text-light">Kwemeza Abarimu (Teacher Verification)</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Total: <strong>{{ $teachers->count() }}</strong></p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 mb-5 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @foreach($teachers as $teacher)
        <div class="p-5 bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">

            <div class="flex items-center gap-3 mb-3">
                <img src="{{ \App\Support\FileUrl::resolve($teacher->image, 'owners') ?? asset('Guest/images/logo.png') }}"
                     class="object-cover w-12 h-12 rounded-full">
                <div>
                    <h2 class="font-bold text-primary-dark dark:text-light">{{ $teacher->firstname }} {{ $teacher->lastname }}</h2>
                    <p class="text-xs text-gray-400">{{ ucfirst($teacher->title) }}</p>
                </div>
                @if($teacher->is_verified)
                    <i class="ml-auto text-lg fa-solid fa-circle-check" style="color:#058e48" title="Verified"></i>
                @endif
            </div>

            <p class="mb-3 text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($teacher->bio, 90) ?: 'Nta bio yashyizweho.' }}</p>

            <div class="flex items-center gap-2 pt-3 border-t dark:border-gray-700">
                @if($teacher->is_verified)
                    <form action="{{ route('owner.teacherVerification.unverify', $teacher->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="px-4 py-2 text-xs font-bold text-gray-600 bg-gray-200 rounded-xl">Kuraho Verification</button>
                    </form>
                @else
                    <form action="{{ route('owner.teacherVerification.verify', $teacher->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="px-4 py-2 text-xs font-bold text-white rounded-xl" style="background:#058e48">
                            <i class="fa-solid fa-check"></i> Emeza (Verify)
                        </button>
                    </form>
                @endif
                <button onclick="openProfileModal({{ $teacher->id }})" class="px-4 py-2 text-xs font-bold rounded-xl bg-primary/10 text-primary-dark">
                    Hindura Bio
                </button>
            </div>

            <div id="teacher-data-{{ $teacher->id }}" class="hidden"
                 data-bio="{{ $teacher->bio }}"
                 data-credentials="{{ $teacher->credentials }}"
                 data-action="{{ route('owner.teacherVerification.updateProfile', $teacher->id) }}"></div>
        </div>
        @endforeach
    </div>

</div>

<div id="profileModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Hindura Umwirondoro</h3>
            <button onclick="document.getElementById('profileModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="profileForm" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 gap-4 p-6">
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Bio</label>
                    <textarea name="bio" id="profile_bio" rows="4" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ibyavuye mu mashuri / Ijazah / Impamyabumenyi</label>
                    <textarea name="credentials" id="profile_credentials" rows="4" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('profileModal').classList.add('hidden')" class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Bika</button>
            </div>
        </form>
    </div>
</div>

<script>
function openProfileModal(id) {
    const box = document.getElementById('teacher-data-' + id);
    document.getElementById('profile_bio').value = box.dataset.bio || '';
    document.getElementById('profile_credentials').value = box.dataset.credentials || '';
    document.getElementById('profileForm').action = box.dataset.action;
    document.getElementById('profileModal').classList.remove('hidden');
}
</script>

@endsection
