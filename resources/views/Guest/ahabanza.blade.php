@extends('Guest.cover')

@section('meta_title', "Kwegereza Islam Umuryango – Amasomo, Ibitabo n'Inyandiko za Islamu")
@section('meta_description', "Iga ubumenyi bwa Islamu bushingiye kuri Qur'an na Sunnah: amasomo ya Darsat, ibitabo, inyandiko z'abamenyi, amatangazo n'ubufasha bw'Abayobozi b'Idini, byose ku rubuga rumwe.")

@section('content')
<style>
  .video-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
    gap:25px;
    margin-top:40px;
}

.video-card{
    background:#fff;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 12px 35px rgba(0,0,0,.08);
    transition:transform .35s var(--ease-spring,cubic-bezier(0.16,1,0.3,1)), box-shadow .35s var(--ease-spring,cubic-bezier(0.16,1,0.3,1));
}

.video-card:hover{
    transform:translateY(-6px);
    box-shadow:var(--shadow-lift, 0 20px 50px -12px rgba(11,61,46,0.28));
}

.video-card iframe{
    width:100%;
    aspect-ratio:16/9;
    border:0;
}

.video-body{
    padding:20px;
}

.video-body h4, .video-body h6{
    color:var(--green-dark, #14532d);
    margin-bottom:10px;
    font-size:20px;
}

.video-body p{
    color:#666;
    line-height:1.6;
}

/* Live stats card — real numbers from the controller below, just a
   friendlier layout: a 3-up row with a live pulse dot instead of a
   plain stacked list. */
.kiu-stats-card{
  background:#fff;
  border-radius:var(--radius,20px);
  box-shadow:var(--shadow,0 12px 40px rgba(11,61,46,0.13));
  border-top:6px solid var(--gold,#C9A227);
  padding:20px 22px;
  animation:fadeUp .8s 1s var(--ease-spring,cubic-bezier(0.16,1,0.3,1)) both;
}
.kiu-stats-title{ font-weight:800; color:var(--green-dark,#0B3D2E); font-size:13px; letter-spacing:.03em; margin-bottom:14px; display:flex; align-items:center; gap:6px; }
.kiu-stats-row{ display:grid; grid-template-columns:repeat(3,1fr); gap:12px; text-align:center; }
.kiu-stats-row .num{ font-family:'Playfair Display',serif; font-size:22px; font-weight:800; color:var(--green,#0B6D20); line-height:1.1; }
.kiu-stats-row .lbl{ font-size:10.5px; color:#888; text-transform:uppercase; letter-spacing:.03em; margin-top:2px; }
.kiu-live-dot{ display:inline-block; width:8px; height:8px; border-radius:50%; background:#22c55e; box-shadow:0 0 0 0 rgba(34,197,94,.6); animation:kiuLivePulse 1.8s infinite; }
@keyframes kiuLivePulse{
  0%{ box-shadow:0 0 0 0 rgba(34,197,94,.55); }
  70%{ box-shadow:0 0 0 8px rgba(34,197,94,0); }
  100%{ box-shadow:0 0 0 0 rgba(34,197,94,0); }
}
@media (max-width:480px){
  .kiu-stats-row{ gap:8px; }
  .kiu-stats-row .num{ font-size:18px; }
}
</style>
<script>
document.addEventListener("DOMContentLoaded", function () {

    loadVisits();     // first load
    startPing();      // keep alive

    // 👇 FAST UI UPDATE (ONLINE FEEL)
    setInterval(loadVisits, 1000); // 1 second UI refresh
});

// ----------------------
// LOAD STATS
// ----------------------
function loadVisits() {
    fetch("{{ route('guest.live.visits') }}", {
        cache: "no-store"
    })
    .then(res => res.json())
    .then(data => {

        document.getElementById("todayVisit").innerText = data.today;
        document.getElementById("totalVisit").innerText = data.total;
        document.getElementById("onlineUsers").innerText = data.online;

    })
    .catch(err => console.log(err));
}

// ----------------------
// PING SYSTEM (IMPORTANT)
// ----------------------
function startPing() {

    function ping() {
        fetch("/guest/ping", {
            method: "GET",
            credentials: "include",
            cache: "no-store"
        });
    }

    ping(); // immediate

    setInterval(ping, 30000); // 30 seconds (CORRECT)
}
</script>

<!-- HERO -->
<section class="hero" id="ahabanza">
  <div class="hero-content">
    <div class="hero-arabic">تقريب السنة بين يدي الأمة</div>
    <h1>KWEGEREZA ISLAM<br><span>UMURYANGO</span></h1>
    <p>Ikaze ku rubuga rwacu rwigisha ubumenyi bw'idini ya Islamu bushingiye kuri Qur'an na Sunnah, mu buryo bworoshye, bwiza kandi bunoze.</p>
    <div class="hero-btns">
      <a href="#amasomo" class="btn-primary"><i class="fas fa-graduation-cap"></i> Tangira Kwiga</a>
      <a href="{{ route('guest.twandikire') }}" class="btn-outline"><i class="fas fa-users"></i> Twiyungeho (Join us)</a>
    </div>
    <div class="System-status">
      <div class="kiu-stats-card">
        <div class="kiu-stats-title"><span class="kiu-live-dot"></span> Site Overview</div>
        <div class="kiu-stats-row">
          <div>
            <div class="num" id="onlineUsers">0</div>
            <div class="lbl">Online</div>
          </div>
          <div>
            <div class="num" id="todayVisit">{{ $todayVisit }}</div>
            <div class="lbl">Uyu munsi</div>
          </div>
          <div>
            <div class="num" id="totalVisit">{{ $totalVisit }}</div>
            <div class="lbl">Byose</div>
          </div>
        </div>
      </div>
    </div> 
  </div>
</section>


<!-- ABOUT -->
<section class="section" id="abo-turi-bo" style="padding-top:60px;">
  <div class="container max-w-4xl mx-auto  px-4">
    <div class="section-title text-center mb-8">
      <div class="tag">Abo-turibo</div>
      <!-- <h2 class="text-2xl font-bold">Menya Kwegereza Islam Umuryango</h2> -->
      <h2>Menya Kwegereza Islam Umuryango</h2>

      <p class="text-gray-600">Intego n'umurongo wacu</p>
    </div>

    <div class="grid md:grid-cols-[1.2fr_0.8fr] gap-9 items-center">
      <!-- LEFT CARD (always visible) -->
      <div class="bg-white p-9 rounded-[20px] shadow-md border-l-6 border-green-700">
        <h3 class="font-playfair text-xl md:text-2xl text-brown mb-3">Murakaza neza ku rubuga rwacu</h3>
        <p class="text-gray-600 mb-2 leading-relaxed">
          Turi urubuga rushingiye kuri <strong>Qur'an na Sunnah</strong> rugamije kwegeraza umuryango ubumenyi bwa Islamu. Twigisha amasomo meza y'izewe kandi yubaka mu buryo bworoshye kandi bwumvikana kuri bose.
        </p>
        <p class="text-gray-600 leading-relaxed">
          Duhereye ku bana, urubyiruko n'abakuru, kugira ngo bunguke ubumenyi ku idini, ku muco mwiza no ku buzima bufite intego nziza.
        </p>
      </div>

      <!-- RIGHT CARD (hidden on small devices) -->
      <!-- <div class="hidden md:flex justify-center">
        <div class="w-full max-w-[300px] h-[300px] rounded-[20px] bg-gradient-to-br from-green-800 to-green-600 
                    flex items-center justify-center border-4 border-yellow-400 shadow-md text-center">
          <div class="text-white/90">
            <div class="font-amiri text-4xl text-yellow-300 mb-2">K.I.U</div>
            <div class="text-sm tracking-widest uppercase">Kwegereza Islam Umuryango</div>
          </div>
        </div>
      </div> -->
      <div class="hidden md:flex justify-center">
        <div class="logo-card">
          
          <img src="{{ URL::to('/') }}/Guest/images/logo.png" alt="K.I.U Logo"
               onerror="this.style.display='none'">

          <!-- Optional fallback text -->
          <div class="logo-fallback">
<!--             <div class="font-amiri text-3xl text-yellow-300">K.I.U</div>
            <div class="text-xs tracking-widest uppercase">Kwegereza Islam Umuryango</div> -->
          </div>

        </div>
      </div>
    </div>
  </div>
</section>

<!-- FEATURES -->
<section class="section section-alt">
  <div class="container">
    <div class="section-title">
      <div class="tag">Ibyacu</div>
      <h2>Ibyo dukora</h2>
      <p>Serivisi n'ibikubiyemo kuri platform yacu</p>
    </div>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-top"><i class="fas fa-book-open"></i></div>
        <div class="feature-body">
          <h4>Amasomo y'ingenzi</h4>
          <p>Fiqh, Hadith, Tawhid</p>
        </div>
      </div>
      <div class="feature-card" onclick="window.location.href='{{ route("guest.teachers") }}'" role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}" style="cursor: pointer;">
        <div class="feature-top"><i class="fas fa-play-circle"></i></div>
        <div class="feature-body">
          <h4>Videos & Audio</h4>
          <p>z'amasoma</p>
        </div>
      </div>
      <div class="feature-card" onclick="window.location.href='{{ route("guest.books") }}'" role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}" style="cursor: pointer;">
        <div class="feature-top"><i class="fas fa-file-pdf"></i></div>
        <div class="feature-body">
          <h4>Ibitabo</h4>
          <p>ibitabo byo kwigiramo</p>
        </div>
      </div>
      <!-- <div class="feature-card">
        <div class="feature-top"><i class="fas fa-question-circle"></i></div>
        <div class="feature-body">
          <h4>Q &amp; A</h4>
          <p>Ibibazo n'ibisubizo</p>
        </div>
      </div> -->
      <div class="feature-card" onclick="if(window.openKwegerezaChat){openKwegerezaChat();}else{window.location.href='{{ route("guest.twandikire") }}';}" role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}" style="cursor: pointer;">
        <div class="feature-top"><i class="fas fa-users"></i></div>
        <div class="feature-body">
          <h4>Community</h4>
          <p>Injira mu muryango</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- SAMPLE LESSONS -->
<section class="section section-alt">
    <div class="container">
        <div class="section-title">
            <div class="tag">Ingero z'Amasomo</div>
            <h2>Reba zimwe mu nyigisho zacu</h2>
            <p>Urugero rw'amasomo ushobora gukurikira kuri Kwegereza Islam Umuryango.</p>
        </div>

        <div class="video-grid">

            <div class="video-card">
                <iframe
                    src="https://www.youtube.com/watch?v=R90sVYixtBI"
                    allowfullscreen>
                </iframe>

                <div class="video-body">
                    <h6><i class="fas fa-list-alt"></i>&nbsp;Imyitwarire y'umuyislamu mubintu byose</h6>
                    <p><i class="fas fa-user"></i>&nbsp;Sheikh Ndahayo Khalid Abuu Muadh</p>
                </div>
            </div>

            <div class="video-card">
                <iframe
                    src="https://www.youtube.com/watch?v=TnhyqsKQmR4"
                    allowfullscreen>
                </iframe>

                <div class="video-body">
                    <h6><i class="fas fa-list-alt"></i>&nbsp;Incamake mu myitwarire y'umuyislamu</h6>
                    <p><i class="fas fa-user"></i>&nbsp;Sheikh Iradukunda Aboubakr Abuu Abdilrahman</p>
                </div>
            </div>

            <div class="video-card">
                <iframe
                    src="https://www.youtube.com/watch?v=06ync-Qr0v0&t=516s"
                    allowfullscreen>
                </iframe>

                <div class="video-body">
                    <h6 title="Ibintu bine buri musilamu ategetswe kumenya"><i class="fas fa-list-alt"></i>&nbsp;Ibintu bine buri musilamu ategetswe kumenya</h6>
                    <p><i class="fas fa-user"></i>&nbsp;Sheikh Munyaneza Ismail Abuu Omar</p>
                </div>
            </div>

        </div>

        <div style="text-align:center;margin-top:40px;">
            <a href="{{ route('guest.teachers') }}" class="btn-primary">
                <i class="fas fa-play-circle"></i>
                Reba amasomo yose
            </a>
        </div>

    </div>
</section>

<!-- COURSES -->
<section class="section" id="amasomo">
  <div class="container">
    <div class="section-title">
      <div class="tag">Amasomo</div>
      <h2>Amasomo yacu</h2>
      <p>Dore zimwe mu nyigisho ushobora gusanga kuri website yacu</p>
    </div>
    <div class="courses-grid">
      <div class="course-card">
        <div class="icon"><i class="fas fa-quran"></i></div>
        <h3>Qur'an</h3>
        <p>Kwigisha gusoma no gusobanukirwa Qur'an mu buryo bworoheje kandi bwumvikana.</p>
      </div>
      <div class="course-card">
        <div class="icon"><i class="fas fa-scroll"></i></div>
        <h3>Hadith</h3>
        <p>Kumenya amagambo, inzira n'imyitwarire y'Intumwa y'Imana mu buzima bwa buri munsi.</p>
      </div>
      <div class="course-card">
        <div class="icon"><i class="fas fa-star-and-crescent"></i></div>
        <h3>Aqida / Tawuhid</h3>
        <p>Kwigisha ukwemera nyakuri n'imizi y'imyemerere ya Islamu.</p>
      </div>
      <div class="course-card">
        <div class="icon"><i class="fas fa-balance-scale"></i></div>
        <h3>Fiqh</h3>
        <p>Kwigisha amategeko y'ibanze y'idini mu masengesho no mu mibereho.</p>
      </div>
    </div>
  </div>
</section>

<!-- JOIN COMMUNITY BANNER -->
<section class="section" id="join">
  <div class="container">
    <div class="join-banner">
      <h2>Injira mu Muryango Wacu</h2>
      <p>Kwiyandikisha no kubona inyigisho nshya buri gihe hamwe n'ibihumbi by'abanyamuryango mu Rwanda no hanze</p>
      <div class="join-form">
        <!-- <input type="email" placeholder="Andika imeyili yawe..."> -->
        <button onclick="if(window.openKwegerezaChat){openKwegerezaChat();}else{window.location.href='{{ route('guest.twandikire') }}';}"><i class="fas fa-paper-plane"></i> Twandikire</button>
      </div>
    </div>
  </div>
</section>

<!-- CONTACT -->
<section class="section" id="twandikire">
  <div class="container">
    <div class="section-title">
      <div class="tag">Twandikire</div>
      <h2>Twandikire</h2>
      <p>Ifatanye natwe ubaza ibibazo, ndetse unatanga ibitekerezo.</p>
    </div>
    <div class="contact-box">
      <div class="email"><i class="fas fa-envelope"></i> umuryangok@gmail.com</div>
      <div class="phones">
        TEL: (+250) 723061482
      </div>
    </div>
  </div>
</section>
<script>
document.querySelectorAll("iframe").forEach(frame => {

    const url = frame.src;

    if (url.includes("youtube.com/watch")) {
        const params = new URL(url).searchParams;
        const id = params.get("v");

        if (id) {
            frame.src = `https://www.youtube.com/embed/${id}`;
        }
    }

    if (url.includes("youtu.be/")) {
        const id = url.split("youtu.be/")[1].split("?")[0];
        frame.src = `https://www.youtube.com/embed/${id}`;
    }

});
</script>
@endsection