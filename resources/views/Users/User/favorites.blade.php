@extends('Users.User.cover')
@section('title', 'Ibyakunze')

@section('content')
<div class="p-4 md:p-6">

    <h1 class="mb-6 text-2xl font-bold" style="color:#094939">Ibyo Nkunda (My Favorites)</h1>

    @php $hasAny = $grouped['darsat']->count() + $grouped['books']->count() + $grouped['inyandiko']->count() + $grouped['amatangazo']->count(); @endphp

    @if($hasAny === 0)
        <div class="p-10 text-center bg-white shadow-lg rounded-3xl text-gray-400">
            Ntabwo urarahagurukira ibintu. Kanda ikimenyetso cy'umutima (❤) ku masomo, ibitabo, inyandiko cyangwa amatangazo ubikunda kugira ngo ubibike hano.
        </div>
    @else

        @if($grouped['darsat']->count())
        <div class="mb-8">
            <h2 class="mb-3 font-bold" style="color:#094939">Amasomo</h2>
            <div class="grid gap-4 md:grid-cols-2">
                @foreach($grouped['darsat'] as $d)
                <div class="flex items-center justify-between p-4 bg-white shadow rounded-2xl">
                    <div>
                        <p class="font-semibold">{{ $d->title }}</p>
                        <p class="text-xs text-gray-400">{{ $d->type }}</p>
                    </div>
                    <a href="{{ route('guest.teacher-darsa', $d->teachers) }}" class="px-3 py-1.5 text-xs font-bold text-white rounded-lg" style="background:#058e48">Reba</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($grouped['books']->count())
        <div class="mb-8">
            <h2 class="mb-3 font-bold" style="color:#094939">Ibitabo</h2>
            <div class="grid gap-4 md:grid-cols-2">
                @foreach($grouped['books'] as $b)
                <div class="flex items-center justify-between p-4 bg-white shadow rounded-2xl">
                    <div>
                        <p class="font-semibold">{{ $b->title }}</p>
                        <p class="text-xs text-gray-400">{{ $b->author }}</p>
                    </div>
                    <a href="{{ route('guest.books') }}" class="px-3 py-1.5 text-xs font-bold text-white rounded-lg" style="background:#058e48">Reba</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($grouped['inyandiko']->count())
        <div class="mb-8">
            <h2 class="mb-3 font-bold" style="color:#094939">Inyandiko</h2>
            <div class="grid gap-4 md:grid-cols-2">
                @foreach($grouped['inyandiko'] as $i)
                <div class="flex items-center justify-between p-4 bg-white shadow rounded-2xl">
                    <div>
                        <p class="font-semibold">{{ $i->title }}</p>
                        <p class="text-xs text-gray-400">{{ $i->category }}</p>
                    </div>
                    <a href="{{ route('guest.inyandiko.show', $i->slug) }}" class="px-3 py-1.5 text-xs font-bold text-white rounded-lg" style="background:#058e48">Soma</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($grouped['amatangazo']->count())
        <div class="mb-8">
            <h2 class="mb-3 font-bold" style="color:#094939">Amatangazo</h2>
            <div class="grid gap-4 md:grid-cols-2">
                @foreach($grouped['amatangazo'] as $a)
                <div class="flex items-center justify-between p-4 bg-white shadow rounded-2xl">
                    <div>
                        <p class="font-semibold">{{ $a->title }}</p>
                        <p class="text-xs text-gray-400">{{ $a->presenter }}</p>
                    </div>
                    <a href="{{ route('guest.news') }}" class="px-3 py-1.5 text-xs font-bold text-white rounded-lg" style="background:#058e48">Reba</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    @endif

</div>
@endsection
