@extends('Guest.cover')
@section('content')

<style>

*{
  box-sizing:border-box;
  margin:0;
  padding:0;
}

body{
  font-family:var(--font-sans);
  background:linear-gradient(135deg,#f8fff9,#eef8ff);
}

/* CONTAINER */
.container{
  max-width:1100px;
  margin:auto;
  padding:0 16px;
}

/* PAGE */
.page{
  padding:28px 0;
}

/* PROFILE */
.profile-card{
  background:linear-gradient(135deg,#0b3d2e,#0B6D20);
  color:#fff;
  border-radius:18px;
  padding:22px;
  display:flex;
  align-items:center;
  gap:16px;
  flex-wrap:wrap;
  box-shadow:0 12px 30px rgba(11,61,46,.18);
}

.avatar{
  width:68px;
  height:68px;
  border-radius:50%;
  background:rgba(255,255,255,.18);
  border:2px solid rgba(255,255,255,.25);
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:20px;
  font-weight:bold;
}

.profile-info h2{
  font-size:19px;
  margin-bottom:5px;
}

.profile-info p{
  font-size:13px;
  opacity:.9;
  margin-bottom:10px;
}

.badges{
  display:flex;
  gap:6px;
  flex-wrap:wrap;
}

.badge{
  background:rgba(255,255,255,.12);
  border:1px solid rgba(255,255,255,.18);
  padding:5px 10px;
  border-radius:999px;
  font-size:11px;
}

/* SEARCH */
.search-wrap{
  margin:18px 0 12px;
  position:relative;
}

.search-wrap svg{
  position:absolute;
  left:12px;
  top:50%;
  transform:translateY(-50%);
  color:#0B6D20;
}

#searchInput{
  width:100%;
  padding:12px 14px 12px 38px;
  border:none;
  border-radius:999px;
  background:#fff;
  font-size:14px;
  box-shadow:0 6px 18px rgba(0,0,0,.06);
  outline:none;
}

#searchInput:focus{
  box-shadow:0 0 0 4px rgba(11,109,32,.12);
}

/* FILTERS */
.filters{
  display:flex;
  gap:8px;
  flex-wrap:wrap;
  margin-bottom:14px;
}

.filter-btn{
  border:none;
  padding:8px 14px;
  border-radius:999px;
  background:#fff;
  color:#444;
  cursor:pointer;
  font-size:12px;
  font-weight:600;
  box-shadow:0 4px 12px rgba(0,0,0,.05);
  transition:.2s;
}

.filter-btn:hover{
  transform:translateY(-2px);
}

.filter-btn.active{
  background:linear-gradient(135deg,#0b3d2e,#0B6D20);
  color:#fff;
}

/* HEADER */
.section-header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:12px;
}

.count{
  font-size:13px;
  color:#555;
  font-weight:600;
}

/* GRID */
.lesson-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(230px,1fr));
  gap:14px;
}

/* CARD */
.lesson-card{
  background:#fff;
  border-radius:16px;
  padding:16px;
  box-shadow:0 8px 20px rgba(0,0,0,.2);
  transition:.2s;
}

.lesson-card:hover{
  transform:translateY(-3px);
}

/* ICON */
.icon-wrap{
  width:46px;
  height:46px;
  border-radius:12px;
  display:flex;
  align-items:center;
  justify-content:center;
  margin-bottom:10px;
}

.icon-video{background:#e7fff0;}
.icon-audio{background:#fff4df;}

/* TEXT */
.lesson-card h4{
  font-size:14px;
  color:#111;
  margin-bottom:8px;
}

.lesson-meta{
  display:flex;
  flex-wrap:wrap;
  gap:5px;
}

.type-tag{
  font-size:10px;
  padding:3px 8px;
  border-radius:999px;
  font-weight:700;
}

.tag-video{background:#e7fff0;color:#0B6D20;}
.tag-audio{background:#fff4df;color:#ad6a00;}
.tag-fiqh{background:#ffe9f1;color:#b13c6b;}
.tag-tawhid{background:#ecebff;color:#5146d8;}

.duration{
  font-size:11px;
  color:#777;
}

/* NO RESULT */
.no-result{
  display:none;
  text-align:center;
  padding:25px;
  color:#666;
  font-weight:600;
}

/* PAGER */
.pager{
  display:flex;
  justify-content:center;
  align-items:center;
  gap:6px;
  margin-top:18px;
}

.pager button{
  background:#0B6D20;
  color:#fff;
  border:none;
  border-radius:999px;
  padding:6px 12px;
  cursor:pointer;
}

.pager span{
  font-size:12px;
  font-weight:600;
}

/* ================= MOBILE ================= */
@media(max-width:768px){

  .page{
    padding:16px 12px; /* 🔥 fixes edge touching */
  }

  /* PROFILE */
  .profile-card{
    flex-direction:column;
    text-align:center;
    padding:18px;
  }

  .avatar{
    width:60px;
    height:60px;
    font-size:18px;
  }

  .profile-info h2{
    font-size:16px;
  }

  .profile-info p{
    font-size:12px;
  }

  .badges{
    justify-content:center;
  }

  /* FILTER SCROLL */
  .filters{
    flex-wrap:nowrap;
    overflow-x:auto;
    padding-bottom:6px;
  }

  .filter-btn{
    flex-shrink:0;
  }

  /* LIST STYLE (APP FEEL) */
  .lesson-grid{
    grid-template-columns:1fr;
    gap:12px;
  }

  .lesson-card{
    display:flex;
    gap:12px;
    align-items:flex-start;
    padding:14px;
    border-radius:14px;
  }

  .icon-wrap{
    width:42px;
    height:42px;
    flex-shrink:0;
  }

  .lesson-card h4{
    font-size:13px;
  }

}
</style>
<br>
<!-- <div class="page" > -->
<!-- <div class="page" style="max-width:80%;margin:0 auto;padding:0 16px;display:flex;flex-direction:column;gap:16px"> -->

<div class="page container">

<div class="profile-card">
  <!-- <div class="avatar">MI</div> -->

  <div class="profile-info">
    <h2 id="sheikhTitle">Sheikh Munyaneza Ismail Abuu Omar</h2>
    <p>Umwarimu wa Tawhid na Fiqh</p>

    <div class="badges">
      <div class="badge">5 Inyigisho</div>
      <div class="badge">3 Videos</div>
      <div class="badge">2 Audio</div>
    </div>
  </div>
</div>

<div class="search-wrap">
<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
<circle cx="11" cy="11" r="8"/>
<path d="M21 21l-4.35-4.35"/>
</svg>
<input type="text" id="searchInput" placeholder="Shakisha isomo...">
</div>

<div class="filters">
<button class="filter-btn active" data-type="all">Byose</button>
<button class="filter-btn" data-type="video">Videos</button>
<button class="filter-btn" data-type="audio">Audio</button>
<button class="filter-btn" data-type="fiqh">Fiqh</button>
<button class="filter-btn" data-type="tawhid">Tawhid</button>
</div>

<div class="section-header">
<span class="count" id="countLabel">5 amasomo</span>
</div>

<div class="lesson-grid">

  <div class="lesson-card lesson-item" data-type="video tawhid">
    <div class="icon-wrap icon-video">▶</div>
      <h4>Tawhid y'ibanze</h4>
      <div class="lesson-meta">
      <span class="type-tag tag-video">Video</span>
      <span class="type-tag tag-tawhid">Tawhid</span>
      <span class="duration">42 min</span>
    </div>
  </div>

<div class="lesson-card lesson-item" data-type="audio tawhid">
<div class="icon-wrap icon-audio">♪</div>
<h4>Shirk n'uburyo bwo kuyirinda</h4>
<div class="lesson-meta">
<span class="type-tag tag-audio">Audio</span>
<span class="type-tag tag-tawhid">Tawhid</span>
<span class="duration">35 min</span>
</div>
</div>

<div class="lesson-card lesson-item" data-type="video fiqh">
<div class="icon-wrap icon-video">▶</div>
<h4>Uburemere bw'Iswala</h4>
<div class="lesson-meta">
<span class="type-tag tag-video">Video</span>
<span class="type-tag tag-fiqh">Fiqh</span>
<span class="duration">28 min</span>
</div>
</div>

<div class="lesson-card lesson-item" data-type="audio fiqh">
<div class="icon-wrap icon-audio">♪</div>
<h4>Igisibo cya Ramadhan</h4>
<div class="lesson-meta">
<span class="type-tag tag-audio">Audio</span>
<span class="type-tag tag-fiqh">Fiqh</span>
<span class="duration">31 min</span>
</div>
</div>

<div class="lesson-card lesson-item" data-type="video tawhid">
<div class="icon-wrap icon-video">▶</div>
<h4>Uburemere bw'Aqida</h4>
<div class="lesson-meta">
<span class="type-tag tag-video">Video</span>
<span class="type-tag tag-tawhid">Tawhid</span>
<span class="duration">50 min</span>
</div>
</div>

</div>

<div class="pager">
  <button onclick="changePage(-1)">←</button>
  <span>Page <strong id="pageNum">1</strong></span>
  <button onclick="changePage(1)">→</button>
</div>
<div class="no-result" id="noResult">Nta somo ribonetse</div>

</div>

<br>
<script>
document.addEventListener("DOMContentLoaded", function () {

  const filters = document.querySelectorAll(".filter-btn");
  const items = document.querySelectorAll(".lesson-item");
  const search = document.getElementById("searchInput");
  const noResult = document.getElementById("noResult");
  const countLabel = document.getElementById("countLabel");
  const sheikhTitle = document.getElementById("sheikhTitle");

  let current = "all";
  let currentPage = 1;
  const perPage = 4;

  // ✅ GET FILTERED ITEMS
  function getFiltered(){
    const keyword = search.value.toLowerCase().trim();

    return [...items].filter(item => {
      const types = item.dataset.type.toLowerCase();
      const text = item.innerText.toLowerCase();

      const matchType = current === "all" || types.includes(current);
      const matchSearch = keyword === "" || text.includes(keyword);

      return matchType && matchSearch;
    });
  }

  // ✅ MAIN RENDER FUNCTION (FILTER + PAGINATION)
  function render(){
    const filtered = getFiltered();

    const start = (currentPage - 1) * perPage;
    const end = start + perPage;

    // hide all
    items.forEach(item => item.style.display = "none");

    // show only current page
    filtered.slice(start, end).forEach(item => {
      item.style.display = "block";
    });

    // update UI
    countLabel.innerText = filtered.length + " amasomo";
    noResult.style.display = filtered.length ? "none" : "block";
    document.getElementById("pageNum").innerText = currentPage;
  }

  // ✅ FILTER BUTTONS
  filters.forEach(btn => {
    btn.addEventListener("click", function () {
      document.querySelector(".filter-btn.active")?.classList.remove("active");
      this.classList.add("active");

      current = this.dataset.type.toLowerCase();
      currentPage = 1;

      render();
    });
  });

  // ✅ SEARCH
  search.addEventListener("input", function(){
    currentPage = 1;
    render();
  });

  // ✅ PAGINATION (MAKE GLOBAL)
  window.changePage = function(dir){
    const max = Math.ceil(getFiltered().length / perPage) || 1;

    currentPage = Math.min(Math.max(currentPage + dir, 1), max);
    render();
  };

  // ✅ LOAD NAME FROM LOCAL STORAGE
  const savedName = localStorage.getItem("sheikh_name");
  if (savedName && sheikhTitle) {
    sheikhTitle.innerText = savedName;
  }

  // INIT
  render();

});
</script>


@endsection