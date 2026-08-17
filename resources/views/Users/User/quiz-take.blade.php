@extends('Users.User.cover')
@section('title', 'Ikizamini')

@section('content')
<div class="max-w-2xl p-4 mx-auto md:p-6">

    <div class="sticky top-0 z-10 p-4 mb-4 bg-white shadow-lg rounded-2xl" style="border:2px solid #094939;">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-bold" style="color:#094939">{{ $quiz->title }}</p>
                <p class="text-xs text-gray-400" id="kiu-progress-label">Ikibazo 1 / {{ $quiz->questions->count() }}</p>
            </div>
            @if($quiz->duration_minutes)
                <div class="text-right">
                    <p class="text-[10px] text-gray-400 uppercase">Igihe gisigaye</p>
                    <p class="text-xl font-bold" id="kiu-overall-timer" style="color:#e11d48">--:--</p>
                </div>
            @endif
        </div>
        <div class="w-full h-1.5 mt-3 overflow-hidden bg-gray-100 rounded-full">
            <div id="kiu-progress-bar" class="h-1.5 rounded-full" style="background:#058e48;width:{{ round(100 / max($quiz->questions->count(),1)) }}%;transition:width .3s;"></div>
        </div>
    </div>

    <form id="kiuQuizForm" action="{{ route('student.quizzes.submit', $attempt->id) }}" method="POST">
        @csrf

        @foreach($quiz->questions as $index => $question)
        <div class="p-6 mb-4 bg-white shadow rounded-2xl kiu-question" data-index="{{ $index }}"
             data-time-limit="{{ $question->time_limit_seconds ?? '' }}"
             style="{{ $index === 0 ? '' : 'display:none;' }}">

            <div class="flex items-start justify-between mb-4">
                <p class="font-semibold" style="color:#094939">{{ $index + 1 }}. {{ $question->question }}</p>
                @if($question->time_limit_seconds)
                    <span class="kiu-question-timer" style="font-size:12px;font-weight:700;color:#994c1d;background:#faece7;padding:3px 10px;border-radius:999px;white-space:nowrap;margin-left:10px;">
                        {{ $question->time_limit_seconds }}s
                    </span>
                @endif
            </div>

            @if($question->type === 'short_answer')
                <input type="text" name="answer_{{ $question->id }}"
                    class="w-full px-4 py-3 border rounded-2xl" placeholder="Andika igisubizo cyawe">
            @else
                <div class="space-y-2">
                    @foreach($question->answers as $answer)
                    <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="answer_{{ $question->id }}" value="{{ $answer->id }}">
                        <span>{{ $answer->answer_text }}</span>
                    </label>
                    @endforeach
                </div>
            @endif

            <div class="flex justify-end mt-5">
                @if($index < $quiz->questions->count() - 1)
                    <button type="button" onclick="kiuNextQuestion()" class="px-6 py-2.5 font-bold text-white rounded-2xl" style="background:#058e48">
                        Komeza <i class="fa-solid fa-arrow-right"></i>
                    </button>
                @else
                    <button type="submit" class="px-6 py-2.5 font-bold text-white rounded-2xl" style="background:#094939">
                        Ohereza Ibisubizo
                    </button>
                @endif
            </div>
        </div>
        @endforeach

    </form>

</div>

<script>
(function() {
    const totalQuestions = {{ $quiz->questions->count() }};
    const questions = document.querySelectorAll('.kiu-question');
    const progressLabel = document.getElementById('kiu-progress-label');
    const progressBar = document.getElementById('kiu-progress-bar');
    let current = 0;
    let questionTimerInterval = null;

    window.kiuNextQuestion = function() {
        if (current >= totalQuestions - 1) return;
        questions[current].style.display = 'none';
        current++;
        questions[current].style.display = '';
        progressLabel.textContent = 'Ikibazo ' + (current + 1) + ' / ' + totalQuestions;
        progressBar.style.width = Math.round(((current + 1) / totalQuestions) * 100) + '%';
        startQuestionTimer();
    };

    function startQuestionTimer() {
        if (questionTimerInterval) clearInterval(questionTimerInterval);

        const q = questions[current];
        const limit = parseInt(q.dataset.timeLimit, 10);
        if (!limit) return;

        let remaining = limit;
        const timerEl = q.querySelector('.kiu-question-timer');

        questionTimerInterval = setInterval(function() {
            remaining--;
            if (timerEl) timerEl.textContent = remaining + 's';
            if (remaining <= 0) {
                clearInterval(questionTimerInterval);
                if (current >= totalQuestions - 1) {
                    document.getElementById('kiuQuizForm').submit();
                } else {
                    kiuNextQuestion();
                }
            }
        }, 1000);
    }

    @if($quiz->duration_minutes)
    const startedAt = new Date("{{ $attempt->started_at->toIso8601String() }}").getTime();
    const durationMs = {{ $quiz->duration_minutes }} * 60 * 1000;
    const deadline = startedAt + durationMs;
    const overallTimerEl = document.getElementById('kiu-overall-timer');

    function updateOverallTimer() {
        const remaining = deadline - Date.now();
        if (remaining <= 0) {
            overallTimerEl.textContent = '00:00';
            document.getElementById('kiuQuizForm').submit();
            return;
        }
        const mins = Math.floor(remaining / 60000);
        const secs = Math.floor((remaining % 60000) / 1000);
        overallTimerEl.textContent = String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
    }
    updateOverallTimer();
    setInterval(updateOverallTimer, 1000);
    @endif

    startQuestionTimer();
})();
</script>
@endsection
