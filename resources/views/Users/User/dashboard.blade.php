@extends('Users.User.cover')
@section('title', 'Dashboard')

@section('content')
<div class="p-4 md:p-6">

    <div class="flex items-center gap-4 p-6 mb-6 text-white shadow-lg bg-gradient-to-br from-[#0B6D20] to-[#0B3D2E] rounded-3xl">
        <div class="flex items-center justify-center w-16 h-16 text-2xl font-bold bg-white rounded-2xl text-primary-dark" style="color:#0B3D2E">
            {{ strtoupper(substr($user->firstname ?? 'U', 0, 1)) }}
        </div>
        <div>
            <h1 class="text-xl font-bold">{{ __('dashboard.greeting') }} {{ $user->firstname ?? __('dashboard.default_name') }}!</h1>
            <p class="text-sm opacity-90">{{ __('dashboard.welcome_sub') }}</p>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">

        <div class="p-5 bg-white shadow-lg rounded-3xl">
            <h2 class="mb-4 font-bold text-primary-dark" style="color:#0B3D2E">{{ __('dashboard.new_announcements') }}</h2>
            @forelse($recentAmatangazo as $item)
                <div class="flex items-center gap-3 py-2 border-b last:border-0">
                    <span class="w-2 h-2 rounded-full" style="background:#0B6D20"></span>
                    <div>
                        <p class="text-sm font-semibold">{{ $item->title }}</p>
                        <p class="text-xs text-gray-400">{{ $item->published_at?->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400">{{ __('dashboard.no_new_announcements') }}</p>
            @endforelse
            <a href="{{ route('guest.news') }}" class="inline-block mt-3 text-sm font-semibold" style="color:#0B6D20">{{ __('dashboard.view_all') }}</a>
        </div>

        <div class="p-5 bg-white shadow-lg rounded-3xl">
            <h2 class="mb-4 font-bold" style="color:#0B3D2E">{{ __('dashboard.new_articles') }}</h2>
            @forelse($recentInyandiko as $item)
                <div class="flex items-center gap-3 py-2 border-b last:border-0">
                    <span class="w-2 h-2 rounded-full" style="background:#C9A227"></span>
                    <div>
                        <p class="text-sm font-semibold">{{ $item->title }}</p>
                        <p class="text-xs text-gray-400">{{ $item->category }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400">{{ __('dashboard.no_new_articles') }}</p>
            @endforelse
            <a href="{{ route('guest.inyandiko_zabamenyi') }}" class="inline-block mt-3 text-sm font-semibold" style="color:#0B6D20">{{ __('dashboard.view_all_articles') }}</a>
        </div>

        <div class="p-5 bg-white shadow-lg rounded-3xl md:col-span-2">
            <h2 class="mb-3 font-bold" style="color:#0B3D2E">{{ __('dashboard.progress_heading') }}</h2>
            <div class="flex gap-6 mb-3">
                <div>
                    <p class="text-2xl font-bold" style="color:#0B6D20">{{ $user->completedLessonsCount() }}</p>
                    <p class="text-xs text-gray-400">{{ __('dashboard.completed') }}</p>
                </div>
                <div>
                    <p class="text-2xl font-bold" style="color:#C9A227">{{ $user->inProgressLessonsCount() }}</p>
                    <p class="text-xs text-gray-400">{{ __('dashboard.in_progress') }}</p>
                </div>
            </div>
            <a href="{{ route('student.progress') }}" class="text-sm font-semibold" style="color:#0B6D20">{{ __('dashboard.view_detailed') }}</a>
        </div>

    </div>
</div>
@endsection
