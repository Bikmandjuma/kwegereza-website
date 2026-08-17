@extends('Users.User.cover')
@section('title', 'Aho Ngeze mu Masomo')

@section('content')
<div class="p-4 md:p-6">

    <h1 class="mb-6 text-2xl font-bold" style="color:#094939">{{ __('dashboard.progress_heading') }}</h1>

    <div class="grid gap-5 mb-6 md:grid-cols-2">
        <div class="p-5 bg-white shadow-lg rounded-3xl">
            <p class="text-sm text-gray-500">{{ __('dashboard.completed_lessons') }}</p>
            <p class="text-3xl font-bold" style="color:#058e48">{{ $completedCount }}</p>
        </div>
        <div class="p-5 bg-white shadow-lg rounded-3xl">
            <p class="text-sm text-gray-500">{{ __('dashboard.in_progress_stat') }}</p>
            <p class="text-3xl font-bold" style="color:#e2b45f">{{ $inProgressCount }}</p>
        </div>
    </div>

    <div class="overflow-hidden bg-white shadow-lg rounded-3xl">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                <tr>
                    <th class="px-5 py-3">{{ __('dashboard.table_lesson') }}</th>
                    <th class="px-5 py-3">{{ __('dashboard.table_teacher') }}</th>
                    <th class="px-5 py-3">{{ __('dashboard.table_status') }}</th>
                    <th class="px-5 py-3">{{ __('dashboard.table_last_activity') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($progress as $p)
                <tr>
                    <td class="px-5 py-3 font-semibold" style="color:#094939">{{ $p->darsat->title ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $p->darsat->teacher->firstname ?? '' }} {{ $p->darsat->teacher->lastname ?? '' }}</td>
                    <td class="px-5 py-3">
                        @if($p->status === 'completed')
                            <span class="px-2 py-0.5 text-xs font-bold text-white rounded-full" style="background:#058e48">{{ __('dashboard.badge_completed') }}</span>
                        @else
                            <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-amber-100 text-amber-700">{{ __('dashboard.badge_in_progress') }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-400">{{ $p->last_played_at?->diffForHumans() ?? $p->updated_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-10 text-center text-gray-400">
                        {{ __('dashboard.progress_empty') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
