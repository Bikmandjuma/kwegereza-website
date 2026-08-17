<!DOCTYPE html>
<html lang="{{ $currentLocale ?? 'rw' }}" dir="{{ ($isRtl ?? false) ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#058e48">
<link rel="manifest" href="/manifest.json">
<link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="K.I.U">

@php
    $seoTitle = trim($__env->yieldContent('meta_title')) ?: "Kwegereza Islam Umuryango – E-Learning";
    $seoDescription = trim($__env->yieldContent('meta_description')) ?: "Kwegereza Islam Umuryango — urubuga rwigisha ubumenyi bwa Islamu bushingiye kuri Qur'an na Sunnah: amasomo, ibitabo, inyandiko n'amatangazo.";
    $seoImage = trim($__env->yieldContent('meta_image')) ?: asset('Guest/images/logo.png');
    $seoCanonical = trim($__env->yieldContent('meta_canonical')) ?: url()->current();
@endphp

<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<link rel="canonical" href="{{ $seoCanonical }}">

<meta property="og:type" content="website">
<meta property="og:site_name" content="Kwegereza Islam Umuryango">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:url" content="{{ $seoCanonical }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "EducationalOrganization",
    "name": "Kwegereza Islam Umuryango",
    "url": "{{ url('/') }}",
    "logo": "{{ asset('Guest/images/logo.png') }}",
    "sameAs": [
        "https://t.me/s/Kwegereza",
        "https://web.facebook.com/Kwegereza",
        "https://www.youtube.com/@Kwegereza"
    ]
}
</script>
@yield('structured_data')

<!-- <meta http-equiv="refresh" content="3"> -->
<link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Playfair+Display:wght@600;800&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="{{ URL::to('/') }}/Guest/assets/style.css" />
<style>
  .modal{
  display:none;
  position:fixed;
  inset:0;
  background:rgba(0,0,0,.6);
  z-index:9999;
  align-items:center;
  justify-content:center;
}

.modal-content{
  background:#fff;
  width:90%;
  max-width:420px;
  padding:25px;
  border-radius:14px;
  position:relative;
}

.closeBtn{
  position:absolute;
  right:15px;
  top:10px;
  font-size:28px;
  cursor:pointer;
}

.join-btn{
  margin-top:15px;
  width:100%;
  padding:12px;
  border:none;
  border-radius:10px;
  background:#0b6d20;
  color:#fff;
  cursor:pointer;
}

.mobile-menu {
  display: none;
}

.mobile-menu.active {
  display: block;
}

#mobileMenu a{
  margin-top: 15px;
}


.logo-box img:hover{
  cursor: pointer;
}

.right {
    display: flex;
    align-items: center;
    gap: 10px;
}

.group {
    position: relative;
}

.group .absolute {
    display: none;
}

.group:hover .absolute {
    display: block;
}

.absolute {
    position: absolute;
    top: 100%;
    right: 0;
    min-width: 190px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 8px 20px rgba(0,0,0,.15);
    overflow: hidden;
    z-index: 1000;
}

.topbar{
  position: relative;
  overflow: visible !important;
  z-index: 9999;
}

.absolute a {
    display: block;
    padding: 12px 16px;
    color: #333;
    text-decoration: none;
    transition: .2s;
}

.absolute a:hover {
    background: #f5f5f5;
}

</style>
</head>
<body>

<!-- TOP BAR -->
<div class="topbar">
  <div class="left">
    <!-- <span><i class="fa fa-phone"></i> (+250) 723061482</span>
    <span><i class="fa fa-envelope"></i>  umuryangok@gmail.com</span> -->
    <span>
        <i class="fa fa-phone"></i>
        <a href="tel:+250723061482">(+250) 723061482</a>
    </span>

    <span>
        <i class="fa fa-envelope"></i>
        <a href="mailto:umuryangok@gmail.com">umuryangok@gmail.com</a>
    </span>
    <!-- <span><i class="fa fa-globe"></i> Rwandcba</span> -->
  </div>
  <!-- <div class="right">
    
    <a href="{{ route('student.login') }}" 
       class="btn-account hidden md:inline-flex items-center gap-2">
       <i class="fas fa-user"></i> Account
    </a>

    <a target="parent" href="{{ route('guest.twandikire') }}" 
       class="btn-account hidden md:inline-flex items-center gap-2">
       <i class="fas fa-phone"></i> Twandikire
    </a>
  </div> -->
  <div class="right flex items-center gap-2" style="z-index: 1000;">

      <!-- Account Dropdown -->
      <div class="relative group hidden md:block">
          <button class="btn-account inline-flex items-center gap-2">
              <i class="fas fa-user"></i>
              Account
              <i class="fas fa-chevron-down text-xs"></i>
          </button>

          <!-- Dropdown Menu -->
          <div class="absolute right-0 w-48 bg-white rounded-lg shadow-lg border opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50" style="margin-top:2px;">

              <a href="{{ route('owner.login') }}"
                 class="block px-4 py-3 text-gray-700 hover:bg-gray-100">
                  <i class="fas fa-user-tie mr-2"></i> Leaders
              </a>

              <a href="{{ route('student.login') }}"
                 class="block px-4 py-3 text-gray-700 hover:bg-gray-100">
                  <i class="fas fa-user-graduate mr-2"></i> Students
              </a>

          </div>
      </div>

      <!-- Contact -->
      <a target="parent"
         href="{{ route('guest.twandikire') }}"
         class="btn-account hidden md:inline-flex items-center gap-2">
          <i class="fas fa-phone"></i> Twandikire
      </a>

  </div>
</div>

<!-- NAVBAR -->
<div class="navbar">
  <div class="nav-inner">
    <div class="logo-box">
      <img src="{{ URL::to('/') }}/Guest/images/logo.png" onclick="window.location.href='{{ route("guest.home") }}#ahabanza'" alt="K.I.U Logo"
           onerror="this.style.background='#0B3D2E';this.src='';this.alt='KIU'">
      <div class="logo-text">
        <h2>K.I.U</h2>
        <p>Kwegereza Islam Umuryango</p>
      </div>
    </div>

    <nav class="nav-links">
      <a href="{{ route('guest.home') }}#ahabanza" class="{{Request::segment(1) == '' ? 'active' : ''}}"><i class="fas fa-home"></i> {{ __('nav.home') }}</a>
      <a href="{{ route('guest.home') }}#abo-turi-bo"><i class="fas fa-info-circle"></i> {{ __('nav.about') }}</a>
      <!-- <a href="{{ route('guest.home') }}#abo-turi-bo"><i class="fas fa-info-circle"></i>Ibibazo</a> -->
      <a href="{{ route('guest.teachers') }}" class="{{Request::segment(1) == 'abasheikh' ? 'active' : ''}}">
        <i class="fas fa-users"></i> {{ __('nav.teachers') }}
      </a>

      <a href="{{ route('guest.inyandiko_zabamenyi') }}" class="{{Request::segment(1) == 'inyandiko-zabamenyi' ? 'active' : ''}}">
        <i class="fas fa-pen"></i> Inyandiko
      </a>

      <a href="{{ route('guest.news') }}" class="{{Request::segment(1) == 'amatangazo' ? 'active' : ''}}">
        <i class="fas fa-bullhorn"></i> Amatangazo
      </a>

      <a href="{{ route('guest.books') }}" class="{{Request::segment(1) == 'ibitabo' ? 'active' : ''}}">
        <i class="fas fa-book"></i> Ibitabo
      </a>

    </nav>

    <div class="nav-right flex">
  
      <!-- Chat Notification -->
      <span id="chatBtn" class="chat-icon">
         <i class="fa fa-microphone"></i>
      </span>

      <span id="searchBtn" onclick="window.location.href='{{ route("guest.search") }}'" class="flex" style="font-weight: bold; margin-right: 10px; color:var(--green); cursor:pointer;opacity: 0.7;">
        <p>{{ __('nav.search') }}</p>&nbsp;<i class="fa fa-search"></i>
      </span>

      <select onchange="window.location.href='?lang='+this.value" style="border:none;background:transparent;font-weight:700;color:var(--green);cursor:pointer;margin-right:10px;">
        <option value="rw" {{ ($currentLocale ?? 'rw') === 'rw' ? 'selected' : '' }}>RW</option>
        <option value="en" {{ ($currentLocale ?? 'rw') === 'en' ? 'selected' : '' }}>EN</option>
        <option value="fr" {{ ($currentLocale ?? 'rw') === 'fr' ? 'selected' : '' }}>FR</option>
        <option value="ar" {{ ($currentLocale ?? 'rw') === 'ar' ? 'selected' : '' }}>AR</option>
      </select>

      <div class="hamburger" id="menuBtn">
        <i class="fas fa-bars"></i>
      </div>

    </div>
  </div>

  <div class="mobile-menu" id="mobileMenu">
    <a href="{{route('guest.home')}}#ahabanza" class="{{Request::segment(1) == '' ? 'active' : ''}}"><i class="fas fa-home"></i> {{ __('nav.home') }}</a>
    <a href="{{route('guest.home')}}#abo-turi-bo"><i class="fas fa-info-circle"></i> {{ __('nav.about') }}</a>
      <!-- <div onclick="window.location.href='{{ route("guest.teachers") }}'" > -->
        <a href="{{ route('guest.teachers') }}" class="{{Request::segment(1) == 'abasheikh' ? 'active' : ''}}"><i class="fas fa-users"></i> {{ __('nav.teachers') }}
        </a>
      <!-- </div> -->

      <a href="{{ route('guest.inyandiko_zabamenyi') }}" class="{{Request::segment(1) == 'inyandiko-zabamenyi' ? 'active' : ''}}"><i class="fas fa-pencil"></i> {{ __('nav.inyandiko') }}</a>
      <a href="{{ route('guest.news') }}" class="{{Request::segment(1) == 'amatangazo' ? 'active' : ''}}"><i class="fas fa-bullhorn"></i> {{ __('nav.amatangazo') }}</a>
      <a href="{{ route('guest.books') }}" class="{{Request::segment(1) == 'ibitabo' ? 'active' : ''}}"><i class="fas fa-book"></i> {{ __('nav.books') }}</a>
    <a href="{{ route('guest.twandikire') }}"><i class="fas fa-phone"></i> {{ __('nav.contact') }}</a>
    <!-- <a href="{{ route('student.login') }}" class="btn-donate hidden sm:hidden" style="max-width: 40%;"><i class="fas fa-user"></i>{{ __('nav.account') }}</a> -->
    <details class="sm:hidden" style="margin-top:5px;">
        <summary class="btn-donate flex items-center justify-between cursor-pointer"
                 style="max-width:40%;list-style:none;">
            <span>
                <i class="fas fa-user"></i>
                {{ __('nav.account') }}
            </span>
            <i class="fas fa-chevron-down"></i>
        </summary>

        <div class="mt-2 bg-white rounded-lg border shadow-md overflow-hidden"
             style="max-width:40%;">
            <a href="{{ route('owner.login') }}"
               class="block px-4 py-3 hover:bg-gray-100">
                <i class="fas fa-user-tie mr-2"></i>
                Leaders
            </a>

            <a href="{{ route('student.login') }}"
               class="block px-4 py-3 hover:bg-gray-100">
                <i class="fas fa-user-graduate mr-2"></i>
                Students
            </a>
        </div>
    </details>
  </div>
</div>

<!-- DAILY AYAH + HADITH -->
<!-- <div class="daily-strip container" style="padding-top:40px;">
  <div class="daily-card">
    <h4><i class="fas fa-users"></i>,<i class="fas fa-eye"></i> &nbsp;System status</h4>
    <div class="trans">System-users : 0</div>
    <div class="trans">Today's visit : 0</div>
    <div class="trans">All-visits : 0</div>    
  </div>

</div> -->

<!-- Modal -->
<div id="classModal" class="modal">
  <div class="modal-content">

    <span class="closeBtn" style="margin-top:-3px;color: red;">&times;</span>

    <h2 >📚 Mwinjire twige</h2>
    <hr>
    <p>
      Isomo turibwige uyumunsi ni Hadith , turi bugezweho na <b>Sheikh IRADUKUNDA ABOUBAKAR ABUU ABDILRAHMAN</b>
    </p>

    <button class="join-btn">Ni mukanya</button>

  </div>
</div>

  @yield('content')

<!-- FOOTER -->
@if(!isset($hideFooter) || !$hideFooter)
<div class="footer-gold"></div>
<footer>
  <div class="footer-inner">
    <div>
      <h4>Twandikire</h4>
      <!-- <p><i class="fa fa-phone"></i> (+250) 723061482</p>
      <p><i class="fa fa-envelope"></i> umuryangok@gmail.com</p> -->
      <p>
          <i class="fa fa-phone"></i>
          <a href="tel:+250723061482">(+250) 723061482</a>
      </p>

      <p>
          <i class="fa fa-envelope"></i>
          <a href="mailto:umuryangok@gmail.com">umuryangok@gmail.com</a>
      </p>
      <p><i class="fa fa-location-dot"></i> Rwanda</p>
    </div>
    <div>
      <h4>Aho tugana</h4>
      <a href="#ahabanza"><i class="fas fa-home"></i> {{ __('nav.home') }}</a>
      <!-- <a href="#abo-turi-bo"><i class="fas fa-info-circle"></i> {{ __('nav.about') }}</a> -->
      <a href="#amasomo"><i class="fas fa-graduation-cap"></i> Amasomo</a>
      <a href="#ibitabo"><i class="fas fa-book"></i> {{ __('nav.books') }}</a>
    </div>
    <div>
      <h4>Imbuga nkoranya mbaga</h4>
      <a href="https://t.me/s/Kwegereza" target="_blank"><i class="fab fa-telegram"></i> Telegram</a>
      <a href="https://web.facebook.com/Kwegereza/?_rdc=1&_rdr#" target="_blank"><i class="fab fa-facebook"></i> Facebook</a>
      <a href="https://chat.whatsapp.com/G87ZLng06dJJA4rqV7DQsS" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a>
      <a href="https://www.youtube.com/@Kwegereza" target="_blank"><i class="fab fa-youtube"></i> YouTube</a>
    </div>
  </div>
  <div class="footer-bottom">
    <p><strong>KWEGEREZA ISLAM UMURYANGO</strong> &nbsp;|&nbsp; تقريب السنة بين يدي الأمة</p>
    <p style="margin-top:6px;">&copy; 2026 K.I.U </p>
  </div>
</footer>
@endif

<script>
  document.addEventListener("DOMContentLoaded", function () {

      const chatBtn  = document.getElementById("chatBtn");
      const modal    = document.getElementById("classModal");
      const closeBtn = document.querySelector(".closeBtn");
      const menuBtn = document.getElementById("menuBtn");
      const mobileMenu = document.getElementById("mobileMenu");
      const icon = menuBtn.querySelector("i");


      // open modal
      if(chatBtn && modal){
          chatBtn.addEventListener("click", function () {
              modal.style.display = "flex";
          });
      }

      // close modal
      if(closeBtn){
          closeBtn.addEventListener("click", function () {
              modal.style.display = "none";
          });
      }

      // close modal when clicking outside
      window.addEventListener("click", function (e) {
          if (e.target === modal) {
              modal.style.display = "none";
          }
      });


    // ✅ TOGGLE MENU
    menuBtn.addEventListener("click", function (e) {
        e.stopPropagation(); // ⭐ VERY IMPORTANT

        if (mobileMenu.style.display === "block") {
            mobileMenu.style.display = "none";
        } else {
            mobileMenu.style.display = "block";
        }
    });

    // ✅ PREVENT CLOSING WHEN CLICKING INSIDE MENU
    mobileMenu.addEventListener("click", function (e) {
        e.stopPropagation();
    });

    // ✅ CLOSE WHEN CLICKING OUTSIDE
    document.addEventListener("click", function () {
        mobileMenu.style.display = "none";
    });

    // ✅ TOGGLE MENU
    menuBtn.addEventListener("click", function (e) {
        e.stopPropagation();

        mobileMenu.classList.toggle("active");

        // ✅ change icon
        if (mobileMenu.classList.contains("active")) {
            icon.classList.remove("fa-bars");
            icon.classList.add("fa-times"); // X
        } else {
            icon.classList.remove("fa-times");
            icon.classList.add("fa-bars");
        }
    });

    // ✅ prevent closing when clicking inside menu
    mobileMenu.addEventListener("click", function (e) {
        e.stopPropagation();
    });

    // ✅ close when clicking outside
    document.addEventListener("click", function () {
        mobileMenu.classList.remove("active");

        // reset icon
        icon.classList.remove("fa-times");
        icon.classList.add("fa-bars");
    });
      

  });
</script>
<!-- <script src="assets/script.js"></script> -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="{{ URL::to('/') }}/Guest/assets/script.js"></script>

@if(\App\Models\FeatureFlag::enabled('guest_chat'))
@include('Guest.partials.faq-chat-widget')
@endif

@auth('student')
<script>
function kiuToggleCommentLike(id, btn) {
  fetch(`/student/comments/${id}/like`, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
  })
    .then(r => r.json())
    .then(data => {
      const icon = btn.querySelector('i');
      const countEl = btn.querySelector('.like-count');
      btn.style.color = data.liked ? '#058e48' : '#888';
      icon.classList.toggle('fa-solid', data.liked);
      icon.classList.toggle('fa-regular', !data.liked);
      countEl.textContent = data.count;
    });
}

function kiuReportComment(id) {
  const reason = prompt("Kubera iki utanga raporo kuri iki gitekerezo? (si ngombwa)");
  if (reason === null) return;

  fetch(`/student/comments/${id}/report`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json',
    },
    body: JSON.stringify({ reason }),
  }).then(() => alert('Twakiriye raporo yawe. Murakoze.'));
}

function kiuUpdateQuizCountdowns() {
  document.querySelectorAll('.kiu-quiz-countdown').forEach(function(el) {
    const target = new Date(el.dataset.starts).getTime();
    const diff = target - Date.now();
    const textEl = el.querySelector('.kiu-quiz-countdown-text');
    if (diff <= 0) {
      location.reload();
      return;
    }
    const totalSeconds = Math.floor(diff / 1000);
    const h = Math.floor(totalSeconds / 3600);
    const m = Math.floor((totalSeconds % 3600) / 60);
    const s = totalSeconds % 60;
    const pad = n => String(n).padStart(2, '0');

    if (h > 0) {
      textEl.textContent = 'Kizatangira mu ' + h + ':' + pad(m) + ':' + pad(s);
    } else {
      textEl.textContent = 'Kizatangira mu ' + pad(m) + ':' + pad(s);
    }
  });
}
if (document.querySelector('.kiu-quiz-countdown')) {
  kiuUpdateQuizCountdowns();
  setInterval(kiuUpdateQuizCountdowns, 1000);
}

function kiuToggleFavorite(type, id, btn) {
  fetch(`/student/favorites/${type}/${id}`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json',
    },
  })
    .then(r => r.json())
    .then(data => {
      const icon = btn.querySelector('i');
      if (data.favorited) {
        btn.style.color = '#e11d48';
        icon.classList.remove('fa-regular');
        icon.classList.add('fa-solid');
      } else {
        btn.style.color = '#c7c7c7';
        icon.classList.remove('fa-solid');
        icon.classList.add('fa-regular');
      }
    });
}

function kiuShowBadgeToast(badges) {
    if (!badges || !badges.length) return;

    badges.forEach((badge, i) => {
        setTimeout(() => {
            const toast = document.createElement('div');
            toast.style.cssText = 'position:fixed;bottom:22px;left:50%;transform:translateX(-50%);' +
                'background:#094939;color:#fff;padding:14px 22px;border-radius:16px;box-shadow:0 10px 24px rgba(0,0,0,.3);' +
                'z-index:10000;font-weight:700;font-size:14px;display:flex;align-items:center;gap:10px;';
            toast.innerHTML = `<span style="font-size:22px;">${badge.icon}</span> Wabonye: ${badge.name}!`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        }, i * 600);
    });
}
</script>
@endauth

<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}
</script>


@include('partials.quiz-alert-popup')
@include('partials.push-notification-prompt')
</body>
</html>