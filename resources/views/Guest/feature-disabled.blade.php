@extends('Guest.cover')
@section('content')

<div style="min-height:50vh;display:flex;align-items:center;justify-content:center;padding:clamp(24px,6vw,40px) 20px;">
    <div style="max-width:420px;text-align:center;background:var(--cream);border-radius:24px;padding:clamp(28px,5vw,40px);box-shadow:var(--shadow-lift);animation:fadeUp .5s var(--ease-spring) both;">
        <i class="fa-solid fa-circle-info" style="font-size:40px;color:var(--gold);margin-bottom:16px;"></i>
        <p style="color:var(--green-dark);font-weight:700;font-size:15px;">{{ $message }}</p>
    </div>
</div>

@endsection
