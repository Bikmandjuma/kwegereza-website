@extends('Guest.cover')
@section('content')

<style>
.std-auth-wrap{
  background: linear-gradient(180deg, #FDFAF3 0%, #fff 60%);
  padding: 50px 16px 70px;
}
.std-auth-card{
  max-width: 420px;
  margin: 0 auto;
  background: #fff;
  border-radius: 22px;
  padding: 34px 30px;
  box-shadow: 0 16px 34px rgba(9,73,57,0.18);
  border-top: 5px solid #C9A227;
}
.std-auth-card h2{
  text-align: center;
  color: #0B3D2E;
  font-family: 'Playfair Display', serif;
  margin-bottom: 6px;
}
.std-auth-card p.sub{ text-align:center; color:#777; font-size:13px; margin-bottom:24px; }
.std-auth-card label{ display:block; margin-bottom:6px; font-size:13px; font-weight:600; color:#333; }
.std-auth-card input{
  width:100%; padding:11px 14px; border:1px solid #ddd; border-radius:12px; margin-bottom:14px; outline:none; font-size:14px;
}
.std-auth-card input:focus{ border-color:#0B6D20; }
.std-auth-card button{
  width:100%; padding:12px; background:#0B3D2E; color:#fff; border:none; border-radius:12px;
  font-weight:700; cursor:pointer; margin-top:6px;
}
.std-auth-card .switch{ text-align:center; margin-top:16px; font-size:13px; color:#666; }
.std-auth-card .switch a{ color:#0B6D20; font-weight:700; text-decoration:none; }
.std-auth-error{ background:#fee; color:#b30000; padding:10px 14px; border-radius:10px; margin-bottom:16px; font-size:13px; }
.std-auth-info{ background:#e7f6ec; color:#0b6d20; padding:10px 14px; border-radius:10px; margin-bottom:16px; font-size:13px; }
</style>

<div class="std-auth-wrap">
  <div class="std-auth-card">
    <h2>Injira mu Konti</h2>
    <p class="sub">Komeza kwiga aho wagarukiye.</p>

    @if(session('error'))
      <div class="std-auth-error">{{ session('error') }}</div>
    @endif
    @if(session('info'))
      <div class="std-auth-info">{{ session('info') }}</div>
    @endif

    <form action="{{ route('student.login.submit') }}" method="POST">
      @csrf

      <label>Email cyangwa Telefone</label>
      <input type="text" name="username" value="{{ old('username') }}" required>

      <label>Ijambo ry'ibanga</label>
      <input type="password" name="password" required>

      <button type="submit">Injira</button>
    </form>

    <div class="switch">
      Nta konti ufite? <a href="{{ route('student.register') }}">Iyandikishe hano</a>
    </div>
  </div>
</div>

@endsection
