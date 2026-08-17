@extends('Users.User.cover')
@section('title', 'Recovery Codes')

@section('content')
<div class="max-w-lg p-4 mx-auto md:p-6">

    <div class="p-6 bg-white shadow-lg rounded-3xl">
        <div class="flex items-center gap-3 mb-4">
            <div class="flex items-center justify-center text-white rounded-2xl w-14 h-14" style="background:#e2b45f;">
                <i class="fa-solid fa-key"></i>
            </div>
            <h1 class="text-xl font-bold" style="color:#094939">{{ __('twofa.recovery_title') }}</h1>
        </div>

        <p class="mb-4 text-sm font-semibold text-red-600">
            {{ __('twofa.recovery_warning') }}
        </p>

        <div class="grid grid-cols-2 gap-2 p-4 mb-4 bg-gray-100 rounded-2xl">
            @foreach($recoveryCodes as $code)
                <code style="font-size:13px;font-weight:700;color:#094939;">{{ $code }}</code>
            @endforeach
        </div>

        <button onclick="window.print()" class="w-full py-3 mb-3 font-semibold rounded-2xl" style="background:#f5ebe2;color:#094939;">
            <i class="fa-solid fa-print"></i> {{ __('twofa.print_save') }}
        </button>

        <a href="{{ route('student.dashboard') }}" class="block w-full py-3 font-semibold text-center text-white rounded-2xl" style="background:#058e48;">
            {{ __('twofa.continue') }}
        </a>
    </div>

</div>
@endsection
