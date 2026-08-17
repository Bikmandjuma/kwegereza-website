@extends('Auth.cover')
@section('content')

@php
    $hideFooter = true;
@endphp

<style>
.otp-container {
    max-width: 400px;
    margin: 40px auto;
    text-align: center;
    background: white;
    padding: 36px;
    border-radius: var(--radius, 20px);
    box-shadow: var(--shadow, 0 12px 40px rgba(11,61,46,.13));
    border-top: 5px solid var(--gold, #C9A227);
}

.otp-container h3{
    font-family:'Playfair Display',serif;
    color:var(--green-dark,#0B3D2E);
    font-size: 22px;
    margin-bottom: 6px;
}

.otp-container p{
    color:#666;
    font-size: 14px;
    margin-bottom: 4px;
}

.otp-boxes {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-top: 24px;
}

.otp-boxes input {
    width: 100%;
    max-width: 50px;
    height: 55px;
    text-align: center;
    font-size: 22px;
    font-weight: 700;
    color: var(--green-dark, #0B3D2E);
    border: 1.5px solid #ddd;
    border-radius: 12px;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}

.otp-boxes input:focus {
    border-color: var(--gold, #C9A227);
    box-shadow: 0 0 0 3px rgba(201,162,39,.18);
}

.otp-boxes input.filled{
    border-color: var(--green, #0B6D20);
}

.otp-back{
    display:inline-block;
    margin-top:22px;
    font-size:13px;
    color:var(--green,#0B6D20);
    font-weight:700;
}

@media(max-width:420px){
    .otp-container{ margin:24px 12px; padding:26px 18px; }
    .otp-boxes{ gap:6px; }
    .otp-boxes input{ height:48px; font-size:18px; }
}
</style>

<div class="container otp-container">

    <h3>Emeza Kode Yoherejwe</h3>
    <p>Kode yoherejwe kuri: <b>{{ decrypt($email) }}</b></p>

    <form id="otpForm" method="POST" action="{{ route('guest.submit.verify.otp') }}">
        @csrf

        <input type="hidden" name="email" value="{{ $email }}">
        <input type="hidden" name="code" id="codeInput">

        <div class="otp-boxes">
            @for($i = 0; $i < 6; $i++)
                <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="otp-input" aria-label="Igice cya kode #{{ $i + 1 }}">
            @endfor
        </div>

        <button type="submit" style="display:none" id="submitBtn"></button>
    </form>

    <a href="{{ route('guest.forgot-password') }}" class="otp-back">&larr; Ongera wandike imeyili</a>

</div>
<script>
const inputs = document.querySelectorAll(".otp-input");

inputs.forEach((input, index) => {

    // Digit-only filtering — the field was type="text" with no actual
    // input restriction beyond maxlength, so letters/symbols could be
    // typed into a numeric verification code.
    input.addEventListener("keypress", (e) => {
        if (!/[0-9]/.test(e.key)) {
            e.preventDefault();
        }
    });

    input.addEventListener("input", () => {
        input.value = input.value.replace(/[^0-9]/g, "");
        input.classList.toggle("filled", input.value.length === 1);

        if (input.value.length === 1 && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }

        if (allFilled()) {
            document.getElementById("codeInput").value = getCode();
            document.getElementById("submitBtn").click();
        }
    });

    input.addEventListener("keydown", (e) => {
        if (e.key === "Backspace" && !input.value && index > 0) {
            inputs[index - 1].focus();
        }
    });

    // Paste the full code at once into the first box
    input.addEventListener("paste", (e) => {
        const pasted = (e.clipboardData || window.clipboardData).getData("text").replace(/[^0-9]/g, "");
        if (!pasted) return;
        e.preventDefault();
        [...pasted].slice(0, inputs.length).forEach((digit, i) => {
            inputs[i].value = digit;
            inputs[i].classList.add("filled");
        });
        const lastFilled = Math.min(pasted.length, inputs.length) - 1;
        if (lastFilled >= 0) inputs[lastFilled].focus();
        if (allFilled()) {
            document.getElementById("codeInput").value = getCode();
            document.getElementById("submitBtn").click();
        }
    });
});

if (inputs.length) {
    inputs[0].focus();
}

function allFilled() {
    return [...inputs].every(i => i.value !== "");
}

function getCode() {
    return [...inputs].map(i => i.value).join('');
}
</script>

@endsection
