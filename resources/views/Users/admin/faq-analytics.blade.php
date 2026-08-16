@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <div class="flex items-center gap-4 mb-6">
        <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
            <i class="fa-solid fa-chart-line"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-primary-dark dark:text-light">Guest Chat Analytics</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                <a href="{{ route('owner.faq') }}" class="underline">← Subira ku bibazo/bisubizo</a>
            </p>
        </div>
    </div>

    <div class="grid gap-5 mb-6 md:grid-cols-3">
        <div class="p-5 bg-white shadow-lg rounded-3xl dark:bg-darker">
            <p class="text-sm text-gray-500">Conversations</p>
            <p class="text-3xl font-bold text-primary-dark dark:text-light">{{ $totalConversations }}</p>
        </div>
        <div class="p-5 bg-white shadow-lg rounded-3xl dark:bg-darker">
            <p class="text-sm text-gray-500">Questions Asked</p>
            <p class="text-3xl font-bold text-primary-dark dark:text-light">{{ $totalQuestions }}</p>
        </div>
        <div class="p-5 bg-white shadow-lg rounded-3xl dark:bg-darker">
            <p class="text-sm text-gray-500">Unanswered</p>
            <p class="text-3xl font-bold text-red-500">{{ $unanswered }}</p>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">

        <div class="p-5 bg-white shadow-lg rounded-3xl dark:bg-darker">
            <h3 class="mb-4 font-bold text-primary-dark dark:text-light">Most Asked Questions</h3>
            <ol class="space-y-2 text-sm">
                @forelse($mostAsked as $i => $q)
                    <li class="flex items-center justify-between p-2 rounded-lg bg-gray-50 dark:bg-dark">
                        <span>{{ $i + 1 }}. {{ $q->question }}</span>
                        <span class="font-bold text-primary-dark">{{ $q->times_matched }}</span>
                    </li>
                @empty
                    <li class="text-gray-400">Nta makuru arahaboneka.</li>
                @endforelse
            </ol>
        </div>

        <div class="p-5 bg-white shadow-lg rounded-3xl dark:bg-darker">
            <h3 class="mb-4 font-bold text-primary-dark dark:text-light">Unanswered Questions (recent)</h3>
            <ol class="space-y-2 overflow-y-auto text-sm max-h-80">
                @forelse($unansweredSamples as $u)
                    <li class="p-2 rounded-lg bg-red-50">
                        <p class="font-medium">{{ $u->question }}</p>
                        <p class="text-xs text-gray-400">{{ $u->page }} · {{ $u->created_at->diffForHumans() }}</p>
                    </li>
                @empty
                    <li class="text-gray-400">Nta bibazo bitasubijwe.</li>
                @endforelse
            </ol>
        </div>

        <div class="p-5 bg-white shadow-lg rounded-3xl dark:bg-darker">
            <h3 class="mb-4 font-bold text-primary-dark dark:text-light">Daily Volume (last 14 days)</h3>
            <div class="space-y-1">
                @forelse($dailyVolume as $d)
                    <div class="flex items-center gap-2 text-xs">
                        <span class="w-20 text-gray-500">{{ $d->day }}</span>
                        <div class="flex-1 h-3 overflow-hidden bg-gray-100 rounded dark:bg-dark">
                            <div class="h-full bg-primary" style="width: {{ min(100, $d->total * 8) }}%"></div>
                        </div>
                        <span class="font-semibold">{{ $d->total }}</span>
                    </div>
                @empty
                    <p class="text-gray-400">Nta makuru arahaboneka.</p>
                @endforelse
            </div>
        </div>

        <div class="p-5 bg-white shadow-lg rounded-3xl dark:bg-darker">
            <h3 class="mb-4 font-bold text-primary-dark dark:text-light">Monthly Volume (last 6 months)</h3>
            <div class="space-y-1">
                @forelse($monthlyVolume as $m)
                    <div class="flex items-center gap-2 text-xs">
                        <span class="w-20 text-gray-500">{{ $m->month }}</span>
                        <div class="flex-1 h-3 overflow-hidden bg-gray-100 rounded dark:bg-dark">
                            <div class="h-full bg-primary" style="width: {{ min(100, $m->total) }}%"></div>
                        </div>
                        <span class="font-semibold">{{ $m->total }}</span>
                    </div>
                @empty
                    <p class="text-gray-400">Nta makuru arahaboneka.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>

@endsection
