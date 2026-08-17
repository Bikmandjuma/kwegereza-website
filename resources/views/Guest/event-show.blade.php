@extends('Guest.cover')

@section('meta_title', $event->title . ' – Ibikorwa – Kwegereza Islam Umuryango')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($event->description ?: $event->title), 155))
@section('meta_image', $event->imageUrl() ?: asset('Guest/images/logo.png'))

@section('content')

<style>
/* Local --kiu-* palette removed — page now uses the shared
   brand variables (--green/--gold/--cream/etc.) from
   Guest/assets/style.css, so this page's greens/golds match
   the header, nav, and footer instead of a second, slightly
   different shade. */
.event-show-hero{ background: var(--grad-green); padding:clamp(28px,6vw,40px) 20px 70px; text-align:center; }
.event-show-hero h1{ color:#fff; font-weight:800; font-size:clamp(20px,4vw,28px); margin-bottom:8px; }
.event-show-body{ background:var(--grad-gold); padding:0 0 60px; }
.event-show-card{ max-width:640px; margin:-30px auto 0; background:var(--cream); border-radius:24px; padding:clamp(20px,5vw,30px); box-shadow:var(--shadow-lift); animation:fadeUp .5s var(--ease-spring) both; }
.event-show-card img.cover{ width:100%; border-radius:16px; margin-bottom:20px; max-height:280px; object-fit:cover; }
.event-meta-row{ display:flex; align-items:center; gap:10px; padding:8px 0; font-size:14px; color:var(--green-dark); font-weight:600; }
.event-btn{ display:block; width:100%; text-align:center; padding:12px; border-radius:14px; font-weight:700; border:none; cursor:pointer; margin-top:20px; text-decoration:none; transition:transform .2s var(--ease-spring), filter .2s; }
.event-btn:hover{ transform:translateY(-1px); filter:brightness(1.08); }
.event-btn.register{ background:var(--green-dark); color:#fff; }
.event-btn.unregister{ background:#e11d48; color:#fff; }
.event-btn.full{ background:#999; color:#fff; cursor:default; }
.event-btn.full:hover{ transform:none; filter:none; }
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
