@extends('Guest.cover')
@section('content')

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
  /* ---- Design tokens ---- */
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
  --shadow-card-hover:0 16px 36px rgba(11,61,46,.14);
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

.container{
  max-width:1180px;
  margin:auto;
  padding:0 16px;
}

.page{
  padding:28px 0 60px;
}

h1,h2,h3,h4,.font-display{
  font-family:var(--font-display);
}

/* =====================================================
   ARABESQUE PATTERN (signature motif, reused twice)
   ===================================================== */
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

/* =====================================================
   PROFILE / SHEIKH CARD
   ===================================================== */
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

.profile-info{ position:relative; z-index:1; }

.profile-info h2{
  font-size:20px;
  font-weight:700;
  margin-bottom:6px;
  letter-spacing:.2px;
}

.profile-info p{
  font-size:13px;
  opacity:.85;
  margin-bottom:12px;
}

.badges{
  display:flex;
  gap:7px;
  flex-wrap:wrap;
}

.badge{
  background:rgba(255,255,255,.14);
  border:1px solid rgba(255,255,255,.22);
  padding:6px 12px;
  border-radius:999px;
  font-size:11px;
  font-weight:600;
  letter-spacing:.3px;
}

/* =====================================================
   SEARCH
   ===================================================== */
.search-wrap{
  margin:20px 0 12px;
  position:relative;
}

.search-wrap svg{
  position:absolute;
  left:14px;
  top:50%;
  transform:translateY(-50%);
  color:var(--green-600);
}

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

#searchInput:focus{
  box-shadow:0 0 0 4px rgba(11,109,32,.14);
}

/* =====================================================
   FILTERS
   ===================================================== */
.filters{
  display:flex;
  gap:8px;
  flex-wrap:wrap;
  margin-bottom:16px;
}

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

/* =====================================================
   SECTION HEADER
   ===================================================== */
.section-header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:14px;
}

.section-header h3{
  font-size:16px;
  font-weight:700;
}

.count{
  font-size:13px;
  color:var(--ink-soft);
  font-weight:600;
}

/* =====================================================
   LESSON GRID + CARD
   ===================================================== */
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
  transition:transform .2s, box-shadow .2s;
  border:1px solid rgba(11,61,46,.05);
}

.lesson-card:hover{
  transform:translateY(-4px);
  box-shadow:var(--shadow-card-hover);
}

.thumb{
  position:relative;
  height:140px;
  display:flex;
  align-items:center;
  justify-content:center;
  overflow:hidden;
}

.thumb.thumb-video{
  background:linear-gradient(135deg,var(--green-100),#d6f5e2);
}

.thumb.thumb-audio{
  background:linear-gradient(135deg,var(--gold-100),#ffe9c2);
}

.thumb .play-icon{
  width:48px;
  height:48px;
  border-radius:50%;
  background:rgba(255,255,255,.85);
  display:flex;
  align-items:center;
  justify-content:center;
  box-shadow:0 6px 16px rgba(0,0,0,.15);
  transition:transform .2s;
}

.lesson-card:hover .play-icon{ transform:scale(1.08); }

.thumb-video .play-icon svg{ color:var(--green-600); }
.thumb-audio .play-icon svg{ color:var(--gold-700); }

.eq-bars{
  position:absolute;
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
  background:var(--gold-700);
  opacity:.55;
  border-radius:2px;
  animation:eq 1.1s ease-in-out infinite;
}
.eq-bars span:nth-child(1){ height:6px; animation-delay:.1s; }
.eq-bars span:nth-child(2){ height:14px; animation-delay:.3s; }
.eq-bars span:nth-child(3){ height:9px; animation-delay:.5s; }
.eq-bars span:nth-child(4){ height:16px; animation-delay:.2s; }
.eq-bars span:nth-child(5){ height:7px; animation-delay:.4s; }

@keyframes eq{
  0%,100%{ transform:scaleY(.5); }
  50%{ transform:scaleY(1); }
}

.duration-pill{
  position:absolute;
  bottom:8px;
  right:8px;
  background:rgba(0,0,0,.7);
  color:#fff;
  font-size:10.5px;
  font-weight:600;
  padding:2px 7px;
  border-radius:5px;
}

.card-body{
  padding:14px 14px 0;
}

.lesson-card h4{
  font-family:var(--font-body);
  font-size:14px;
  font-weight:700;
  color:var(--ink);
  margin-bottom:8px;
  line-height:1.35;
}

.lesson-meta{
  display:flex;
  flex-wrap:wrap;
  align-items:center;
  gap:6px;
}

.type-tag{
  font-size:10px;
  padding:3px 9px;
  border-radius:999px;
  font-weight:700;
  letter-spacing:.2px;
}

.tag-video{background:var(--green-100);color:var(--green-600);}
.tag-audio{background:var(--gold-100);color:var(--gold-700);}
.tag-fiqh{background:var(--rose-100);color:var(--rose-600);}
.tag-tawhid{background:var(--violet-100);color:var(--violet-600);}

.meta-sep{ color:#c7d0cb; font-size:11px; }

.duration{ font-size:11px; color:var(--ink-soft); }

/* =====================================================
   NO RESULT / PAGER
   ===================================================== */
.no-result{
  display:none;
  text-align:center;
  padding:30px;
  color:var(--ink-soft);
  font-weight:600;
}

.pager{
  display:flex;
  justify-content:center;
  align-items:center;
  gap:10px;
  margin-top:22px;
}

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

/* =====================================================
   THEATER (PLAYER) VIEW
   ===================================================== */
.theater{
  display:none;
}
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

.theater-grid{
  display:grid;
  grid-template-columns:2.1fr 1fr;
  gap:20px;
  align-items:start;
}

.player-shell{
  background:var(--green-950);
  border-radius:var(--radius-lg);
  overflow:hidden;
  box-shadow:0 20px 40px rgba(10,42,31,.3);
  position:relative;
}

.player-media{
  position:relative;
  width:100%;
  aspect-ratio:16/9;
  background:#000;
  display:flex;
  align-items:center;
  justify-content:center;
}

.player-media video{
  width:100%;
  height:100%;
  display:block;
  background:#000;
}

.player-media.is-audio{
  background:linear-gradient(135deg,var(--green-950),var(--green-800));
}

.audio-visual{
  position:relative;
  width:100%;
  height:100%;
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  gap:18px;
  color:#fff;
}
.audio-visual .geo-pattern{ opacity:.08; }

.audio-orb{
  width:96px;
  height:96px;
  border-radius:50%;
  background:radial-gradient(circle at 30% 30%, var(--gold-300), var(--gold-700));
  display:flex;
  align-items:center;
  justify-content:center;
  box-shadow:0 0 0 10px rgba(255,255,255,.06);
  z-index:1;
}

.audio-visual audio{
  width:86%;
  max-width:520px;
  z-index:1;
  border-radius:999px;
}

.audio-visual .a-title{
  font-family:var(--font-display);
  font-weight:700;
  font-size:15px;
  z-index:1;
  opacity:.9;
}

.player-info{
  padding:20px 22px 22px;
  background:var(--surface);
}

.player-info h2{
  font-size:19px;
  font-weight:700;
  margin-bottom:10px;
  color:var(--ink);
}

.player-tags{
  display:flex;
  align-items:center;
  gap:8px;
  flex-wrap:wrap;
  margin-bottom:14px;
}

.player-teacher{
  display:flex;
  align-items:center;
  gap:12px;
  padding:14px 0;
  border-top:1px solid rgba(11,61,46,.08);
  border-bottom:1px solid rgba(11,61,46,.08);
}

.player-teacher .mini-avatar{
  width:40px;
  height:40px;
  border-radius:50%;
  background:radial-gradient(circle at 30% 30%, var(--gold-300), var(--gold-700));
  display:flex;
  align-items:center;
  justify-content:center;
  color:#fff;
  font-size:14px;
  flex-shrink:0;
}

.player-teacher .t-name{
  font-size:13.5px;
  font-weight:700;
  color:var(--ink);
}
.player-teacher .t-sub{
  font-size:11.5px;
  color:var(--ink-soft);
}

.player-desc{
  margin-top:14px;
  font-size:13px;
  line-height:1.6;
  color:var(--ink-soft);
}

/* -------- Playlist sidebar -------- */
.playlist-panel{
  background:var(--surface);
  border-radius:var(--radius-lg);
  padding:16px;
  box-shadow:var(--shadow-card);
  max-height:640px;
  overflow-y:auto;
}

.playlist-panel h3{
  font-size:14.5px;
  font-weight:700;
  margin-bottom:3px;
}

.playlist-panel .pl-sub{
  font-size:11.5px;
  color:var(--ink-soft);
  margin-bottom:14px;
}

.pl-item{
  display:flex;
  gap:10px;
  padding:9px;
  border-radius:var(--radius-sm);
  cursor:pointer;
  margin-bottom:6px;
  transition:background .15s;
  align-items:center;
}

.pl-item:hover{ background:var(--bg); }

.pl-item.active{
  background:var(--green-100);
}

.pl-thumb{
  width:64px;
  height:44px;
  border-radius:8px;
  flex-shrink:0;
  display:flex;
  align-items:center;
  justify-content:center;
  position:relative;
}
.pl-thumb.thumb-video{ background:var(--green-100); }
.pl-thumb.thumb-audio{ background:var(--gold-100); }
.pl-thumb svg{ width:16px; height:16px; }
.pl-thumb.thumb-video svg{ color:var(--green-600); }
.pl-thumb.thumb-audio svg{ color:var(--gold-700); }

.pl-info{ min-width:0; }

.pl-info .pl-title{
  font-size:12.5px;
  font-weight:600;
  color:var(--ink);
  line-height:1.3;
  display:-webkit-box;
  -webkit-line-clamp:2;
  -webkit-box-orient:vertical;
  overflow:hidden;
}

.pl-info .pl-meta{
  font-size:10.5px;
  color:var(--ink-soft);
  margin-top:3px;
}

/* =====================================================
   MOBILE
   ===================================================== */
@media(max-width:900px){
  .theater-grid{
    grid-template-columns:1fr;
  }
  .playlist-panel{ max-height:none; }
}

@media(max-width:768px){

  .page{ padding:16px 12px 48px; }

  .profile-card{
    flex-direction:column;
    text-align:center;
    padding:20px;
  }

  .avatar{ width:64px; height:64px; font-size:24px; }
  .profile-info h2{ font-size:17px; }
  .profile-info p{ font-size:12px; }
  .badges{ justify-content:center; }

  .filters{
    flex-wrap:nowrap;
    overflow-x:auto;
    padding-bottom:6px;
  }
  .filter-btn{ flex-shrink:0; }

  .lesson-grid{ grid-template-columns:1fr; gap:12px; }

  .lesson-card{
    display:flex;
    flex-direction:row;
    align-items:stretch;
    padding:0;
    border-radius:14px;
  }
  .thumb{
    width:120px;
    height:auto;
    flex-shrink:0;
  }
  .card-body{ padding:10px 12px; }
  .lesson-card h4{ font-size:13px; }

  .player-info h2{ font-size:16px; }
}
</style>

<div class="page container">

<div class="profile-card">
  <div class="geo-pattern"></div>
  <div class="avatar">
    <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor"><path d="M12.3 3a8.7 8.7 0 1 0 8.4 10.9 7 7 0 0 1-8.4-10.9Z" opacity=".95"/></svg>
  </div>

  <div class="profile-info">
    <h2 id="sheikhTitle">Sheikh Munyaneza Ismail Abuu Omar</h2>
    <p>Umwarimu wa Tawhid na Fiqh</p>

    <div class="badges">
      <div class="badge">5 Inyigisho</div>
      <div class="badge">3 Videos</div>
      <div class="badge">2 Audio</div>
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
  <button class="filter-btn" data-type="video">Videos</button>
  <button class="filter-btn" data-type="audio">Audio</button>
  <button class="filter-btn" data-type="fiqh">Fiqh</button>
  <button class="filter-btn" data-type="tawhid">Tawhid</button>
</div>

<div class="lesson-grid-wrap" id="gridWrap">

  <div class="section-header">
    <h3>Amasomo</h3>
    <span class="count" id="countLabel">5 amasomo</span>
  </div>

  <div class="lesson-grid" id="lessonGrid">

    <div class="lesson-card lesson-item"
         data-type="video tawhid"
         data-title="Tawhid y'ibanze"
         data-duration="42 min"
         data-media-type="video"
         data-src="{{ asset('uploads/audio/1783201677_IGITABO_CYA_TAWHID_02.mp3') }}"
         data-desc="Isomo risobanura ibanze bya Tawhid, icyo aricyo n'impamvu ari ryo shingiro ry'ukwemera muri Isilamu.">
      <div class="thumb thumb-video">
        <div class="play-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
        </div>
        <span class="duration-pill">42 min</span>
      </div>
      <div class="card-body">
        <h4>Tawhid y'ibanze</h4>
        <div class="lesson-meta">
          <span class="type-tag tag-video">Video</span>
          <span class="type-tag tag-tawhid">Tawhid</span>
        </div>
      </div>
    </div>

    <div class="lesson-card lesson-item"
         data-type="audio tawhid"
         data-title="Shirk n'uburyo bwo kuyirinda"
         data-duration="35 min"
         data-media-type="audio"
         data-src="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3"
         data-desc="Inyigisho igaragaza uburyo bwo kwirinda shirk mu buzima bwa buri munsi, hifashishijwe ingero zifatika.">
      <div class="thumb thumb-audio">
        <div class="play-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
        </div>
        <div class="eq-bars"><span></span><span></span><span></span><span></span><span></span></div>
        <span class="duration-pill">35 min</span>
      </div>
      <div class="card-body">
        <h4>Shirk n'uburyo bwo kuyirinda</h4>
        <div class="lesson-meta">
          <span class="type-tag tag-audio">Audio</span>
          <span class="type-tag tag-tawhid">Tawhid</span>
        </div>
      </div>
    </div>

    <div class="lesson-card lesson-item"
         data-type="video fiqh"
         data-title="Uburemere bw'Iswala"
         data-duration="28 min"
         data-media-type="video"
         data-src="https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4"
         data-desc="Isomo ryibanda ku buremere bw'Iswala mu buzima bw'Umuyisilamu n'ingaruka zo kuyitakaza.">
      <div class="thumb thumb-video">
        <div class="play-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
        </div>
        <span class="duration-pill">28 min</span>
      </div>
      <div class="card-body">
        <h4>Uburemere bw'Iswala</h4>
        <div class="lesson-meta">
          <span class="type-tag tag-video">Video</span>
          <span class="type-tag tag-fiqh">Fiqh</span>
        </div>
      </div>
    </div>

    <div class="lesson-card lesson-item"
         data-type="audio fiqh"
         data-title="Igisibo cya Ramadhan"
         data-duration="31 min"
         data-media-type="audio"
         data-src="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3"
         data-desc="Amabwiriza y'ibanze ku byerekeye igisibo cya Ramadhan, ibicyangiza n'ibihembo byacyo.">
      <div class="thumb thumb-audio">
        <div class="play-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
        </div>
        <div class="eq-bars"><span></span><span></span><span></span><span></span><span></span></div>
        <span class="duration-pill">31 min</span>
      </div>
      <div class="card-body">
        <h4>Igisibo cya Ramadhan</h4>
        <div class="lesson-meta">
          <span class="type-tag tag-audio">Audio</span>
          <span class="type-tag tag-fiqh">Fiqh</span>
        </div>
      </div>
    </div>

    <div class="lesson-card lesson-item"
         data-type="video tawhid"
         data-title="Uburemere bw'Aqida"
         data-duration="50 min"
         data-media-type="video"
         data-src="https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4"
         data-desc="Isesengura ry'uburemere bw'Aqida isukuye n'uko igira uruhare mu buzima bw'Umuyisilamu.">
      <div class="thumb thumb-video">
        <div class="play-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
        </div>
        <span class="duration-pill">50 min</span>
      </div>
      <div class="card-body">
        <h4>Uburemere bw'Aqida</h4>
        <div class="lesson-meta">
          <span class="type-tag tag-video">Video</span>
          <span class="type-tag tag-tawhid">Tawhid</span>
        </div>
      </div>
    </div>

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

    <!-- Player + info -->
    <div>
      <div class="player-shell">
        <div class="player-media" id="playerMedia"></div>
        <div class="player-info">
          <h2 id="playerTitle">Umutwe w'isomo</h2>
          <div class="player-tags" id="playerTags"></div>

          <div class="player-teacher">
            <div class="mini-avatar">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.3 3a8.7 8.7 0 1 0 8.4 10.9 7 7 0 0 1-8.4-10.9Z"/></svg>
            </div>
            <div>
              <div class="t-name">Sheikh Munyaneza Ismail Abuu Omar</div>
              <div class="t-sub">Umwarimu wa Tawhid na Fiqh</div>
            </div>
          </div>

          <p class="player-desc" id="playerDesc"></p>
        </div>
      </div>
    </div>

    <!-- Playlist sidebar -->
    <div class="playlist-panel">
      <h3>Inyigisho z'Ubu Bwoko</h3>
      <div class="pl-sub" id="playlistCount">5 amasomo</div>
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
  const playlistItems = document.getElementById("playlistItems");
  const playlistCount = document.getElementById("playlistCount");

  let current = "all";
  let currentPage = 1;
  const perPage = 4;

  const videoIconSVG = '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>';

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

  // ---------------- THEATER / PLAYER ----------------

  function tagsMarkupFor(item){
    const isVideo = item.dataset.mediaType === "video";
    const isTawhid = item.dataset.type.includes("tawhid");
    const typeTag = isVideo
      ? '<span class="type-tag tag-video">Video</span>'
      : '<span class="type-tag tag-audio">Audio</span>';
    const topicTag = isTawhid
      ? '<span class="type-tag tag-tawhid">Tawhid</span>'
      : '<span class="type-tag tag-fiqh">Fiqh</span>';
    return typeTag + topicTag + '<span class="duration">' + item.dataset.duration + '</span>';
  }

  function buildPlaylist(activeItem){
    playlistItems.innerHTML = "";
    playlistCount.innerText = items.length + " amasomo";

    items.forEach(item => {
      const isVideo = item.dataset.mediaType === "video";
      const row = document.createElement("div");
      row.className = "pl-item" + (item === activeItem ? " active" : "");
      row.innerHTML = `
        <div class="pl-thumb ${isVideo ? 'thumb-video' : 'thumb-audio'}">${videoIconSVG}</div>
        <div class="pl-info">
          <div class="pl-title">${item.dataset.title}</div>
          <div class="pl-meta">${isVideo ? 'Video' : 'Audio'} · ${item.dataset.duration}</div>
        </div>
      `;
      row.addEventListener("click", () => openTheater(item));
      playlistItems.appendChild(row);
    });
  }

  function openTheater(item){
    const isVideo = item.dataset.mediaType === "video";

    playerTitle.innerText = item.dataset.title;
    playerDesc.innerText = item.dataset.desc || "";
    playerTags.innerHTML = tagsMarkupFor(item);

    if (isVideo) {
      playerMedia.className = "player-media";
      playerMedia.innerHTML = `<video src="${item.dataset.src}" controls autoplay playsinline></video>`;
    } else {
      playerMedia.className = "player-media is-audio";
      playerMedia.innerHTML = `
        <div class="audio-visual">
          <div class="geo-pattern"></div>
          <div class="audio-orb">${videoIconSVG}</div>
          <div class="a-title">${item.dataset.title}</div>
          <audio src="${item.dataset.src}" controls autoplay></audio>
        </div>`;
    }

    buildPlaylist(item);

    gridWrap.classList.add("hidden");
    theater.classList.add("active");
    window.scrollTo({ top: 0, behavior: "smooth" });
  }

  window.closeTheater = function(){
    theater.classList.remove("active");
    gridWrap.classList.remove("hidden");
    playerMedia.innerHTML = "";
  };

  items.forEach(item => {
    item.addEventListener("click", () => openTheater(item));
  });

  // ---------------- SHEIKH NAME (persisted in-memory) ----------------
  // Note: localStorage isn't available in every embedding context;
  // fall back gracefully if it's blocked.
  try {
    const savedName = localStorage.getItem("sheikh_name");
    if (savedName && sheikhTitle) sheikhTitle.innerText = savedName;
  } catch (e) { /* storage unavailable, ignore */ }

  render();
});
</script>

@endsection