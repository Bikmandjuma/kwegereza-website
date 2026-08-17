@extends('Guest.cover')
@section('content')

@php
    $hideFooter = true;
@endphp

<style>
@media(max-width:780px){
    .container{ margin-top: 10%; }
}

.spinner {
    width: 18px;
    height: 18px;
    border: 3px solid #fff;
    border-top: 3px solid transparent;
    border-radius: 50%;
    display: inline-block;
    animation: spin 1s linear infinite;
    vertical-align: middle;
}

@keyframes spin {
    100% { transform: rotate(360deg); }
}

#email:focus{ border-color:var(--green,#0B6D20); box-shadow:0 0 0 3px rgba(11,109,32,.12); }
#submitBtn:hover{ filter:brightness(1.08); transform:translateY(-1px); }
#submitBtn:focus-visible{ outline:2px solid var(--green,#0B6D20); outline-offset:2px; }
</style>

<section class="section">

    <div class="container" style="max-width:400px;">

        <div style="background:white;padding:36px;border-radius:var(--radius,20px);box-shadow:var(--shadow,0 12px 40px rgba(11,61,46,.13));border-top:5px solid var(--gold,#C9A227);">

            <h2 style="text-align:center;font-family:'Playfair Display',serif;color:var(--green-dark,#0B3D2E);margin-bottom:20px;">Wibagiwe umubare-banga</h2>

            <form id="forgotForm" action="{{ route('guest.submit-forgot-password') }}" method="POST">
                @csrf

                <div style="margin-bottom:16px;">
                    <label for="email" style="display:block;font-weight:700;margin-bottom:6px;">Imeyili</label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        placeholder="Andika imeyili yawe"
                        style="width:100%;padding:12px 16px;border-radius:999px;border:1px solid #ccc;outline:none;font-size:14px;transition:border-color .2s, box-shadow .2s;"
                    >

                    @error('email')
                        <small style="color:#dc2626;display:block;margin-top:6px;">{{ $message }}</small>
                    @enderror
                </div>

                <button id="submitBtn"
                        type="submit"
                        style="width:100%;padding:14px;background:var(--gold,#C9A227);color:var(--green-dark,#0B3D2E);border:none;border-radius:999px;font-weight:700;font-size:16px;cursor:pointer;transition:transform .2s, filter .2s;">

                    <span id="btnText">Ohereza emeyili</span>
                    <span id="btnSpinner" style="display:none;" class="spinner"></span>
                </button>

                <div style="text-align:center;margin-top:18px;font-size:13px;">
                    <a href="{{ route('owner.login') }}" style="color:var(--green,#0B6D20);font-weight:700;">
                        &larr; Garuka ku kwinjira
                    </a>
                </div>

            </form>

        </div>

    </div>

</section>

{{-- SUCCESS HANDLER --}}
@if(session('success'))
<script>
document.addEventListener("DOMContentLoaded", function () {

    const email = "{{ session('email') }}";
    const message = "{{ session('success') }}";

    toastr.success(message);

    // save email
    if (email) {
        localStorage.setItem("reset_email", email);
    }

    // show loading redirect
    const loader = document.createElement("div");
    loader.innerHTML = `
        <div style="
            position:fixed;
            top:0;left:0;
            width:100%;height:100%;
            background:rgba(0,0,0,0.4);
            display:flex;
            justify-content:center;
            align-items:center;
            color:white;
            font-size:18px;
            z-index:9999;">
            Turakwohereza...
        </div>
    `;
    document.body.appendChild(loader);

    setTimeout(() => {
        window.location.href = "{{ route('guest.verify.otp',['email' => $email]) }}";
    }, 2000);

});
</script>
@endif

{{-- SUBMIT LOADER --}}
<script>
document.getElementById("forgotForm").addEventListener("submit", function () {
    document.getElementById("btnText").style.display = "none";
    document.getElementById("btnSpinner").style.display = "inline-block";
});

document.addEventListener("DOMContentLoaded", function () {

    const email = "{{ $email ?? '' }}";

    if (email) {
        localStorage.setItem("reset_email", email);
    }

});
</script>

@endsection