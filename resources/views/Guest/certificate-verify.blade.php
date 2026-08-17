@extends('Guest.cover')
@section('content')

<style>
:root{ --kiu-green:#058e48; --kiu-green-deep:#094939; --kiu-gold-1:#c8a36c; --kiu-gold-2:#e2b45f; --kiu-cream:#f5ebe2; }
.verify-hero{ background: linear-gradient(135deg, var(--kiu-green), var(--kiu-green-deep)); padding:40px 20px 60px; text-align:center; }
.verify-hero h1{ color:#fff; font-weight:800; font-size:clamp(20px,4vw,28px); }
.verify-body{ background:linear-gradient(180deg, var(--kiu-gold-1), var(--kiu-gold-2)); padding:0 0 60px; }
.verify-card{ max-width:600px; margin:-30px auto 0; background:var(--kiu-cream); border-radius:24px; padding:34px; box-shadow:0 16px 34px rgba(9,73,57,.25); text-align:center; }
.verify-form input{ width:100%; padding:12px 18px; border-radius:999px; border:none; margin-bottom:14px; font-size:14px; }
.verify-form button{ background:var(--kiu-green-deep); color:#fff; padding:12px 24px; border-radius:14px; border:none; font-weight:700; cursor:pointer; }
.verify-badge{ display:inline-block; padding:6px 18px; border-radius:999px; font-weight:800; font-size:13px; margin-bottom:16px; }
.verify-badge.valid{ background:#058e48; color:#fff; }
.verify-badge.invalid{ background:#e11d48; color:#fff; }
.verify-row{ display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid rgba(9,73,57,.1); font-size:14px; }
.verify-row span.label{ color:#666; }
.verify-row span.value{ font-weight:700; color:#094939; }
</style>

<div class="verify-hero">
  <h1>Kwemeza Icyemezo (Certificate Verification)</h1>
</div>

<div class="verify-body">
  <div class="verify-card">

    @if(!$code)
      <form method="GET" class="verify-form">
        <input type="text" name="code" placeholder="Andika Certificate ID cyangwa verification code">
        <button type="submit">Kwemeza</button>
      </form>
    @elseif($certificate)
      <span class="verify-badge valid"><i class="fa-solid fa-circle-check"></i> ICYEMEZO NYAKURI</span>

      <div class="verify-row"><span class="label">Izina</span><span class="value">{{ $certificate->user->firstname }} {{ $certificate->user->lastname }}</span></div>
      <div class="verify-row"><span class="label">Porogaramu</span><span class="value">{{ $certificate->course->title ?? $certificate->title }}</span></div>
      <div class="verify-row"><span class="label">Certificate ID</span><span class="value">{{ $certificate->certificate_number }}</span></div>
      <div class="verify-row"><span class="label">Itariki</span><span class="value">{{ $certificate->issued_at->format('F j, Y') }}</span></div>
    @else
      <span class="verify-badge invalid"><i class="fa-solid fa-circle-xmark"></i> NTA CYEMEZO CYABONETSE</span>
      <p style="color:#094939;">Iyi kode ntabwo ihuye n'icyemezo cyabonetse mu bubiko bwacu.</p>
    @endif

  </div>
</div>

@endsection
