@extends('Users.User.cover')
@section('title', 'Ibimenyetso')

@section('content')
<div class="p-4 md:p-6">

    <div class="flex items-center gap-4 p-6 mb-6 text-white shadow-lg bg-gradient-to-br from-[#C9A227] to-[#c8a36c] rounded-3xl">
        <div class="text-4xl">🔥</div>
        <div>
            <p class="text-2xl font-bold">{{ $streak->current_streak ?? 0 }} {{ ($streak->current_streak ?? 0) == 1 ? __('badges.day') : __('badges.days') }}</p>
            <p class="text-sm opacity-90">{{ __('badges.streak_label') }} {{ $streak->longest_streak ?? 0 }}</p>
        </div>
    </div>

    <h2 class="mb-4 text-lg font-bold" style="color:#0B3D2E">{{ __('badges.earned_heading') }}</h2>

    <div class="grid gap-4 mb-8 md:grid-cols-3">
        @forelse($allBadges as $badge)
            @php $isEarned = $earnedBadgeIds->contains($badge->id); @endphp
            <div class="p-5 text-center bg-white shadow rounded-2xl {{ $isEarned ? '' : 'opacity-40 grayscale' }}">
                <div class="mb-2 text-4xl">{{ $badge->icon }}</div>
                <p class="font-bold" style="color:#0B3D2E">{{ $badge->name }}</p>
                <p class="mt-1 text-xs text-gray-400">{{ $badge->description }}</p>
                @if($isEarned)
                    <span class="inline-block px-2 py-0.5 mt-2 text-[10px] font-bold text-white rounded-full" style="background:#0B6D20">{{ __('badges.earned_tag') }}</span>
                @endif
            </div>
        @empty
            <p class="text-gray-400 col-span-full">{{ __('badges.empty_state') }}</p>
        @endforelse
    </div>

</div>
@endsection
