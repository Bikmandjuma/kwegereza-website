<!DOCTYPE html>
<html lang="rw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Kwegereza Islam Umuryango</title>
    <link rel="icon" href="{{ URL::to('/') }}/Guest/images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root{
            --kiu-green: #058e48;
            --kiu-green-deep: #094939;
            --kiu-gold: #e2b45f;
            --kiu-cream: #f5ebe2;
        }
        body{ background:#f6f8f7; font-family: 'Nunito', sans-serif; }
        .sidebar-link{
            display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:12px;
            color:#4b5563; font-size:14px; font-weight:600; transition:.2s;
        }
        .sidebar-link:hover, .sidebar-link.active{ background: var(--kiu-cream); color: var(--kiu-green-deep); }
        .sidebar-link.disabled{ color:#c3c3c3; cursor:not-allowed; }
        .sidebar-link.disabled:hover{ background:none; }
        .soon-badge{ font-size:9px; background:#eee; color:#999; padding:2px 6px; border-radius:999px; margin-left:auto; }
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
            <a href="{{ route('student.notifications') }}" class="relative ml-auto text-lg" style="color:var(--kiu-green-deep)">
                <i class="fa-solid fa-bell"></i>
                <span id="kiuUnreadBadge" class="absolute hidden items-center justify-center w-4 h-4 text-[9px] font-bold text-white rounded-full -top-1 -right-1" style="background:#e11d48"></span>
            </a>
        </div>

        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <a href="{{ route('student.dashboard') }}" class="sidebar-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge w-5"></i> Dashboard
            </a>
            <a href="{{ route('guest.teachers') }}" class="sidebar-link">
                <i class="fa-solid fa-graduation-cap w-5"></i> Darsat
            </a>
            <a href="{{ route('student.courses') }}" class="sidebar-link {{ request()->routeIs('student.courses') ? 'active' : '' }}">
                <i class="fa-solid fa-diagram-project w-5"></i> Amasomo Agenda
            </a>
            <a href="{{ route('student.events') }}" class="sidebar-link {{ request()->routeIs('student.events') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-days w-5"></i> Ibikorwa Byanjye
            </a>
            <a href="{{ auth('student')->user()->two_factor_enabled ? route('student.2fa.manage') : route('student.2fa.setup') }}" class="sidebar-link {{ request()->routeIs('student.2fa.*') ? 'active' : '' }}">
                <i class="fa-solid fa-shield-halved w-5"></i> Umutekano (2FA)
            </a>
            <a href="{{ route('student.support') }}" class="sidebar-link {{ request()->routeIs('student.support*') ? 'active' : '' }}">
                <i class="fa-solid fa-headset w-5"></i> Ubufasha
            </a>
            <a href="{{ route('student.privacy') }}" class="sidebar-link {{ request()->routeIs('student.privacy*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-lock w-5"></i> Ubuzima Bwite
            </a>
            <a href="{{ route('guest.books') }}" class="sidebar-link">
                <i class="fa-solid fa-book w-5"></i> Ibitabo
            </a>
            <a href="{{ route('guest.inyandiko_zabamenyi') }}" class="sidebar-link">
                <i class="fa-solid fa-pen-nib w-5"></i> Inyandiko
            </a>
            <a href="{{ route('guest.news') }}" class="sidebar-link">
                <i class="fa-solid fa-bullhorn w-5"></i> Amatangazo
            </a>
            <span class="sidebar-link disabled">
                <i class="fa-solid fa-video w-5"></i> Amasomo ya Video
                <span class="soon-badge">Coming soon</span>
            </span>
            <span class="sidebar-link disabled">
                <i class="fa-solid fa-headphones w-5"></i> Amasomo y'Amajwi
                <span class="soon-badge">Coming soon</span>
            </span>
            <a href="{{ route('guest.twandikire') }}" class="sidebar-link">
                <i class="fa-solid fa-comments w-5"></i> Ubutumwa
            </a>
            <a href="{{ route('student.progress') }}" class="sidebar-link {{ request()->routeIs('student.progress') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-simple w-5"></i> Aho Ngeze mu Masomo
            </a>
            <a href="{{ route('student.favorites') }}" class="sidebar-link {{ request()->routeIs('student.favorites') ? 'active' : '' }}">
                <i class="fa-solid fa-heart w-5"></i> Ibyo Nkunda
            </a>
            <a href="{{ route('student.quizzes.history') }}" class="sidebar-link {{ request()->routeIs('student.quizzes.*') ? 'active' : '' }}">
                <i class="fa-solid fa-list-check w-5"></i> Ibizamini Byanjye
            </a>
            <a href="{{ route('student.certificates') }}" class="sidebar-link {{ request()->routeIs('student.certificates*') ? 'active' : '' }}">
                <i class="fa-solid fa-award w-5"></i> Ibyemezo Byanjye
            </a>
            <a href="{{ route('student.badges') }}" class="sidebar-link {{ request()->routeIs('student.badges') ? 'active' : '' }}">
                <i class="fa-solid fa-fire w-5"></i> Ibimenyetso
            </a>

            <div class="pt-3 mt-3 border-t">
                <a href="{{ route('student.profile') }}" class="sidebar-link {{ request()->routeIs('student.profile') ? 'active' : '' }}">
                    <i class="fa-solid fa-user w-5"></i> Umwirondoro
                </a>
                <a href="{{ route('student.settings') }}" class="sidebar-link {{ request()->routeIs('student.settings') ? 'active' : '' }}">
                    <i class="fa-solid fa-gear w-5"></i> Igenamiterere
                </a>
                <form action="{{ route('student.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full sidebar-link" style="color:#b30000">
                        <i class="fa-solid fa-right-from-bracket w-5"></i> Sohoka
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <!-- MOBILE TOP BAR -->
    <div class="fixed top-0 left-0 right-0 z-40 flex items-center justify-between px-4 py-3 bg-white border-b shadow-sm lg:hidden">
        <div class="flex items-center gap-2">
            <img src="{{ URL::to('/') }}/Guest/images/logo.png" class="w-8 h-8" alt="Logo">
            <span class="text-sm font-bold" style="color:var(--kiu-green-deep)">K.I.U</span>
        </div>
        <button onclick="document.getElementById('mobileDrawer').classList.toggle('hidden')" class="text-xl" style="color:var(--kiu-green-deep)">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    <div id="mobileDrawer" class="fixed inset-0 z-50 hidden bg-black/50 lg:hidden" onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="w-64 h-full p-4 bg-white">
            <a href="{{ route('student.dashboard') }}" class="sidebar-link"><i class="fa-solid fa-gauge w-5"></i> Dashboard</a>
            <a href="{{ route('guest.teachers') }}" class="sidebar-link"><i class="fa-solid fa-graduation-cap w-5"></i> Darsat</a>
            <a href="{{ route('student.courses') }}" class="sidebar-link"><i class="fa-solid fa-diagram-project w-5"></i> Amasomo Agenda</a>
            <a href="{{ route('student.events') }}" class="sidebar-link"><i class="fa-solid fa-calendar-days w-5"></i> Ibikorwa Byanjye</a>
            <a href="{{ auth('student')->user()->two_factor_enabled ? route('student.2fa.manage') : route('student.2fa.setup') }}" class="sidebar-link"><i class="fa-solid fa-shield-halved w-5"></i> Umutekano (2FA)</a>
            <a href="{{ route('student.support') }}" class="sidebar-link"><i class="fa-solid fa-headset w-5"></i> Ubufasha</a>
            <a href="{{ route('student.privacy') }}" class="sidebar-link"><i class="fa-solid fa-user-lock w-5"></i> Ubuzima Bwite</a>
            <a href="{{ route('guest.books') }}" class="sidebar-link"><i class="fa-solid fa-book w-5"></i> Ibitabo</a>
            <a href="{{ route('guest.inyandiko_zabamenyi') }}" class="sidebar-link"><i class="fa-solid fa-pen-nib w-5"></i> Inyandiko</a>
            <a href="{{ route('guest.news') }}" class="sidebar-link"><i class="fa-solid fa-bullhorn w-5"></i> Amatangazo</a>
            <a href="{{ route('guest.twandikire') }}" class="sidebar-link"><i class="fa-solid fa-comments w-5"></i> Ubutumwa</a>
            <a href="{{ route('student.progress') }}" class="sidebar-link"><i class="fa-solid fa-chart-simple w-5"></i> Aho Ngeze mu Masomo</a>
            <a href="{{ route('student.favorites') }}" class="sidebar-link"><i class="fa-solid fa-heart w-5"></i> Ibyo Nkunda</a>
            <a href="{{ route('student.quizzes.history') }}" class="sidebar-link"><i class="fa-solid fa-list-check w-5"></i> Ibizamini Byanjye</a>
            <a href="{{ route('student.certificates') }}" class="sidebar-link"><i class="fa-solid fa-award w-5"></i> Ibyemezo Byanjye</a>
            <a href="{{ route('student.badges') }}" class="sidebar-link"><i class="fa-solid fa-fire w-5"></i> Ibimenyetso</a>
            <a href="{{ route('student.profile') }}" class="sidebar-link"><i class="fa-solid fa-user w-5"></i> Umwirondoro</a>
            <a href="{{ route('student.settings') }}" class="sidebar-link"><i class="fa-solid fa-gear w-5"></i> Igenamiterere</a>
            <form action="{{ route('student.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full sidebar-link" style="color:#b30000"><i class="fa-solid fa-right-from-bracket w-5"></i> Sohoka</button>
            </form>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="flex-1 pt-16 lg:pt-0 lg:ml-64">
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
</body>
</html>
