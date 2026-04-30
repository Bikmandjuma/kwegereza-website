@extends('Guest.cover')
@section('content')

<style>

/* PAGE */
.section{
  padding:30px 0;
  background:#f6f8f7;
}

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

/* GRID (AMATANGAZO STYLE) */
.books-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
  gap:20px;
  margin-top:20px;
}

/* CARD (MODERN) */
.card{
  background:#fff;
  border-radius:14px;
  padding:18px;
  box-shadow:0 4px 15px #eee;
  transition:0.3s;
  position:relative;
  overflow:hidden;
}

.card:hover{
  transform:translateY(-6px);
  box-shadow:0 6px 18px #e5e5e5;
}

/* BADGE */
.book-badge{
  position:absolute;
  top:12px;
  right:12px;
  background:#0b6d20;
  color:white;
  font-size:11px;
  padding:4px 10px;
  border-radius:20px;
  margin-bottom: 5px;
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

/* BUTTONS */
.book-actions{
  display:flex;
  gap:10px;
  margin-top:12px;
}

.btn-book{
  flex:1;
  text-align:center;
  padding:8px 10px;
  font-size:12px;
  border-radius:20px;
  text-decoration:none;
  font-weight:600;
  transition:0.3s;
}

.btn-book:first-child{
  background:#0b6d20;
  color:white;
}

.btn-book:last-child{
  background:#f1f1f1;
  color:#0b6d20;
}

.btn-book:hover{
  opacity:0.9;
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
  color:#888;
  margin-top:20px;
  display:none;
}

</style>
<br>
<section class="section" id="ibitabo">

  <div class="container">

    <div class="section-title">
      <h2>Ibitabo byacu</h2>
      <p>soma cyangwa ufungure ibitabo bya PDF</p>
    </div>

    <!-- SEARCH -->
    <div class="search-boxx">
      <input type="text" id="searchInput" placeholder="Shakisha igitabo...">
    </div>

    <!-- GRID -->
<div class="books-grid">

  <div class="card">
    <span class="book-badge">Igitabo 1</span>
    <br>
    <h3>ESE BIREMEWE GUSIBA KUMUNSI W'IJUMA</h3>
    <p>Igitabo cya mbere cya PDF cyasobanura neza ibi bibazo mu Islamu.</p>
    <div class="book-actions">
      <a href="igitabo1.pdf" target="_blank" class="btn-book">
        <i class="fas fa-eye"></i> soma igitabo
      </a>
      <a href="igitabo1.pdf" download class="btn-book">
        <i class="fas fa-download"></i> Download
      </a>
    </div>
  </div>

  <div class="card">
    <span class="book-badge">Igitabo 2</span>
    <br>
    <h3>KWIGANA IMIGENZO Y'ABAHAKANYE</h3>
    <p>Igitabo kijyanye n’ingaruka zo kwigana imigenzo y’abahakanye.</p>
    <div class="book-actions">
      <a href="igitabo2.pdf" target="_blank" class="btn-book">
        <i class="fas fa-eye"></i> soma igitabo
      </a>
      <a href="igitabo2.pdf" download class="btn-book">
        <i class="fas fa-download"></i> Download
      </a>
    </div>
  </div>

  <div class="card">
    <span class="book-badge">Igitabo 3</span>
    <br>
    <h3>UMUNSI W'IVUKA RY'INTUMWA</h3>
    <p>Ubusobanuro bw’uyu munsi mu Islamu n’impaka zawo.</p>
    <div class="book-actions">
      <a href="igitabo3.pdf" target="_blank" class="btn-book">
        <i class="fas fa-eye"></i> soma igitabo
      </a>
      <a href="igitabo3.pdf" download class="btn-book">
        <i class="fas fa-download"></i> Download
      </a>
    </div>
  </div>

  <!-- NEW 4 -->
  <div class="card">
    <span class="book-badge">Igitabo 4</span>
    <br>
    <h3>FIQH Y’ISENGESHO</h3>
    <p>Isengesho n’uburyo rikorwa mu buryo bwuzuye bwa Sharia.</p>
    <div class="book-actions">
      <a href="igitabo4.pdf" target="_blank" class="btn-book">
        <i class="fas fa-eye"></i> soma igitabo
      </a>
      <a href="igitabo4.pdf" download class="btn-book">
        <i class="fas fa-download"></i> Download
      </a>
    </div>
  </div>

  <!-- NEW 5 -->
  <div class="card">
    <span class="book-badge">Igitabo 5</span>
    <br>
    <h3>TAWHID</h3>
    <p>Ubusobanuro bwa Tawhid n'ibiyirwanya mu buzima bwa Muslim.</p>
    <div class="book-actions">
      <a href="igitabo5.pdf" target="_blank" class="btn-book">
        <i class="fas fa-eye"></i> soma igitabo
      </a>
      <a href="igitabo5.pdf" download class="btn-book">
        <i class="fas fa-download"></i> Download
      </a>
    </div>
  </div>

  <!-- NEW 6 -->
  <div class="card">
    <span class="book-badge">Igitabo 6</span>
    <br>
    <h3>HADITH Z'INTUMWA</h3>
    <p>Hadith zifasha Muslim mu buzima bwa buri munsi.</p>
    <div class="book-actions">
      <a href="igitabo6.pdf" target="_blank" class="btn-book">
        <i class="fas fa-eye"></i> soma igitabo
      </a>
      <a href="igitabo6.pdf" download class="btn-book">
        <i class="fas fa-download"></i> Download
      </a>
    </div>
  </div>

</div>

    <div class="empty" id="emptyState">
      Nta gitabo cyabonetse 😕
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
const perPage = 3;

const cards = document.querySelectorAll(".card");
const searchInput = document.getElementById("searchInput");
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