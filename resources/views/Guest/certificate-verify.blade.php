@extends('Guest.cover')
@section('content')

<style>
/* Aligned to the shared brand palette from Guest/assets/style.css
   (--green / --green-dark / --gold / --cream) instead of a second,
   slightly-different set of hex values this page used to define on
   its own (--kiu-green, --kiu-gold-1, etc.) — same visual result,
   one source of truth. */
.verify-hero{ background: var(--grad-green); padding:clamp(32px,6vw,40px) 20px 60px; text-align:center; }
.verify-hero h1{ color:#fff; font-weight:800; font-size:clamp(20px,4vw,28px); }
.verify-body{ background:var(--grad-gold); padding:0 0 60px; }
.verify-card{
  max-width:600px; margin:-30px auto 0; background:var(--cream);
  border-radius:24px; padding:clamp(24px,5vw,34px); box-shadow:var(--shadow-lift);
  text-align:center; animation:fadeUp .5s var(--ease-spring) both;
}
.verify-form{ display:flex; flex-wrap:wrap; gap:10px; justify-content:center; }
.verify-form input{
  flex:1; min-width:220px; padding:12px 18px; border-radius:999px;
  border:1px solid rgba(11,61,46,.15); margin-bottom:0; font-size:14px;
  outline:none; transition:border-color .2s, box-shadow .2s;
}
.verify-form input:focus-visible{ border-color:var(--green); box-shadow:0 0 0 3px rgba(11,109,32,.15); }
.verify-form button{
  background:var(--green-dark); color:#fff; padding:12px 24px; border-radius:14px;
  border:none; font-weight:700; cursor:pointer; transition:transform .2s var(--ease-spring), background-color .2s;
}
.verify-form button:hover{ background:var(--green); transform:translateY(-1px); }
.verify-badge{ display:inline-block; padding:6px 18px; border-radius:999px; font-weight:800; font-size:13px; margin-bottom:16px; }
.verify-badge.valid{ background:var(--green); color:#fff; }
.verify-badge.invalid{ background:#e11d48; color:#fff; }
.verify-row{ display:flex; flex-wrap:wrap; gap:4px 12px; justify-content:space-between; padding:10px 0; border-bottom:1px solid rgba(11,61,46,.1); font-size:14px; text-align:left; }
.verify-row span.label{ color:#777; }
.verify-row span.value{ font-weight:700; color:var(--green-dark); }

@media (max-width:480px){
  .verify-row{ flex-direction:column; gap:2px; text-align:center; }
}
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
      <p style="color:var(--green-dark);">Iyi kode ntabwo ihuye n'icyemezo cyabonetse mu bubiko bwacu.</p>
    @endif

  </div>
</div>

@endsection
