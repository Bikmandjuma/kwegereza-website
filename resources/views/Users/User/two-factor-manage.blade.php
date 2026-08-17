@extends('Users.User.cover')
@section('title', '2FA')

@section('content')
<div class="max-w-lg p-4 mx-auto md:p-6">

    <div class="p-6 bg-white shadow-lg rounded-3xl">
        <div class="flex items-center gap-3 mb-4">
            <div class="flex items-center justify-center text-white rounded-2xl w-14 h-14" style="background:#0B6D20;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold" style="color:#0B3D2E">{{ __('twofa.heading') }}</h1>
                <span class="inline-block px-2 py-0.5 mt-1 text-xs font-bold text-white rounded-full" style="background:#0B6D20;">{{ __('twofa.status_enabled') }}</span>
            </div>
        </div>

        @if(session('error'))
            <div class="p-3 mb-4 text-sm font-medium text-red-700 bg-red-100 rounded-xl">{{ session('error') }}</div>
        @endif

        <p class="mb-6 text-sm text-gray-600">
            {{ __('twofa.enabled_since') }} {{ $user->two_factor_confirmed_at?->format('F j, Y') }}.
            {{ __('twofa.every_login') }}
        </p>

        <form action="{{ route('student.2fa.disable') }}" method="POST" onsubmit="return confirm('{{ __('twofa.disable_confirm') }}')">
            @csrf
            <label class="block mb-1 text-sm font-semibold text-gray-700">{{ __('twofa.password_to_disable') }}</label>
            <input type="password" name="password" required class="w-full px-4 py-3 mb-3 border rounded-2xl">
            <button type="submit" class="w-full py-3 font-semibold text-white bg-red-600 rounded-2xl">
                {{ __('twofa.disable_button') }}
            </button>
        </form>
    </div>

</div>
@endsection
