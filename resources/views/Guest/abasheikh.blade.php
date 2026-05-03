@extends('Guest.cover')
@section('content')

<style>

/* PAGE BACKGROUND */
.page{
  padding:30px 0;
  background:#f6f8f7;
}

/* CONTAINER */
.container{
  max-width:1100px;
  margin:0 auto;
  padding:0 16px;
}

/* HEADER */
.section-label{
  font-size:12px;
  font-weight:600;
  color:#0b3d2e;
  text-transform:uppercase;
  letter-spacing:.08em;
}

h2{
  font-size:20px;
  font-weight:800;
  color:#0b3d2e;
  margin:6px 0;
}

p{
  font-size:13px;
  color:#666;
}

/* SEARCH */
input[type=text]{
  width:100%;
  padding:12px 16px;
  border-radius:999px;
  border:1px solid #ddd;
  outline:none;
  background:#fff;
  box-shadow:0 2px 10px rgba(0,0,0,0.05);
  margin:15px 0;
}

/* FILTERS */
.filter-bar{
  display:flex;
  flex-wrap:wrap;
  gap:8px;
  margin-bottom:15px;
}

.filter-btn{
  border:none;
  background:#fff;
  padding:7px 14px;
  border-radius:999px;
  cursor:pointer;
  font-size:13px;
  font-weight:600;
  color:#444;
  box-shadow:0 2px 8px #eee;
  transition:0.2s;
}

.filter-btn:hover{
  transform:scale(1.05);
}

.filter-btn.active{
  background:#0b6d20;
  color:#fff;
}

/* GRID */
.teacher-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
  gap:16px;
}

/* CARD */
.teacher-card{
  background:#fff;
  border-radius:14px;
  padding:18px;
  box-shadow:0 4px 15px #eee;
  transition:0.3s;
  display:flex;
  flex-direction:column;
  gap:12px;
}

.teacher-card:hover{
  transform:translateY(-6px);
  box-shadow:0 6px 18px #e5e5e5;
}

/* AVATAR */
.avatar{
  width:46px;
  height:46px;
  border-radius:50%;
  background:#0b6d20;
  color:#fff;
  display:flex;
  align-items:center;
  justify-content:center;
  font-weight:600;
}

/* NAME */
.teacher-name{
  font-size:15px;
  font-weight:700;
  color:#0b3d2e;
}

/* BADGE */
.spec-badge{
  display:inline-block;
  background:#e9f7ef;
  color:#0b3d2e;
  border-radius:999px;
  font-size:11px;
  font-weight:600;
  padding:4px 10px;
}

/* STATS */
.stat-row{
  display:flex;
  flex-wrap:wrap;
  gap:6px;
}

.stat{
  font-size:11px;
  color:#666;
  background:#f3f3f3;
  padding:4px 10px;
  border-radius:999px;
}

/* SEARCH */
/*.search-boxx{
  display:flex;
  justify-content:center;
  margin:15px 0;
}

.search-boxx input{
  width:100%;
  max-width:420px;
  padding:12px 16px;
  border-radius:30px;
  border:1px solid #ddd;
  outline:none;
  background:white;
  box-shadow:0 2px 10px rgba(0,0,0,0.05);
}*/

/* BUTTON */
.btn-view{
  display:inline-flex;
  align-items:center;
  gap:6px;
  background:#0b6d20;
  color:#fff;
  padding:8px 14px;
  border-radius:999px;
  font-size:12px;
  text-decoration:none;
  font-weight:600;
  width:fit-content;
}

.btn-view:hover{
  opacity:0.9;
}

/* PAGINATION */
.pager{
  display:flex;
  justify-content:center;
  align-items:center;
  gap:10px;
  margin-top:25px;
  background:#fff;
  padding:8px 12px;
  border-radius:999px;
  box-shadow:0 4px 12px rgba(0,0,0,0.08);
  width:fit-content;
  margin-left:auto;
  margin-right:auto;
}

.pager button{
  background:#0b6d20;
  color:#fff;
  border:none;
  border-radius:999px;
  padding:6px 14px;
  cursor:pointer;
}

/* EMPTY */
.no-result{
  text-align:center;
  padding:30px 0;
  color:#888;
  display:none;
}

/* HIDE */
.hide{ display:none !important; }

</style>

<div class="page">
  <div class="container">

    <div class="section-label">Amasomo</div>
    <h2>Abarimu bacu</h2>
    <p>Hitamo umwarimu urebe amasomo ye</p>

    <!-- SEARCH -->
    <div class="search-boxx">
      <input type="text" id="searchInput" placeholder="Shakisha umwarimu...">
    </div>

    <!-- FILTERS -->
    <div class="filter-bar">
      <button class="filter-btn active" data-spec="all">Bose</button>
      <button class="filter-btn" data-spec="tawhid">Tawhid</button>
      <button class="filter-btn" data-spec="fiqh">Fiqh</button>
      <button class="filter-btn" data-spec="hadith">Hadith</button>
      <button class="filter-btn" data-spec="arabic">Arabic</button>
    </div>

    <!-- GRID -->
    <div class="teacher-grid" id="teacherGrid">

      <div class="teacher-card" data-spec="tawhid fiqh" data-name="sheikh munyaneza ismail Abuu omar">
        <div style="display:flex;gap:12px;align-items:center">
          <div class="avatar">MI</div>
          <div>
            <div class="teacher-name">Sheikh MUNYANEZA ISMAIL ABUU OMAR</div>
            <span class="spec-badge">Tawhid · Fiqh</span>
          </div>
        </div>

        <div class="stat-row">
          <span class="stat">5 Inyigisho</span>
          <span class="stat">3 Video</span>
          <span class="stat">2 Audio</span>
        </div>

        <a href="{{ route('guest.teacher-darsa') }}" class="btn-view" data-name="Sheikh MUNYANEZA ISMAIL">
          Reba amasomo
        </a>
      </div>

      <div class="teacher-card" data-spec="hadith fiqh" data-name="sheikh aboubakar ABUU ABDOULRAHMAN">
        <div style="display:flex;gap:12px;align-items:center">
          <div class="avatar">AB</div>
          <div>
            <div class="teacher-name">Sheikh ABOUBAKAR ABUU ABDOULRAHMAN</div>
            <span class="spec-badge">Hadith · Fiqh</span>
          </div>
        </div>

        <div class="stat-row">
          <span class="stat">8 Inyigisho</span>
          <span class="stat">5 Video</span>
          <span class="stat">3 Audio</span>
        </div>

        <a href="{{ route('guest.teacher-darsa') }}" class="btn-view" data-name="Sheikh ABOUBAKAR">
          Reba amasomo
        </a>
      </div>

      <div class="teacher-card" data-spec="tawhid arabic" data-name="sheikh ndahayo khalid Abuu Muadh">
        <div style="display:flex;gap:12px;align-items:center">
          <div class="avatar">NK</div>
          <div>
            <div class="teacher-name">Sheikh NDAHAYO KHALID ABUU MUADH</div>
            <span class="spec-badge">Tawhid · Arabic</span>
          </div>
        </div>

        <div class="stat-row">
          <span class="stat">6 Inyigisho</span>
          <span class="stat">4 Video</span>
          <span class="stat">2 Audio</span>
        </div>

        <a href="{{ route('guest.teacher-darsa') }}" class="btn-view" data-name="Sheikh NDAHAYO KHALID">
          Reba amasomo
        </a>
      </div>
      
    </div>

    <!-- EMPTY -->
    <div class="no-result" id="noResult">
      Nta mwarimu ubonetse 😕
    </div>

    <!-- PAGINATION -->
    <div class="pager">
      <button onclick="changePage(-1)">Prev</button>
      <span id="pageNum">1</span>
      <button onclick="changePage(1)">Next</button>
    </div>

  </div>
</div>

<script>

let currentPage = 1;
const perPage = 3;

const cards = document.querySelectorAll(".teacher-card");
const searchInput = document.getElementById("searchInput");
const noResult = document.getElementById("noResult");
let currentSpec = "all";

/* FILTER */
function getFiltered(){
  const kw = searchInput.value.toLowerCase();

  return [...cards].filter(c=>{
    const matchSpec = currentSpec === "all" || c.dataset.spec.includes(currentSpec);
    const matchSearch = c.dataset.name.includes(kw);
    return matchSpec && matchSearch;
  });
}

/* RENDER */
function render(){
  const filtered = getFiltered();

  const start = (currentPage - 1) * perPage;
  const end = start + perPage;

  cards.forEach(c => c.classList.add("hide"));
  filtered.slice(start,end).forEach(c => c.classList.remove("hide"));

  noResult.style.display = filtered.length ? "none" : "block";
  document.getElementById("pageNum").innerText = currentPage;
}

/* FILTER BUTTONS */
document.querySelectorAll(".filter-btn").forEach(btn=>{
  btn.addEventListener("click",function(){
    document.querySelector(".filter-btn.active").classList.remove("active");
    this.classList.add("active");
    currentSpec = this.dataset.spec;
    currentPage = 1;
    render();
  });
});

/* SEARCH */
searchInput.addEventListener("input",()=>{
  currentPage = 1;
  render();
});

/* PAGINATION */
function changePage(dir){
  const max = Math.ceil(getFiltered().length / perPage) || 1;
  currentPage = Math.min(Math.max(currentPage + dir, 1), max);
  render();
}

/* INIT */
render();

</script>

@endsection