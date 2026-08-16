@extends('Users.User.cover')
@section('title', 'Ubuzima Bwite (Privacy)')

@section('content')
<div class="max-w-lg p-4 mx-auto md:p-6">

    @if(session('success'))
        <div class="p-4 mb-4 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 mb-4 font-medium text-red-700 bg-red-100 rounded-xl">{{ session('error') }}</div>
    @endif

    <div class="p-6 mb-4 bg-white shadow-lg rounded-3xl">
        <h1 class="mb-4 text-xl font-bold" style="color:#0B3D2E">Kugaragara kwa Profile</h1>
        <p class="mb-4 text-sm text-gray-600">
            Iyo bihagaritswe, izina ryawe risimbuzwa "Umunyeshuri (Anonymous)" mu bitekerezo (comments) ushyiraho ku nyandiko za rubanda.
        </p>

        <form action="{{ route('student.privacy.visibility') }}" method="POST" class="flex items-center gap-3">
            @csrf
            <input type="hidden" name="profile_visible" value="0">
            <input type="checkbox" name="profile_visible" value="1" id="profVis" onchange="this.form.submit()"
                {{ $user->profile_visible ? 'checked' : '' }} class="w-5 h-5">
            <label for="profVis" class="text-sm font-semibold text-gray-700">Emerera izina ryanjye kugaragara ku bitekerezo rubanda ibona</label>
        </form>
    </div>

    <div class="p-6 mb-4 bg-white shadow-lg rounded-3xl">
        <h2 class="mb-2 text-lg font-bold" style="color:#0B3D2E">Amakuru Yanjye (Data Export)</h2>
        <p class="mb-4 text-sm text-gray-600">
            Kuraho dosiye ya JSON irimo amakuru yose urubuga rufite kuri wewe — amasomo warangije, ibyemezo, ibimenyetso, n'ibindi.
        </p>
        <a href="{{ route('student.privacy.export') }}" class="inline-block px-5 py-2.5 text-sm font-semibold text-white rounded-xl" style="background:#0B6D20">
            <i class="fa-solid fa-download"></i> Kuraho Amakuru Yanjye
        </a>
    </div>

    <div class="p-6 border-2 border-red-100 bg-white shadow-lg rounded-3xl">
        <h2 class="mb-2 text-lg font-bold text-red-600">Gusiba Konti</h2>

        @if($pendingDeletion)
            <p class="text-sm font-semibold text-yellow-700">
                <i class="fa-solid fa-clock"></i> Ufite icyifuzo cyo gusiba konti gitegereje gusuzumwa n'ubuyobozi.
            </p>
        @else
            <p class="mb-4 text-sm text-gray-600">
                Iki cyifuzo kizasuzumwa n'ubuyobozi mbere yo gushyirwa mu bikorwa — konti ntizahita isibwa ako kanya.
            </p>
            <form action="{{ route('student.privacy.deleteRequest') }}" method="POST" onsubmit="return confirm('Uzi neza ko ushaka gusaba gusiba konti yawe?')">
                @csrf
                <textarea name="reason" rows="2" placeholder="Impamvu (si ngombwa)" class="w-full px-4 py-3 mb-3 border rounded-2xl"></textarea>
                <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl">
                    Saba Gusiba Konti
                </button>
            </form>
        @endif
    </div>

</div>
@endsection
