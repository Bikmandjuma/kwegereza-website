@extends('Users.User.cover')
@section('title', 'Ibikorwa Byanjye')

@section('content')
<div class="p-4 md:p-6">

    <h1 class="mb-6 text-2xl font-bold" style="color:#094939">{{ __('events.title') }}</h1>

    @if($registrations->isEmpty())
        <div class="p-10 text-center bg-white shadow-lg rounded-3xl text-gray-400">
            {{ __('events.empty_prefix') }} <a href="{{ route('guest.events') }}" style="color:#058e48;font-weight:700;">{{ __('events.browse_link') }}</a>.
        </div>
    @else
        <div class="grid gap-5 md:grid-cols-2">
            @foreach($registrations as $reg)
            <div class="p-5 bg-white shadow-lg rounded-3xl">
                <h2 class="mb-1 font-bold" style="color:#094939">{{ $reg->event->title }}</h2>
                <p class="mb-1 text-xs text-gray-400"><i class="fa-solid fa-clock"></i> {{ $reg->event->startsAtLocal()->format('l, F j, Y — g:i A') }}</p>
                @if($reg->event->location)
                    <p class="mb-4 text-xs text-gray-400"><i class="fa-solid fa-location-dot"></i> {{ $reg->event->location }}</p>
                @endif
                <a href="{{ route('guest.event.show', $reg->event->slug) }}" class="text-sm font-semibold" style="color:#058e48">{{ __('events.view_details') }}</a>
            </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
