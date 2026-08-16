<!DOCTYPE html>
<html lang="rw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Kwegereza Islam Umuryango</title>
    <link rel="icon" href="{{ URL::to('/') }}/Guest/images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Tailwind CDN defaults to following the OS color scheme with no
        // way to override it — configuring darkMode:'class' here (must
        // run before the CDN script parses, so this tag stays right
        // after it, not before) makes `dark:` variants respond to a
        // toggle instead, matching the same pattern the admin panel uses.
        tailwind.config = { darkMode: 'class' };

        (function () {
            // Was falling back to the OS/browser's dark-mode preference
            // when no explicit choice had been made yet — meaning
            // anyone whose system is set to dark mode saw a black
            // student portal on their very first visit, despite never
            // touching the toggle themselves. Defaults to light now;
            // dark only happens if someone actually clicks the toggle.
            const stored = localStorage.getItem('kiu_student_theme');
            const theme = stored === 'dark' ? 'dark' : 'light';
            document.documentElement.classList.toggle('dark', theme === 'dark');
        })();
    </script>
    <style>
        :root{
            /* Matched to the main site's brand tokens (Guest/assets/style.css
               --green/--green-dark/--gold/--cream) — this student portal
               previously used a slightly different green (#058e48) than
               the marketing site (#0B6D20), so the whole platform looked
               like two different shades of "brand green" depending which
               part a student was on. Same values now, everywhere. */
            --kiu-green: #0B6D20;
            --kiu-green-deep: #0B3D2E;
            --kiu-gold: #C9A227;
            --kiu-cream: #FDFAF3;
        }
        body{ background:#f6f8f7; font-family: 'Nunito', sans-serif; }
        .dark body{ background:#0d1512; color:#e5e7eb; }
        .dark aside, .dark #kiuTopbar, .dark #mobileDrawer > div{ background:#111c17; border-color:rgba(255,255,255,.08); }
        .dark .sidebar-link{ color:#9ca3af; }
        .dark .sidebar-link:hover, .dark .sidebar-link.active{ background:rgba(201,162,39,.12); color:#e8c870; }
        .dark .sidebar-group summary{ color:#6b7280; }
        .sidebar-link{
            display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:12px;
            color:#4b5563; font-size:14px; font-weight:600; transition:background-color .2s, color .2s, transform .15s;
        }
        .sidebar-link:hover, .sidebar-link.active{ background: var(--kiu-cream); color: var(--kiu-green-deep); }
        .sidebar-link:hover{ transform:translateX(2px); }
        .sidebar-link:focus-visible{ outline:2px solid var(--kiu-gold); outline-offset:2px; }
        .sidebar-link.disabled{ color:#c3c3c3; cursor:not-allowed; }
        .sidebar-link.disabled:hover{ background:none; transform:none; }
        .soon-badge{ font-size:9px; background:#eee; color:#999; padding:2px 6px; border-radius:999px; margin-left:auto; }

        /* Sidebar dropdown groups — native <details>/<summary>, no JS
           needed for collapse/expand, and keyboard-accessible by
           default (Enter/Space toggles a focused <summary> natively). */
        .sidebar-group{ margin-bottom:2px; }
        .sidebar-group summary{
            display:flex; align-items:center; justify-content:space-between; cursor:pointer;
            padding:9px 14px; font-size:11px; font-weight:700; text-transform:uppercase;
            letter-spacing:.04em; color:#9ca3af; border-radius:10px; list-style:none;
        }
        .sidebar-group summary::-webkit-details-marker{ display:none; }
        .sidebar-group summary:hover{ color:var(--kiu-green-deep); background:#f6f8f7; }
        .sidebar-group summary:focus-visible{ outline:2px solid var(--kiu-gold); outline-offset:2px; }
        .sidebar-group summary .chevron{ transition:transform .2s; font-size:10px; }
        .sidebar-group[open] summary .chevron{ transform:rotate(180deg); }
        .sidebar-group .sidebar-link{ margin-left:10px; font-size:13.5px; }
    </style>
</head>
<body>

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="fixed inset-y-0 left-0 z-40 flex-col hidden w-64 bg-white border-r shadow-sm lg:flex">
        <div class="flex items-center gap-3 px-5 py-5 border-b">
            <img src="{{ URL::to('/') }}/Guest/images/logo.png" class="w-10 h-10" alt="Logo">
            <div>
                <p class="text-sm font-bold" style="color:var(--kiu-green-deep)">K.I.U</p>
                <p class="text-[11px] text-gray-400">Umunyeshuri</p>
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            @include('partials.student-sidebar-nav')
        </nav>
    </aside>

    <!-- TOPBAR — visible at every breakpoint now, not just mobile.
         Notification bell, theme toggle, and account menu moved here
         from the sidebar header, matching the same topbar pattern the
         admin panel uses. -->
    <div id="kiuTopbar" class="fixed top-0 right-0 left-0 lg:left-64 z-40 flex items-center justify-between gap-2 px-4 py-3 bg-white border-b shadow-sm">
        <div class="flex items-center gap-2 lg:hidden">
            <button onclick="document.getElementById('mobileDrawer').classList.toggle('hidden')" class="text-xl" style="color:var(--kiu-green-deep)">
                <i class="fa-solid fa-bars"></i>
            </button>
            <img src="{{ URL::to('/') }}/Guest/images/logo.png" class="w-8 h-8" alt="Logo">
        </div>
        <span class="hidden lg:block"></span>

        <div class="flex items-center gap-1">
            <!-- Theme toggle -->
            <button onclick="kiuToggleTheme()" aria-label="Hindura imiterere y'umucyo" class="flex items-center justify-center w-9 h-9 text-gray-500 rounded-xl hover:bg-gray-100">
                <i class="fa-solid fa-moon dark:hidden"></i>
                <i class="hidden fa-solid fa-sun dark:inline"></i>
            </button>

            <!-- Notifications -->
            <a href="{{ route('student.notifications') }}" class="relative flex items-center justify-center w-9 h-9 text-gray-500 rounded-xl hover:bg-gray-100" aria-label="Ubutumwa">
                <i class="fa-solid fa-bell"></i>
                <span id="kiuUnreadBadge" class="absolute hidden items-center justify-center w-4 h-4 text-[9px] font-bold text-white rounded-full top-1 right-1" style="background:#e11d48"></span>
            </a>

            <!-- Account menu: image + name -> Umwirondoro / Igenamiterere / Sohoka -->
            <div class="relative" id="kiuAccountMenuWrap">
                <button onclick="kiuToggleAccountMenu()" class="flex items-center gap-2 py-1 pl-1 pr-2 rounded-xl hover:bg-gray-100" id="kiuAccountMenuBtn">
                    @if(auth('student')->user()->image)
                        <img src="{{ \App\Support\FileUrl::resolve(auth('student')->user()->image, 'students', 'uploads/students') ?? asset('Guest/images/logo.png') }}" class="object-cover w-8 h-8 rounded-full">
                    @else
                        <span class="flex items-center justify-center w-8 h-8 text-xs font-bold text-white rounded-full" style="background:var(--kiu-green-deep)">
                            {{ strtoupper(substr(auth('student')->user()->firstname ?? 'U', 0, 1)) }}
                        </span>
                    @endif
                    <span class="hidden text-sm font-semibold sm:block" style="color:var(--kiu-green-deep)">{{ auth('student')->user()->firstname }}</span>
                    <i class="text-xs text-gray-400 fa-solid fa-chevron-down"></i>
                </button>

                <div id="kiuAccountMenu" class="absolute right-0 z-50 hidden w-52 py-1.5 mt-2 bg-white border shadow-lg rounded-2xl">
                    <div class="px-3.5 py-2.5 border-b">
                        <p class="text-sm font-semibold truncate" style="color:var(--kiu-green-deep)">{{ auth('student')->user()->firstname }} {{ auth('student')->user()->lastname }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ auth('student')->user()->email ?? auth('student')->user()->phone }}</p>
                    </div>
                    <a href="{{ route('student.profile') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="w-4 fa-solid fa-user text-gray-400"></i> Umwirondoro
                    </a>
                    <a href="{{ route('student.settings') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="w-4 fa-solid fa-lock text-gray-400"></i> Ijambo ry'ibanga
                    </a>
                    <form action="{{ route('student.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center w-full gap-2.5 px-3.5 py-2 text-sm text-left text-red-600 hover:bg-red-50">
                            <i class="w-4 fa-solid fa-right-from-bracket"></i> Sohoka
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function kiuToggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('kiu_student_theme', isDark ? 'dark' : 'light');
        }
        function kiuToggleAccountMenu() {
            document.getElementById('kiuAccountMenu').classList.toggle('hidden');
        }
        document.addEventListener('click', function (e) {
            const wrap = document.getElementById('kiuAccountMenuWrap');
            if (wrap && !wrap.contains(e.target)) {
                document.getElementById('kiuAccountMenu').classList.add('hidden');
            }
        });

        // Was only ever registered on the public Guest pages — a student
        // whose very first visit to the site is straight to their login
        // page (never touching a Guest page first) would have no service
        // worker at all in the student portal, so the push-notification
        // prompt below would hang forever awaiting
        // navigator.serviceWorker.ready. Registering here too is a safe,
        // idempotent no-op if it's already registered from a Guest page.
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        }
    </script>

    <div id="mobileDrawer" class="fixed inset-0 z-50 hidden bg-black/50 lg:hidden" onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="w-72 h-full p-4 bg-white overflow-y-auto">
            @include('partials.student-sidebar-nav')
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="flex-1 pt-16 lg:ml-64">
        @if(session('success'))
            <div class="p-3 m-4 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="p-3 m-4 font-medium text-red-700 bg-red-100 rounded-xl">{{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="p-3 m-4 font-medium text-blue-700 bg-blue-100 rounded-xl">{{ session('info') }}</div>
        @endif

        @yield('content')
    </main>

</div>

<script>
function kiuRefreshUnreadBadge() {
    fetch("{{ route('student.notifications.unreadCount') }}")
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('kiuUnreadBadge');
            if (!badge) return;
            if (data.count > 0) {
                badge.textContent = data.count > 9 ? '9+' : data.count;
                badge.classList.remove('hidden');
                badge.classList.add('flex');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }
        })
        .catch(() => {});
}
kiuRefreshUnreadBadge();
setInterval(kiuRefreshUnreadBadge, 30000);

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
</script>


@include('partials.quiz-alert-popup')
@include('partials.push-notification-prompt')
@if(\App\Models\FeatureFlag::enabled('guest_chat'))
@include('partials.kwegereza-chat-widget')
@endif
</body>
</html>
