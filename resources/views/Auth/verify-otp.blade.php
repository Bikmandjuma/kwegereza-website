@extends('Auth.cover')
@section('content')

@php
    $hideFooter = true;
@endphp

<style>
.otp-container {
    max-width: 400px;
    margin: auto;
    text-align: center;
}

.otp-boxes {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-top: 20px;
}

.otp-boxes input {
    width: 50px;
    height: 55px;
    text-align: center;
    font-size: 22px;
    border: 1px solid #ccc;
    border-radius: 8px;
    outline: none;
}

.otp-boxes input:focus {
    border: 2px solid #d4af37;
}
</style>
<br>
<div class="container otp-container">

    <h3>Enter verification code</h3>
    <p>Code sent to: <b>{{ decrypt($email) }}</b></p>

    <form id="otpForm" method="POST" action="{{ route('guest.submit.verify.otp') }}">
        @csrf

        <input type="hidden" name="email" value="{{ $email }}">
        <input type="hidden" name="code" id="codeInput">

        <div class="otp-boxes">
            @for($i = 0; $i < 6; $i++)
                <input type="text" maxlength="1" class="otp-input">
            @endfor
        </div>

        <button type="submit" style="display:none" id="submitBtn"></button>
    </form>

</div>
<script>
const inputs = document.querySelectorAll(".otp-input");

inputs.forEach((input, index) => {

    input.addEventListener("input", () => {
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
});

function allFilled() {
    return [...inputs].every(i => i.value !== "");
}

function getCode() {
    return [...inputs].map(i => i.value).join('');
}
</script>

@endsection