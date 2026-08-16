@extends('Users.User.cover')
@section('title', 'Amasomo Agenda')

@section('content')
<div class="p-4 md:p-6">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold" style="color:#0B3D2E">Amasomo Agenda Nyiyandikishijemo</h1>
        <a href="{{ route('guest.courses') }}" class="px-4 py-2 text-sm font-semibold rounded-xl" style="background:#0B6D20;color:#fff">
            Shakisha andi masomo agenda
        </a>
    </div>

    @if($enrollments->isEmpty())
        <div class="p-10 text-center bg-white shadow-lg rounded-3xl text-gray-400">
            Ntabwo urarahagurukira isomo rigenda. Kanda "Shakisha andi masomo agenda" hejuru.
        </div>
    @else
        <div class="grid gap-5 md:grid-cols-2">
            @foreach($enrollments as $enrollment)
                @php $percent = $enrollment->course->completionPercentFor(auth('student')->id()); @endphp
                <div class="p-5 bg-white shadow-lg rounded-3xl">
                    <h2 class="mb-1 font-bold" style="color:#0B3D2E">{{ $enrollment->course->title }}</h2>
                    <p class="mb-3 text-xs text-gray-400">
                        @if($enrollment->completed_at) Ryarangiye {{ $enrollment->completed_at->diffForHumans() }}
                        @else Aho ugeze: {{ $percent }}%
                        @endif
                    </p>
                    <div class="h-2 mb-4 overflow-hidden bg-gray-100 rounded-full">
                        <div class="h-full" style="width:{{ $percent }}%;background:#0B6D20"></div>
                    </div>
                    <a href="{{ route('guest.course.show', $enrollment->course->slug) }}" class="text-sm font-semibold" style="color:#0B6D20">Komeza →</a>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
