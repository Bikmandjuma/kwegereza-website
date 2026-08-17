@extends('Guest.cover')

@section('meta_title', $item->title . ' – Kwegereza Islam Umuryango')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($item->summary ?: $item->content), 155))
@section('meta_image', $item->imageUrl())

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": @json($item->title),
    "author": { "@type": "Organization", "name": "Kwegereza Islam Umuryango" },
    "datePublished": "{{ $item->published_at?->toIso8601String() }}",
    "image": "{{ $item->imageUrl() }}"
}
</script>
@endsection

@section('content')

<style>
:root{
  --kiu-green: #058e48;
  --kiu-green-deep: #094939;
  --kiu-gold-1: #c8a36c;
  --kiu-gold-2: #e2b45f;
  --kiu-cream: #f5ebe2;
}

.iny-show-hero{
  background: linear-gradient(135deg, var(--kiu-green) 0%, var(--kiu-green-deep) 100%);
  padding: 40px 20px 70px;
  text-align:center;
}
.iny-show-hero span.badge{
  display:inline-block; padding:5px 14px; border-radius:999px; font-size:11px; font-weight:800;
  background: var(--kiu-gold-2); color: var(--kiu-green-deep); margin-bottom:14px;
}
.iny-show-hero h1{ color:#fff; font-weight:800; font-size: clamp(22px,4vw,30px); margin-bottom:8px; }
.iny-show-hero p{ color: rgba(255,255,255,0.85); font-size:14px; }

.iny-show-body{ background: linear-gradient(180deg, var(--kiu-gold-1) 0%, var(--kiu-gold-2) 100%); padding: 0 0 60px; }

.iny-show-card{
  max-width: 800px; margin: -34px auto 0; background: var(--kiu-cream); border-radius: 24px;
  padding: 34px; box-shadow: 0 16px 34px rgba(9,73,57,0.25);
}

.iny-show-card img.cover{ width:100%; border-radius:16px; margin-bottom:22px; max-height:320px; object-fit:cover; }

.iny-show-card .content{ color:#2c2c2c; line-height:1.8; font-size:15.5px; white-space:pre-line; }

.iny-show-card a.file-download{
  display:inline-flex; align-items:center; gap:8px; margin-top:22px; padding:12px 20px;
  background: var(--kiu-green-deep); color:#fff; border-radius:14px; font-weight:700; text-decoration:none;
}

.iny-back{
  display:inline-flex; align-items:center; gap:8px; margin: 24px auto 0; padding: 10px 18px;
  background: var(--kiu-cream); color: var(--kiu-green-deep); border-radius:12px; font-weight:700;
  text-decoration:none; box-shadow: 0 6px 14px rgba(9,73,57,0.18);
}
.iny-back-wrap{ text-align:center; }
</style>

<div class="iny-show-hero">
  @if($item->category)
    <span class="badge">{{ $item->category }}</span>
  @endif
  <h1>{{ $item->title }}</h1>
  @if($item->author)
    <p>{{ $item->author }}</p>
  @endif
</div>

<div class="iny-show-body">

  <div class="iny-show-card">

    @if($item->image)
      <img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}" class="cover">
    @endif

    <div class="content">
        {{ $item->content ?: $item->summary }}
    </div>

    @if($item->fileUrl())
      <a href="{{ $item->fileUrl() }}" target="_blank" class="file-download">
        <i class="fa-solid fa-file-arrow-down"></i> Kuraho Dosiye
      </a>
    @endif

  </div>

  <div class="iny-back-wrap">
    <a href="{{ route('guest.inyandiko_zabamenyi') }}" class="iny-back">
      <i class="fa-solid fa-arrow-left"></i> Subira ku nyandiko zose
    </a>
  </div>

</div>

@endsection
