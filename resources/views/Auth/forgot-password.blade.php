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
}

@keyframes spin {
    100% { transform: rotate(360deg); }
}
</style>

<section class="section">

    <div class="container" style="max-width:400px;">

        <div style="background:white;padding:36px;border-radius:12px;box-shadow:0 5px 20px rgba(0,0,0,.1);">

            <h2 style="text-align:center;">Wibagiwe umubare-banga</h2>

            <form id="forgotForm" action="{{ route('guest.submit-forgot-password') }}" method="POST">
                @csrf

                <div style="margin-bottom:16px;">
                    <label>Imeyili</label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        placeholder="Andika imeyili yawe"
                        style="width:100%;padding:12px;border-radius:999px;border:1px solid #ccc;"
                    >

                    @error('email')
                        <small style="color:red">{{ $message }}</small>
                    @enderror
                </div>

                <button id="submitBtn"
                        type="submit"
                        style="width:100%;padding:14px;background:#d4af37;color:#000;border:none;border-radius:999px;font-weight:bold;">

                    <span id="btnText">Ohereza emeyili</span>
                    <span id="btnSpinner" style="display:none;" class="spinner"></span>
                </button>

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
            Redirecting...
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