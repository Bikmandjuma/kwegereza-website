@extends('Guest.cover')
@section('content')

<style>

/* PAGE BACKGROUND (same as books) */
.section{
  padding:30px 0;
  background:#f6f8f7;
}

/* CONTAINER */
.container{
  max-width:1100px;
  margin:auto;
  padding:0 15px;
}

/* TITLE */
.section-title{
  text-align:center;
  margin-bottom:20px;
}

.section-title h2{
  font-size:22px;
  color:#0b3d2e;
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
  max-width:420px;
  padding:12px 16px;
  border-radius:30px;
  border:1px solid #ddd;
  outline:none;
  background:white;
  box-shadow:0 2px 10px rgba(0,0,0,0.05);
}

/* GRID (same as books) */
.grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
  gap:20px;
  margin-top:20px;
}

/* CARD (MATCH BOOK STYLE) */
.card{
  background:#fff;
  border-radius:14px;
  padding:18px;
  box-shadow:0 4px 15px #eee;
  transition:0.3s;
  position:relative;
}

.card:hover{
  transform:translateY(-6px);
  box-shadow:0 6px 18px #e5e5e5;
}

/* BADGE (NEW STYLE) */
.badge{
  position:absolute;
  top:12px;
  right:12px;
  background:#0b6d20;
  color:white;
  font-size:11px;
  padding:4px 10px;
  border-radius:20px;
}

/* TEXT */
.card h3{
  margin-top:10px;
  font-size:16px;
  color:#0b3d2e;
  font-weight:700;
}

.card p{
  font-size:13px;
  color:#666;
  margin-top:8px;
}

/* LINK BUTTON */
.card a{
  display:inline-block;
  margin-top:10px;
  color:#0b6d20;
  font-weight:700;
  text-decoration:none;
  font-size:13px;
}

/* PAGINATION */
.pagination{
  display:flex;
  justify-content:center;
  align-items:center;
  gap:10px;
  margin-top:25px;
}

.pagination button{
  padding:8px 14px;
  border:none;
  border-radius:8px;
  background:#0b6d20;
  color:white;
  cursor:pointer;
}

.pagination span{
  font-weight:bold;
}

/* HIDE */
.hide{ display:none !important; }

/* EMPTY */
.empty{
  text-align:center;
  margin-top:20px;
  color:#888;
  display:none;
}

</style>
<br>
<section class="section">

<div class="container">

  <!-- TITLE -->
  <div class="section-title">
    <h2>Inyandiko z’Abamenyi</h2>
    <p>Abamenyi b’ingenzi muri Islamu n’inyigisho zabo</p>
  </div>

  <!-- SEARCH -->
  <div class="search-boxx">
    <input type="text" id="searchMain" placeholder="Shakisha inyandiko...">
  </div>

  <!-- GRID -->
  <div class="grid">

    <div class="card">
      <span class="badge">Imam</span>
      <h3>Imam Shafi’i</h3>
      <p>Imam wamenyekanye cyane mu fiqh ya Shafi’i.</p>
      <a href="#">Soma inyandiko</a>
    </div>

    <div class="card">
      <span class="badge">Imam</span>
      <h3>Imam Malik ibn Anas</h3>
      <p>Umushinze madhhab ya Maliki mu Hadith.</p>
      <a href="#">Soma inyandiko</a>
    </div>

    <div class="card">
      <span class="badge">Imam</span>
      <h3>Imam Ahmad ibn Hanbal</h3>
      <p>Uzwi ku kwishingikiriza kuri Hadith.</p>
      <a href="#">Soma inyandiko</a>
    </div>

    <div class="card">
      <span class="badge">Sheikh</span>
      <h3>Sheikh Ibn Baaz</h3>
      <p>Sheikh uzwi ku fatwa n’inyigisho za Tawhid.</p>
      <a href="#">Soma inyandiko</a>
    </div>

    <div class="card">
      <span class="badge">Scholar</span>
      <h3>Ibn al-Qayyim</h3>
      <p>Umunyeshuri wa Ibn Taymiyyah.</p>
      <a href="#">Soma inyandiko</a>
    </div>

  </div>

  <!-- EMPTY -->
  <div class="empty" id="emptyState">
    Nta nyandiko ibonetse 😕
  </div>

  <!-- PAGINATION -->
  <div class="pagination">
    <button onclick="changePage(-1)">Prev</button>
    <span id="pageNum">1</span>
    <button onclick="changePage(1)">Next</button>
  </div>

</div>

</section>

<script>

let currentPage = 1;
const perPage = 4;

const cards = document.querySelectorAll(".card");
const searchInput = document.getElementById("searchMain");
const emptyState = document.getElementById("emptyState");

function getFiltered(){
  let search = searchInput.value.toLowerCase();

  return [...cards].filter(card =>
    card.innerText.toLowerCase().includes(search)
  );
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