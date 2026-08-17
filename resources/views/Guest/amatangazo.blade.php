@extends('Guest.cover')

@section('meta_title', "Amatangazo y'Amasomo – Kwegereza Islam Umuryango")
@section('meta_description', "Reba amatangazo agezweho ku masomo ya Kwegereza Islam Umuryango — ayo mukanya, asigaye n'ayarangiye.")

@section('content')

<style>

/* ===== Palette sampled directly from the KIU announcement reference image ===== */
/* Local --kiu-* palette removed — page now uses the shared
   brand variables (--green/--gold/--cream/etc.) from
   Guest/assets/style.css, so this page's greens/golds match
   the header, nav, and footer instead of a second, slightly
   different shade. */
.amatangazo-hero{
  background: linear-gradient(135deg, var(--green) 0%, var(--green-dark) 100%);
  padding: 42px 20px 60px;
  text-align: center;
  position: relative;
  overflow: hidden;
}

.amatangazo-hero::before{
  content:"";
  position:absolute; inset:0;
  background-image:
    radial-gradient(circle at 20% 30%, rgba(255,255,255,0.06) 0, transparent 40%),
    radial-gradient(circle at 80% 70%, rgba(255,255,255,0.06) 0, transparent 40%);
}

.amatangazo-hero h1{
  position:relative;
  color:#fff;
  font-weight:800;
  font-size: clamp(22px, 4vw, 32px);
  margin-bottom: 8px;
  letter-spacing: .3px;
}

.amatangazo-hero p{
  position:relative;
  color: rgba(255,255,255,0.85);
  font-size: 14px;
}

.gold-divider{
  height: 4px;
  width: 90px;
  margin: 14px auto 0;
  border-radius: 4px;
  background: linear-gradient(90deg, var(--gold), var(--gold-light));
  position: relative;
}

/* GOLD GRADIENT SECTION BEHIND THE CARDS, like the reference image */
.amatangazo-body{
  background: linear-gradient(180deg, var(--gold) 0%, var(--gold-light) 100%);
  padding: 0 0 50px;
}

.amatangazo-toolbar{
  max-width: 1100px;
  margin: 0 auto;
  padding: 26px 16px 10px;
  display:flex;
  flex-direction: column;
  align-items:center;
  gap:14px;
}

.amatangazo-search{
  width:100%;
  max-width: 420px;
  position:relative;
}

.amatangazo-search input{
  width:100%;
  padding: 12px 18px 12px 42px;
  border-radius: 999px;
  border: none;
  outline:none;
  background: var(--cream);
  box-shadow: 0 6px 18px rgba(9,73,57,0.18);
  font-size:14px;
  color: var(--green-dark);
}

.amatangazo-search i{
  position:absolute; left:16px; top:50%; transform:translateY(-50%);
  color: var(--green);
}

.amatangazo-tabs{
  display:flex;
  gap:6px;
  background: rgba(255,255,255,0.35);
  padding:6px;
  border-radius: 999px;
  flex-wrap:wrap;
  justify-content:center;
}

.amatangazo-tab{
  border:none;
  background:transparent;
  padding: 8px 18px;
  border-radius: 999px;
  font-weight:700;
  font-size:13px;
  color: var(--green-dark);
  cursor:pointer;
  transition:.25s;
}

.amatangazo-tab.active{
  background: var(--green-dark);
  color:#fff;
  box-shadow: 0 4px 12px rgba(9,73,57,0.35);
}

.amatangazo-grid{
  max-width: 1100px;
  margin: 24px auto 0;
  padding: 0 16px;
  display:grid;
  grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
  gap: 22px;
}

/* CARD — cream, matching the reference "IMICO IBONEYE" panel */
.amatangazo-card{
  background: var(--cream);
  border-radius: 20px;
  padding: 20px;
  box-shadow: 0 10px 25px rgba(9,73,57,0.18);
  transition: transform .25s, box-shadow .25s;
  position:relative;
  border: 1px solid rgba(255,255,255,0.5);
}

.amatangazo-card:hover{
  transform: translateY(-6px);
  box-shadow: 0 16px 32px rgba(9,73,57,0.25);
}

.amatangazo-card .thumb{
  width:100%;
  height:150px;
  border-radius:14px;
  overflow:hidden;
  margin-bottom:14px;
  background: linear-gradient(135deg, var(--green), var(--green-dark));
  display:flex; align-items:center; justify-content:center;
}

.amatangazo-card .thumb img{
  width:100%; height:100%; object-fit:cover;
}

.amatangazo-card .thumb i{
  font-size:34px;
  color: rgba(255,255,255,0.8);
}

.amatangazo-badge{
  display:inline-block;
  padding: 4px 12px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 800;
  letter-spacing:.5px;
  margin-bottom: 10px;
}

.amatangazo-badge.live{ background:#b30000; color:#fff; }
.amatangazo-badge.upcoming{ background: var(--gold-light); color: var(--green-dark); }
.amatangazo-badge.done{ background: var(--green); color:#fff; }

.amatangazo-card h3{
  color: var(--green-dark);
  font-weight: 800;
  font-size: 17px;
  margin: 4px 0 6px;
}

.amatangazo-card p{
  color: #4a4a4a;
  font-size: 13.5px;
  line-height:1.5;
}

.amatangazo-empty{
  grid-column: 1 / -1;
  text-align:center;
  background: var(--cream);
  border-radius: 20px;
  padding: 40px 20px;
  color: var(--green-dark);
  font-weight:600;
}

.amatangazo-pagination{
  display:flex;
  justify-content:center;
  gap:10px;
  margin-top: 30px;
}

.amatangazo-page-btn{
  padding: 9px 16px;
  border: none;
  background: var(--cream);
  color: var(--green-dark);
  border-radius: 12px;
  cursor:pointer;
  font-weight:700;
  box-shadow: 0 4px 10px rgba(9,73,57,0.15);
}

.amatangazo-page-btn.active{
  background: var(--green-dark);
  color:#fff;
}

.amatangazo-hide{ display:none !important; }

</style>

<div class="amatangazo-hero">
  <h1>Amatangazo y'amasomo</h1>
  <p>Amasomo, live, asigaye n'ayarangiye</p>
  <div class="gold-divider"></div>
</div>

<div class="amatangazo-body">

  <div class="amatangazo-toolbar">

    <div class="amatangazo-search">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" id="searchInput" placeholder="Shakisha itangazo...">
    </div>

    <div class="amatangazo-tabs">
      <button class="amatangazo-tab active" data-tab="all">Yose</button>
      <button class="amatangazo-tab" data-tab="live">Live</button>
      <button class="amatangazo-tab" data-tab="upcoming">Asigaye</button>
      <button class="amatangazo-tab" data-tab="done">Ayarangiye</button>
    </div>

  </div>

  <div class="amatangazo-grid" id="amatangazoGrid">

    @forelse($amatangazo as $item)

      <div class="amatangazo-card" data-status="{{ $item->status }}">

        <div class="thumb">
          @if($item->image)
            <img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}">
          @else
            <i class="fa-solid fa-bullhorn"></i>
          @endif
        </div>

        <span class="amatangazo-badge {{ $item->status }}">
          @if($item->status === 'live') LIVE
          @elseif($item->status === 'upcoming') ASIGAYE
          @else RYARANGIYE
          @endif
        </span>

        <h3>{{ $item->title }}</h3>

        @if($item->presenter)
          <p>{{ $item->presenter }}</p>
        @endif

        @if($item->description)
          <p>{{ \Illuminate\Support\Str::limit($item->description, 90) }}</p>
        @endif

        @auth('student')
          <button
            type="button"
            onclick="kiuToggleFavorite('amatangazo', {{ $item->id }}, this)"
            style="margin-top:8px;background:none;border:none;cursor:pointer;font-size:18px;color:{{ $item->isFavoritedBy(auth('student')->id()) ? '#e11d48' : '#c7c7c7' }};"
            title="Ongeraho ku bikunzwe">
            <i class="fa-{{ $item->isFavoritedBy(auth('student')->id()) ? 'solid' : 'regular' }} fa-heart"></i>
          </button>
        @endauth

      </div>

    @empty

      <div class="amatangazo-empty">
        Nta matangazo arahaboneka ubu. Garuka vuba, hazaba hari amatangazo mashya y'amasomo.
      </div>

    @endforelse

    <div class="amatangazo-empty amatangazo-hide" id="emptyState">
      Nta somo ribonetse 😕
    </div>

  </div>

  <div class="amatangazo-pagination" id="paginationBox">
    <button class="amatangazo-page-btn" onclick="changePage(-1)">‹ Prev</button>
    <button class="amatangazo-page-btn active" id="pageNum">1</button>
    <button class="amatangazo-page-btn" onclick="changePage(1)">Next ›</button>
  </div>

</div>

<script>

let currentTab = "all";
let currentPage = 1;
const perPage = 6;

const cards = document.querySelectorAll(".amatangazo-card");
const searchInput = document.getElementById("searchInput");
const emptyState = document.getElementById("emptyState");
const paginationBox = document.getElementById("paginationBox");

function getFiltered(){
  const search = searchInput.value.toLowerCase();

  return [...cards].filter(card => {
    const matchTab = currentTab === "all" || card.dataset.status === currentTab;
    const matchSearch = card.innerText.toLowerCase().includes(search);
    return matchTab && matchSearch;
  });
}

function render(){
  const filtered = getFiltered();
  const maxPage = Math.ceil(filtered.length / perPage) || 1;

  if (currentPage > maxPage) currentPage = maxPage;
  if (currentPage < 1) currentPage = 1;

  const start = (currentPage - 1) * perPage;
  const end = start + perPage;

  cards.forEach(c => c.classList.add("amatangazo-hide"));
  filtered.slice(start, end).forEach(c => c.classList.remove("amatangazo-hide"));

  if (emptyState) {
    emptyState.style.display = filtered.length === 0 ? "block" : "none";
  }

  paginationBox.style.display = filtered.length > perPage ? "flex" : "none";

  const pageNumEl = document.getElementById("pageNum");
  if (pageNumEl) pageNumEl.innerText = currentPage;
}

document.querySelectorAll(".amatangazo-tab").forEach(tab => {
  tab.addEventListener("click", () => {
    document.querySelectorAll(".amatangazo-tab").forEach(t => t.classList.remove("active"));
    tab.classList.add("active");
    currentTab = tab.dataset.tab;
    currentPage = 1;
    render();
  });
});

if (searchInput) {
  searchInput.addEventListener("input", () => {
    currentPage = 1;
    render();
  });
}

function changePage(dir){
  const filtered = getFiltered();
  const maxPage = Math.ceil(filtered.length / perPage) || 1;

  currentPage += dir;
  if (currentPage < 1) currentPage = 1;
  if (currentPage > maxPage) currentPage = maxPage;

  render();
}

if (cards.length) render();

</script>

@endsection
