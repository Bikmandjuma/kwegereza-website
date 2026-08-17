@php
    $twoFaRoute = auth('student')->user()->two_factor_enabled ? route('student.2fa.manage') : route('student.2fa.setup');
@endphp

<a href="{{ route('student.dashboard') }}" class="sidebar-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
    <i class="fa-solid fa-gauge w-5"></i> Ahabanza
</a>

<details class="sidebar-group" {{ request()->routeIs(['student.courses', 'guest.teachers', 'guest.books', 'guest.inyandiko_zabamenyi']) ? 'open' : '' }}>
    <summary>Ubwigishe <i class="fa-solid fa-chevron-down chevron"></i></summary>
    <a href="{{ route('guest.teachers') }}" class="sidebar-link"><i class="fa-solid fa-graduation-cap w-5"></i> Darsat</a>
    <a href="{{ route('student.courses') }}" class="sidebar-link {{ request()->routeIs('student.courses') ? 'active' : '' }}"><i class="fa-solid fa-diagram-project w-5"></i> Amasomo Agenda</a>
    <a href="{{ route('guest.books') }}" class="sidebar-link"><i class="fa-solid fa-book w-5"></i> Ibitabo</a>
    <a href="{{ route('guest.inyandiko_zabamenyi') }}" class="sidebar-link"><i class="fa-solid fa-pen-nib w-5"></i> Inyandiko</a>
    <span class="sidebar-link disabled">
        <i class="fa-solid fa-video w-5"></i> Amasomo ya Video
        <span class="soon-badge">Coming soon</span>
    </span>
    <span class="sidebar-link disabled">
        <i class="fa-solid fa-headphones w-5"></i> Amasomo y'Amajwi
        <span class="soon-badge">Coming soon</span>
    </span>
</details>

<details class="sidebar-group" {{ request()->routeIs(['student.events', 'guest.news']) ? 'open' : '' }}>
    <summary>Ibikorwa n'Amatangazo <i class="fa-solid fa-chevron-down chevron"></i></summary>
    <a href="{{ route('student.events') }}" class="sidebar-link {{ request()->routeIs('student.events') ? 'active' : '' }}"><i class="fa-solid fa-calendar-days w-5"></i> Ibikorwa Byanjye</a>
    <a href="{{ route('guest.news') }}" class="sidebar-link"><i class="fa-solid fa-bullhorn w-5"></i> Amatangazo</a>
</details>

<details class="sidebar-group" {{ request()->routeIs(['student.groupChat*', 'student.support*']) ? 'open' : '' }}>
    <summary>Itumanaho <i class="fa-solid fa-chevron-down chevron"></i></summary>
    <a href="{{ route('guest.twandikire') }}" class="sidebar-link"><i class="fa-solid fa-comments w-5"></i> Ubutumwa</a>
    <a href="{{ route('student.groupChat.page') }}" class="sidebar-link {{ request()->routeIs('student.groupChat*') ? 'active' : '' }}"><i class="fa-solid fa-user-group w-5"></i> Itsinda Ryanjye</a>
    <a href="{{ route('student.support') }}" class="sidebar-link {{ request()->routeIs('student.support*') ? 'active' : '' }}"><i class="fa-solid fa-headset w-5"></i> Ubufasha</a>
</details>

<details class="sidebar-group" {{ request()->routeIs(['student.progress', 'student.favorites', 'student.quizzes.*', 'student.certificates*', 'student.badges']) ? 'open' : '' }}>
    <summary>Iterambere Ryanjye <i class="fa-solid fa-chevron-down chevron"></i></summary>
    <a href="{{ route('student.progress') }}" class="sidebar-link {{ request()->routeIs('student.progress') ? 'active' : '' }}"><i class="fa-solid fa-chart-simple w-5"></i> Aho Ngeze mu Masomo</a>
    <a href="{{ route('student.favorites') }}" class="sidebar-link {{ request()->routeIs('student.favorites') ? 'active' : '' }}"><i class="fa-solid fa-heart w-5"></i> Ibyo Nkunda</a>
    <a href="{{ route('student.quizzes.history') }}" class="sidebar-link {{ request()->routeIs('student.quizzes.*') ? 'active' : '' }}"><i class="fa-solid fa-list-check w-5"></i> Ibizamini Byanjye</a>
    <a href="{{ route('student.certificates') }}" class="sidebar-link {{ request()->routeIs('student.certificates*') ? 'active' : '' }}"><i class="fa-solid fa-award w-5"></i> Ibyemezo Byanjye</a>
    <a href="{{ route('student.badges') }}" class="sidebar-link {{ request()->routeIs('student.badges') ? 'active' : '' }}"><i class="fa-solid fa-fire w-5"></i> Ibimenyetso</a>
</details>

<details class="sidebar-group" {{ request()->routeIs(['student.2fa.*', 'student.privacy*', 'student.profile', 'student.settings']) ? 'open' : '' }}>
    <summary>Umutekano n'Umwirondoro <i class="fa-solid fa-chevron-down chevron"></i></summary>
    <a href="{{ $twoFaRoute }}" class="sidebar-link {{ request()->routeIs('student.2fa.*') ? 'active' : '' }}"><i class="fa-solid fa-shield-halved w-5"></i> Umutekano (2FA)</a>
    <a href="{{ route('student.privacy') }}" class="sidebar-link {{ request()->routeIs('student.privacy*') ? 'active' : '' }}"><i class="fa-solid fa-user-lock w-5"></i> Ubuzima Bwite</a>
    <a href="{{ route('student.profile') }}" class="sidebar-link {{ request()->routeIs('student.profile') ? 'active' : '' }}"><i class="fa-solid fa-user w-5"></i> Umwirondoro</a>
    <a href="{{ route('student.settings') }}" class="sidebar-link {{ request()->routeIs('student.settings') ? 'active' : '' }}"><i class="fa-solid fa-gear w-5"></i> Igenamiterere</a>
</details>

<div class="pt-3 mt-3 border-t">
    <form action="{{ route('student.logout') }}" method="POST">
        @csrf
        <button type="submit" class="w-full sidebar-link" style="color:#b30000">
            <i class="fa-solid fa-right-from-bracket w-5"></i> Sohoka
        </button>
    </form>
</div>
