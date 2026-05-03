@extends('Auth.cover')
@section('content')

@php
    $hideFooter = true;
@endphp

<style>
    @media(max-width:780px){
        #login{
            margin-top: 10%;
        }    
    }
</style>

<section class="section" id="login">

    @if(session('success'))
        <script>
            toastr.success("{{ session('success') }}", "Success");
        </script>
    @endif

    @if(session('error'))
        <script>
            toastr.error("{{ session('error') }}", "Error");
        </script>
    @endif

    <div class="container" style="max-width:400px;">

        <div style="background:white;padding:36px;border-radius:var(--radius);box-shadow:var(--shadow);border-top:5px solid var(--gold);">

            <h2 style="text-align:center;font-family:'Playfair Display',serif;color:var(--green-dark);margin-bottom:20px;">
                Hindura umubare-banga
            </h2>

            <form action="{{ route('guest.submit.reset.password') }}" method="POST" onsubmit="showLoader()">
                @csrf

                <input type="hidden" name="email" value="{{ $email }}">

                <!-- NEW PASSWORD -->
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-weight:700;margin-bottom:6px;">
                        Umubare banga mushya
                    </label>

                    <div style="position:relative;">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Andika password nshya"
                            style="width:100%;padding:12px 45px 12px 16px;border-radius:999px;border:1px solid #ccc;outline:none;font-size:14px;"
                        >

                        <span onclick="togglePassword('password','eye1')"
                            style="position:absolute;right:16px;top:50%;transform:translateY(-50%);cursor:pointer;color:#777;">
                            <i id="eye1" class="fa fa-eye"></i>
                        </span>
                    </div>

                    @error('password')
                        <small style="color:red;">{{ $message }}</small>
                    @enderror
                </div>

                <!-- CONFIRM PASSWORD -->
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-weight:700;margin-bottom:6px;">
                        Emeza umubare-banga
                    </label>

                    <div style="position:relative;">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Ongera wandike umubare-banga"
                            style="width:100%;padding:12px 45px 12px 16px;border-radius:999px;border:1px solid #ccc;outline:none;font-size:14px;"
                        >

                        <span onclick="togglePassword('password_confirmation','eye2')"
                            style="position:absolute;right:16px;top:50%;transform:translateY(-50%);cursor:pointer;color:#777;">
                            <i id="eye2" class="fa fa-eye"></i>
                        </span>
                    </div>
                </div>

                <!-- BUTTON -->
                <button type="submit" id="btn"
                    style="width:100%;padding:14px 0;background:var(--gold);color:var(--green-dark);
                    border:none;border-radius:999px;font-weight:700;font-size:16px;cursor:pointer;">

                    <span id="btnText">Hindura password</span>
                    <span id="btnLoader" style="display:none;">
                        <i class="fa fa-spinner fa-spin"></i> Turimo kubika...
                    </span>

                </button>

            </form>

        </div>

    </div>

</section>

<script>
function togglePassword(id, eyeId) {
    const input = document.getElementById(id);
    const eye = document.getElementById(eyeId);

    if (input.type === "password") {
        input.type = "text";
        eye.classList.remove("fa-eye");
        eye.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        eye.classList.remove("fa-eye-slash");
        eye.classList.add("fa-eye");
    }
}

function showLoader() {
    document.getElementById("btnText").style.display = "none";
    document.getElementById("btnLoader").style.display = "inline";
    document.getElementById("btn").disabled = true;
}
</script>

@endsection