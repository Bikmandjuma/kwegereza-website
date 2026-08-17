@extends('Users.User.cover')
@section('title', "Amateka y'Ibizamini")

@section('content')
<div class="p-4 md:p-6">

    <h1 class="mb-6 text-2xl font-bold" style="color:#094939">Ibizamini Byanjye</h1>

    <h2 class="mb-3 text-lg font-bold" style="color:#094939">Ibitegereje (Available / Undone)</h2>
    @if($undone->isEmpty())
        <div class="p-6 mb-8 text-center text-gray-400 bg-white shadow rounded-2xl">
            Nta kizamini gitegereje ubu.
        </div>
    @else
        <div class="grid gap-4 mb-8 md:grid-cols-2">
            @foreach($undone as $quiz)
            <div class="p-5 bg-white shadow-lg rounded-2xl">
                <p class="mb-1 font-bold" style="color:#094939">{{ $quiz->title }}</p>
                <p class="mb-3 text-xs text-gray-400">{{ $quiz->questions_count ?? $quiz->questions()->count() }} ibibazo · {{ $quiz->passing_percentage }}% byo gutsinda</p>
                @include('Guest.partials.quiz-status-widget', ['quiz' => $quiz])
            </div>
            @endforeach
        </div>
    @endif

    <h2 class="mb-3 text-lg font-bold" style="color:#094939">Amateka (Byarangiye)</h2>
    <div class="overflow-hidden bg-white shadow-lg rounded-3xl">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                <tr>
                    <th class="px-5 py-3">Ikizamini</th>
                    <th class="px-5 py-3">Amanota</th>
                    <th class="px-5 py-3">Igisubizo</th>
                    <th class="px-5 py-3">Itariki</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($attempts as $attempt)
                <tr>
                    <td class="px-5 py-3 font-semibold" style="color:#094939">{{ $attempt->quiz->title }}</td>
                    <td class="px-5 py-3">{{ $attempt->percentage }}%</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ $attempt->passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $attempt->passed ? 'YATSINZE' : 'NTIYATSINZE' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-400">{{ $attempt->submitted_at->diffForHumans() }}</td>
                    <td class="px-5 py-3">
                        <a href="{{ route('student.quizzes.result', $attempt->id) }}" class="font-semibold" style="color:#058e48">Reba →</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-10 text-center text-gray-400">Ntabwo urarakora ikizamini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $attempts->links() }}</div>

</div>
@endsection
