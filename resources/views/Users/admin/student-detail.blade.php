@extends('Users.admin.cover')

@section('content')

<div class="max-w-3xl p-4 mx-auto md:p-6">

    <a href="{{ route('owner.students') }}" class="inline-flex items-center gap-2 mb-4 text-sm text-gray-400 hover:text-primary-dark">
        <i class="fa-solid fa-arrow-left"></i> Subira ku rutonde
    </a>

    <div class="flex items-center justify-between p-6 mb-6 bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">
        <div>
            <h1 class="text-xl font-bold text-primary-dark dark:text-light">{{ $student->firstname }} {{ $student->lastname }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $student->email ?: '—' }} · {{ $student->phone ?: '—' }}</p>
            <p class="mt-1 text-xs text-gray-400">Yiyandikishije: {{ $student->created_at?->format('F j, Y') }}</p>
        </div>
        <div>
            @if($student->deactivated_at)
                <form action="{{ route('owner.students.unblock', $student->id) }}" method="POST">
                    @csrf @method('PATCH')
                    <button class="px-5 py-2.5 text-sm font-bold text-white bg-green-600 rounded-xl">Kuraho Block</button>
                </form>
            @else
                <form action="{{ route('owner.students.block', $student->id) }}" method="POST" onsubmit="return confirm('Emeza guhagarika iyi konti?')">
                    @csrf @method('PATCH')
                    <button class="px-5 py-2.5 text-sm font-bold text-white bg-red-600 rounded-xl">Block Iyi Konti</button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 mb-5 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="p-4 text-center bg-white border border-gray-100 shadow rounded-2xl dark:bg-darker dark:border-gray-700">
            <p class="text-2xl font-bold" style="color:#058e48">{{ $completedDarsat }}</p>
            <p class="text-xs text-gray-400">Amasomo Yarangiye</p>
        </div>
        <div class="p-4 text-center bg-white border border-gray-100 shadow rounded-2xl dark:bg-darker dark:border-gray-700">
            <p class="text-2xl font-bold" style="color:#e2b45f">{{ $certificates->count() }}</p>
            <p class="text-xs text-gray-400">Ibyemezo</p>
        </div>
        <div class="p-4 text-center bg-white border border-gray-100 shadow rounded-2xl dark:bg-darker dark:border-gray-700">
            <p class="text-2xl font-bold" style="color:#094939">{{ $badges->count() }}</p>
            <p class="text-xs text-gray-400">Ibimenyetso</p>
        </div>
    </div>

    <div class="p-5 mb-6 bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">
        <h2 class="mb-3 font-bold text-primary-dark dark:text-light">Amateka y'Ibizamini (Quiz History)</h2>
        @forelse($quizAttempts as $attempt)
        <div class="flex items-center justify-between py-2 border-t dark:border-gray-700">
            <span class="text-sm">{{ $attempt->quiz->title ?? '—' }}</span>
            <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ $attempt->passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ $attempt->percentage }}% {{ $attempt->passed ? '· Yatsinze' : '· Ntiyatsinze' }}
            </span>
        </div>
        @empty
        <p class="text-sm text-gray-400">Nta kizamini yarafata.</p>
        @endforelse
    </div>

    <div class="p-5 bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">
        <h2 class="mb-3 font-bold text-primary-dark dark:text-light">Ibimenyetso (Badges)</h2>
        @if($badges->isEmpty())
            <p class="text-sm text-gray-400">Nta kimenyetso arabona.</p>
        @else
            <div class="flex flex-wrap gap-3">
                @foreach($badges as $ub)
                    <span class="px-3 py-2 text-sm bg-gray-50 dark:bg-dark rounded-xl">{{ $ub->badge->icon ?? '' }} {{ $ub->badge->name ?? '' }}</span>
                @endforeach
            </div>
        @endif
    </div>

</div>

@endsection
