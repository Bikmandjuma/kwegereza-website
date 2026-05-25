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
    <div class="book-actions" style="align-content: center;align-items: center;">
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

=========================================================================================

@extends('Guest.cover')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

<style>
:root{
  --green:#0b6d20;
  --dark:#0b3d2e;
  --bg:#f4f6f5;
  --card:#fff;
  --border:#e0e6e2;
  --text:#1a2e25;
  --muted:#6b8070;
  --accent:#e8f3eb;
}

*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

/* ── PAGE ── */
.lib-section{
  background:var(--bg);
  min-height:100vh;
  padding:40px 0 60px;
  font-family:'DM Sans',sans-serif;
}

.lib-container{
  max-width:1140px;
  margin:auto;
  padding:0 20px;
}

/* ── HEADER ── */
.lib-header{
  text-align:center;
  margin-bottom:32px;
}

.lib-header h2{
  font-family:'Playfair Display',serif;
  font-size:2rem;
  color:var(--dark);
  letter-spacing:-0.02em;
}

.lib-header p{
  color:var(--muted);
  font-size:14px;
  margin-top:6px;
}

/* ── SEARCH ── */
.lib-search{
  display:flex;
  justify-content:center;
  margin-bottom:28px;
}

.lib-search input{
  width:100%;
  max-width:440px;
  padding:13px 20px;
  border-radius:40px;
  border:1.5px solid var(--border);
  outline:none;
  font-family:'DM Sans',sans-serif;
  font-size:14px;
  background:#fff;
  box-shadow:0 2px 12px rgba(11,61,46,.07);
  transition:.2s;
}

.lib-search input:focus{
  border-color:var(--green);
  box-shadow:0 2px 18px rgba(11,109,32,.12);
}

/* ── GRID ── */
.books-grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
  gap:22px;
}

/* ── CARD ── */
.bk-card{
  background:var(--card);
  border-radius:16px;
  padding:0;
  box-shadow:0 3px 14px rgba(11,61,46,.07);
  overflow:hidden;
  cursor:pointer;
  transition:.25s;
  border:1.5px solid var(--border);
  display:flex;
  flex-direction:column;
}

.bk-card:hover{
  transform:translateY(-5px);
  box-shadow:0 10px 30px rgba(11,61,46,.13);
  border-color:#b6d4be;
}

.bk-thumb{
  width:100%;
  height:158px;
  background:linear-gradient(135deg,#0b3d2e 0%,#0b6d20 100%);
  display:flex;
  align-items:center;
  justify-content:center;
  position:relative;
  overflow:hidden;
}

.bk-thumb::after{
  content:'';
  position:absolute;
  inset:0;
  background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.bk-num{
  background:rgba(255,255,255,.15);
  backdrop-filter:blur(4px);
  border:1px solid rgba(255,255,255,.2);
  color:#fff;
  font-family:'Playfair Display',serif;
  font-size:1.7rem;
  font-weight:900;
  width:68px;
  height:68px;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
  z-index:1;
}

.bk-badge{
  position:absolute;
  top:10px;
  right:10px;
  background:rgba(255,255,255,.18);
  color:#fff;
  font-size:10px;
  font-weight:600;
  padding:3px 9px;
  border-radius:20px;
  letter-spacing:.04em;
  border:1px solid rgba(255,255,255,.3);
  z-index:1;
}

.bk-info{
  padding:16px 18px 18px;
  flex:1;
  display:flex;
  flex-direction:column;
}

.bk-info h3{
  font-size:14px;
  font-weight:700;
  color:var(--text);
  line-height:1.4;
  margin-bottom:6px;
}

.bk-info p{
  font-size:12px;
  color:var(--muted);
  flex:1;
  line-height:1.5;
}

.bk-actions{
  display:flex;
  gap:8px;
  margin-top:14px;
}

.btn-read{
  flex:1;
  text-align:center;
  padding:9px 10px;
  font-size:12px;
  font-weight:600;
  border-radius:24px;
  text-decoration:none;
  cursor:pointer;
  border:none;
  font-family:'DM Sans',sans-serif;
  transition:.2s;
}

.btn-read.primary{
  background:var(--green);
  color:#fff;
}

.btn-read.secondary{
  background:var(--accent);
  color:var(--green);
}

.btn-read:hover{opacity:.88}

/* ── PAGINATION ── */
.lib-pagination{
  display:flex;
  justify-content:center;
  align-items:center;
  gap:10px;
  margin-top:34px;
}

.lib-pagination button{
  padding:9px 18px;
  border:none;
  border-radius:10px;
  background:var(--dark);
  color:#fff;
  font-family:'DM Sans',sans-serif;
  font-weight:600;
  font-size:13px;
  cursor:pointer;
  transition:.2s;
}

.lib-pagination button:hover{background:var(--green)}

.lib-pagination span{
  font-weight:700;
  color:var(--text);
  font-size:14px;
  min-width:60px;
  text-align:center;
}

.lib-empty{
  text-align:center;
  color:var(--muted);
  margin-top:30px;
  font-size:15px;
  display:none;
}

.hide{display:none!important}

/* ══════════════════════════════════
   VIEWER OVERLAY (Scribd-style)
══════════════════════════════════ */
#viewer-overlay{
  display:none;
  position:fixed;
  inset:0;
  z-index:9999;
  background:#1a1a1a;
  flex-direction:column;
}

#viewer-overlay.active{display:flex}

/* TOP BAR */
.vw-topbar{
  display:flex;
  align-items:center;
  gap:14px;
  background:#111;
  padding:0 20px;
  height:58px;
  border-bottom:1px solid #2a2a2a;
  flex-shrink:0;
}

.vw-close{
  background:none;
  border:none;
  color:#aaa;
  font-size:22px;
  cursor:pointer;
  padding:6px;
  line-height:1;
  transition:.2s;
}

.vw-close:hover{color:#fff}

.vw-title{
  flex:1;
  font-family:'Playfair Display',serif;
  font-size:15px;
  color:#eee;
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
}

.vw-likes{
  display:flex;
  align-items:center;
  gap:6px;
  color:#aaa;
  font-size:13px;
}

.vw-btn-top{
  padding:8px 16px;
  border-radius:8px;
  font-size:13px;
  font-weight:600;
  font-family:'DM Sans',sans-serif;
  cursor:pointer;
  border:none;
  transition:.2s;
  text-decoration:none;
  display:inline-flex;
  align-items:center;
  gap:6px;
}

.vw-btn-top.save{
  background:#2a2a2a;
  color:#ccc;
}

.vw-btn-top.save:hover{background:#333;color:#fff}

.vw-btn-top.download{
  background:#e8a020;
  color:#fff;
}

.vw-btn-top.download:hover{opacity:.9}

/* BODY LAYOUT */
.vw-body{
  display:flex;
  flex:1;
  overflow:hidden;
}

/* SIDEBAR - thumbnails */
.vw-sidebar{
  width:180px;
  background:#141414;
  border-right:1px solid #222;
  overflow-y:auto;
  flex-shrink:0;
  padding:12px 8px;
  display:flex;
  flex-direction:column;
  gap:8px;
}

.vw-sidebar::-webkit-scrollbar{width:4px}
.vw-sidebar::-webkit-scrollbar-thumb{background:#333;border-radius:4px}

.vw-thumb-item{
  cursor:pointer;
  border-radius:8px;
  overflow:hidden;
  border:2px solid transparent;
  transition:.15s;
  position:relative;
}

.vw-thumb-item:hover{border-color:#555}
.vw-thumb-item.active{border-color:#e8a020}

.vw-thumb-num{
  position:absolute;
  bottom:4px;
  right:5px;
  background:rgba(0,0,0,.7);
  color:#fff;
  font-size:10px;
  padding:1px 5px;
  border-radius:4px;
}

.vw-thumb-img{
  width:100%;
  aspect-ratio:3/4;
  background:#2a2a2a;
  display:flex;
  align-items:center;
  justify-content:center;
  font-family:'Playfair Display',serif;
  font-size:1.4rem;
  color:#555;
  user-select:none;
}

/* MAIN VIEWER */
.vw-main{
  flex:1;
  display:flex;
  flex-direction:column;
  overflow:hidden;
}

.vw-canvas{
  flex:1;
  overflow-y:auto;
  display:flex;
  align-items:flex-start;
  justify-content:center;
  padding:24px 16px;
  background:#1e1e1e;
}

.vw-canvas::-webkit-scrollbar{width:6px}
.vw-canvas::-webkit-scrollbar-thumb{background:#333;border-radius:4px}

.vw-page-frame{
  background:#fff;
  width:100%;
  max-width:680px;
  min-height:480px;
  border-radius:6px;
  box-shadow:0 6px 40px rgba(0,0,0,.6);
  overflow:hidden;
  position:relative;
}

.vw-page-inner{
  width:100%;
  min-height:480px;
  display:flex;
  align-items:center;
  justify-content:center;
  flex-direction:column;
  gap:12px;
  padding:40px 32px;
  text-align:center;
}

/* Simulated page content */
.vw-page-cover{
  background:linear-gradient(135deg,#0b3d2e 0%,#0b6d20 55%,#145e2e 100%);
  min-height:520px;
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  color:#fff;
  padding:48px 40px;
  text-align:center;
  gap:14px;
}

.vw-page-cover .big-title{
  font-family:'Playfair Display',serif;
  font-size:1.5rem;
  line-height:1.3;
  font-weight:900;
}

.vw-page-cover .sub{
  font-size:13px;
  opacity:.75;
  max-width:340px;
  line-height:1.5;
}

.vw-page-cover .ornament{
  font-size:2.5rem;
  opacity:.4;
}

.vw-page-text{
  padding:48px 40px;
  text-align:left;
  line-height:1.9;
  font-size:14px;
  color:#222;
}

.vw-page-text h4{
  font-family:'Playfair Display',serif;
  font-size:17px;
  color:var(--dark);
  margin-bottom:12px;
  margin-top:18px;
}

.vw-page-text .arabic{
  font-size:18px;
  text-align:right;
  color:#0b6d20;
  font-weight:700;
  margin:10px 0;
  direction:rtl;
}

.vw-page-watermark{
  position:absolute;
  bottom:16px;
  right:20px;
  font-size:10px;
  color:#ccc;
  letter-spacing:.05em;
}

/* BOTTOM NAV */
.vw-bottombar{
  display:flex;
  align-items:center;
  justify-content:center;
  gap:14px;
  background:#111;
  border-top:1px solid #222;
  padding:12px 20px;
  flex-shrink:0;
}

.vw-nav-btn{
  background:#2a2a2a;
  border:none;
  color:#ccc;
  padding:8px 20px;
  border-radius:8px;
  font-family:'DM Sans',sans-serif;
  font-weight:600;
  font-size:13px;
  cursor:pointer;
  transition:.2s;
}

.vw-nav-btn:hover{background:#333;color:#fff}
.vw-nav-btn:disabled{opacity:.35;cursor:not-allowed}

.vw-page-indicator{
  color:#aaa;
  font-size:13px;
  min-width:90px;
  text-align:center;
}

.vw-page-indicator strong{color:#fff}
</style>

<br>
<section class="lib-section" id="ibitabo">
  <div class="lib-container">

    <div class="lib-header">
      <h2>Ibitabo byacu</h2>
      <p>Kanda igitabo usomere ku buryo bwuzuye</p>
    </div>

    <div class="lib-search">
      <input type="text" id="searchInput" placeholder="Shakisha igitabo...">
    </div>

    <div class="books-grid" id="booksGrid">

      <div class="bk-card" onclick="openViewer(0)">
        <div class="bk-thumb">
          <span class="bk-badge">PDF</span>
          <div class="bk-num">01</div>
        </div>
        <div class="bk-info">
          <h3>ESE BIREMEWE GUSIBA KUMUNSI W'IJUMA</h3>
          <p>Igitabo cya mbere cya PDF cyasobanura neza ibi bibazo mu Islamu.</p>
          <div class="bk-actions">
            <button class="btn-read primary" onclick="event.stopPropagation();openViewer(0)">&#128065; Soma</button>
            <a class="btn-read secondary" href="igitabo1.pdf" download onclick="event.stopPropagation()">&#11015; Download</a>
          </div>
        </div>
      </div>

      <div class="bk-card" onclick="openViewer(1)">
        <div class="bk-thumb" style="background:linear-gradient(135deg,#1a3a6b,#2962b8)">
          <span class="bk-badge">PDF</span>
          <div class="bk-num">02</div>
        </div>
        <div class="bk-info">
          <h3>KWIGANA IMIGENZO Y'ABAHAKANYE</h3>
          <p>Igitabo kijyanye n'ingaruka zo kwigana imigenzo y'abahakanye.</p>
          <div class="bk-actions">
            <button class="btn-read primary" onclick="event.stopPropagation();openViewer(1)">&#128065; Soma</button>
            <a class="btn-read secondary" href="igitabo2.pdf" download onclick="event.stopPropagation()">&#11015; Download</a>
          </div>
        </div>
      </div>

      <div class="bk-card" onclick="openViewer(2)">
        <div class="bk-thumb" style="background:linear-gradient(135deg,#4a1060,#8b2fc9)">
          <span class="bk-badge">PDF</span>
          <div class="bk-num">03</div>
        </div>
        <div class="bk-info">
          <h3>UMUNSI W'IVUKA RY'INTUMWA</h3>
          <p>Ubusobanuro bw'uyu munsi mu Islamu n'impaka zawo.</p>
          <div class="bk-actions">
            <button class="btn-read primary" onclick="event.stopPropagation();openViewer(2)">&#128065; Soma</button>
            <a class="btn-read secondary" href="igitabo3.pdf" download onclick="event.stopPropagation()">&#11015; Download</a>
          </div>
        </div>
      </div>

      <div class="bk-card" onclick="openViewer(3)">
        <div class="bk-thumb" style="background:linear-gradient(135deg,#5c2d0a,#b85c1a)">
          <span class="bk-badge">PDF</span>
          <div class="bk-num">04</div>
        </div>
        <div class="bk-info">
          <h3>FIQH Y'ISENGESHO</h3>
          <p>Isengesho n'uburyo rikorwa mu buryo bwuzuye bwa Sharia.</p>
          <div class="bk-actions">
            <button class="btn-read primary" onclick="event.stopPropagation();openViewer(3)">&#128065; Soma</button>
            <a class="btn-read secondary" href="igitabo4.pdf" download onclick="event.stopPropagation()">&#11015; Download</a>
          </div>
        </div>
      </div>

      <div class="bk-card" onclick="openViewer(4)">
        <div class="bk-thumb" style="background:linear-gradient(135deg,#0b3d2e,#0b6d20)">
          <span class="bk-badge">PDF</span>
          <div class="bk-num">05</div>
        </div>
        <div class="bk-info">
          <h3>TAWHID</h3>
          <p>Ubusobanuro bwa Tawhid n'ibiyirwanya mu buzima bwa Muslim.</p>
          <div class="bk-actions">
            <button class="btn-read primary" onclick="event.stopPropagation();openViewer(4)">&#128065; Soma</button>
            <a class="btn-read secondary" href="igitabo5.pdf" download onclick="event.stopPropagation()">&#11015; Download</a>
          </div>
        </div>
      </div>

      <div class="bk-card" onclick="openViewer(5)">
        <div class="bk-thumb" style="background:linear-gradient(135deg,#1a1a3a,#2d2d7a)">
          <span class="bk-badge">PDF</span>
          <div class="bk-num">06</div>
        </div>
        <div class="bk-info">
          <h3>HADITH Z'INTUMWA</h3>
          <p>Hadith zifasha Muslim mu buzima bwa buri munsi.</p>
          <div class="bk-actions">
            <button class="btn-read primary" onclick="event.stopPropagation();openViewer(5)">&#128065; Soma</button>
            <a class="btn-read secondary" href="igitabo6.pdf" download onclick="event.stopPropagation()">&#11015; Download</a>
          </div>
        </div>
      </div>

    </div>

    <div class="lib-empty" id="emptyState">Nta gitabo cyabonetse 😕</div>

    <div class="lib-pagination">
      <button onclick="changePage(-1)">&#8592; Ibanza</button>
      <span id="pageNum">Urupapuro 1</span>
      <button onclick="changePage(1)">Imbere &#8594;</button>
    </div>

  </div>
</section>

<!-- ══════════ VIEWER OVERLAY ══════════ -->
<div id="viewer-overlay">

  <!-- TOP BAR -->
  <div class="vw-topbar">
    <button class="vw-close" onclick="closeViewer()" title="Funga">&#10005;</button>
    <div class="vw-title" id="vw-title">—</div>
    <div class="vw-likes">&#128077; <span id="vw-likes">4</span></div>
    <button class="vw-btn-top save">&#9873; Bika</button>
    <button class="vw-btn-top save">&#8596; Sangira</button>
    <a class="vw-btn-top download" id="vw-download-link" href="#" download>&#11015; Download</a>
  </div>

  <!-- BODY -->
  <div class="vw-body">

    <!-- SIDEBAR -->
    <div class="vw-sidebar" id="vw-sidebar">
      <!-- thumbnails injected by JS -->
    </div>

    <!-- MAIN -->
    <div class="vw-main">
      <div class="vw-canvas">
        <div class="vw-page-frame">
          <div id="vw-page-content"></div>
          <div class="vw-page-watermark" id="vw-watermark">IKINYARWANDA</div>
        </div>
      </div>

      <!-- BOTTOM NAV -->
      <div class="vw-bottombar">
        <button class="vw-nav-btn" id="vw-prev" onclick="viewerNav(-1)">&#8592; Prev</button>
        <div class="vw-page-indicator">Urupapuro <strong id="vw-cur">1</strong> / <strong id="vw-total">1</strong></div>
        <button class="vw-nav-btn" id="vw-next" onclick="viewerNav(1)">Next &#8594;</button>
      </div>
    </div>

  </div>
</div>

<script>
/* ── BOOK DATA ── */
const BOOKS = [
  {
    title: "ESE BIREMEWE GUSIBA KUMUNSI W'IJUMA",
    file: "igitabo1.pdf",
    color: "#0b3d2e",
    color2: "#0b6d20",
    pages: buildPages("ESE BIREMEWE GUSIBA KUMUNSI W'IJUMA",
      "Igitabo cya mbere cya PDF cyasobanura neza ibi bibazo mu Islamu.", 12)
  },
  {
    title: "KWIGANA IMIGENZO Y'ABAHAKANYE",
    file: "igitabo2.pdf",
    color: "#1a3a6b",
    color2: "#2962b8",
    pages: buildPages("KWIGANA IMIGENZO Y'ABAHAKANYE",
      "Igitabo kijyanye n'ingaruka zo kwigana imigenzo y'abahakanye.", 10)
  },
  {
    title: "UMUNSI W'IVUKA RY'INTUMWA",
    file: "igitabo3.pdf",
    color: "#4a1060",
    color2: "#8b2fc9",
    pages: buildPages("UMUNSI W'IVUKA RY'INTUMWA",
      "Ubusobanuro bw'uyu munsi mu Islamu n'impaka zawo.", 8)
  },
  {
    title: "FIQH Y'ISENGESHO",
    file: "igitabo4.pdf",
    color: "#5c2d0a",
    color2: "#b85c1a",
    pages: buildPages("FIQH Y'ISENGESHO",
      "Isengesho n'uburyo rikorwa mu buryo bwuzuye bwa Sharia.", 15)
  },
  {
    title: "TAWHID",
    file: "igitabo5.pdf",
    color: "#0b3d2e",
    color2: "#0b6d20",
    pages: buildPages("TAWHID",
      "Ubusobanuro bwa Tawhid n'ibiyirwanya mu buzima bwa Muslim.", 9)
  },
  {
    title: "HADITH Z'INTUMWA",
    file: "igitabo6.pdf",
    color: "#1a1a3a",
    color2: "#2d2d7a",
    pages: buildPages("HADITH Z'INTUMWA",
      "Hadith zifasha Muslim mu buzima bwa buri munsi.", 11)
  }
];

/* build demo pages for a book */
function buildPages(title, desc, n){
  const pages = [];
  // Page 1: cover
  pages.push({ type:'cover', title, desc });
  // Remaining: text pages
  const texts = [
    { h:"Intangiriro", body:"Bismillahi Rahmani Rahim. Gushimira Imana ni ibisabwa umukristo wese kandi bituruka ku mutima wacu wose. Imana yaremye ijuru n'isi kandi yatugiriye ubuntu butagira ingano.", arabic:"بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيم" },
    { h:"Intandaro", body:"Mu mibereho ya buri munsi, Muslim agomba gukurikiza amategeko ya Islamu neza. Ibyo birimo gusenga inshuro eshanu ku munsi, gusoma Qur'an, no gufasha abafitiye ubukene.", arabic:"إِنَّ اللَّهَ مَعَ الصَّابِرِين" },
    { h:"Ubusobanuro", body:"Urupapuro rwa gatatu rusobanura neza ibyerekeranye n'ikibazo kiganirwaho mu gitabo. Abanyarwanda bose bafite uburenganzira bwo kwiga no gusobanukirwa Islamu neza.", arabic:"وَمَن يَتَّقِ اللَّهَ يَجْعَل لَّهُ مَخْرَجًا" },
    { h:"Inzira ya Sharia", body:"Sharia ni amategeko ya Islamu atuganira uburyo bwo kubana neza. Yubakiwe ku Qur'an no ku Hadith z'Intumwa Muhammad (s.a.w). Igamije kuzana amahoro mu bwoko bw'abantu.", arabic:"وَأَقِيمُوا الصَّلَاةَ وَآتُوا الزَّكَاةَ" },
    { h:"Ibisobanuro Bya Hadith", body:"Hadith ni amajwi n'ibikorwa by'Intumwa Muhammad (s.a.w) byanditswe neza kandi byemejwe n'abahanga mu Hadith. Zifasha abakristo gusobanukirwa imigenzo ya Islamu.", arabic:"خَيْرُكُمْ مَنْ تَعَلَّمَ الْقُرْآنَ وَعَلَّمَهُ" },
    { h:"Umwanzuro", body:"Turangije urupapuro rw'ingenzi mu gitabo. Dusenga Imana iduhe ubugingo bwiza, amahoro n'umunezero mu buzima bwacu bwose. Amina.", arabic:"رَبَّنَا آتِنَا فِي الدُّنْيَا حَسَنَةً" }
  ];
  for(let i=1;i<n;i++){
    pages.push({ type:'text', num:i+1, ...texts[i % texts.length] });
  }
  return pages;
}

/* ── LIBRARY LOGIC ── */
let currentPage = 1;
const perPage = 3;
const cards = document.querySelectorAll(".bk-card");
const searchInput = document.getElementById("searchInput");
const emptyState = document.getElementById("emptyState");

function getFiltered(){
  const s = searchInput.value.toLowerCase();
  return [...cards].filter(c => c.innerText.toLowerCase().includes(s));
}

function render(){
  const filtered = getFiltered();
  const maxPage = Math.ceil(filtered.length / perPage) || 1;
  if(currentPage > maxPage) currentPage = maxPage;
  if(currentPage < 1) currentPage = 1;
  const start = (currentPage-1)*perPage;
  cards.forEach(c=>c.classList.add("hide"));
  filtered.slice(start, start+perPage).forEach(c=>c.classList.remove("hide"));
  emptyState.style.display = filtered.length===0 ? "block" : "none";
  document.getElementById("pageNum").innerText = "Urupapuro "+currentPage;
}

searchInput.addEventListener("input",()=>{ currentPage=1; render(); });
function changePage(d){
  currentPage += d;
  render();
}
render();

/* ── VIEWER ── */
let vBook = null;
let vPage = 0;

function openViewer(idx){
  vBook = BOOKS[idx];
  vPage = 0;
  document.getElementById("vw-title").textContent = vBook.title;
  document.getElementById("vw-download-link").href = vBook.file;
  document.getElementById("vw-total").textContent = vBook.pages.length;
  buildSidebar();
  renderPage();
  document.getElementById("viewer-overlay").classList.add("active");
  document.body.style.overflow = "hidden";
}

function closeViewer(){
  document.getElementById("viewer-overlay").classList.remove("active");
  document.body.style.overflow = "";
}

function buildSidebar(){
  const sb = document.getElementById("vw-sidebar");
  sb.innerHTML = "";
  vBook.pages.forEach((pg,i)=>{
    const item = document.createElement("div");
    item.className = "vw-thumb-item" + (i===0 ? " active" : "");
    item.onclick = ()=>{ vPage=i; renderPage(); };
    item.id = "thumb-"+i;

    const thumb = document.createElement("div");
    thumb.className = "vw-thumb-img";
    if(pg.type==="cover"){
      thumb.style.background = `linear-gradient(135deg,${vBook.color},${vBook.color2})`;
      thumb.style.color = "rgba(255,255,255,.5)";
      thumb.textContent = "✦";
    } else {
      thumb.style.background = "#2a2a2a";
      thumb.style.color = "#555";
      thumb.style.fontSize = "12px";
      thumb.textContent = pg.h||"";
    }

    const num = document.createElement("div");
    num.className = "vw-thumb-num";
    num.textContent = i+1;

    item.appendChild(thumb);
    item.appendChild(num);
    sb.appendChild(item);
  });
}

function renderPage(){
  const pg = vBook.pages[vPage];
  const content = document.getElementById("vw-page-content");
  document.getElementById("vw-cur").textContent = vPage+1;

  // update sidebar active
  document.querySelectorAll(".vw-thumb-item").forEach((el,i)=>{
    el.classList.toggle("active", i===vPage);
  });
  // scroll thumb into view
  const activeThumb = document.getElementById("thumb-"+vPage);
  if(activeThumb) activeThumb.scrollIntoView({ block:"nearest", behavior:"smooth" });

  // buttons
  document.getElementById("vw-prev").disabled = vPage===0;
  document.getElementById("vw-next").disabled = vPage===vBook.pages.length-1;

  if(pg.type==="cover"){
    content.innerHTML = `
      <div class="vw-page-cover" style="background:linear-gradient(135deg,${vBook.color} 0%,${vBook.color2} 55%,${vBook.color} 100%)">
        <div class="ornament">☪</div>
        <div class="big-title">${pg.title}</div>
        <div class="sub">${pg.desc}</div>
        <div class="ornament" style="margin-top:10px">✦</div>
        <div style="font-size:11px;opacity:.5;margin-top:8px;letter-spacing:.08em">IKINYARWANDA</div>
      </div>`;
  } else {
    content.innerHTML = `
      <div class="vw-page-text">
        <h4>${pg.h}</h4>
        <div class="arabic">${pg.arabic}</div>
        <p>${pg.body}</p>
        <p style="margin-top:14px;color:#555">Ubu busobanuro buherekeza abasomi kugira ngo basobanukirwe neza umugambi w'igitabo n'ibigamijwe na Sharia mu buzima bwa buri munsi.</p>
        <p style="margin-top:12px;color:#555">Muslim wese agomba gutahura no gukurikiza amategeko ya Islamu mu bwumvikane bwuzuye kandi mu mutima wuje kwizerana.</p>
      </div>`;
  }
}

function viewerNav(d){
  const np = vPage + d;
  if(np < 0 || np >= vBook.pages.length) return;
  vPage = np;
  renderPage();
  document.querySelector(".vw-canvas").scrollTo({ top:0, behavior:"smooth" });
}

// close on Escape
document.addEventListener("keydown", e=>{
  if(e.key==="Escape") closeViewer();
  if(e.key==="ArrowRight") viewerNav(1);
  if(e.key==="ArrowLeft") viewerNav(-1);
});
</script>

@endsection


=================================================================================

@extends('Guest.cover')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700;800&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root {
  --bg:        #0d0f0e;
  --surface:   #141a16;
  --card:      #1a2320;
  --border:    #2a3830;
  --green:     #22c55e;
  --green-dim: #166534;
  --amber:     #f59e0b;
  --text:      #e8f0eb;
  --muted:     #6b8070;
  --soft:      #a3b8aa;
  --radius:    16px;
  --shadow:    0 8px 40px rgba(0,0,0,.55);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
  background: var(--bg);
  font-family: 'Outfit', sans-serif;
  color: var(--text);
  min-height: 100vh;
}

::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }
::-webkit-scrollbar-thumb:hover { background: var(--green-dim); }

/* PAGE */
.bp-page { padding: 28px 0 60px; }
.bp-wrap { width: 96%; max-width: 1600px; margin: auto; }

/* HEADER */
.bp-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 18px;
  margin-bottom: 28px;
  padding-bottom: 22px;
  border-bottom: 1px solid var(--border);
}

.bp-header-left h1 {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(1.5rem, 3vw, 2.4rem);
  font-weight: 800;
  color: var(--text);
  line-height: 1.2;
  max-width: 680px;
  transition: .4s;
}

.bp-author {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 8px;
  color: var(--muted);
  font-size: 13px;
}

.bp-author-dot {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--green-dim), var(--green));
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  font-weight: 700;
  color: #fff;
  flex-shrink: 0;
}

.bp-meta-pills {
  display: flex;
  gap: 8px;
  margin-top: 12px;
  flex-wrap: wrap;
}

.bp-pill {
  background: var(--card);
  border: 1px solid var(--border);
  color: var(--soft);
  font-size: 11px;
  font-weight: 600;
  padding: 4px 12px;
  border-radius: 20px;
  display: flex;
  align-items: center;
  gap: 5px;
}

.bp-pill.green {
  background: rgba(34,197,94,.12);
  border-color: rgba(34,197,94,.3);
  color: var(--green);
}

.bp-header-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 12px;
}

.bp-search {
  position: relative;
  width: 280px;
}

.bp-search input {
  width: 100%;
  background: var(--card);
  border: 1.5px solid var(--border);
  color: var(--text);
  font-family: 'Outfit', sans-serif;
  font-size: 13px;
  padding: 11px 40px 11px 16px;
  border-radius: 40px;
  outline: none;
  transition: .25s;
}

.bp-search input::placeholder { color: var(--muted); }

.bp-search input:focus {
  border-color: var(--green);
  background: var(--surface);
  box-shadow: 0 0 0 3px rgba(34,197,94,.1);
}

.bp-search i {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--muted);
  font-size: 13px;
  pointer-events: none;
}

.bp-actions { display: flex; gap: 10px; }

.btn-act {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  border-radius: 40px;
  font-family: 'Outfit', sans-serif;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  text-decoration: none;
  transition: .25s;
}

.btn-act.amber {
  background: var(--amber);
  color: #1a1200;
}

.btn-act.amber:hover {
  background: #fbbf24;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(245,158,11,.35);
}

.btn-act.ghost {
  background: var(--card);
  color: var(--soft);
  border: 1.5px solid var(--border);
}

.btn-act.ghost:hover { background: var(--border); color: var(--text); }

/* LAYOUT */
.bp-layout {
  display: grid;
  grid-template-columns: 195px 1fr 320px;
  gap: 20px;
  align-items: start;
}

/* LEFT SIDEBAR */
.bp-sidebar {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 14px 10px;
  max-height: 82vh;
  overflow-y: auto;
  position: sticky;
  top: 20px;
}

.sb-label {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: .1em;
  color: var(--muted);
  text-transform: uppercase;
  margin-bottom: 12px;
  padding: 0 4px;
}

.thumb-item {
  position: relative;
  border-radius: 10px;
  overflow: hidden;
  margin-bottom: 10px;
  cursor: pointer;
  border: 2px solid transparent;
  transition: .2s;
}

.thumb-item:hover { border-color: var(--green-dim); transform: scale(1.02); }
.thumb-item.active { border-color: var(--amber); }

.thumb-item img {
  width: 100%;
  display: block;
  aspect-ratio: 3/4;
  object-fit: cover;
  background: var(--card);
}

.thumb-fallback {
  width: 100%;
  aspect-ratio: 3/4;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  color: var(--border);
  background: var(--card);
}

.thumb-num {
  position: absolute;
  bottom: 5px;
  right: 5px;
  background: rgba(0,0,0,.75);
  color: #fff;
  font-size: 10px;
  font-weight: 600;
  padding: 2px 7px;
  border-radius: 6px;
}

/* CENTER VIEWER */
.bp-viewer {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: var(--shadow);
}

.vw-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 18px;
  border-bottom: 1px solid var(--border);
  background: var(--bg);
  gap: 12px;
}

.vw-indicator {
  background: var(--card);
  border: 1px solid var(--border);
  color: var(--text);
  font-size: 13px;
  font-weight: 600;
  padding: 7px 16px;
  border-radius: 30px;
  white-space: nowrap;
}

.vw-toolbar-actions { display: flex; gap: 8px; }

.vw-icon-btn {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: var(--card);
  border: 1px solid var(--border);
  color: var(--soft);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  transition: .2s;
  text-decoration: none;
}

.vw-icon-btn:hover {
  background: var(--green-dim);
  border-color: var(--green);
  color: #fff;
}

.vw-mode-tabs {
  display: flex;
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 30px;
  padding: 3px;
}

.vw-mode-tab {
  padding: 5px 14px;
  font-size: 12px;
  font-weight: 600;
  border-radius: 24px;
  cursor: pointer;
  color: var(--muted);
  border: none;
  background: transparent;
  font-family: 'Outfit', sans-serif;
  transition: .2s;
}

.vw-mode-tab.active { background: var(--green); color: #fff; }

.vw-stage {
  position: relative;
  min-height: 540px;
  background: #0a0c0b;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.vw-stage img#mainImage {
  max-width: 100%;
  max-height: 72vh;
  object-fit: contain;
  display: block;
  border-radius: 4px;
  transition: opacity .3s;
}

#pdfEmbed {
  width: 100%;
  height: 72vh;
  border: none;
  display: none;
  background: #fff;
}

.vw-nav {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 18px;
  border-top: 1px solid var(--border);
  background: var(--bg);
}

.vw-nav-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 22px;
  border-radius: 10px;
  background: var(--green-dim);
  color: #fff;
  font-size: 13px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  font-family: 'Outfit', sans-serif;
  transition: .2s;
}

.vw-nav-btn:hover { background: var(--green); color: #000; transform: translateY(-1px); }
.vw-nav-btn:disabled { opacity: .3; cursor: not-allowed; transform: none; }

.vw-progress {
  flex: 1;
  height: 4px;
  background: var(--border);
  border-radius: 4px;
  margin: 0 16px;
  overflow: hidden;
}

.vw-progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--green-dim), var(--green));
  border-radius: 4px;
  transition: width .3s;
}

/* RIGHT RECOMMENDED */
.bp-recommended {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 18px 16px;
  max-height: 82vh;
  overflow-y: auto;
  position: sticky;
  top: 20px;
}

.rec-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: 8px;
}

.rec-book {
  display: flex;
  gap: 12px;
  padding: 12px;
  border-radius: 12px;
  cursor: pointer;
  transition: .2s;
  border: 1.5px solid transparent;
  margin-bottom: 10px;
}

.rec-book:hover { background: var(--card); border-color: var(--border); transform: translateX(3px); }
.rec-book.active { background: rgba(34,197,94,.07); border-color: rgba(34,197,94,.25); }

.rec-thumb {
  width: 72px;
  flex-shrink: 0;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid var(--border);
}

.rec-thumb img {
  width: 100%;
  aspect-ratio: 3/4;
  object-fit: cover;
  display: block;
  background: var(--card);
}

.rec-thumb-fallback {
  width: 100%;
  aspect-ratio: 3/4;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: var(--border);
  background: var(--card);
}

.rec-info h4 {
  font-size: 12px;
  font-weight: 700;
  color: var(--text);
  line-height: 1.4;
  margin-bottom: 5px;
}

.rec-info .rec-author { font-size: 11px; color: var(--muted); margin-bottom: 6px; }

.rec-slides {
  font-size: 11px;
  color: var(--green);
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 4px;
  flex-wrap: wrap;
}

.rec-empty {
  text-align: center;
  color: var(--muted);
  padding: 28px 10px;
  display: none;
  font-size: 14px;
}

/* FALLBACK IMG BOX */
#imgFallback {
  display: none;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  color: #333;
  font-family: 'Cormorant Garamond', serif;
  font-size: 2.5rem;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}
.anim-in { animation: fadeIn .3s ease forwards; }

/* RESPONSIVE */
@media (max-width: 1200px) {
  .bp-layout { grid-template-columns: 155px 1fr 280px; gap: 14px; }
}

@media (max-width: 900px) {
  .bp-layout { grid-template-columns: 1fr; }
  .bp-sidebar {
    display: flex;
    flex-direction: row;
    overflow-x: auto;
    overflow-y: hidden;
    max-height: none;
    position: static;
    gap: 10px;
    padding: 10px;
  }
  .thumb-item { min-width: 90px; margin-bottom: 0; flex-shrink: 0; }
  .sb-label { display: none; }
  .bp-recommended { max-height: none; position: static; }
  .bp-header { flex-direction: column; align-items: flex-start; }
  .bp-header-right { align-items: flex-start; width: 100%; }
  .bp-search { width: 100%; }
}

@media (max-width: 600px) {
  .bp-wrap { width: 100%; padding: 0 12px; }
  .bp-header-left h1 { font-size: 1.3rem; }
  .btn-act span { display: none; }
  .vw-nav-btn span { display: none; }
  .vw-toolbar { flex-wrap: wrap; gap: 8px; }
}
</style>

@php
$books = [
  [
    'title'  => "IBISOBANURO BY'IKINYECUMI CYA NYUMA MURI QOR'AN NTAGATIFU",
    'author' => 'Islamic Invitation',
    'views'  => '3,483',
    'slides' => 190,
    'pdf'    => asset('books/book1/book.pdf'),
    'pages'  => [
      asset('books/book1/1.jpg'),
      asset('books/book1/2.jpg'),
      asset('books/book1/3.jpg'),
      asset('books/book1/4.jpg'),
      asset('books/book1/5.jpg'),
    ]
  ],
  [
    'title'  => 'MALAMULO OFUNIKIRA KWA MUSLAMU ALIYEREVUKA',
    'author' => 'Islamic Invitation',
    'views'  => '2,400',
    'slides' => 234,
    'pdf'    => asset('books/book2/book.pdf'),
    'pages'  => [
      asset('books/book2/1.jpg'),
      asset('books/book2/2.jpg'),
      asset('books/book2/3.jpg'),
      asset('books/book2/4.jpg'),
    ]
  ],
  [
    'title'  => 'ISUS A FOST CRESTIN SAU MUSULMAN?',
    'author' => 'Islamic Invitation',
    'views'  => '407',
    'slides' => 37,
    'pdf'    => asset('books/book3/book.pdf'),
    'pages'  => [
      asset('books/book3/1.jpg'),
      asset('books/book3/2.jpg'),
      asset('books/book3/3.jpg'),
    ]
  ],
  [
    'title'  => "VOUS N'AVEZ AUCUN DROIT DE TRADUIRE LES NOMS DE PERSONNES",
    'author' => 'Islamic Invitation',
    'views'  => '1,200',
    'slides' => 12,
    'pdf'    => asset('books/book4/book.pdf'),
    'pages'  => [
      asset('books/book4/1.jpg'),
      asset('books/book4/2.jpg'),
    ]
  ],
  [
    'title'  => 'KAKO SO MOLILI POSLANICI IBRAHIM, MOJZES, JEZUS IN MUHAMMED',
    'author' => 'Islamic Invitation',
    'views'  => '715',
    'slides' => 4,
    'pdf'    => asset('books/book5/book.pdf'),
    'pages'  => [
      asset('books/book5/1.jpg'),
      asset('books/book5/2.jpg'),
      asset('books/book5/3.jpg'),
    ]
  ],
];
@endphp

<section class="bp-page">
<div class="bp-wrap">

  <!-- HEADER -->
  <div class="bp-header">
    <div class="bp-header-left">
      <h1 id="bookTitle">{{ $books[0]['title'] }}</h1>
      <div class="bp-author">
        <div class="bp-author-dot">II</div>
        <span id="bookAuthor">by {{ $books[0]['author'] }}</span>
      </div>
      <div class="bp-meta-pills">
        <span class="bp-pill green"><i class="fas fa-circle" style="font-size:7px"></i> PDF</span>
        <span class="bp-pill"><i class="fas fa-eye"></i> <span id="bookViews">{{ $books[0]['views'] }}</span> views</span>
        <span class="bp-pill"><i class="fas fa-file-alt"></i> <span id="bookSlides">{{ $books[0]['slides'] }}</span> slides</span>
      </div>
    </div>

    <div class="bp-header-right">
      <div class="bp-search">
        <input type="text" id="searchInput" placeholder="Search books...">
        <i class="fas fa-search"></i>
      </div>
      <div class="bp-actions">
        <button class="btn-act ghost" onclick="shareBook()">
          <i class="fas fa-share-alt"></i>
          <span>Share</span>
        </button>
        <a class="btn-act amber" id="headerDownload" href="{{ $books[0]['pdf'] }}" download>
          <i class="fas fa-download"></i>
          <span>Download PDF</span>
        </a>
      </div>
    </div>
  </div>

  <!-- LAYOUT -->
  <div class="bp-layout">

    <!-- LEFT -->
    <div class="bp-sidebar" id="thumbnailContainer">
      <div class="sb-label">Pages</div>
    </div>

    <!-- CENTER -->
    <div class="bp-viewer">

      <div class="vw-toolbar">
        <div class="vw-indicator" id="pageIndicator">1 / 5</div>

        <div class="vw-mode-tabs">
          <button class="vw-mode-tab active" id="tabImg" onclick="setMode('img')">
            <i class="fas fa-image"></i> Slides
          </button>
          <button class="vw-mode-tab" id="tabPdf" onclick="setMode('pdf')">
            <i class="fas fa-file-pdf"></i> PDF
          </button>
        </div>

        <div class="vw-toolbar-actions">
          <button class="vw-icon-btn" title="Like" onclick="this.style.color='#22c55e'">
            <i class="fas fa-thumbs-up"></i>
          </button>
          <button class="vw-icon-btn" title="Bookmark" onclick="this.style.color='#f59e0b'">
            <i class="fas fa-bookmark"></i>
          </button>
          <a class="vw-icon-btn" id="toolbarDownload" href="{{ $books[0]['pdf'] }}" download title="Download">
            <i class="fas fa-download"></i>
          </a>
        </div>
      </div>

      <div class="vw-stage">
        <img src="{{ $books[0]['pages'][0] }}"
             id="mainImage"
             onerror="this.style.display='none';document.getElementById('imgFallback').style.display='flex'"
             alt="Book page">
        <div id="imgFallback">
          📖
          <span style="font-size:13px;font-family:'Outfit',sans-serif;color:#555">Page loading...</span>
        </div>
        <iframe id="pdfEmbed" title="PDF Viewer"></iframe>
      </div>

      <div class="vw-nav">
        <button class="vw-nav-btn" id="btnPrev" onclick="prevPage()">
          <i class="fas fa-arrow-left"></i>
          <span>Previous</span>
        </button>
        <div class="vw-progress">
          <div class="vw-progress-fill" id="progressFill" style="width:20%"></div>
        </div>
        <button class="vw-nav-btn" id="btnNext" onclick="nextPage()">
          <span>Next</span>
          <i class="fas fa-arrow-right"></i>
        </button>
      </div>
    </div>

    <!-- RIGHT -->
    <div class="bp-recommended">
      <div class="rec-title">
        <i class="fas fa-star" style="color:var(--amber);font-size:.9rem"></i>
        Recommended
      </div>
      <div id="recommendedBooks"></div>
      <div class="rec-empty" id="recEmpty">
        <i class="fas fa-search" style="font-size:1.4rem;margin-bottom:8px;display:block"></i>
        Nta gitabo cyabonetse 😕
      </div>
    </div>

  </div>

</div>
</section>

<script>
const BOOKS = @json($books);

let currentBook = 0;
let currentPage = 0;

function loadBook(idx) {
  currentBook = idx;
  currentPage = 0;

  const b = BOOKS[idx];
  const titleEl = document.getElementById('bookTitle');
  titleEl.classList.remove('anim-in');
  void titleEl.offsetWidth;
  titleEl.classList.add('anim-in');

  titleEl.innerText = b.title;
  document.getElementById('bookAuthor').innerText  = 'by ' + b.author;
  document.getElementById('bookViews').innerText   = b.views;
  document.getElementById('bookSlides').innerText  = b.slides;
  document.getElementById('headerDownload').href   = b.pdf;
  document.getElementById('toolbarDownload').href  = b.pdf;

  // reset to slide mode
  setMode('img', true);
  buildThumbs();
  renderPage();
  renderRecommended(document.getElementById('searchInput').value);
}

function buildThumbs() {
  const c = document.getElementById('thumbnailContainer');
  c.innerHTML = '<div class="sb-label">Pages</div>';

  BOOKS[currentBook].pages.forEach((pg, i) => {
    const d = document.createElement('div');
    d.className = 'thumb-item' + (i === 0 ? ' active' : '');
    d.id = 'thumb-' + i;
    d.onclick = () => changePage(i);
    d.innerHTML = `
      <img src="${pg}"
           onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"
           alt="Page ${i+1}">
      <div class="thumb-fallback" style="display:none">☪</div>
      <div class="thumb-num">${i+1}</div>`;
    c.appendChild(d);
  });
}

function changePage(idx) {
  currentPage = idx;
  renderPage();
}

function renderPage() {
  const b     = BOOKS[currentBook];
  const total = b.pages.length;

  document.getElementById('pageIndicator').innerText = `${currentPage + 1} / ${total}`;
  document.getElementById('progressFill').style.width = `${((currentPage + 1) / total) * 100}%`;
  document.getElementById('btnPrev').disabled = currentPage === 0;
  document.getElementById('btnNext').disabled = currentPage === total - 1;

  const img = document.getElementById('mainImage');
  const fb  = document.getElementById('imgFallback');
  img.style.opacity = '0';
  fb.style.display  = 'none';
  img.style.display = 'block';
  img.src = b.pages[currentPage];
  img.onload  = () => { img.style.opacity = '1'; };
  img.onerror = () => { img.style.display = 'none'; fb.style.display = 'flex'; };

  document.querySelectorAll('.thumb-item').forEach((el, i) => {
    el.classList.toggle('active', i === currentPage);
  });

  const at = document.getElementById('thumb-' + currentPage);
  if (at) at.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
}

function nextPage() {
  if (currentPage < BOOKS[currentBook].pages.length - 1) { currentPage++; renderPage(); }
}

function prevPage() {
  if (currentPage > 0) { currentPage--; renderPage(); }
}

function setMode(mode, silent) {
  const img = document.getElementById('mainImage');
  const fb  = document.getElementById('imgFallback');
  const pdf = document.getElementById('pdfEmbed');

  document.getElementById('tabImg').classList.toggle('active', mode === 'img');
  document.getElementById('tabPdf').classList.toggle('active', mode === 'pdf');

  if (mode === 'pdf') {
    img.style.display = 'none';
    fb.style.display  = 'none';
    pdf.style.display = 'block';
    pdf.src = BOOKS[currentBook].pdf + '#toolbar=1&navpanes=1';
  } else {
    pdf.style.display = 'none';
    if (!silent) renderPage();
  }
}

function renderRecommended(q) {
  q = q || '';
  const c   = document.getElementById('recommendedBooks');
  const emp = document.getElementById('recEmpty');
  c.innerHTML = '';

  const filtered = BOOKS.filter(b =>
    b.title.toLowerCase().includes(q.toLowerCase())
  );

  emp.style.display = filtered.length === 0 ? 'block' : 'none';

  filtered.forEach(b => {
    const ri   = BOOKS.indexOf(b);
    const card = document.createElement('div');
    card.className = 'rec-book' + (ri === currentBook ? ' active' : '');
    card.onclick   = () => loadBook(ri);
    card.innerHTML = `
      <div class="rec-thumb">
        <img src="${b.pages[0]}"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"
             alt="${b.title}">
        <div class="rec-thumb-fallback" style="display:none">☪</div>
      </div>
      <div class="rec-info">
        <h4>${b.title}</h4>
        <div class="rec-author">${b.author}</div>
        <div class="rec-slides">
          <i class="fas fa-layer-group"></i> ${b.slides} slides
          &nbsp;·&nbsp;
          <i class="fas fa-eye"></i> ${b.views}
        </div>
      </div>`;
    c.appendChild(card);
  });
}

document.getElementById('searchInput').addEventListener('input', function () {
  renderRecommended(this.value);
});

function shareBook() {
  if (navigator.share) {
    navigator.share({ title: BOOKS[currentBook].title, url: window.location.href });
  } else {
    navigator.clipboard.writeText(window.location.href)
      .then(() => {
        const btn = document.querySelector('.btn-act.ghost');
        btn.innerHTML = '<i class="fas fa-check"></i><span>Copied!</span>';
        setTimeout(() => { btn.innerHTML = '<i class="fas fa-share-alt"></i><span>Share</span>'; }, 2000);
      });
  }
}

document.addEventListener('keydown', e => {
  if (e.key === 'ArrowRight') nextPage();
  if (e.key === 'ArrowLeft')  prevPage();
});

loadBook(0);
</script>

@endsection


==================================================================

@extends('Guest.cover')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700;800&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root {
  --bg:        #f0f4f1;
  --surface:   #ffffff;
  --card:      #f7faf8;
  --border:    #dce8e0;
  --green:     #16a34a;
  --green-dim: #166534;
  --amber:     #f59e0b;
  --text:      #0f2d1c;
  --muted:     #6b8070;
  --soft:      #4a6357;
  --radius:    16px;
  --shadow:    0 4px 24px rgba(11,61,46,.10);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
  background: var(--bg);
  font-family: 'Outfit', sans-serif;
  color: var(--text);
  min-height: 100vh;
}

::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }
::-webkit-scrollbar-thumb:hover { background: var(--green-dim); }

/* PAGE */
.bp-page { padding: 28px 0 60px; }
.bp-wrap { width: 96%; max-width: 1600px; margin: auto; }

/* HEADER */
.bp-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 18px;
  margin-bottom: 28px;
  padding-bottom: 22px;
  border-bottom: 1px solid var(--border);
}

.bp-header-left h1 {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(1.5rem, 3vw, 2.4rem);
  font-weight: 800;
  color: var(--text);
  line-height: 1.2;
  max-width: 680px;
  transition: .4s;
}

.bp-author {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 8px;
  color: var(--muted);
  font-size: 13px;
}

.bp-author-dot {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--green-dim), var(--green));
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  font-weight: 700;
  color: #fff;
  flex-shrink: 0;
}

.bp-meta-pills {
  display: flex;
  gap: 8px;
  margin-top: 12px;
  flex-wrap: wrap;
}

.bp-pill {
  background: var(--card);
  border: 1px solid var(--border);
  color: var(--soft);
  font-size: 11px;
  font-weight: 600;
  padding: 4px 12px;
  border-radius: 20px;
  display: flex;
  align-items: center;
  gap: 5px;
}

.bp-pill.green {
  background: rgba(34,197,94,.12);
  border-color: rgba(34,197,94,.3);
  color: var(--green);
}

.bp-header-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 12px;
}

.bp-search {
  position: relative;
  width: 280px;
}

.bp-search input {
  width: 100%;
  background: var(--card);
  border: 1.5px solid var(--border);
  color: var(--text);
  font-family: 'Outfit', sans-serif;
  font-size: 13px;
  padding: 11px 40px 11px 16px;
  border-radius: 40px;
  outline: none;
  transition: .25s;
}

.bp-search input::placeholder { color: var(--muted); }

.bp-search input:focus {
  border-color: var(--green);
  background: var(--surface);
  box-shadow: 0 0 0 3px rgba(34,197,94,.1);
}

.bp-search i {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--muted);
  font-size: 13px;
  pointer-events: none;
}

.bp-actions { display: flex; gap: 10px; }

.btn-act {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  border-radius: 40px;
  font-family: 'Outfit', sans-serif;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  text-decoration: none;
  transition: .25s;
}

.btn-act.amber {
  background: var(--amber);
  color: #1a1200;
}

.btn-act.amber:hover {
  background: #fbbf24;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(245,158,11,.35);
}

.btn-act.ghost {
  background: var(--card);
  color: var(--soft);
  border: 1.5px solid var(--border);
}

.btn-act.ghost:hover { background: var(--border); color: var(--text); }

/* LAYOUT */
.bp-layout {
  display: grid;
  grid-template-columns: 195px 1fr 320px;
  gap: 20px;
  align-items: start;
}

/* LEFT SIDEBAR */
.bp-sidebar {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 14px 10px;
  max-height: 82vh;
  overflow-y: auto;
  position: sticky;
  top: 20px;
}

.sb-label {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: .1em;
  color: var(--muted);
  text-transform: uppercase;
  margin-bottom: 12px;
  padding: 0 4px;
}

.thumb-item {
  position: relative;
  border-radius: 10px;
  overflow: hidden;
  margin-bottom: 10px;
  cursor: pointer;
  border: 2px solid transparent;
  transition: .2s;
}

.thumb-item:hover { border-color: var(--green-dim); transform: scale(1.02); }
.thumb-item.active { border-color: var(--amber); }

.thumb-item img {
  width: 100%;
  display: block;
  aspect-ratio: 3/4;
  object-fit: cover;
  background: var(--card);
}

.thumb-fallback {
  width: 100%;
  aspect-ratio: 3/4;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  color: var(--border);
  background: var(--card);
}

.thumb-num {
  position: absolute;
  bottom: 5px;
  right: 5px;
  background: rgba(0,0,0,.75);
  color: #fff;
  font-size: 10px;
  font-weight: 600;
  padding: 2px 7px;
  border-radius: 6px;
}

/* CENTER VIEWER */
.bp-viewer {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: var(--shadow);
}

.vw-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 18px;
  border-bottom: 1px solid var(--border);
  background: var(--surface);
  gap: 12px;
}

.vw-indicator {
  background: var(--card);
  border: 1px solid var(--border);
  color: var(--text);
  font-size: 13px;
  font-weight: 600;
  padding: 7px 16px;
  border-radius: 30px;
  white-space: nowrap;
}

.vw-toolbar-actions { display: flex; gap: 8px; }

.vw-icon-btn {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: var(--card);
  border: 1px solid var(--border);
  color: var(--soft);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  transition: .2s;
  text-decoration: none;
}

.vw-icon-btn:hover {
  background: var(--green-dim);
  border-color: var(--green);
  color: #fff;
}

.vw-mode-tabs {
  display: flex;
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 30px;
  padding: 3px;
}

.vw-mode-tab {
  padding: 5px 14px;
  font-size: 12px;
  font-weight: 600;
  border-radius: 24px;
  cursor: pointer;
  color: var(--muted);
  border: none;
  background: transparent;
  font-family: 'Outfit', sans-serif;
  transition: .2s;
}

.vw-mode-tab.active { background: var(--green); color: #fff; }

.vw-stage {
  position: relative;
  min-height: 540px;
  background: #e8f0eb;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.vw-stage img#mainImage {
  max-width: 100%;
  max-height: 72vh;
  object-fit: contain;
  display: block;
  border-radius: 4px;
  transition: opacity .3s;
}

#pdfEmbed {
  width: 100%;
  height: 72vh;
  border: none;
  display: none;
  background: #fff;
}

.vw-nav {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 18px;
  border-top: 1px solid var(--border);
  background: var(--surface);
}

.vw-nav-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 22px;
  border-radius: 10px;
  background: var(--green-dim);
  color: #fff;
  font-size: 13px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  font-family: 'Outfit', sans-serif;
  transition: .2s;
}

.vw-nav-btn:hover { background: var(--green); color: #000; transform: translateY(-1px); }
.vw-nav-btn:disabled { opacity: .3; cursor: not-allowed; transform: none; }

.vw-progress {
  flex: 1;
  height: 4px;
  background: var(--border);
  border-radius: 4px;
  margin: 0 16px;
  overflow: hidden;
}

.vw-progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--green-dim), var(--green));
  border-radius: 4px;
  transition: width .3s;
}

/* RIGHT RECOMMENDED */
.bp-recommended {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 18px 16px;
  max-height: 82vh;
  overflow-y: auto;
  position: sticky;
  top: 20px;
}

.rec-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: 8px;
}

.rec-book {
  display: flex;
  gap: 12px;
  padding: 12px;
  border-radius: 12px;
  cursor: pointer;
  transition: .2s;
  border: 1.5px solid transparent;
  margin-bottom: 10px;
}

.rec-book:hover { background: var(--card); border-color: var(--border); transform: translateX(3px); }
.rec-book.active { background: rgba(34,197,94,.07); border-color: rgba(34,197,94,.25); }

.rec-thumb {
  width: 72px;
  flex-shrink: 0;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid var(--border);
}

.rec-thumb img {
  width: 100%;
  aspect-ratio: 3/4;
  object-fit: cover;
  display: block;
  background: var(--card);
}

.rec-thumb-fallback {
  width: 100%;
  aspect-ratio: 3/4;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: var(--border);
  background: var(--card);
}

.rec-info h4 {
  font-size: 12px;
  font-weight: 700;
  color: var(--text);
  line-height: 1.4;
  margin-bottom: 5px;
}

.rec-info .rec-author { font-size: 11px; color: var(--muted); margin-bottom: 6px; }

.rec-slides {
  font-size: 11px;
  color: var(--green);
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 4px;
  flex-wrap: wrap;
}

.rec-empty {
  text-align: center;
  color: var(--muted);
  padding: 28px 10px;
  display: none;
  font-size: 14px;
}

/* FALLBACK IMG BOX */
#imgFallback {
  display: none;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  color: #999;
  font-family: 'Cormorant Garamond', serif;
  font-size: 2.5rem;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}
.anim-in { animation: fadeIn .3s ease forwards; }

/* RESPONSIVE */
@media (max-width: 1200px) {
  .bp-layout { grid-template-columns: 155px 1fr 280px; gap: 14px; }
}

@media (max-width: 900px) {
  .bp-layout { grid-template-columns: 1fr; }
  .bp-sidebar {
    display: flex;
    flex-direction: row;
    overflow-x: auto;
    overflow-y: hidden;
    max-height: none;
    position: static;
    gap: 10px;
    padding: 10px;
  }
  .thumb-item { min-width: 90px; margin-bottom: 0; flex-shrink: 0; }
  .sb-label { display: none; }
  .bp-recommended { max-height: none; position: static; }
  .bp-header { flex-direction: column; align-items: flex-start; }
  .bp-header-right { align-items: flex-start; width: 100%; }
  .bp-search { width: 100%; }
}

@media (max-width: 600px) {
  .bp-wrap { width: 100%; padding: 0 12px; }
  .bp-header-left h1 { font-size: 1.3rem; }
  .btn-act span { display: none; }
  .vw-nav-btn span { display: none; }
  .vw-toolbar { flex-wrap: wrap; gap: 8px; }
}
</style>

@php
$books = [
  [
    'title'  => "IBISOBANURO BY'IKINYECUMI CYA NYUMA MURI QOR'AN NTAGATIFU",
    'author' => 'Islamic Invitation',
    'views'  => '3,483',
    'slides' => 190,
    'pdf'    => asset('books/book1/book.pdf'),
    'pages'  => [
      asset('books/book1/1.jpg'),
      asset('books/book1/2.jpg'),
      asset('books/book1/3.jpg'),
      asset('books/book1/4.jpg'),
      asset('books/book1/5.jpg'),
    ]
  ],
  [
    'title'  => 'MALAMULO OFUNIKIRA KWA MUSLAMU ALIYEREVUKA',
    'author' => 'Islamic Invitation',
    'views'  => '2,400',
    'slides' => 234,
    'pdf'    => asset('books/book2/book.pdf'),
    'pages'  => [
      asset('books/book2/1.jpg'),
      asset('books/book2/2.jpg'),
      asset('books/book2/3.jpg'),
      asset('books/book2/4.jpg'),
    ]
  ],
  [
    'title'  => 'Artificial intelligence',
    'author' => 'Islamic Invitation',
    'views'  => '407',
    'slides' => 37,
    'pdf'    => asset('Guest/books/Artificial_intelligence.pdf'),
    'pages'  => [
      asset('books/book3/1.jpg'),
      asset('books/book3/2.jpg'),
      asset('books/book3/3.jpg'),
    ]
  ],
  [
    'title'  => "VOUS N'AVEZ AUCUN DROIT DE TRADUIRE LES NOMS DE PERSONNES",
    'author' => 'Islamic Invitation',
    'views'  => '1,200',
    'slides' => 12,
    'pdf'    => asset('books/book4/book.pdf'),
    'pages'  => [
      asset('books/book4/1.jpg'),
      asset('books/book4/2.jpg'),
    ]
  ],
  [
    'title'  => 'KAKO SO MOLILI POSLANICI IBRAHIM, MOJZES, JEZUS IN MUHAMMED',
    'author' => 'Islamic Invitation',
    'views'  => '715',
    'slides' => 4,
    'pdf'    => asset('books/book5/book.pdf'),
    'pages'  => [
      asset('books/book5/1.jpg'),
      asset('books/book5/2.jpg'),
      asset('books/book5/3.jpg'),
    ]
  ],
];
@endphp

<section class="bp-page">
<div class="bp-wrap">

  <!-- HEADER -->
  <div class="bp-header">
    <div class="bp-header-left">
      <h1 id="bookTitle">{{ $books[0]['title'] }}</h1>
      <div class="bp-author">
        <div class="bp-author-dot">II</div>
        <span id="bookAuthor">by {{ $books[0]['author'] }}</span>
      </div>
      <div class="bp-meta-pills">
        <span class="bp-pill green"><i class="fas fa-circle" style="font-size:7px"></i> PDF</span>
        <span class="bp-pill"><i class="fas fa-eye"></i> <span id="bookViews">{{ $books[0]['views'] }}</span> views</span>
        <span class="bp-pill"><i class="fas fa-file-alt"></i> <span id="bookSlides">{{ $books[0]['slides'] }}</span> slides</span>
      </div>
    </div>

    <div class="bp-header-right">
      <div class="bp-search">
        <input type="text" id="searchInput" placeholder="Search books...">
        <i class="fas fa-search"></i>
      </div>
      <div class="bp-actions">
        <button class="btn-act ghost" onclick="shareBook()">
          <i class="fas fa-share-alt"></i>
          <span>Share</span>
        </button>
        <a class="btn-act amber" id="headerDownload" href="{{ $books[0]['pdf'] }}" download>
          <i class="fas fa-download"></i>
          <span>Download PDF</span>
        </a>
      </div>
    </div>
  </div>

  <!-- LAYOUT -->
  <div class="bp-layout">

    <!-- LEFT -->
    <div class="bp-sidebar" id="thumbnailContainer">
      <div class="sb-label">Pages</div>
    </div>

    <!-- CENTER -->
    <div class="bp-viewer">

      <div class="vw-toolbar">
        <div class="vw-indicator" id="pageIndicator">1 / 5</div>

        <div class="vw-mode-tabs">
          <button class="vw-mode-tab active" id="tabImg" onclick="setMode('img')">
            <i class="fas fa-image"></i> Slides
          </button>
          <button class="vw-mode-tab" id="tabPdf" onclick="setMode('pdf')">
            <i class="fas fa-file-pdf"></i> PDF
          </button>
        </div>

        <div class="vw-toolbar-actions">
          <button class="vw-icon-btn" title="Like" onclick="this.style.color='#22c55e'">
            <i class="fas fa-thumbs-up"></i>
          </button>
          <button class="vw-icon-btn" title="Bookmark" onclick="this.style.color='#f59e0b'">
            <i class="fas fa-bookmark"></i>
          </button>
          <a class="vw-icon-btn" id="toolbarDownload" href="{{ $books[0]['pdf'] }}" download title="Download">
            <i class="fas fa-download"></i>
          </a>
        </div>
      </div>

      <div class="vw-stage">
        <img src="{{ $books[0]['pages'][0] }}"
             id="mainImage"
             onerror="this.style.display='none';document.getElementById('imgFallback').style.display='flex'"
             alt="Book page">
        <div id="imgFallback">
          📖
          <span style="font-size:13px;font-family:'Outfit',sans-serif;color:#999">Page loading...</span>
        </div>
        <iframe id="pdfEmbed" title="PDF Viewer"></iframe>
      </div>

      <div class="vw-nav">
        <button class="vw-nav-btn" id="btnPrev" onclick="prevPage()">
          <i class="fas fa-arrow-left"></i>
          <span>Previous</span>
        </button>
        <div class="vw-progress">
          <div class="vw-progress-fill" id="progressFill" style="width:20%"></div>
        </div>
        <button class="vw-nav-btn" id="btnNext" onclick="nextPage()">
          <span>Next</span>
          <i class="fas fa-arrow-right"></i>
        </button>
      </div>
    </div>

    <!-- RIGHT -->
    <div class="bp-recommended">
      <div class="rec-title">
        <i class="fas fa-star" style="color:var(--amber);font-size:.9rem"></i>
        Recommended
      </div>
      <div id="recommendedBooks"></div>
      <div class="rec-empty" id="recEmpty">
        <i class="fas fa-search" style="font-size:1.4rem;margin-bottom:8px;display:block"></i>
        Nta gitabo cyabonetse 😕
      </div>
    </div>

  </div>

</div>
</section>

<script>
const BOOKS = @json($books);

let currentBook = 0;
let currentPage = 0;

function loadBook(idx) {
  currentBook = idx;
  currentPage = 0;

  const b = BOOKS[idx];
  const titleEl = document.getElementById('bookTitle');
  titleEl.classList.remove('anim-in');
  void titleEl.offsetWidth;
  titleEl.classList.add('anim-in');

  titleEl.innerText = b.title;
  document.getElementById('bookAuthor').innerText  = 'by ' + b.author;
  document.getElementById('bookViews').innerText   = b.views;
  document.getElementById('bookSlides').innerText  = b.slides;
  document.getElementById('headerDownload').href   = b.pdf;
  document.getElementById('toolbarDownload').href  = b.pdf;

  // reset to slide mode
  setMode('img', true);
  buildThumbs();
  renderPage();
  renderRecommended(document.getElementById('searchInput').value);
}

function buildThumbs() {
  const c = document.getElementById('thumbnailContainer');
  c.innerHTML = '<div class="sb-label">Pages</div>';

  BOOKS[currentBook].pages.forEach((pg, i) => {
    const d = document.createElement('div');
    d.className = 'thumb-item' + (i === 0 ? ' active' : '');
    d.id = 'thumb-' + i;
    d.onclick = () => changePage(i);
    d.innerHTML = `
      <img src="${pg}"
           onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"
           alt="Page ${i+1}">
      <div class="thumb-fallback" style="display:none">☪</div>
      <div class="thumb-num">${i+1}</div>`;
    c.appendChild(d);
  });
}

function changePage(idx) {
  currentPage = idx;
  renderPage();
}

function renderPage() {
  const b     = BOOKS[currentBook];
  const total = b.pages.length;

  document.getElementById('pageIndicator').innerText = `${currentPage + 1} / ${total}`;
  document.getElementById('progressFill').style.width = `${((currentPage + 1) / total) * 100}%`;
  document.getElementById('btnPrev').disabled = currentPage === 0;
  document.getElementById('btnNext').disabled = currentPage === total - 1;

  const img = document.getElementById('mainImage');
  const fb  = document.getElementById('imgFallback');
  img.style.opacity = '0';
  fb.style.display  = 'none';
  img.style.display = 'block';
  img.src = b.pages[currentPage];
  img.onload  = () => { img.style.opacity = '1'; };
  img.onerror = () => { img.style.display = 'none'; fb.style.display = 'flex'; };

  document.querySelectorAll('.thumb-item').forEach((el, i) => {
    el.classList.toggle('active', i === currentPage);
  });

  const at = document.getElementById('thumb-' + currentPage);
  if (at) at.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
}

function nextPage() {
  if (currentPage < BOOKS[currentBook].pages.length - 1) { currentPage++; renderPage(); }
}

function prevPage() {
  if (currentPage > 0) { currentPage--; renderPage(); }
}

function setMode(mode, silent) {
  const img = document.getElementById('mainImage');
  const fb  = document.getElementById('imgFallback');
  const pdf = document.getElementById('pdfEmbed');

  document.getElementById('tabImg').classList.toggle('active', mode === 'img');
  document.getElementById('tabPdf').classList.toggle('active', mode === 'pdf');

  if (mode === 'pdf') {
    img.style.display = 'none';
    fb.style.display  = 'none';
    pdf.style.display = 'block';
    pdf.src = BOOKS[currentBook].pdf + '#toolbar=1&navpanes=1';
  } else {
    pdf.style.display = 'none';
    if (!silent) renderPage();
  }
}

function renderRecommended(q) {
  q = q || '';
  const c   = document.getElementById('recommendedBooks');
  const emp = document.getElementById('recEmpty');
  c.innerHTML = '';

  const filtered = BOOKS.filter(b =>
    b.title.toLowerCase().includes(q.toLowerCase())
  );

  emp.style.display = filtered.length === 0 ? 'block' : 'none';

  filtered.forEach(b => {
    const ri   = BOOKS.indexOf(b);
    const card = document.createElement('div');
    card.className = 'rec-book' + (ri === currentBook ? ' active' : '');
    card.onclick   = () => loadBook(ri);
    card.innerHTML = `
      <div class="rec-thumb">
        <img src="${b.pages[0]}"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"
             alt="${b.title}">
        <div class="rec-thumb-fallback" style="display:none">☪</div>
      </div>
      <div class="rec-info">
        <h4>${b.title}</h4>
        <div class="rec-author">${b.author}</div>
        <div class="rec-slides">
          <i class="fas fa-layer-group"></i> ${b.slides} slides
          &nbsp;·&nbsp;
          <i class="fas fa-eye"></i> ${b.views}
        </div>
      </div>`;
    c.appendChild(card);
  });
}

document.getElementById('searchInput').addEventListener('input', function () {
  renderRecommended(this.value);
});

function shareBook() {
  if (navigator.share) {
    navigator.share({ title: BOOKS[currentBook].title, url: window.location.href });
  } else {
    navigator.clipboard.writeText(window.location.href)
      .then(() => {
        const btn = document.querySelector('.btn-act.ghost');
        btn.innerHTML = '<i class="fas fa-check"></i><span>Copied!</span>';
        setTimeout(() => { btn.innerHTML = '<i class="fas fa-share-alt"></i><span>Share</span>'; }, 2000);
      });
  }
}

document.addEventListener('keydown', e => {
  if (e.key === 'ArrowRight') nextPage();
  if (e.key === 'ArrowLeft')  prevPage();
});

loadBook(0);
</script>

@endsection