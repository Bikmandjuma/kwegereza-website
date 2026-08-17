@extends('Guest.cover')

@section('meta_title', "Ibikorwa – Kwegereza Islam Umuryango")
@section('meta_description', "Reba ibikorwa biteganijwe bya Kwegereza Islam Umuryango — amasomo, Ramadan, Eid n'ibindi bikorwa by'umuryango.")

@section('content')

<style>
:root{ --kiu-green:#058e48; --kiu-green-deep:#094939; --kiu-gold-1:#c8a36c; --kiu-gold-2:#e2b45f; --kiu-cream:#f5ebe2; }
.events-hero{ background: linear-gradient(135deg, var(--kiu-green), var(--kiu-green-deep)); padding:40px 20px 60px; text-align:center; }
.events-hero h1{ color:#fff; font-weight:800; font-size:clamp(22px,4vw,30px); }
.events-body{ background:linear-gradient(180deg, var(--kiu-gold-1), var(--kiu-gold-2)); padding:30px 16px 60px; }
.events-section{ max-width:900px; margin:0 auto 30px; }
.events-section h2{ color:#094939; font-weight:800; font-size:16px; margin-bottom:14px; }
.event-card{ display:flex; gap:16px; background:var(--kiu-cream); border-radius:18px; padding:16px; margin-bottom:14px; box-shadow:0 8px 20px rgba(9,73,57,.15); text-decoration:none; }
.event-date{ flex-shrink:0; width:64px; height:64px; background:var(--kiu-green-deep); color:#fff; border-radius:14px; display:flex; flex-direction:column; align-items:center; justify-content:center; }
.event-date .day{ font-size:22px; font-weight:800; line-height:1; }
.event-date .month{ font-size:10px; text-transform:uppercase; }
.event-info h3{ color:#094939; font-weight:800; font-size:15px; margin-bottom:4px; }
.event-info p{ color:#666; font-size:12.5px; }
.events-empty{ text-align:center; background:var(--kiu-cream); border-radius:20px; padding:30px; color:#094939; font-weight:600; }
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
