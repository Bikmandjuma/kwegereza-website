@php
    $hasPassed = $quiz->hasPassedBy(auth('student')->id());
@endphp

<style>
.kiu-quiz-start-btn:hover { filter: brightness(1.08); transform: translateY(-1px); }
</style>

<div class="mt-2 kiu-quiz-widget" onclick="event.stopPropagation()">
  @if($hasPassed)
    <span style="font-size:11px;font-weight:700;color:var(--green);">
      <i class="fa-solid fa-circle-check"></i> Watsinze iki kizamini
    </span>

  @elseif(!$quiz->hasStarted())
    <span class="kiu-quiz-countdown" data-starts="{{ $quiz->starts_at->toIso8601String() }}"
          style="font-size:11px;font-weight:700;color:#994c1d;background:#faece7;padding:4px 10px;border-radius:999px;display:inline-block;">
      <i class="fa-solid fa-clock"></i> <span class="kiu-quiz-countdown-text">Kizatangira vuba</span>
    </span>

  @else
    <form action="{{ route('student.quizzes.start', $quiz->id) }}" method="POST" style="display:inline;">
      @csrf
      <button type="submit" class="kiu-quiz-start-btn" style="font-size:11px;font-weight:700;color:#fff;background:var(--gold-light);border:none;padding:5px 12px;border-radius:999px;cursor:pointer;transition:transform .2s var(--ease-spring), filter .2s;">
        <i class="fa-solid fa-list-check"></i> Tangira Ikizamini
        @if($quiz->duration_minutes) ({{ $quiz->duration_minutes }} min) @endif
      </button>
    </form>
  @endif
</div>
