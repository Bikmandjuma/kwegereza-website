@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('owner.quizzes') }}" class="text-gray-400 hover:text-primary-dark">
            <i class="text-xl fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-primary-dark dark:text-light">{{ $quiz->title }}</h1>
            <p class="text-sm text-gray-500">Total points: {{ $quiz->totalPoints() }} · Passing: {{ $quiz->passing_percentage }}%</p>
        </div>
        <button onclick="document.getElementById('addQuestionModal').classList.remove('hidden')"
            class="flex items-center gap-2 px-5 py-3 ml-auto text-sm font-semibold text-white shadow-md rounded-xl bg-primary">
            <i class="fa-solid fa-plus"></i> Ongeraho Ikibazo
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 mb-5 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif

    <div class="space-y-4">
        @forelse($quiz->questions as $question)
        <div class="p-5 bg-white shadow rounded-2xl dark:bg-darker">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-primary/10 text-primary-dark uppercase">{{ str_replace('_', ' ', $question->type) }}</span>
                    <span class="ml-2 text-xs text-gray-400">{{ $question->points }} {{ $question->points > 1 ? 'points' : 'point' }}</span>
                    @if($question->time_limit_seconds)
                        <span class="ml-2 text-xs font-semibold" style="color:#994c1d;"><i class="fa-solid fa-clock"></i> {{ $question->time_limit_seconds }}s</span>
                    @endif
                </div>
                <form action="{{ route('owner.quizzes.questions.destroy', $question->id) }}" method="POST" onsubmit="return confirm('Gusiba iki kibazo?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600"><i class="fa-solid fa-trash"></i></button>
                </form>
            </div>

            <p class="mb-3 font-semibold text-primary-dark dark:text-light">{{ $question->question }}</p>

            @if($question->type === 'short_answer')
                <p class="text-sm text-gray-500">Igisubizo cyemewe: <strong>{{ $question->short_answer }}</strong></p>
            @else
                <ul class="space-y-1">
                    @foreach($question->answers as $answer)
                        <li class="flex items-center gap-2 text-sm">
                            <i class="fa-solid {{ $answer->is_correct ? 'fa-circle-check text-green-600' : 'fa-circle text-gray-300' }}"></i>
                            {{ $answer->answer_text }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        @empty
        <div class="p-10 text-center text-gray-400 bg-white rounded-3xl dark:bg-darker">
            Nta bibazo birahaboneka muri iki kizamini. Kanda "Ongeraho Ikibazo" hejuru.
        </div>
        @endforelse
    </div>

</div>

<!-- ADD QUESTION -->
<div id="addQuestionModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Ikibazo Gishya</h3>
            <button onclick="document.getElementById('addQuestionModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="{{ route('owner.quizzes.questions.store', $quiz->id) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-4 p-6 overflow-y-auto max-h-[65vh]">

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ikibazo</label>
                    <textarea name="question" rows="2" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ubwoko</label>
                    <select name="type" id="questionType" onchange="kiuToggleQuestionType()" class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="true_false">True / False</option>
                        <option value="short_answer">Short Answer</option>
                    </select>
                </div>

                <div id="mcFields">
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Amahitamo (hitamo igisubizo nyacyo)</label>
                    @for($i = 0; $i < 4; $i++)
                    <div class="flex items-center gap-2 mb-2">
                        <input type="radio" name="correct" value="{{ $i }}" {{ $i === 0 ? 'checked' : '' }}>
                        <input type="text" name="answers[{{ $i }}]" placeholder="Ihitamo {{ $i + 1 }}"
                            class="flex-1 px-3 py-2 border rounded-xl dark:bg-dark dark:border-gray-700 dark:text-white">
                    </div>
                    @endfor
                </div>

                <div id="tfFields" class="hidden">
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Igisubizo nyacyo</label>
                    <select name="correct" class="w-full px-4 py-3 border rounded-2xl dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="true">True</option>
                        <option value="false">False</option>
                    </select>
                </div>

                <div id="saFields" class="hidden">
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Igisubizo cyemewe</label>
                    <input type="text" name="short_answer" class="w-full px-4 py-3 border rounded-2xl dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Amanota</label>
                    <input type="number" name="points" value="1" min="1" max="100" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Igihe kigenewe iki kibazo (amasegonda, optional)</label>
                    <input type="number" name="time_limit_seconds" min="5" max="1800" placeholder="e.g. 60"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                    <p class="mt-1 text-xs text-gray-400">Nta na kimwe = nta gihe cyihariye kuri iki kibazo (Igihe cyose cy'ikizamini kigikurikizwa).</p>
                </div>

            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('addQuestionModal').classList.add('hidden')" class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Bika</button>
            </div>
        </form>
    </div>
</div>

<script>
function kiuToggleQuestionType() {
    const type = document.getElementById('questionType').value;

    const mc = document.getElementById('mcFields');
    const tf = document.getElementById('tfFields');
    const sa = document.getElementById('saFields');

    mc.classList.toggle('hidden', type !== 'multiple_choice');
    tf.classList.toggle('hidden', type !== 'true_false');
    sa.classList.toggle('hidden', type !== 'short_answer');

    mc.querySelectorAll('input').forEach(el => el.disabled = (type !== 'multiple_choice'));
    tf.querySelectorAll('select').forEach(el => el.disabled = (type !== 'true_false'));
    sa.querySelectorAll('input').forEach(el => el.disabled = (type !== 'short_answer'));
}
kiuToggleQuestionType();
</script>

@endsection
