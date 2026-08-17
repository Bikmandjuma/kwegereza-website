@extends('Guest.cover')

@section('meta_title', "Ibikorwa – Kwegereza Islam Umuryango")
@section('meta_description', "Reba ibikorwa biteganijwe bya Kwegereza Islam Umuryango — amasomo, Ramadan, Eid n'ibindi bikorwa by'umuryango.")

@section('content')

<style>
/* Local --kiu-* palette removed — page now uses the shared
   brand variables (--green/--gold/--cream/etc.) from
   Guest/assets/style.css, so this page's greens/golds match
   the header, nav, and footer instead of a second, slightly
   different shade. */
.events-hero{ background: var(--grad-green); padding:clamp(28px,6vw,40px) 20px 60px; text-align:center; }
.events-hero h1{ color:#fff; font-weight:800; font-size:clamp(22px,4vw,30px); }
.events-body{ background:var(--grad-gold); padding:30px 16px 60px; }
.events-section{ max-width:900px; margin:0 auto 30px; }
.events-section h2{ color:var(--green-dark); font-weight:800; font-size:16px; margin-bottom:14px; }
.event-card{ display:flex; gap:16px; background:var(--cream); border-radius:18px; padding:16px; margin-bottom:14px; box-shadow:0 8px 20px rgba(9,73,57,.15); text-decoration:none; transition:transform .25s var(--ease-spring), box-shadow .25s var(--ease-spring); }
.event-card:hover{ transform:translateY(-3px); box-shadow:var(--shadow-lift); }
.event-date{ flex-shrink:0; width:64px; height:64px; background:var(--green-dark); color:#fff; border-radius:14px; display:flex; flex-direction:column; align-items:center; justify-content:center; }
.event-date .day{ font-size:22px; font-weight:800; line-height:1; }
.event-date .month{ font-size:10px; text-transform:uppercase; }
.event-info h3{ color:var(--green-dark); font-weight:800; font-size:15px; margin-bottom:4px; }
.event-info p{ color:#666; font-size:12.5px; }
.events-empty{ text-align:center; background:var(--cream); border-radius:20px; padding:30px; color:var(--green-dark); font-weight:600; }

@media (max-width:480px){
  .event-card{ gap:12px; padding:12px; }
  .event-date{ width:52px; height:52px; }
  .event-date .day{ font-size:18px; }
}
</style>

<div class="events-hero">
  <h1>Ibikorwa (Islamic Events & Calendar)</h1>
</div>

<div class="events-body">

  <div class="events-section">
    <h2>Ibiteganijwe (Upcoming)</h2>
    @forelse($upcoming as $event)
      <a href="{{ route('guest.event.show', $event->slug) }}" class="event-card">
        <div class="event-date">
          <span class="day">{{ $event->startsAtLocal()->format('d') }}</span>
          <span class="month">{{ $event->startsAtLocal()->format('M') }}</span>
        </div>
        <div class="event-info">
          <h3>{{ $event->title }}</h3>
          <p><i class="fa-solid fa-clock"></i> {{ $event->startsAtLocal()->format('g:i A') }} @if($event->location) · <i class="fa-solid fa-location-dot"></i> {{ $event->location }} @endif</p>
        </div>
      </a>
    @empty
      <div class="events-empty">Nta bikorwa biteganijwe ubu. Garuka vuba.</div>
    @endforelse
  </div>

  @if($past->count())
  <div class="events-section">
    <h2>Byabaye vuba (Recent)</h2>
    @foreach($past as $event)
      <a href="{{ route('guest.event.show', $event->slug) }}" class="event-card" style="opacity:.7">
        <div class="event-date" style="background:#999">
          <span class="day">{{ $event->startsAtLocal()->format('d') }}</span>
          <span class="month">{{ $event->startsAtLocal()->format('M') }}</span>
        </div>
        <div class="event-info">
          <h3>{{ $event->title }}</h3>
          <p>{{ $event->location }}</p>
        </div>
      </a>
    @endforeach
  </div>
  @endif

</div>

@endsection
