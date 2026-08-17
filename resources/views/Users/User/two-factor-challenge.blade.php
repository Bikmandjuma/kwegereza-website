@extends('Guest.cover')

@section('meta_title', "2FA Verification – Kwegereza Islam Umuryango")

@section('content')

<div style="min-height:60vh;display:flex;align-items:center;justify-content:center;padding:40px 16px;">
  <div style="max-width:380px;width:100%;background:#f5ebe2;border-radius:24px;padding:34px;box-shadow:0 16px 34px rgba(9,73,57,.25);text-align:center;">

    <i class="fa-solid fa-shield-halved" style="font-size:36px;color:#058e48;margin-bottom:14px;"></i>
    <h1 style="color:#094939;font-weight:800;font-size:18px;margin-bottom:6px;">{{ __('twofa.challenge_title') }}</h1>
    <p style="color:#666;font-size:13px;margin-bottom:20px;">{{ __('twofa.challenge_desc') }}</p>

    @if(session('error'))
      <div style="background:#fee2e2;color:#b91c1c;padding:10px;border-radius:12px;margin-bottom:14px;font-size:13px;font-weight:600;">{{ session('error') }}</div>
    @endif

    <form action="{{ route('student.2fa.challenge.submit') }}" method="POST">
      @csrf
      <input type="text" name="code" required maxlength="20" autofocus placeholder="123456"
        style="width:100%;padding:14px;border-radius:14px;border:1px solid #ddd;text-align:center;font-size:20px;letter-spacing:3px;margin-bottom:14px;">
      <button type="submit" style="width:100%;padding:13px;background:#058e48;color:#fff;border:none;border-radius:14px;font-weight:700;cursor:pointer;">
        {{ __('twofa.confirm') }}
      </button>
    </form>

    <p style="margin-top:16px;font-size:12px;color:#888;">
      {{ __('twofa.lost_phone') }}
    </p>
  </div>
</div>

@endsection
