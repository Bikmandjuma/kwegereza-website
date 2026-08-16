@extends('Guest.cover')

@section('meta_title', "Inyandiko z'Abamenyi – Kwegereza Islam Umuryango")
@section('meta_description', "Menya abamenyi b'ingenzi muri Islamu n'inyigisho zabo — Imam, Sheikh n'abandi, ku rubuga rwa Kwegereza Islam Umuryango.")

@section('content')

<style>
/* Local --kiu-* palette removed — page now uses the shared
   brand variables (--green/--gold/--cream/etc.) from
   Guest/assets/style.css, so this page's greens/golds match
   the header, nav, and footer instead of a second, slightly
   different shade. */
.iny-hero{
  background: linear-gradient(135deg, var(--green) 0%, var(--green-dark) 100%);
  padding: 42px 20px 60px;
  text-align: center;
}
.iny-hero h1{ color:#fff; font-weight:800; font-size: clamp(22px,4vw,32px); margin-bottom:8px; }
.iny-hero p{ color: rgba(255,255,255,0.85); font-size:14px; }
.iny-divider{ height:4px; width:90px; margin:14px auto 0; border-radius:4px;
  background: linear-gradient(90deg, var(--gold), var(--gold-light)); }

.iny-body{ background: linear-gradient(180deg, var(--gold) 0%, var(--gold-light) 100%); padding: 0 0 50px; }

.iny-search{ max-width:420px; margin: 26px auto 0; position:relative; padding: 0 16px; }
.iny-search input{
  width:100%; padding:12px 18px 12px 42px; border-radius:999px; border:none; outline:none;
  background: var(--cream); box-shadow: 0 6px 18px rgba(9,73,57,0.18); font-size:14px; color: var(--green-dark);
}
.iny-search i{ position:absolute; left:32px; top:50%; transform:translateY(-50%); color: var(--green); }

.iny-grid{
  max-width:1100px; margin:24px auto 0; padding:0 16px;
  display:grid; grid-template-columns: repeat(auto-fit, minmax(270px,1fr)); gap:22px;
}

.iny-card{
  background: var(--cream); border-radius:20px; padding:20px;
  box-shadow: 0 10px 25px rgba(9,73,57,0.18); transition: transform .25s, box-shadow .25s;
  display:flex; flex-direction:column;
}
.iny-card:hover{ transform: translateY(-6px); box-shadow: 0 16px 32px rgba(9,73,57,0.25); }

.iny-card .thumb{
  width:100%; height:140px; border-radius:14px; overflow:hidden; margin-bottom:14px;
  background: linear-gradient(135deg, var(--green), var(--green-dark));
  display:flex; align-items:center; justify-content:center;
}
.iny-card .thumb img{ width:100%; height:100%; object-fit:cover; }
.iny-card .thumb i{ font-size:30px; color: rgba(255,255,255,0.8); }

.iny-badge{
  display:inline-block; align-self:flex-start; padding:4px 12px; border-radius:999px;
  font-size:11px; font-weight:800; letter-spacing:.5px; margin-bottom:10px;
  background: var(--gold-light); color: var(--green-dark);
}

.iny-card h3{ color: var(--green-dark); font-weight:800; font-size:17px; margin:4px 0 6px; }
.iny-card p{ color:#4a4a4a; font-size:13.5px; line-height:1.5; margin-bottom:14px; flex-grow:1; }

.iny-card a.read-more{
  display:inline-flex; align-items:center; gap:6px; align-self:flex-start;
  color:#fff; background: var(--green-dark); padding:8px 16px; border-radius:12px;
  font-size:13px; font-weight:700; text-decoration:none;
}

.iny-empty{
  grid-column: 1/-1; text-align:center; background: var(--cream); border-radius:20px;
  padding:40px 20px; color: var(--green-dark); font-weight:600;
}
.iny-hide{ display:none !important; }
</style>

<div class="iny-hero">
  <h1>Inyandiko z'Abamenyi</h1>
  <p>Abamenyi b'ingenzi muri Islamu n'inyigisho zabo</p>
  <div class="iny-divider"></div>
</div>

<div class="iny-body">

  <div class="iny-search">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input type="text" id="inySearch" placeholder="Shakisha inyandiko...">
  </div>

  <div class="iny-grid" id="inyGrid">

    @forelse($inyandiko as $item)
      <div class="iny-card" data-search="{{ strtolower($item->title.' '.$item->author.' '.$item->category) }}">

        <div class="thumb">
          @if($item->image)
            <img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}">
          @else
            <i class="fa-solid fa-pen-nib"></i>
          @endif
        </div>

        @if($item->category)
          <span class="iny-badge">{{ $item->category }}</span>
        @endif

        <h3>{{ $item->title }}</h3>

        @if($item->author)
          <p style="margin-bottom:4px;font-weight:600;color:var(--green-dark)">{{ $item->author }}</p>
        @endif

        @if($item->summary)
          <p>{{ \Illuminate\Support\Str::limit($item->summary, 90) }}</p>
        @endif

        <div style="display:flex;align-items:center;gap:10px;">
          <a href="{{ route('guest.inyandiko.show', $item->slug) }}" class="read-more">
            Soma inyandiko <i class="fa-solid fa-arrow-right"></i>
          </a>

          @auth('student')
            <button
              type="button"
              onclick="kiuToggleFavorite('inyandiko', {{ $item->id }}, this)"
              style="background:none;border:none;cursor:pointer;font-size:18px;color:{{ $item->isFavoritedBy(auth('student')->id()) ? '#e11d48' : '#c7c7c7' }};"
              title="Ongeraho ku bikunzwe">
              <i class="fa-{{ $item->isFavoritedBy(auth('student')->id()) ? 'solid' : 'regular' }} fa-heart"></i>
            </button>
          @endauth
        </div>

      </div>
    @empty
      <div class="iny-empty">Nta nyandiko zirahaboneka ubu. Garuka vuba.</div>
    @endforelse

    <div class="iny-empty iny-hide" id="inyEmptyState">Nta nyandiko ibonetse 😕</div>

  </div>

</div>

<script>
const inySearchInput = document.getElementById('inySearch');
const inyCards = document.querySelectorAll('.iny-card');
const inyEmpty = document.getElementById('inyEmptyState');

if (inySearchInput) {
  inySearchInput.addEventListener('input', () => {
    const term = inySearchInput.value.toLowerCase();
    let visible = 0;

    inyCards.forEach(card => {
      const match = card.dataset.search.includes(term);
      card.classList.toggle('iny-hide', !match);
      if (match) visible++;
    });

    if (inyEmpty) inyEmpty.style.display = (visible === 0 && inyCards.length) ? 'block' : 'none';
  });
}
</script>

@endsection
