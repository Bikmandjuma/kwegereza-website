@extends('Users.User.cover')
@section('title', 'Gushyiraho 2FA')

@section('content')
<div class="max-w-lg p-4 mx-auto md:p-6">

    <div class="p-6 bg-white shadow-lg rounded-3xl">
        <div class="flex items-center gap-3 mb-4">
            <div class="flex items-center justify-center text-white rounded-2xl w-14 h-14" style="background:#0B6D20;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h1 class="text-xl font-bold" style="color:#0B3D2E">{{ __('twofa.heading') }}</h1>
        </div>

        @if(session('error'))
            <div class="p-3 mb-4 text-sm font-medium text-red-700 bg-red-100 rounded-xl">{{ session('error') }}</div>
        @endif

        <p class="mb-4 text-sm text-gray-600">
            {{ __('twofa.setup_step1') }}<br>
            {{ __('twofa.setup_step2') }}<br>
            {{ __('twofa.setup_step3') }}
        </p>

        <div class="p-4 mb-4 text-center bg-gray-100 rounded-2xl">
            <code style="font-size:16px;font-weight:700;letter-spacing:2px;color:#0B3D2E;">{{ chunk_split($secret, 4, ' ') }}</code>
        </div>

        <p class="mb-4 text-xs text-gray-400">
            {{ __('twofa.account_label') }} {{ auth('student')->user()->email ?: auth('student')->user()->phone }} · {{ __('twofa.issuer_label') }}
        </p>

        <form action="{{ route('student.2fa.enable') }}" method="POST">
            @csrf
            <label class="block mb-1 text-sm font-semibold text-gray-700">{{ __('twofa.setup_step4') }}</label>
            <input type="text" name="code" required maxlength="6" pattern="\d{6}" placeholder="123456"
                class="w-full px-4 py-3 mb-3 tracking-widest text-center border rounded-2xl focus:ring-2" style="font-size:20px;">
            <button type="submit" class="w-full py-3 font-semibold text-white rounded-2xl" style="background:#0B6D20;">
                {{ __('twofa.confirm_enable') }}
            </button>
        </form>

        <p class="mt-4 text-xs text-gray-400">
            {{ __('twofa.no_qr_explanation') }}
        </p>
    </div>

</div>
@endsection
