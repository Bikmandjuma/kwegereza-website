@extends('Users.User.cover')
@section('title', 'Igisubizo')

@section('content')
<div class="max-w-2xl p-4 mx-auto md:p-6">

    <div class="p-8 mb-6 text-center bg-white shadow-lg rounded-3xl">
        <div class="flex items-center justify-center w-20 h-20 mx-auto mb-4 text-3xl font-bold text-white rounded-full"
             style="background: {{ $attempt->passed ? '#0B6D20' : '#e11d48' }}">
            {{ $attempt->percentage }}%
        </div>
        <h1 class="mb-1 text-xl font-bold" style="color:#0B3D2E">{{ $attempt->quiz->title }}</h1>
        <p class="font-semibold {{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">
            {{ $attempt->passed ? 'Watsinze!' : 'Ntabwo watsinze' }}
        </p>
        <p class="mt-2 text-sm text-gray-400">
            Amanota: {{ $attempt->score }} / {{ $attempt->total_points }} · Igipimo cyo gutsinda: {{ $attempt->quiz->passing_percentage }}%
        </p>
    </div>

    <div class="space-y-3">
        @foreach($attempt->quiz->questions as $index => $question)
            @php $given = $attempt->answers->firstWhere('quiz_question_id', $question->id); @endphp
            <div class="p-5 bg-white shadow rounded-2xl">
                <div class="flex items-start gap-3">
                    <i class="fa-solid {{ $given && $given->is_correct ? 'fa-circle-check text-green-600' : 'fa-circle-xmark text-red-500' }} mt-1"></i>
                    <div>
                        <p class="font-semibold" style="color:#0B3D2E">{{ $index + 1 }}. {{ $question->question }}</p>

                        @if($question->type === 'short_answer')
                            <p class="mt-1 text-sm text-gray-500">Igisubizo cyawe: {{ $given->answer_text ?? '—' }}</p>
                            @if(!$given?->is_correct)
                                <p class="text-sm text-green-600">Igisubizo nyacyo: {{ $question->short_answer }}</p>
                            @endif
                        @else
                            <p class="mt-1 text-sm text-gray-500">Igisubizo cyawe: {{ $given?->chosenAnswer?->answer_text ?? '—' }}</p>
                            @if(!$given?->is_correct)
                                <p class="text-sm text-green-600">Igisubizo nyacyo: {{ $question->correctAnswer()?->answer_text }}</p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <a href="{{ route('student.quizzes.history') }}" class="block mt-6 text-sm font-semibold text-center" style="color:#0B6D20">
        Reba amateka y'ibizamini byanjye →
    </a>

</div>
@endsection
