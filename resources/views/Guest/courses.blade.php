@extends('Guest.cover')

@section('meta_title', "Amasomo Agenda (Learning Paths) – Kwegereza Islam Umuryango")
@section('meta_description', "Kurikirana urutonde rw'amasomo ya Islamu runoze, uva ku bindi ujya ku bindi, ku rubuga rwa Kwegereza Islam Umuryango.")

@section('content')

<style>
:root{
  --kiu-green: #058e48; --kiu-green-deep: #094939;
  --kiu-gold-1: #c8a36c; --kiu-gold-2: #e2b45f; --kiu-cream: #f5ebe2;
}
.course-hero{ background: linear-gradient(135deg, var(--kiu-green), var(--kiu-green-deep)); padding:40px 20px 60px; text-align:center; }
.course-hero h1{ color:#fff; font-weight:800; font-size:clamp(22px,4vw,30px); margin-bottom:8px; }
.course-hero p{ color:rgba(255,255,255,.85); font-size:14px; }
.course-body{ background:linear-gradient(180deg, var(--kiu-gold-1), var(--kiu-gold-2)); padding:30px 16px 60px; }
.course-grid{ max-width:1000px; margin:0 auto; display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:22px; }
.course-card{ background:var(--kiu-cream); border-radius:20px; overflow:hidden; box-shadow:0 10px 25px rgba(9,73,57,.18); transition:transform .25s; }
.course-card:hover{ transform:translateY(-6px); }
.course-card .thumb{ height:140px; background:linear-gradient(135deg,var(--kiu-green),var(--kiu-green-deep)); display:flex; align-items:center; justify-content:center; }
.course-card .thumb img{ width:100%; height:100%; object-fit:cover; }
.course-card .thumb i{ font-size:34px; color:rgba(255,255,255,.8); }
.course-card .body{ padding:18px; }
.course-card h3{ color:var(--kiu-green-deep); font-weight:800; font-size:16px; margin-bottom:6px; }
.course-card p{ color:#555; font-size:13px; margin-bottom:12px; }
.course-card a.go{ display:inline-block; background:var(--kiu-green-deep); color:#fff; padding:8px 16px; border-radius:10px; font-size:13px; font-weight:700; text-decoration:none; }
.course-empty{ text-align:center; background:var(--kiu-cream); border-radius:20px; padding:40px; color:var(--kiu-green-deep); font-weight:600; max-width:1000px; margin:0 auto; }
</style>

<div class="course-hero">
  <h1>Amasomo Agenda (Learning Paths)</h1>
  <p>Kurikirana urutonde rw'amasomo runoze, uva ku bindi ujya ku bindi</p>
</div>

<div class="course-body">
  <div class="course-grid">
    @forelse($courses as $course)
      <div class="course-card">
        <div class="thumb">
          @if($course->thumbnailUrl())
            <img src="{{ $course->thumbnailUrl() }}">
          @else
            <i class="fa-solid fa-diagram-project"></i>
          @endif
        </div>
        <div class="body">
          <h3>{{ $course->title }}</h3>
          <p>{{ $course->lessons_count }} amasomo</p>
          @if($course->description)
            <p>{{ \Illuminate\Support\Str::limit($course->description, 80) }}</p>
          @endif
          <a href="{{ route('guest.course.show', $course->slug) }}" class="go">Reba isomo rigenda</a>
        </div>
      </div>
    @empty
      <div class="course-empty">Nta masomo agenda arahaboneka ubu. Garuka vuba.</div>
    @endforelse
  </div>
</div>

@endsection
