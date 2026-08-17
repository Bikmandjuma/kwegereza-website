@extends('Guest.cover')

@section('meta_title', $event->title . ' – Ibikorwa – Kwegereza Islam Umuryango')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($event->description ?: $event->title), 155))
@section('meta_image', $event->imageUrl() ?: asset('Guest/images/logo.png'))

@section('content')

<style>
:root{ --kiu-green:#058e48; --kiu-green-deep:#094939; --kiu-gold-1:#c8a36c; --kiu-gold-2:#e2b45f; --kiu-cream:#f5ebe2; }
.event-show-hero{ background: linear-gradient(135deg, var(--kiu-green), var(--kiu-green-deep)); padding:40px 20px 70px; text-align:center; }
.event-show-hero h1{ color:#fff; font-weight:800; font-size:clamp(20px,4vw,28px); margin-bottom:8px; }
.event-show-body{ background:linear-gradient(180deg, var(--kiu-gold-1), var(--kiu-gold-2)); padding:0 0 60px; }
.event-show-card{ max-width:640px; margin:-30px auto 0; background:var(--kiu-cream); border-radius:24px; padding:30px; box-shadow:0 16px 34px rgba(9,73,57,.25); }
.event-show-card img.cover{ width:100%; border-radius:16px; margin-bottom:20px; max-height:280px; object-fit:cover; }
.event-meta-row{ display:flex; align-items:center; gap:10px; padding:8px 0; font-size:14px; color:#094939; font-weight:600; }
.event-btn{ display:block; width:100%; text-align:center; padding:12px; border-radius:14px; font-weight:700; border:none; cursor:pointer; margin-top:20px; text-decoration:none; }
.event-btn.register{ background:var(--kiu-green-deep); color:#fff; }
.event-btn.unregister{ background:#e11d48; color:#fff; }
.event-btn.full{ background:#999; color:#fff; }
</style>

<div class="event-show-hero">
  <h1>{{ $event->title }}</h1>
  @if($event->status === 'cancelled')
    <span style="background:#e11d48;color:#fff;padding:4px 14px;border-radius:999px;font-size:12px;font-weight:800;">CYAHAGARITSWE (CANCELLED)</span>
  @endif
</div>

<div class="event-show-body">
  <div class="event-show-card">

    @if($event->imageUrl())
      <img src="{{ $event->imageUrl() }}" class="cover" alt="{{ $event->title }}">
    @endif

    <div class="event-meta-row"><i class="fa-solid fa-clock"></i> {{ $event->startsAtLocal()->format('l, F j, Y — g:i A') }}</div>

    @if($event->location)
      <div class="event-meta-row"><i class="fa-solid fa-location-dot"></i> {{ $event->location }}</div>
    @endif

    @if($event->capacity)
      <div class="event-meta-row"><i class="fa-solid fa-users"></i> {{ $event->spotsLeft() }} / {{ $event->capacity }} spots remaining</div>
    @endif

    @if($event->description)
      <p style="margin-top:16px;color:#333;line-height:1.7;font-size:14px;">{{ $event->description }}</p>
    @endif

    @auth('student')
      @if($event->status === 'cancelled')
        <span class="event-btn full">Iki gikorwa cyahagaritswe</span>
      @elseif($event->isRegisteredBy(auth('student')->id()))
        <form action="{{ route('student.events.unregister', $event->slug) }}" method="POST">
          @csrf
          <button type="submit" class="event-btn unregister">Kureka Kwiyandikisha</button>
        </form>
      @elseif($event->isFull())
        <span class="event-btn full">Byuzuye</span>
      @else
        <form action="{{ route('student.events.register', $event->slug) }}" method="POST">
          @csrf
          <button type="submit" class="event-btn register">Iyandikishe Kuri Iki Gikorwa</button>
        </form>
      @endif
    @else
      <a href="{{ route('student.login') }}" class="event-btn register">Injira kugira ngo wiyandikishe</a>
    @endauth

  </div>
</div>

@endsection
