@extends('Users.User.cover')
@section('title', 'Ibyemezo Byanjye')

@section('content')
<div class="p-4 md:p-6">

    <h1 class="mb-6 text-2xl font-bold" style="color:#0B3D2E">{{ __('certificates.title') }}</h1>

    @if($certificates->isEmpty())
        <div class="p-10 text-center bg-white shadow-lg rounded-3xl text-gray-400">
            {{ __('certificates.empty_state') }}
        </div>
    @else
        <div class="grid gap-5 md:grid-cols-2">
            @foreach($certificates as $cert)
            <div class="p-5 bg-white shadow-lg rounded-3xl">
                <p class="mb-1 text-xs font-bold text-gray-400">{{ $cert->certificate_number }}</p>
                <h2 class="mb-2 font-bold" style="color:#0B3D2E">{{ $cert->course->title ?? $cert->title }}</h2>
                <p class="mb-4 text-xs text-gray-400">{{ __('certificates.issued_label') }} {{ $cert->issued_at->format('F j, Y') }}</p>
                <a href="{{ route('student.certificates.show', $cert->id) }}" target="_blank"
                   class="inline-block px-4 py-2 text-sm font-semibold text-white rounded-xl" style="background:#0B6D20">
                    <i class="fa-solid fa-download"></i> {{ __('certificates.view_download') }}
                </a>
            </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
