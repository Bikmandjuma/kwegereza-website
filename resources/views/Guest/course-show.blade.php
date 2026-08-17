@extends('Guest.cover')

@section('meta_title', $course->title . ' – Amasomo Agenda – Kwegereza Islam Umuryango')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($course->description ?: $course->title), 155))
@section('meta_image', $course->thumbnailUrl() ?: asset('Guest/images/logo.png'))

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Course",
    "name": @json($course->title),
    "description": @json(strip_tags($course->description ?: $course->title)),
    "provider": { "@type": "Organization", "name": "Kwegereza Islam Umuryango", "sameAs": "{{ url('/') }}" }
}
</script>
@endsection

@section('content')

<style>
/* Local --kiu-* palette removed — page now uses the shared
   brand variables (--green/--gold/--cream/etc.) from
   Guest/assets/style.css, so this page's greens/golds match
   the header, nav, and footer instead of a second, slightly
   different shade. */
.course-show-hero{ background: linear-gradient(135deg, var(--green), var(--green-dark)); padding:40px 20px 70px; text-align:center; }
.course-show-hero h1{ color:#fff; font-weight:800; font-size:clamp(22px,4vw,30px); margin-bottom:8px; }
.course-show-hero p{ color:rgba(255,255,255,.85); font-size:14px; }
.course-show-body{ background:linear-gradient(180deg, var(--gold), var(--gold-light)); padding:0 0 60px; }
.course-show-card{ max-width:700px; margin:-30px auto 0; background:var(--cream); border-radius:24px; padding:30px; box-shadow:0 16px 34px rgba(9,73,57,.25); }
.course-progress-bar{ height:10px; background:#fff; border-radius:999px; overflow:hidden; margin:14px 0 20px; }
.course-progress-fill{ height:100%; background:var(--green); }
.course-lesson-item{ display:flex; align-items:center; gap:12px; padding:12px 0; border-bottom:1px solid rgba(9,73,57,.1); }
.course-lesson-item:last-child{ border-bottom:none; }
.course-lesson-num{ width:28px; height:28px; border-radius:50%; background:var(--green-dark); color:#fff; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; flex-shrink:0; }
.course-lesson-num.done{ background:var(--green); }
.course-enroll-btn{ display:block; width:100%; text-align:center; background:var(--green-dark); color:#fff; padding:12px; border-radius:14px; font-weight:700; border:none; cursor:pointer; margin-top:20px; text-decoration:none; }
</style>

<div class="course-show-hero">
  <h1>{{ $course->title }}</h1>
  @if($course->description)
    <p>{{ $course->description }}</p>
  @endif
</div>

<div class="course-show-body">
  <div class="course-show-card">

    @auth('student')
      @php $percent = $course->completionPercentFor(auth('student')->id()); @endphp
      <p style="font-size:13px;color:#094939;font-weight:700;">Aho ugeze: {{ $percent }}%</p>
      <div class="course-progress-bar"><div class="course-progress-fill" style="width:{{ $percent }}%"></div></div>
    @endauth

    @foreach($course->lessons as $lesson)
      <div class="course-lesson-item">
        <span class="course-lesson-num {{ auth('student')->check() && $lesson->isCompletedBy(auth('student')->id()) ? 'done' : '' }}">
          @if(auth('student')->check() && $lesson->isCompletedBy(auth('student')->id()))
            <i class="fa-solid fa-check"></i>
          @else
            {{ $loop->iteration }}
          @endif
        </span>
        <div style="flex:1;">
          <p style="font-weight:700;color:#094939;font-size:14px;">{{ $lesson->title }}</p>
          @if($lesson->description)
            <p style="font-size:12px;color:#666;">{{ $lesson->description }}</p>
          @endif

          @php $lessonQuiz = $lesson->quizzes->firstWhere('status', 'published'); @endphp
          @if($lessonQuiz)
            @auth('student')
              <a href="{{ route('student.quizzes.take', $lessonQuiz->id) }}" style="font-size:12px;font-weight:700;color:#e2b45f;">
                <i class="fa-solid fa-list-check"></i> Fata ikizamini
                @if($lessonQuiz->hasPassedBy(auth('student')->id())) <i class="fa-solid fa-circle-check" style="color:#058e48;"></i> @endif
              </a>
            @endauth
          @endif
        </div>

        @auth('student')
          @if($course->isEnrolledBy(auth('student')->id()) && !$lesson->isCompletedBy(auth('student')->id()))
            <button onclick="kiuCompleteLesson({{ $lesson->id }}, this)"
              style="font-size:11px;font-weight:700;padding:6px 12px;border-radius:999px;border:1px solid #058e48;color:#058e48;background:#fff;cursor:pointer;white-space:nowrap;">
              Ryarangiye
            </button>
          @endif
        @endauth
      </div>
    @endforeach

    @auth('student')
      @if(!$course->isEnrolledBy(auth('student')->id()))
        <form action="{{ route('student.courses.enroll', $course->slug) }}" method="POST">
          @csrf
          <button type="submit" class="course-enroll-btn">Iyandikishe muri iri somo rigenda</button>
        </form>
      @endif
    @else
      <a href="{{ route('student.login') }}" class="course-enroll-btn">Injira kugira ngo wiyandikishe</a>
    @endauth

  </div>
</div>

@auth('student')
<script>
function kiuCompleteLesson(lessonId, btn) {
  btn.disabled = true;
  fetch(`/student/courses/lessons/${lessonId}/complete`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json',
    },
  })
    .then(r => r.json())
    .then((data) => {
      if (typeof kiuShowBadgeToast === 'function' && data.newBadges && data.newBadges.length) {
        kiuShowBadgeToast(data.newBadges);
        setTimeout(() => location.reload(), data.newBadges.length * 600 + 300);
      } else {
        location.reload();
      }
    });
}
</script>
@endauth

@endsection
