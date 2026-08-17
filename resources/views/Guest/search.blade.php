@extends('Guest.cover')
@section('content')

<style>
/* Local --kiu-* palette removed — page now uses the shared
   brand variables (--green/--gold/--cream/etc.) from
   Guest/assets/style.css, so this page's greens/golds match
   the header, nav, and footer instead of a second, slightly
   different shade. */
.search-hero{
  background: linear-gradient(135deg, var(--green) 0%, var(--green-dark) 100%);
  padding: 40px 20px 60px;
  text-align: center;
}
.search-hero h1{ color:#fff; font-weight:800; font-size: clamp(22px,4vw,30px); margin-bottom:16px; }

.search-box{
  max-width: 560px;
  margin: 0 auto;
  position: relative;
}
.search-box input{
  width:100%; padding:14px 20px 14px 46px; border-radius:999px; border:none; outline:none;
  font-size:15px; box-shadow: 0 8px 22px rgba(9,73,57,0.25);
}
.search-box i{ position:absolute; left:18px; top:50%; transform:translateY(-50%); color:#058e48; }

#liveSuggest{
  max-width: 560px; margin: 6px auto 0; background:#fff; border-radius:16px;
  box-shadow: 0 10px 24px rgba(9,73,57,0.2); overflow:hidden; display:none; text-align:left;
}
#liveSuggest a{ display:flex; justify-content:space-between; padding:10px 18px; font-size:13px; color:#094939; text-decoration:none; border-bottom:1px solid #f1f1f1; }
#liveSuggest a:last-child{ border-bottom:none; }
#liveSuggest a span.tag{ font-size:10px; font-weight:800; color:#058e48; text-transform:uppercase; }

.search-body{ background: linear-gradient(180deg, var(--gold) 0%, var(--gold-light) 100%); padding: 30px 16px 60px; }

.search-tabs{
  max-width: 900px; margin: 0 auto 24px; display:flex; gap:8px; flex-wrap:wrap; justify-content:center;
}
.search-tabs a{
  padding:8px 18px; border-radius:999px; font-weight:700; font-size:13px; text-decoration:none;
  background: rgba(255,255,255,0.4); color:#094939;
}
.search-tabs a.active{ background:#094939; color:#fff; }

.search-section{ max-width: 900px; margin: 0 auto 26px; }
.search-section h2{ color:#094939; font-weight:800; font-size:16px; margin-bottom:12px; }

.search-card{
  background: var(--cream); border-radius:16px; padding:16px 18px; margin-bottom:12px;
  box-shadow: 0 6px 16px rgba(9,73,57,0.15); display:flex; justify-content:space-between; align-items:center; gap:12px;
}
.search-card h3{ color:#094939; font-size:15px; font-weight:700; margin-bottom:2px; }
.search-card p{ color:#666; font-size:13px; }
.search-card a.go{
  background:#058e48; color:#fff; padding:8px 16px; border-radius:10px; font-size:13px; font-weight:700; text-decoration:none; white-space:nowrap;
}

.search-empty{
  text-align:center; background: var(--cream); border-radius:20px; padding:40px 20px; color:#094939; font-weight:600;
  max-width:900px; margin:0 auto;
}
</style>

<div class="search-hero">
  <h1>Shakisha kuri Kwegereza Islam Umuryango</h1>

  <form method="GET" action="{{ route('guest.search') }}" class="search-box">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input type="text" id="searchInput" name="q" value="{{ $query }}" placeholder="Shakisha isomo, igitabo, inyandiko, itangazo cyangwa umwarimu...">
  </form>

  <div id="liveSuggest"></div>
</div>

<div class="search-body">

  <div class="search-tabs">
    <a href="{{ route('guest.search', ['q' => $query, 'type' => 'all']) }}" class="{{ $type === 'all' ? 'active' : '' }}">Byose</a>
    <a href="{{ route('guest.search', ['q' => $query, 'type' => 'darsat']) }}" class="{{ $type === 'darsat' ? 'active' : '' }}">Amasomo</a>
    <a href="{{ route('guest.search', ['q' => $query, 'type' => 'books']) }}" class="{{ $type === 'books' ? 'active' : '' }}">Ibitabo</a>
    <a href="{{ route('guest.search', ['q' => $query, 'type' => 'inyandiko']) }}" class="{{ $type === 'inyandiko' ? 'active' : '' }}">Inyandiko</a>
    <a href="{{ route('guest.search', ['q' => $query, 'type' => 'amatangazo']) }}" class="{{ $type === 'amatangazo' ? 'active' : '' }}">Amatangazo</a>
    <a href="{{ route('guest.search', ['q' => $query, 'type' => 'teachers']) }}" class="{{ $type === 'teachers' ? 'active' : '' }}">Abarimu</a>
  </div>

  @if($query === '')

    <div class="search-empty">Andika ikintu ushaka gushakisha hejuru.</div>

  @elseif($totalCount === 0)

    <div class="search-empty">Nta bisubizo bibonetse kuri "{{ $query }}". Gerageza andika ijambo rindi.</div>

  @else

    @if($darsat->count())
      <div class="search-section">
        <h2>Amasomo ({{ $darsat->count() }})</h2>
        @foreach($darsat as $lesson)
          <div class="search-card">
            <div>
              <h3>{{ $lesson->title }}</h3>
              <p>{{ $lesson->type }} @if($lesson->teacher) · {{ $lesson->teacher->firstname }} {{ $lesson->teacher->lastname }} @endif</p>
            </div>
            <a href="{{ route('guest.teacher-darsa', $lesson->teachers) }}" class="go">Reba</a>
          </div>
        @endforeach
      </div>
    @endif

    @if($books->count())
      <div class="search-section">
        <h2>Ibitabo ({{ $books->count() }})</h2>
        @foreach($books as $book)
          <div class="search-card">
            <div>
              <h3>{{ $book->title }}</h3>
              <p>{{ $book->author ?: 'Kwegereza Islam Umuryango' }} @if($book->category) · {{ $book->category }} @endif</p>
            </div>
            <a href="{{ route('guest.books') }}" class="go">Reba</a>
          </div>
        @endforeach
      </div>
    @endif

    @if($inyandiko->count())
      <div class="search-section">
        <h2>Inyandiko ({{ $inyandiko->count() }})</h2>
        @foreach($inyandiko as $item)
          <div class="search-card">
            <div>
              <h3>{{ $item->title }}</h3>
              <p>{{ $item->category }} @if($item->author) · {{ $item->author }} @endif</p>
            </div>
            <a href="{{ route('guest.inyandiko.show', $item->slug) }}" class="go">Soma</a>
          </div>
        @endforeach
      </div>
    @endif

    @if($amatangazo->count())
      <div class="search-section">
        <h2>Amatangazo ({{ $amatangazo->count() }})</h2>
        @foreach($amatangazo as $item)
          <div class="search-card">
            <div>
              <h3>{{ $item->title }}</h3>
              <p>{{ $item->presenter }}</p>
            </div>
            <a href="{{ route('guest.news') }}" class="go">Reba</a>
          </div>
        @endforeach
      </div>
    @endif

    @if($teachers->count())
      <div class="search-section">
        <h2>Abarimu ({{ $teachers->count() }})</h2>
        @foreach($teachers as $teacher)
          <div class="search-card">
            <div>
              <h3>{{ $teacher->title }} {{ $teacher->firstname }} {{ $teacher->lastname }}</h3>
              <p>{{ $teacher->darsat_count }} amasomo</p>
            </div>
            <a href="{{ route('guest.teacher-darsa', $teacher->id) }}" class="go">Reba</a>
          </div>
        @endforeach
      </div>
    @endif

  @endif

</div>

<script>
const searchInput = document.getElementById('searchInput');
const liveSuggest = document.getElementById('liveSuggest');
let debounceTimer;

searchInput.addEventListener('input', () => {
  clearTimeout(debounceTimer);
  const term = searchInput.value.trim();

  if (term.length < 2) {
    liveSuggest.style.display = 'none';
    return;
  }

  debounceTimer = setTimeout(() => {
    fetch(`{{ route('guest.search.suggest') }}?q=${encodeURIComponent(term)}`)
      .then(r => r.json())
      .then(items => {
        if (!items.length) {
          liveSuggest.style.display = 'none';
          return;
        }

        liveSuggest.innerHTML = items.map(i =>
          `<a href="${i.url}">${i.title} <span class="tag">${i.type}</span></a>`
        ).join('');
        liveSuggest.style.display = 'block';
      })
      .catch(() => { liveSuggest.style.display = 'none'; });
  }, 250);
});

document.addEventListener('click', (e) => {
  if (!liveSuggest.contains(e.target) && e.target !== searchInput) {
    liveSuggest.style.display = 'none';
  }
});
</script>

@endsection
