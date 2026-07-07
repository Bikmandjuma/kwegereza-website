@extends('Guest.cover')
@section('content')
@php
use Illuminate\Support\Facades\Storage;
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
  box-sizing:border-box;
  margin:0;
  padding:0;
}

:root{
  --ink:#132018;
  --ink-soft:#4d5c53;
  --surface:#ffffff;
  --bg:#f6faf5;

  --green-950:#0a2a1f;
  --green-800:#0b3d2e;
  --green-600:#0B6D20;
  --green-500:#12902f;
  --green-100:#e7fff0;

  --gold-700:#8f6613;
  --gold-500:#c8992a;
  --gold-300:#e9c877;
  --gold-100:#fff4df;

  --rose-600:#b13c6b;
  --rose-100:#ffe9f1;
  --violet-600:#5146d8;
  --violet-100:#ecebff;

  --font-display:'Poppins', system-ui, sans-serif;
  --font-body:'Inter', system-ui, sans-serif;

  --radius-lg:20px;
  --radius-md:14px;
  --radius-sm:9px;
  --shadow-card:0 10px 28px rgba(11,61,46,.08);
  --shadow-card-hover:0 18px 38px rgba(11,61,46,.16);
}

body{
  font-family:var(--font-body);
  color:var(--ink);
  background:
    radial-gradient(1100px 500px at 12% -10%, rgba(11,109,32,.10), transparent 60%),
    radial-gradient(900px 460px at 90% 10%, rgba(200,153,42,.12), transparent 55%),
    linear-gradient(180deg, #eef6ee 0%, var(--bg) 40%, #f2f0e8 100%);
  min-height:100vh;
}

.container{ max-width:1180px; margin:auto; padding:0 16px; }
.page{ padding:28px 0 60px; }
h1,h2,h3,h4,.font-display{ font-family:var(--font-display); }

.geo-pattern{
  position:absolute;
  inset:0;
  opacity:.14;
  background-image:
    radial-gradient(circle at 10px 10px, rgba(255,255,255,.9) 1.5px, transparent 1.6px),
    linear-gradient(45deg, transparent 48%, rgba(255,255,255,.5) 49%, rgba(255,255,255,.5) 51%, transparent 52%),
    linear-gradient(-45deg, transparent 48%, rgba(255,255,255,.5) 49%, rgba(255,255,255,.5) 51%, transparent 52%);
  background-size:22px 22px, 44px 44px, 44px 44px;
  pointer-events:none;
}

/* ================= PROFILE CARD ================= */
.profile-card{
  position:relative;
  overflow:hidden;
  background:linear-gradient(135deg,var(--green-950),var(--green-600));
  color:#fff;
  border-radius:var(--radius-lg);
  padding:26px;
  display:flex;
  align-items:center;
  gap:18px;
  flex-wrap:wrap;
  box-shadow:0 16px 34px rgba(10,42,31,.28);
}

.avatar{
  position:relative;
  z-index:1;
  width:72px;
  height:72px;
  border-radius:50%;
  background:radial-gradient(circle at 30% 30%, var(--gold-300), var(--gold-700));
  border:3px solid rgba(255,255,255,.35);
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:28px;
  flex-shrink:0;
  box-shadow:0 6px 16px rgba(0,0,0,.25);
}

.profile-info{ position:relative; z-index:1; min-width:0; }

.profile-info h2{
  font-size:20px;
  font-weight:700;
  margin-bottom:6px;
  letter-spacing:.2px;
}

.teacher-types{
  display:flex;
  flex-wrap:wrap;
  align-items:center;
  gap:4px;
  font-size:13px;
  opacity:.9;
  margin-bottom:12px;
}
.teacher-types p{ margin:0; }
.teacher-types strong{ font-weight:600; }

.badges{ display:flex; gap:7px; flex-wrap:wrap; }

.badge{
  background:rgba(255,255,255,.14);
  border:1px solid rgba(255,255,255,.22);
  padding:6px 12px;
  border-radius:999px;
  font-size:11px;
  font-weight:600;
  letter-spacing:.3px;
}

/* ================= SEARCH ================= */
.search-wrap{ margin:20px 0 12px; position:relative; }
.search-wrap svg{ position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--green-600); }

#searchInput{
  width:100%;
  padding:13px 16px 13px 42px;
  border:1px solid rgba(11,61,46,.08);
  border-radius:999px;
  background:var(--surface);
  font-family:var(--font-body);
  font-size:14px;
  color:var(--ink);
  box-shadow:var(--shadow-card);
  outline:none;
  transition:box-shadow .2s;
}
#searchInput:focus{ box-shadow:0 0 0 4px rgba(11,109,32,.14); }

/* ================= FILTERS ================= */
.filters{ display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px; }

.filter-btn{
  border:none;
  padding:9px 16px;
  border-radius:999px;
  background:var(--surface);
  color:var(--ink-soft);
  cursor:pointer;
  font-family:var(--font-body);
  font-size:12.5px;
  font-weight:600;
  box-shadow:0 4px 12px rgba(0,0,0,.05);
  transition:.2s;
}
.filter-btn:hover{ transform:translateY(-2px); }
.filter-btn.active{
  background:linear-gradient(135deg,var(--green-800),var(--green-600));
  color:#fff;
  box-shadow:0 8px 18px rgba(11,109,32,.28);
}

/* ================= SECTION HEADER ================= */
.section-header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; }
.section-header h3{ font-size:16px; font-weight:700; }
.count{ font-size:13px; color:var(--ink-soft); font-weight:600; }

/* ================= LESSON GRID + CARD ================= */
.lesson-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
  gap:16px;
}

.lesson-card{
  background:var(--surface);
  border-radius:var(--radius-md);
  padding:0 0 14px;
  overflow:hidden;
  box-shadow:var(--shadow-card);
  cursor:pointer;
  transition:transform .22s ease, box-shadow .22s ease;
  border:1px solid rgba(11,61,46,.05);
}

.lesson-card:hover{ transform:translateY(-5px); box-shadow:var(--shadow-card-hover); }

.thumb{
  position:relative;
  height:150px;
  display:flex;
  align-items:center;
  justify-content:center;
  overflow:hidden;
}

.thumb.thumb-video{ background:linear-gradient(135deg,var(--green-100),#d6f5e2); }

.thumb.thumb-audio{
  background-color:var(--green-800);
  background-image:
    linear-gradient(135deg, rgba(10,42,31,.35), rgba(10,42,31,.65)),
    url('{{ asset("Guest/images/logo.png") }}');
  background-repeat:no-repeat, no-repeat;
  background-position:center, center;
  background-size:cover, 46%;
}

.thumb .play-icon{
  position:relative;
  z-index:2;
  width:50px;
  height:50px;
  border-radius:50%;
  background:rgba(255,255,255,.92);
  backdrop-filter:blur(2px);
  display:flex;
  align-items:center;
  justify-content:center;
  box-shadow:0 8px 20px rgba(0,0,0,.25);
  transition:transform .2s;
}
.lesson-card:hover .play-icon{ transform:scale(1.1); }

.thumb-video .play-icon svg{ color:var(--green-600); }
.thumb-audio .play-icon svg{ color:var(--gold-700); }

.eq-bars{
  position:absolute;
  z-index:2;
  bottom:10px;
  left:50%;
  transform:translateX(-50%);
  display:flex;
  gap:3px;
  align-items:flex-end;
  height:16px;
}
.eq-bars span{
  width:3px;
  background:#fff;
  opacity:.75;
  border-radius:2px;
  animation:eq 1.1s ease-in-out infinite;
}
.eq-bars span:nth-child(1){ height:6px; animation-delay:.1s; }
.eq-bars span:nth-child(2){ height:14px; animation-delay:.3s; }
.eq-bars span:nth-child(3){ height:9px; animation-delay:.5s; }
.eq-bars span:nth-child(4){ height:16px; animation-delay:.2s; }
.eq-bars span:nth-child(5){ height:7px; animation-delay:.4s; }
@keyframes eq{ 0%,100%{ transform:scaleY(.5); } 50%{ transform:scaleY(1); } }

.duration-pill{
  position:absolute;
  z-index:2;
  bottom:8px;
  right:8px;
  background:rgba(0,0,0,.72);
  color:#fff;
  font-size:10.5px;
  font-weight:600;
  padding:2px 7px;
  border-radius:5px;
  min-width:28px;
  text-align:center;
}

.media-badge{
  position:absolute;
  z-index:2;
  top:8px;
  left:8px;
  display:flex;
  align-items:center;
  gap:4px;
  background:rgba(255,255,255,.16);
  border:1px solid rgba(255,255,255,.3);
  color:#fff;
  font-size:10px;
  font-weight:700;
  letter-spacing:.3px;
  padding:3px 9px;
  border-radius:999px;
}

.card-body{ padding:14px 14px 0; }

.lesson-card h4{
  font-family:var(--font-body);
  font-size:14px;
  font-weight:700;
  color:var(--ink);
  margin-bottom:8px;
  line-height:1.35;
  display:-webkit-box;
  -webkit-line-clamp:2;
  -webkit-box-orient:vertical;
  overflow:hidden;
}

.lesson-meta{ display:flex; flex-wrap:wrap; align-items:center; gap:6px; }

.type-tag{
  font-size:10px;
  padding:3px 9px;
  border-radius:999px;
  font-weight:700;
  letter-spacing:.2px;
  text-transform:capitalize;
}

.tag-video{background:var(--green-100);color:var(--green-600);}
.tag-audio{background:var(--gold-100);color:var(--gold-700);}
.tag-fiqh{background:var(--rose-100);color:var(--rose-600);}
.tag-tawhid{background:var(--violet-100);color:var(--violet-600);}
.tag-dynamic{background:var(--green-100);color:var(--green-600);}

/* ================= NO RESULT / PAGER ================= */
.no-result{ display:none; text-align:center; padding:30px; color:var(--ink-soft); font-weight:600; }

.pager{ display:flex; justify-content:center; align-items:center; gap:10px; margin-top:22px; }
.pager button{
  background:var(--surface);
  color:var(--green-600);
  border:1px solid rgba(11,109,32,.2);
  border-radius:999px;
  width:34px;
  height:34px;
  cursor:pointer;
  font-weight:700;
  transition:.2s;
}
.pager button:hover{ background:var(--green-600); color:#fff; }
.pager span{ font-size:12.5px; font-weight:600; color:var(--ink-soft); }

/* ================= THEATER (PLAYER) VIEW ================= */
.theater{ display:none; }
.theater.active{ display:block; }
.lesson-grid-wrap.hidden{ display:none; }

.back-btn{
  display:inline-flex;
  align-items:center;
  gap:8px;
  background:var(--surface);
  border:1px solid rgba(11,61,46,.1);
  color:var(--green-600);
  font-weight:700;
  font-size:13px;
  padding:9px 16px;
  border-radius:999px;
  cursor:pointer;
  margin-bottom:16px;
  box-shadow:var(--shadow-card);
  transition:.2s;
}
.back-btn:hover{ transform:translateX(-3px); }

.theater-grid{ display:grid; grid-template-columns:2.1fr 1fr; gap:20px; align-items:start; }

.player-shell{
  background:var(--green-950);
  border-radius:var(--radius-lg);
  overflow:hidden;
  box-shadow:0 20px 40px rgba(10,42,31,.3);
  position:relative;
}
.player-shell:fullscreen{ border-radius:0; display:flex; align-items:center; justify-content:center; }
.player-shell:fullscreen .player-media{ height:100vh; aspect-ratio:auto; }
.player-shell:fullscreen .player-info{ display:none; }

.player-media{
  position:relative;
  width:100%;
  aspect-ratio:16/9;
  background:#000;
  display:flex;
  align-items:center;
  justify-content:center;
}
.player-media video{ width:100%; height:100%; display:block; background:#000; }
.player-media.is-audio{ background:linear-gradient(135deg,var(--green-950),var(--green-800)); }

.audio-visual{
  position:relative;
  width:100%;
  height:100%;
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  gap:16px;
  color:#fff;
  padding:20px 0;
}
.audio-visual .geo-pattern{ opacity:.08; }

.audio-orb{
  width:100px;
  height:100px;
  border-radius:50%;
  background-color:var(--surface);
  background-image:url('{{ asset("Guest/images/logo.png") }}');
  background-repeat:no-repeat;
  background-position:center;
  background-size:62%;
  display:flex;
  align-items:center;
  justify-content:center;
  box-shadow:0 0 0 10px rgba(255,255,255,.08), 0 10px 30px rgba(0,0,0,.35);
  z-index:1;
}

.a-title{ font-family:var(--font-display); font-weight:700; font-size:15px; z-index:1; opacity:.92; text-align:center; padding:0 20px; }

.player-error{
  display:none;
  background:rgba(220,53,69,.16);
  border:1px solid rgba(220,53,69,.4);
  color:#ffd7db;
  font-size:12px;
  padding:8px 14px;
  border-radius:10px;
  z-index:1;
  max-width:88%;
  text-align:center;
}

/* -------- custom audio controls (YouTube-style) -------- */
.audio-controls{
  width:92%;
  max-width:620px;
  z-index:1;
  color:#fff;
  margin-top:6px;
}

.progress-row{ display:flex; align-items:center; gap:10px; margin-bottom:12px; }
.progress-row .time{ font-size:11px; font-variant-numeric:tabular-nums; opacity:.85; min-width:34px; }
.progress-row .time.duration{ text-align:right; }

input[type=range]{
  -webkit-appearance:none;
  appearance:none;
  height:4px;
  border-radius:999px;
  background:rgba(255,255,255,.25);
  outline:none;
  cursor:pointer;
}
.seek-bar{ flex:1; }
.seek-bar:disabled{ opacity:.35; cursor:not-allowed; }
.seek-bar::-webkit-slider-thumb{
  -webkit-appearance:none;
  width:13px;height:13px;border-radius:50%;
  background:var(--gold-300);
  box-shadow:0 0 0 4px rgba(233,200,119,.25);
  transition:transform .15s;
}
.seek-bar:hover::-webkit-slider-thumb{ transform:scale(1.15); }
.seek-bar::-moz-range-thumb{ width:13px;height:13px;border-radius:50%;background:var(--gold-300);border:none; }

.buttons-row{ display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap; }
.controls-left, .controls-right{ display:flex; align-items:center; gap:6px; }

.ctrl-btn{
  background:rgba(255,255,255,.08);
  border:1px solid rgba(255,255,255,.14);
  color:#fff;
  width:36px;height:36px;
  border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  cursor:pointer;
  transition:.2s;
  position:relative;
  flex-shrink:0;
}
.ctrl-btn:hover{ background:rgba(255,255,255,.18); transform:translateY(-1px); }

.ctrl-btn.play-btn{
  width:46px;height:46px;
  background:linear-gradient(135deg,var(--gold-300),var(--gold-700));
  color:var(--green-950);
  border:none;
  box-shadow:0 6px 16px rgba(0,0,0,.3);
}
.ctrl-btn.play-btn:hover{ transform:translateY(-1px) scale(1.05); }

.skip-num{
  position:absolute;
  bottom:-3px; right:-3px;
  background:var(--green-950);
  border:1px solid rgba(255,255,255,.35);
  font-size:8px;
  font-weight:700;
  padding:0 3px;
  border-radius:5px;
  line-height:1.3;
}

.volume-bar{ width:80px; }
.volume-bar::-webkit-slider-thumb{ -webkit-appearance:none; width:11px;height:11px;border-radius:50%;background:#fff; }

.player-info{ padding:20px 22px 22px; background:var(--surface); }
.player-info h2{ font-size:19px; font-weight:700; margin-bottom:10px; color:var(--ink); }
.player-tags{ display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:14px; }

.player-teacher{
  display:flex;
  align-items:center;
  gap:12px;
  padding:14px 0;
  border-top:1px solid rgba(11,61,46,.08);
  border-bottom:1px solid rgba(11,61,46,.08);
}
.player-teacher .mini-avatar{
  width:40px; height:40px; border-radius:50%;
  background:radial-gradient(circle at 30% 30%, var(--gold-300), var(--gold-700));
  display:flex; align-items:center; justify-content:center; color:#fff; font-size:14px; flex-shrink:0;
}
.player-teacher .t-name{ font-size:13.5px; font-weight:700; color:var(--ink); }
.player-teacher .t-sub{ font-size:11.5px; color:var(--ink-soft); }
.player-desc{ margin-top:14px; font-size:13px; line-height:1.6; color:var(--ink-soft); }

/* -------- Playlist sidebar -------- */
.playlist-panel{
  background:var(--surface);
  border-radius:var(--radius-lg);
  padding:16px;
  box-shadow:var(--shadow-card);
  max-height:640px;
  overflow-y:auto;
}
.playlist-panel h3{ font-size:14.5px; font-weight:700; margin-bottom:3px; }
.playlist-panel .pl-sub{ font-size:11.5px; color:var(--ink-soft); margin-bottom:14px; }

.pl-item{
  display:flex; gap:10px; padding:9px; border-radius:var(--radius-sm);
  cursor:pointer; margin-bottom:6px; transition:background .15s; align-items:center;
}
.pl-item:hover{ background:var(--bg); }
.pl-item.active{ background:var(--green-100); }

.pl-thumb{
  width:64px; height:44px; border-radius:8px; flex-shrink:0;
  display:flex; align-items:center; justify-content:center; position:relative;
}
.pl-thumb.thumb-video{ background:var(--green-100); }
.pl-thumb.thumb-audio{
  background-color:var(--green-800);
  background-image:url('{{ asset("Guest/images/logo.png") }}');
  background-repeat:no-repeat;
  background-position:center;
  background-size:44%;
}
.pl-thumb svg{ width:16px; height:16px; }
.pl-thumb.thumb-video svg{ color:var(--green-600); }

.pl-info{ min-width:0; }
.pl-info .pl-title{
  font-size:12.5px; font-weight:600; color:var(--ink); line-height:1.3;
  display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
}
.pl-info .pl-meta{ font-size:10.5px; color:var(--ink-soft); margin-top:3px; }

/* ================= RESPONSIVE ================= */
@media(max-width:900px){
  .theater-grid{ grid-template-columns:1fr; }
  .playlist-panel{ max-height:none; }
}

@media(max-width:768px){

  .page{ padding:16px 12px 48px; }

  .profile-card{ flex-direction:column; text-align:center; padding:20px; }
  .avatar{ width:64px; height:64px; font-size:24px; }
  .profile-info h2{ font-size:17px; }
  .teacher-types{ justify-content:center; font-size:12px; }
  .badges{ justify-content:center; }

  .filters{ flex-wrap:nowrap; overflow-x:auto; padding-bottom:6px; }
  .filter-btn{ flex-shrink:0; }

  .lesson-grid{ grid-template-columns:1fr; gap:12px; }

  .lesson-card{ display:flex; flex-direction:row; align-items:stretch; padding:0; border-radius:14px; }
  .thumb{ width:128px; height:auto; flex-shrink:0; }
  .thumb.thumb-audio{ background-size:cover, 62%; }
  .card-body{ padding:10px 12px; }
  .lesson-card h4{ font-size:13px; }

  .player-info h2{ font-size:16px; }
  .audio-orb{ width:80px; height:80px; }
  .volume-bar{ width:50px; }
  .ctrl-btn{ width:32px; height:32px; }
  .ctrl-btn.play-btn{ width:42px; height:42px; }
  .buttons-row{ gap:8px; }
}

@media(max-width:420px){
  .thumb{ width:104px; }
  .volume-bar{ display:none; }
}
</style>

<div class="page container">

<div class="profile-card">
  <div class="geo-pattern"></div>
  <div class="avatar">
    <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor"><path d="M12.3 3a8.7 8.7 0 1 0 8.4 10.9 7 7 0 0 1-8.4-10.9Z" opacity=".95"/></svg>
  </div>

  <div class="profile-info">
    <h2 id="sheikhTitle">
        {{ $teacher->title }} {{ $teacher->firstname }} {{ $teacher->lastname }}
    </h2>

    <div class="teacher-types">
        <p><strong>Isomo rya</strong></p>
        @foreach($types as $type)
            <p>{{ ucfirst($type) }}@if(!$loop->last),@endif</p>
        @endforeach
    </div>

    <div class="badges">
      <div class="badge">{{ $teacher->darsat_count }} Inyigisho</div>
      <div class="badge">{{ $teacher->darsat_count }} Audio</div>
    </div>
  </div>
</div>

<div class="search-wrap">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
    <circle cx="11" cy="11" r="8"/>
    <path d="M21 21l-4.35-4.35"/>
  </svg>
  <input type="text" id="searchInput" placeholder="Shakisha isomo...">
</div>

<div class="filters">
    <button class="filter-btn active" data-type="all">Byose</button>
    @foreach($types as $type)
        <button class="filter-btn" data-type="{{ strtolower($type) }}">{{ ucfirst($type) }}</button>
    @endforeach
</div>

<div class="lesson-grid-wrap" id="gridWrap">

  <div class="section-header">
    <h3>Amasomo</h3>
    <span class="count" id="countLabel">{{ $teacher->darsat_count }} amasomo</span>
  </div>

  <div class="lesson-grid" id="lessonGrid">

    @foreach($darsat as $lesson)
      @php $typeSlug = strtolower(str_replace(' ', '-', $lesson->type)); @endphp
      <div class="lesson-card lesson-item"
           data-type="audio {{ $typeSlug }}"
           data-title="{{ $lesson->title }}"
           data-lesson-type="{{ $lesson->type }}"
           data-media-type="audio"
           data-src="{{ asset('storage/'.$lesson->audio) }}"
           data-desc="">

          <div class="thumb thumb-audio">
              <span class="media-badge">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 14a3 3 0 0 0 3-3V6a3 3 0 0 0-6 0v5a3 3 0 0 0 3 3Zm5-3a5 5 0 0 1-10 0H5a7 7 0 0 0 6 6.92V21h2v-3.08A7 7 0 0 0 19 11h-2Z"/></svg>
                Audio
              </span>
              <div class="play-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
              </div>
              <div class="eq-bars"><span></span><span></span><span></span><span></span><span></span></div>
              <span class="duration-pill">--:--</span>
          </div>

          <div class="card-body">
              <h4>{{ $lesson->title }}</h4>
              <div class="lesson-meta">
                  <span class="type-tag tag-dynamic">{{ $lesson->type }}</span>
              </div>
          </div>

      </div>
    @endforeach

  </div>

  <div class="pager">
    <button onclick="changePage(-1)">←</button>
    <span>Page <strong id="pageNum">1</strong></span>
    <button onclick="changePage(1)">→</button>
  </div>
  <div class="no-result" id="noResult">Nta somo ribonetse</div>

</div>

<!-- ================= THEATER (PLAYER) VIEW ================= -->
<div class="theater" id="theater">

  <button class="back-btn" onclick="closeTheater()">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg>
    Subira ku masomo
  </button>

  <div class="theater-grid">

    <div>
      <div class="player-shell">
        <div class="player-media" id="playerMedia">

          <video id="videoEl" style="display:none" playsinline></video>

          <div class="audio-visual" id="audioVisual" style="display:none">
            <div class="geo-pattern"></div>
            <div class="audio-orb" id="audioOrb"></div>
            <div class="a-title" id="audioTitle"></div>
            <div class="player-error" id="playerError"></div>

            <div class="audio-controls">
              <div class="progress-row">
                <span class="time current" id="curTime">0:00</span>
                <input type="range" id="seekBar" class="seek-bar" min="0" max="100" value="0" step="0.1">
                <span class="time duration" id="durTime">0:00</span>
              </div>

              <div class="buttons-row">
                <div class="controls-left">
                  <button id="prevBtn" class="ctrl-btn" title="Ibibanziriza">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M6 6h2v12H6V6zm3.5 6l8.5 6V6l-8.5 6z"/></svg>
                  </button>
                  <button id="rewindBtn" class="ctrl-btn" title="Subira inyuma">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 14 4 9l5-5"/><path d="M4 9h9a6 6 0 1 1-6 8.5"/></svg>
                    <span class="skip-num">10</span>
                  </button>
                  <button id="playPauseBtn" class="ctrl-btn play-btn" title="Tangira/Hagarika">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                  </button>
                  <button id="forwardBtn" class="ctrl-btn" title="Jya imbere">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 14l5-5-5-5"/><path d="M20 9h-9a6 6 0 1 0 6 8.5"/></svg>
                    <span class="skip-num">10</span>
                  </button>
                  <button id="nextBtn" class="ctrl-btn" title="Ibikurikira">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/></svg>
                  </button>
                </div>

                <div class="controls-right">
                  <button id="muteBtn" class="ctrl-btn" title="Ijwi">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M4 9v6h4l5 5V4L8 9H4z"/><path d="M16.5 12a4.5 4.5 0 0 0-2.5-4v8a4.5 4.5 0 0 0 2.5-4z"/></svg>
                  </button>
                  <input type="range" id="volumeBar" class="volume-bar" min="0" max="1" step="0.01" value="1">
                  <button id="fullscreenBtn" class="ctrl-btn" title="Efishi yose">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9V4h5M4 4l6 6M20 9V4h-5M20 4l-6 6M4 15v5h5M4 20l6-6M20 15v5h-5M20 20l-6-6"/></svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="player-info">
          <h2 id="playerTitle">Umutwe w'isomo</h2>
          <div class="player-tags" id="playerTags"></div>

          <div class="player-teacher">
            <div class="mini-avatar">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.3 3a8.7 8.7 0 1 0 8.4 10.9 7 7 0 0 1-8.4-10.9Z"/></svg>
            </div>
            <div>
              <div class="t-name">{{ $teacher->title }} {{ $teacher->firstname }} {{ $teacher->lastname }}</div>
              <div class="t-sub" id="playerTeacherSub">Inyigisho</div>
            </div>
          </div>

          <p class="player-desc" id="playerDesc"></p>
        </div>
      </div>
    </div>

    <div class="playlist-panel">
      <h3>Inyigisho z'Ubu Bwoko</h3>
      <div class="pl-sub" id="playlistCount">{{ $teacher->darsat_count }} amasomo</div>
      <div id="playlistItems"></div>
    </div>

  </div>
</div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

  const filters = document.querySelectorAll(".filter-btn");
  const items = [...document.querySelectorAll(".lesson-item")];
  const search = document.getElementById("searchInput");
  const noResult = document.getElementById("noResult");
  const countLabel = document.getElementById("countLabel");
  const sheikhTitle = document.getElementById("sheikhTitle");

  const gridWrap = document.getElementById("gridWrap");
  const theater = document.getElementById("theater");
  const playerMedia = document.getElementById("playerMedia");
  const playerTitle = document.getElementById("playerTitle");
  const playerTags = document.getElementById("playerTags");
  const playerDesc = document.getElementById("playerDesc");
  const playerTeacherSub = document.getElementById("playerTeacherSub");
  const playlistItems = document.getElementById("playlistItems");
  const playlistCount = document.getElementById("playlistCount");

  const videoEl = document.getElementById("videoEl");
  const audioVisual = document.getElementById("audioVisual");
  const audioTitleEl = document.getElementById("audioTitle");
  const playerError = document.getElementById("playerError");
  const playerShell = document.querySelector(".player-shell");

  const playPauseBtn = document.getElementById("playPauseBtn");
  const rewindBtn = document.getElementById("rewindBtn");
  const forwardBtn = document.getElementById("forwardBtn");
  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");
  const seekBar = document.getElementById("seekBar");
  const curTimeEl = document.getElementById("curTime");
  const durTimeEl = document.getElementById("durTime");
  const muteBtn = document.getElementById("muteBtn");
  const volumeBar = document.getElementById("volumeBar");
  const fullscreenBtn = document.getElementById("fullscreenBtn");

  let current = "all";
  let currentPage = 1;
  const perPage = 4;
  let currentIndex = -1;
  let currentAudio = null;

  const playIconSVG = '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>';
  const ICON_PLAY = '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>';
  const ICON_PAUSE = '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="5" width="4" height="14"/><rect x="14" y="5" width="4" height="14"/></svg>';
  const ICON_VOLUME = '<svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M4 9v6h4l5 5V4L8 9H4z"/><path d="M16.5 12a4.5 4.5 0 0 0-2.5-4v8a4.5 4.5 0 0 0 2.5-4z"/></svg>';
  const ICON_MUTE = '<svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M4 9v6h4l5 5V4L8 9H4z"/><path d="M19 8.5 15.5 12 19 15.5l-1 1L14.5 13 11 16.5l-1-1L13.5 12 10 8.5l1-1L14.5 11 18 7.5z"/></svg>';

  function formatTime(s){
    if(!isFinite(s) || s < 0) s = 0;
    const m = Math.floor(s / 60);
    let sec = Math.floor(s % 60);
    if (sec < 10) sec = '0' + sec;
    return m + ':' + sec;
  }

  function getFiltered(){
    const keyword = search.value.toLowerCase().trim();
    return items.filter(item => {
      const types = item.dataset.type.toLowerCase();
      const text = item.innerText.toLowerCase();
      const matchType = current === "all" || types.includes(current);
      const matchSearch = keyword === "" || text.includes(keyword);
      return matchType && matchSearch;
    });
  }

  function render(){
    const filtered = getFiltered();
    const start = (currentPage - 1) * perPage;
    const end = start + perPage;

    items.forEach(item => item.style.display = "none");
    filtered.slice(start, end).forEach(item => item.style.display = "");

    countLabel.innerText = filtered.length + " amasomo";
    noResult.style.display = filtered.length ? "none" : "block";
    document.getElementById("pageNum").innerText = currentPage;
  }

  filters.forEach(btn => {
    btn.addEventListener("click", function () {
      document.querySelector(".filter-btn.active")?.classList.remove("active");
      this.classList.add("active");
      current = this.dataset.type.toLowerCase();
      currentPage = 1;
      render();
    });
  });

  search.addEventListener("input", function(){
    currentPage = 1;
    render();
  });

  window.changePage = function(dir){
    const max = Math.ceil(getFiltered().length / perPage) || 1;
    currentPage = Math.min(Math.max(currentPage + dir, 1), max);
    render();
  };

  // ---------------- CUSTOM AUDIO ENGINE ----------------

  function updateSeekFill(){
    const max = parseFloat(seekBar.max) || 1;
    const val = parseFloat(seekBar.value) || 0;
    const pct = (val / max) * 100;
    seekBar.style.background = `linear-gradient(to right, var(--gold-300) ${pct}%, rgba(255,255,255,.25) ${pct}%)`;
  }

  function stopCurrentAudio(){
    if (currentAudio){
      currentAudio.pause();
      currentAudio.src = "";
      currentAudio = null;
    }
  }

  function showPlayerError(src){
    console.error("Audio failed to load:", src);
    playerError.textContent = "Isomo ntiribonetse !";
    playerError.style.display = "block";
  }
  function hidePlayerError(){
    playerError.style.display = "none";
  }

  function loadAudioForItem(item){
    stopCurrentAudio();
    hidePlayerError();

    currentAudio = new Audio();
    currentAudio.preload = "metadata";
    currentAudio.volume = parseFloat(volumeBar.value);

    seekBar.value = 0;
    seekBar.max = 0;
    seekBar.disabled = true;
    curTimeEl.textContent = "0:00";
    durTimeEl.textContent = "0:00";
    updateSeekFill();
    playPauseBtn.innerHTML = ICON_PLAY;

    const setDuration = () => {
      if (isFinite(currentAudio.duration) && currentAudio.duration > 0){
        seekBar.max = currentAudio.duration;
        seekBar.disabled = false;
        durTimeEl.textContent = formatTime(currentAudio.duration);
      }
    };
    currentAudio.addEventListener("loadedmetadata", setDuration);
    currentAudio.addEventListener("durationchange", setDuration);
    currentAudio.addEventListener("canplay", setDuration);

    currentAudio.addEventListener("timeupdate", () => {
      if (!seekBar.dataset.dragging){
        seekBar.value = currentAudio.currentTime;
        updateSeekFill();
      }
      curTimeEl.textContent = formatTime(currentAudio.currentTime);
    });

    currentAudio.addEventListener("play", () => { playPauseBtn.innerHTML = ICON_PAUSE; });
    currentAudio.addEventListener("pause", () => { playPauseBtn.innerHTML = ICON_PLAY; });
    currentAudio.addEventListener("ended", () => { playPauseBtn.innerHTML = ICON_PLAY; goNext(); });
    currentAudio.addEventListener("error", () => {
      durTimeEl.textContent = "--:--";
      showPlayerError(item.dataset.src);
    });

    // set src AFTER listeners are wired so a fast/cached load can't be missed
    currentAudio.src = item.dataset.src;
    currentAudio.load();
    currentAudio.play().catch(() => { playPauseBtn.innerHTML = ICON_PLAY; });
  }

  playPauseBtn.addEventListener("click", () => {
    if (!currentAudio) return;
    if (currentAudio.paused) currentAudio.play(); else currentAudio.pause();
  });

  rewindBtn.addEventListener("click", () => {
    if (!currentAudio) return;
    currentAudio.currentTime = Math.max(0, currentAudio.currentTime - 10);
  });
  forwardBtn.addEventListener("click", () => {
    if (!currentAudio) return;
    // IMPORTANT: duration can be NaN before metadata finishes loading, and
    // NaN is falsy in JS, so "duration || 0" silently collapsed to 0 and
    // this button used to reset playback to the start. Fall back to
    // Infinity instead so a forward-skip never gets clamped to zero.
    const dur = (isFinite(currentAudio.duration) && currentAudio.duration > 0) ? currentAudio.duration : Infinity;
    currentAudio.currentTime = Math.min(dur, currentAudio.currentTime + 10);
  });

  seekBar.addEventListener("input", () => {
    seekBar.dataset.dragging = "1";
    updateSeekFill();
    curTimeEl.textContent = formatTime(parseFloat(seekBar.value));
    // seek live while dragging/clicking, not only on release
    if (currentAudio && !seekBar.disabled) currentAudio.currentTime = parseFloat(seekBar.value);
  });
  seekBar.addEventListener("change", () => {
    if (currentAudio && !seekBar.disabled) currentAudio.currentTime = parseFloat(seekBar.value);
    delete seekBar.dataset.dragging;
  });

  muteBtn.addEventListener("click", () => {
    if (!currentAudio) return;
    currentAudio.muted = !currentAudio.muted;
    muteBtn.innerHTML = currentAudio.muted ? ICON_MUTE : ICON_VOLUME;
  });

  volumeBar.addEventListener("input", () => {
    const v = parseFloat(volumeBar.value);
    if (currentAudio){ currentAudio.volume = v; currentAudio.muted = false; }
    muteBtn.innerHTML = v === 0 ? ICON_MUTE : ICON_VOLUME;
  });

  fullscreenBtn.addEventListener("click", () => {
    if (!document.fullscreenElement){
      (playerShell.requestFullscreen || playerShell.webkitRequestFullscreen)?.call(playerShell);
    } else {
      (document.exitFullscreen || document.webkitExitFullscreen)?.call(document);
    }
  });

  function goNext(){
    if (currentIndex < 0 || !items.length) return;
    openTheater(items[(currentIndex + 1) % items.length]);
  }
  function goPrev(){
    if (currentIndex < 0 || !items.length) return;
    openTheater(items[(currentIndex - 1 + items.length) % items.length]);
  }
  nextBtn.addEventListener("click", goNext);
  prevBtn.addEventListener("click", goPrev);

  // ---------------- THEATER / PLAYER ----------------

  function tagsMarkupFor(item){
    const isVideo = item.dataset.mediaType === "video";
    const typeTag = isVideo
      ? '<span class="type-tag tag-video">Video</span>'
      : '<span class="type-tag tag-audio">Audio</span>';
    const topicTag = '<span class="type-tag tag-dynamic">' + item.dataset.lessonType + '</span>';
    return typeTag + topicTag;
  }

  function buildPlaylist(activeItem){
    playlistItems.innerHTML = "";
    playlistCount.innerText = items.length + " amasomo";

    items.forEach(item => {
      const isVideo = item.dataset.mediaType === "video";
      const durationPill = item.querySelector('.duration-pill');
      const durationTxt = durationPill ? durationPill.textContent : '';
      const row = document.createElement("div");
      row.className = "pl-item" + (item === activeItem ? " active" : "");
      row.innerHTML = `
        <div class="pl-thumb ${isVideo ? 'thumb-video' : 'thumb-audio'}">${isVideo ? playIconSVG : ''}</div>
        <div class="pl-info">
          <div class="pl-title">${item.dataset.title}</div>
          <div class="pl-meta">${item.dataset.lessonType}${durationTxt ? ' · ' + durationTxt : ''}</div>
        </div>
      `;
      row.addEventListener("click", () => openTheater(item));
      playlistItems.appendChild(row);
    });
  }

  function openTheater(item){
    currentIndex = items.indexOf(item);
    const isVideo = item.dataset.mediaType === "video";

    playerTitle.innerText = item.dataset.title;
    playerDesc.innerText = item.dataset.desc || "";
    playerTags.innerHTML = tagsMarkupFor(item);
    playerTeacherSub.innerText = "Inyigisho ya " + item.dataset.lessonType;

    if (isVideo){
      stopCurrentAudio();
      audioVisual.style.display = "none";
      videoEl.style.display = "block";
      playerMedia.className = "player-media";
      videoEl.src = item.dataset.src;
      videoEl.play();
    } else {
      videoEl.pause();
      videoEl.style.display = "none";
      videoEl.src = "";
      audioVisual.style.display = "flex";
      playerMedia.className = "player-media is-audio";
      audioTitleEl.innerText = item.dataset.title;
      loadAudioForItem(item);
    }

    buildPlaylist(item);

    gridWrap.classList.add("hidden");
    theater.classList.add("active");
    window.scrollTo({ top: 0, behavior: "smooth" });
  }

  window.closeTheater = function(){
    stopCurrentAudio();
    videoEl.pause();
    videoEl.src = "";
    theater.classList.remove("active");
    gridWrap.classList.remove("hidden");
  };

  items.forEach(item => {
    item.addEventListener("click", () => openTheater(item));
  });

  try {
    const savedName = localStorage.getItem("sheikh_name");
    if (savedName && sheikhTitle) sheikhTitle.innerText = savedName;
  } catch (e) { /* storage unavailable, ignore */ }

  render();
  updateSeekFill();

  // populate real audio durations onto each card's thumbnail badge
  document.querySelectorAll('.lesson-card').forEach(card => {
      const audioSrc = card.getAttribute('data-src');
      const durationEl = card.querySelector('.duration-pill');
      if (!audioSrc || !durationEl) return;

      const probe = new Audio();
      probe.preload = 'metadata';

      const setFromProbe = () => {
        const seconds = Math.floor(probe.duration);
        if (!isFinite(seconds) || seconds <= 0) return;
        const min = Math.floor(seconds / 60);
        let sec = seconds % 60;
        if (sec < 10) sec = '0' + sec;
        durationEl.textContent = `${min}:${sec}`;
      };

      probe.addEventListener('loadedmetadata', setFromProbe);
      probe.addEventListener('durationchange', setFromProbe);
      probe.addEventListener('error', () => { durationEl.textContent = '--:--'; });

      probe.src = audioSrc;
      probe.load();
  });
});
</script>

@endsection