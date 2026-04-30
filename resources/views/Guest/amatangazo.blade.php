@extends('Guest.cover')
@section('content')

<style>

/* CONTAINER */
.container{
  max-width:1100px;
  margin:auto;
  padding:30px 20px;
}

/* TITLE */
.section-title{
  text-align:center;
  margin-bottom:10px;
}

.section-title h2{
  color:#0b3d2e;
  font-size:22px;
  font-weight:800;
}

.section-title p{
  color:#666;
  font-size:14px;
}

/* SEARCH */
.search-boxx{
  display:flex;
  justify-content:center;
  margin:15px 0;
}

.search-boxx input{
  width:100%;
  max-width:400px;
  padding:10px 15px;
  border-radius:30px;
  border:1px solid #ddd;
  outline:none;
}

/* TABS */
.tabs{
  background:#f6f8f7;
  padding:10px;
  border-radius:999px;
  width:fit-content;
  margin:15px auto;
  box-shadow:0 2px 10px rgba(0,0,0,0.05);
}

.tab{
  border:none;
  padding:8px 14px;
  border-radius:999px;
  cursor:pointer;
  font-weight:600;
  transition:0.3s;
  background:white;
}

.tab:hover{
  transform:scale(1.05);
}

.tab.active{
  background:#0b6d20;
  color:white;
}

/* GRID */
.grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
  gap:20px;
}

/* CARD (FIXED) */
.card{
  background:yellowgreen;
  padding:18px;
  border-radius:14px;
  box-shadow:0 4px 15px #eee;
  transition:0.3s;
  animation:fadeIn 0.3s ease;
}

.card:hover{
  transform:translateY(-5px);
  box-shadow:0 6px 18px #e5e5e5;
}

/* ANIMATION */
@keyframes fadeIn{
  from{opacity:0; transform:translateY(10px);}
  to{opacity:1; transform:translateY(0);}
}

/* BADGES */
.badge{
  display:inline-block;
  padding:4px 10px;
  border-radius:20px;
  font-size:12px;
  font-weight:700;
}

.done{ background:white; color:#0b6d20;box-shadow:0 2px 10px rgba(0,0,0,0.2); }
.upcoming{ background:white; color:#b26a00; box-shadow:0 2px 10px rgba(0,0,0,0.2);}
.live{ background:white; color:#b30000; box-shadow:0 2px 10px rgba(0,0,0,0.2);}

h3{
  margin:10px 0 6px;
  color:#0b3d2e;
}

p{
  font-size:14px;
  color:#555;
}

/* EMPTY STATE */
.empty{
  text-align:center;
  color:#888;
  margin-top:20px;
  display:none;
}

/* PAGINATION */
.pagination{
  display:flex;
  justify-content:center;
  gap:10px;
  margin:25px 0;
}

.page-btn{
  padding:8px 14px;
  border:1px solid #0b6d20;
  background:white;
  color:#0b6d20;
  border-radius:10px;
  cursor:pointer;
}

.page-btn.active{
  background:#0b6d20;
  color:white;
}

.hide{ display:none !important; }

</style>

<div class="container">

  <div class="section-title">
    <h2>Amatangazo y'amasomo</h2>
    <p>Amasomo, live, asigaye n’ayarangiye</p>
  </div>

  <!-- SEARCH -->
  <div class="search-boxx">
    <input type="text" id="searchInput" placeholder="Shakisha isomo...">
  </div>

  <!-- TABS -->
  <div class="tabs">
    <button class="tab active" onclick="setTab('all',event)">Yose</button>
    <button class="tab" onclick="setTab('live',event)">Live</button>
    <button class="tab" onclick="setTab('upcoming',event)">Asigaye</button>
    <button class="tab" onclick="setTab('done',event)">Ayarangiye</button>
  </div>

  <!-- GRID -->
  <div class="grid">

    <div class="card live">
      <span class="badge live">LIVE</span>
      <h3>Hadith</h3>
      <p>Sheikh ABOUBAKAR</p>
    </div>

    <div class="card upcoming">
      <span class="badge upcoming">RISIGAYE</span>
      <h3>Tawhid</h3>
      <p>Aqida lesson</p>
    </div>

    <div class="card upcoming">
      <span class="badge upcoming">RISIGAYE</span>
      <h3>Fiqh Salah</h3>
      <p>Isengesho</p>
    </div>

    <div class="card done">
      <span class="badge done">RYARARANGIYE</span>
      <h3>Qur’an</h3>
      <p>By Sheikh Ndahayo Halid</p>
    </div>

    <div class="card done">
      <span class="badge done">RYARARANGIYE</span>
      <h3>Shafi’i</h3>
      <p>Fiqh class</p>
    </div>

    <div class="empty" id="emptyState">
      Nta somo ribonetse 😕
    </div>

  </div>

  <!-- PAGINATION -->
  <div class="pagination">
    <button class="page-btn" onclick="changePage(-1)">Prev</button>
    <button class="page-btn active" id="pageNum">1</button>
    <button class="page-btn" onclick="changePage(1)">Next</button>
  </div>

</div>

<script>

let currentTab = "all";
let currentPage = 1;
const perPage = 3;

const cards = document.querySelectorAll(".card");
const searchInput = document.getElementById("searchInput");
const emptyState = document.getElementById("emptyState");

function getFiltered(){
  let search = searchInput.value.toLowerCase();

  return [...cards].filter(card=>{
    let matchTab = currentTab === "all" || card.classList.contains(currentTab);
    let matchSearch = card.innerText.toLowerCase().includes(search);
    return matchTab && matchSearch;
  });
}

function render(){
  let filtered = getFiltered();

  let maxPage = Math.ceil(filtered.length / perPage) || 1;

  if(currentPage > maxPage) currentPage = maxPage;
  if(currentPage < 1) currentPage = 1;

  let start = (currentPage - 1) * perPage;
  let end = start + perPage;

  cards.forEach(c => c.classList.add("hide"));

  filtered.slice(start, end).forEach(c => c.classList.remove("hide"));

  emptyState.style.display = filtered.length === 0 ? "block" : "none";

  document.getElementById("pageNum").innerText = currentPage;
}

function setTab(tab, e){
  currentTab = tab;
  currentPage = 1;

  document.querySelectorAll(".tab").forEach(t => t.classList.remove("active"));
  e.target.classList.add("active");

  render();
}

searchInput.addEventListener("input", ()=>{
  currentPage = 1;
  render();
});

function changePage(dir){
  let filtered = getFiltered();
  let maxPage = Math.ceil(filtered.length / perPage) || 1;

  currentPage += dir;

  if(currentPage < 1) currentPage = 1;
  if(currentPage > maxPage) currentPage = maxPage;

  render();
}

render();

</script>

@endsection