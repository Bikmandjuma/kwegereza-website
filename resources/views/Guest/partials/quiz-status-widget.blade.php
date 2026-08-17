@php
    $hasPassed = $quiz->hasPassedBy(auth('student')->id());
@endphp

<div class="mt-2 kiu-quiz-widget" onclick="event.stopPropagation()">
  @if($hasPassed)
    <span style="font-size:11px;font-weight:700;color:#058e48;">
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
      <button type="submit" style="font-size:11px;font-weight:700;color:#fff;background:#e2b45f;border:none;padding:5px 12px;border-radius:999px;cursor:pointer;">
        <i class="fa-solid fa-list-check"></i> Tangira Ikizamini
        @if($quiz->duration_minutes) ({{ $quiz->duration_minutes }} min) @endif
      </button>
    </form>
  @endif
</div>
