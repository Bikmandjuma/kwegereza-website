@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <div class="flex items-center gap-4 mb-6">
        <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
            <i class="fa-solid fa-server"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-primary-dark dark:text-light">System Monitor</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Live checks, run fresh on every page load. Reload to re-check.</p>
        </div>
    </div>

    <!-- HEALTH CHECKS -->
    <div class="grid gap-5 mb-8 md:grid-cols-3">
        @foreach($checks as $name => $check)
        <div class="p-5 bg-white border-2 shadow-lg rounded-3xl dark:bg-darker {{ $check['status'] === 'ok' ? 'border-green-200' : 'border-red-300' }}">
            <div class="flex items-center gap-2 mb-2">
                <i class="fa-solid {{ $check['status'] === 'ok' ? 'fa-circle-check text-green-600' : 'fa-circle-xmark text-red-600' }}"></i>
                <h2 class="font-bold uppercase text-primary-dark dark:text-light">{{ str_replace('_', ' ', $name) }}</h2>
            </div>
            <p class="text-xs text-gray-500">{{ $check['message'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid gap-6 mb-8 lg:grid-cols-2">

        <!-- ENVIRONMENT -->
        <div class="p-5 bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">
            <h2 class="mb-3 font-bold text-primary-dark dark:text-light">Environment</h2>
            <table class="w-full text-sm">
                @foreach($config as $key => $value)
                <tr class="border-t dark:border-gray-700">
                    <td class="py-2 text-gray-500 capitalize">{{ str_replace('_', ' ', $key) }}</td>
                    <td class="py-2 font-semibold text-right {{ str_contains($value, 'should be OFF') ? 'text-red-600' : 'text-primary-dark dark:text-light' }}">{{ $value }}</td>
                </tr>
                @endforeach
            </table>
            @if(config('queue.default') === 'sync')
                <p class="p-3 mt-3 text-xs font-semibold text-yellow-700 bg-yellow-50 rounded-xl">
                    <i class="fa-solid fa-triangle-exclamation"></i> Queue is "sync" — background jobs (bulk notifications, etc.) run inline during the request rather than in the background. Fine at small scale; worth a real queue worker as usage grows.
                </p>
            @endif
        </div>

        <!-- DISK USAGE -->
        <div class="p-5 bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">
            <h2 class="mb-3 font-bold text-primary-dark dark:text-light">Disk Usage</h2>
            @if($disk)
                <div class="w-full h-4 mb-2 overflow-hidden bg-gray-200 rounded-full">
                    <div class="h-4 {{ $disk['used_pct'] > 85 ? 'bg-red-500' : ($disk['used_pct'] > 70 ? 'bg-yellow-500' : 'bg-primary') }}" style="width:{{ $disk['used_pct'] }}%"></div>
                </div>
                <p class="text-sm text-gray-500">{{ $disk['used_gb'] }} GB used of {{ $disk['total_gb'] }} GB ({{ $disk['used_pct'] }}%)</p>
            @else
                <p class="text-sm text-gray-400">disk_free_space() is disabled on this host — can't report real numbers, so showing nothing rather than a fake one.</p>
            @endif
        </div>

    </div>

    <!-- CONTENT COUNTS -->
    <div class="p-5 mb-8 bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">
        <h2 class="mb-4 font-bold text-primary-dark dark:text-light">Platform Totals</h2>
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            @foreach($counts as $label => $count)
            <div class="text-center">
                <p class="text-2xl font-bold" style="color:#058e48">{{ number_format($count) }}</p>
                <p class="text-xs text-gray-400 capitalize">{{ $label }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- RECENT ERRORS -->
    <div class="p-5 bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">
        <h2 class="mb-3 font-bold text-primary-dark dark:text-light">Recent Errors (from storage/logs/laravel.log)</h2>
        @if(empty($recentErrors))
            <p class="text-sm text-green-600"><i class="fa-solid fa-circle-check"></i> No recent ERROR/CRITICAL entries found in the log tail.</p>
        @else
            <div class="space-y-2 overflow-y-auto max-h-80">
                @foreach($recentErrors as $err)
                <div class="p-3 font-mono text-xs text-red-700 bg-red-50 rounded-xl">{{ $err['line'] }}</div>
                @endforeach
            </div>
        @endif
    </div>

</div>

@endsection
