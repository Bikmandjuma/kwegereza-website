@extends('Guest.cover')
@section('content')

<div style="min-height:50vh;display:flex;align-items:center;justify-content:center;padding:40px 20px;">
    <div style="max-width:420px;text-align:center;background:#f5ebe2;border-radius:24px;padding:40px;box-shadow:0 16px 34px rgba(9,73,57,.2);">
        <i class="fa-solid fa-circle-info" style="font-size:40px;color:#e2b45f;margin-bottom:16px;"></i>
        <p style="color:#094939;font-weight:700;font-size:15px;">{{ $message }}</p>
    </div>
</div>

@endsection
