@extends('Guest.cover')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700;800&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

<style>
:root {
  --bg: #f0f4f1;
  --surface: #ffffff;
  --card: #f7faf8;
  --border: #dce8e0;
  --green: #16a34a;
  --green-dim: #166534;
  --amber: #f59e0b;
  --text: #0f2d1c;
  --muted: #6b8070;
  --soft: #4a6357;
  --radius: 16px;
  --shadow: 0 4px 24px rgba(11,61,46,.10);
}

*, *::before, *::after{
  box-sizing:border-box;
  margin:0;
  padding:0;
}

body{
  background:var(--bg);
  font-family:'Outfit',sans-serif;
  color:var(--text);
}

.bp-page{
  padding:28px 0 60px;
}

.bp-wrap{
  width:96%;
  max-width:1600px;
  margin:auto;
}

.bp-header{
  display:flex;
  justify-content:space-between;
  flex-wrap:wrap;
  gap:18px;
  margin-bottom:28px;
  padding-bottom:22px;
  border-bottom:1px solid var(--border);
}

.bp-header-left h1{
  font-family:'Cormorant Garamond',serif;
  font-size:clamp(1.5rem,3vw,2.4rem);
  font-weight:800;
}

.bp-author{
  display:flex;
  align-items:center;
  gap:8px;
  margin-top:8px;
  color:var(--muted);
  font-size:13px;
}

.bp-author-dot{
  width:28px;
  height:28px;
  border-radius:50%;
  background:linear-gradient(135deg,var(--green-dim),var(--green));
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:10px;
  color:#fff;
}

.bp-meta-pills{
  display:flex;
  gap:8px;
  margin-top:12px;
  flex-wrap:wrap;
}

.bp-pill{
  background:var(--card);
  border:1px solid var(--border);
  color:var(--soft);
  font-size:11px;
  font-weight:600;
  padding:4px 12px;
  border-radius:20px;
}

.bp-pill.green{
  background:rgba(34,197,94,.12);
  border-color:rgba(34,197,94,.3);
  color:var(--green);
}

.bp-header-right{
  display:flex;
  flex-direction:column;
  align-items:flex-end;
  gap:12px;
}

.bp-search{
  position:relative;
  width:280px;
}

.bp-search input{
  width:100%;
  background:var(--card);
  border:1.5px solid var(--border);
  padding:11px 40px 11px 16px;
  border-radius:40px;
  outline:none;
}

.bp-search i{
  position:absolute;
  right:15px;
  top:50%;
  transform:translateY(-50%);
  color:var(--muted);
}

.bp-actions{
  display:flex;
  gap:10px;
}

.btn-act{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:10px 20px;
  border-radius:40px;
  font-size:13px;
  font-weight:600;
  cursor:pointer;
  border:none;
  text-decoration:none;
}

.btn-act.amber{
  background:var(--amber);
  color:#1a1200;
}

.btn-act.ghost{
  background:var(--card);
  color:var(--soft);
  border:1.5px solid var(--border);
}

.bp-layout{
  display:grid;
  grid-template-columns:195px 1fr 320px;
  gap:20px;
}

.bp-sidebar{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:14px 10px;
  max-height:82vh;
  overflow-y:auto;
}

.sb-label{
  font-size:10px;
  font-weight:700;
  letter-spacing:.1em;
  color:var(--muted);
  text-transform:uppercase;
  margin-bottom:12px;
}

.thumb-item{
  position:relative;
  border-radius:10px;
  overflow:hidden;
  margin-bottom:10px;
  cursor:pointer;
  border:2px solid transparent;
}

.thumb-item.active{
  border-color:var(--amber);
}

.thumb-item canvas{
  width:100%;
  display:block;
  background:#fff;
}

.thumb-num{
  position:absolute;
  bottom:5px;
  right:5px;
  background:rgba(0,0,0,.75);
  color:#fff;
  font-size:10px;
  padding:2px 7px;
  border-radius:6px;
}

.bp-viewer{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius);
  overflow:hidden;
  box-shadow:var(--shadow);
}

.vw-toolbar{
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:14px 18px;
  border-bottom:1px solid var(--border);
}

.vw-indicator{
  background:var(--card);
  border:1px solid var(--border);
  padding:7px 16px;
  border-radius:30px;
  font-size:13px;
  font-weight:600;
}

.vw-toolbar-actions{
  display:flex;
  gap:8px;
}

.vw-icon-btn{
  width:36px;
  height:36px;
  border-radius:50%;
  background:var(--card);
  border:1px solid var(--border);
  display:flex;
  align-items:center;
  justify-content:center;
  text-decoration:none;
  color:var(--soft);
}

.vw-stage{
  min-height:540px;
  background:#e8f0eb;
  display:flex;
  align-items:center;
  justify-content:center;
  overflow:auto;
  padding:20px;
}

#pdfCanvas{
  max-width:100%;
  background:#fff;
  box-shadow:0 5px 20px rgba(0,0,0,.1);
}

.vw-nav{
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:14px 18px;
  border-top:1px solid var(--border);
}

.vw-nav-btn{
  display:flex;
  align-items:center;
  gap:8px;
  padding:10px 22px;
  border-radius:10px;
  background:var(--green-dim);
  color:#fff;
  border:none;
  cursor:pointer;
}

.vw-progress{
  flex:1;
  height:4px;
  background:var(--border);
  border-radius:4px;
  margin:0 16px;
}

.vw-progress-fill{
  height:100%;
  background:linear-gradient(90deg,var(--green-dim),var(--green));
  width:0%;
}

.bp-recommended{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:18px 16px;
  max-height:82vh;
  overflow-y:auto;
}

.rec-title{
  font-family:'Cormorant Garamond',serif;
  font-size:1.25rem;
  font-weight:700;
  margin-bottom:16px;
}

.rec-book{
  display:flex;
  gap:12px;
  padding:12px;
  border-radius:12px;
  cursor:pointer;
  margin-bottom:10px;
}

.rec-book.active{
  background:rgba(34,197,94,.07);
}

.rec-thumb{
  width:72px;
}

.rec-thumb canvas{
  width:100%;
  background:#fff;
}

.rec-info h4{
  font-size:12px;
  font-weight:700;
  margin-bottom:5px;
}

.rec-author{
  font-size:11px;
  color:var(--muted);
}

.rec-slides{
  font-size:11px;
  color:var(--green);
  margin-top:6px;
}

@media(max-width:900px){

  .bp-layout{
    grid-template-columns:1fr;
  }

}
</style>

@php
$books = [
[
'title' => 'MU BYIZA BY’UKWEZI KWA RAMADHANI 01',
'author' => 'Iradukunda Aboubakr',
'views' => '0',
'pdf' => asset('Guest/books/MU BYIZA BY’UKWEZI KWA RAMADHANI 01.pdf')
],

[
'title' => 'فصول في الصيام والتراويح والزكاة',
'author' => 'Iradukunda Aboubakr',
'views' => '0',
'pdf' => asset('Guest/books/فصول في الصيام والتراويح والزكاة.pdf')
],

[
'title' => 'نبذة_في_العقيدة_الإسلامية_ابن_عثيمين_',
'author' => 'Iradukunda Aboubakr',
'views' => '0',
'pdf' => asset('Guest/books/نبذة_في_العقيدة_الإسلامية_ابن_عثيمين_2.pdf')
],
];
@endphp

<section class="bp-page">
<div class="bp-wrap">

<div class="bp-header">

<div class="bp-header-left">

<h1 id="bookTitle">{{ $books[0]['title'] }}</h1>

<div class="bp-author">
<div class="bp-author-dot">II</div>
<span id="bookAuthor">by {{ $books[0]['author'] }}</span>
</div>

<div class="bp-meta-pills">
<span class="bp-pill green">PDF</span>

<span class="bp-pill">
<i class="fas fa-eye"></i>
<span id="bookViews">{{ $books[0]['views'] }}</span>
views
</span>

<span class="bp-pill">
<i class="fas fa-file"></i>
<span id="bookSlides">0</span>
pages
</span>
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

<a class="btn-act amber"
id="headerDownload"
href="{{ $books[0]['pdf'] }}"
download>

<i class="fas fa-download"></i>
<span>Download PDF</span>

</a>

</div>

</div>

</div>

<div class="bp-layout">

<div class="bp-sidebar" id="thumbnailContainer">
<div class="sb-label">Pages</div>
</div>

<div class="bp-viewer">

<div class="vw-toolbar">

<div class="vw-indicator" id="pageIndicator">
1 / 1
</div>

<div class="vw-toolbar-actions">

<a class="vw-icon-btn"
id="toolbarDownload"
href="{{ $books[0]['pdf'] }}"
download>

<i class="fas fa-download"></i>

</a>

</div>

</div>

<div class="vw-stage">

<canvas id="pdfCanvas"></canvas>

</div>

<div class="vw-nav">

<button class="vw-nav-btn"
id="btnPrev"
onclick="prevPage()">

<i class="fas fa-arrow-left"></i>
<span>Previous</span>

</button>

<div class="vw-progress">
<div class="vw-progress-fill"
id="progressFill"></div>
</div>

<button class="vw-nav-btn"
id="btnNext"
onclick="nextPage()">

<span>Next</span>
<i class="fas fa-arrow-right"></i>

</button>

</div>

</div>

<div class="bp-recommended">

<div class="rec-title">
urutonde rw'ibitabo
</div>

<div id="recommendedBooks"></div>

</div>

</div>

</div>
</section>

<script>

pdfjsLib.GlobalWorkerOptions.workerSrc =
'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

const BOOKS = @json($books);

let currentBook = 0;
let currentPage = 1;
let pdfDoc = null;

const canvas = document.getElementById('pdfCanvas');
const ctx = canvas.getContext('2d');

/*
|--------------------------------------------------------------------------
| LOAD BOOK
|--------------------------------------------------------------------------
*/

async function loadBook(index){

    currentBook = index;
    currentPage = 1;

    const book = BOOKS[index];

    document.getElementById('bookTitle').innerText =
        book.title;

    document.getElementById('bookAuthor').innerText =
        'by ' + book.author;

    document.getElementById('bookViews').innerText =
        book.views;

    document.getElementById('headerDownload').href =
        book.pdf;

    document.getElementById('toolbarDownload').href =
        book.pdf;

    /*
    |--------------------------------------------------------------------------
    | LOAD PDF
    |--------------------------------------------------------------------------
    */

    pdfDoc = await pdfjsLib
        .getDocument(book.pdf)
        .promise;

    document.getElementById('bookSlides').innerText =
        pdfDoc.numPages;

    buildThumbs();

    renderPage(currentPage);

    renderRecommended(
        document.getElementById('searchInput').value
    );
}

/*
|--------------------------------------------------------------------------
| RENDER MAIN PAGE
|--------------------------------------------------------------------------
*/

async function renderPage(pageNumber){

    const page =
        await pdfDoc.getPage(pageNumber);

    const viewport =
        page.getViewport({ scale: 1.5 });

    canvas.width =
        viewport.width;

    canvas.height =
        viewport.height;

    await page.render({
        canvasContext: ctx,
        viewport: viewport
    }).promise;

    /*
    |--------------------------------------------------------------------------
    | PAGE INDICATOR
    |--------------------------------------------------------------------------
    */

    document.getElementById('pageIndicator').innerText =
        `${pageNumber} / ${pdfDoc.numPages}`;

    document.getElementById('progressFill').style.width =
        `${(pageNumber / pdfDoc.numPages) * 100}%`;

    /*
    |--------------------------------------------------------------------------
    | BUTTONS
    |--------------------------------------------------------------------------
    */

    document.getElementById('btnPrev').disabled =
        pageNumber <= 1;

    document.getElementById('btnNext').disabled =
        pageNumber >= pdfDoc.numPages;

    /*
    |--------------------------------------------------------------------------
    | ACTIVE THUMB
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.thumb-item')
    .forEach((el,index)=>{

        el.classList.toggle(
            'active',
            index + 1 === pageNumber
        );

    });

}

/*
|--------------------------------------------------------------------------
| BUILD THUMBNAILS
|--------------------------------------------------------------------------
*/

async function buildThumbs(){

    const container =
        document.getElementById('thumbnailContainer');

    container.innerHTML =
        '<div class="sb-label">Pages</div>';

    for(let i = 1; i <= pdfDoc.numPages; i++){

        const page =
            await pdfDoc.getPage(i);

        const viewport =
            page.getViewport({ scale: 0.25 });

        const thumb =
            document.createElement('canvas');

        const thumbCtx =
            thumb.getContext('2d');

        thumb.width =
            viewport.width;

        thumb.height =
            viewport.height;

        await page.render({
            canvasContext: thumbCtx,
            viewport: viewport
        }).promise;

        const div =
            document.createElement('div');

        div.className =
            'thumb-item' + (i === 1 ? ' active' : '');

        div.onclick = () => {

            currentPage = i;

            renderPage(currentPage);

        };

        div.innerHTML =
            `<div class="thumb-num">${i}</div>`;

        div.prepend(thumb);

        container.appendChild(div);

    }

}

/*
|--------------------------------------------------------------------------
| NEXT PAGE
|--------------------------------------------------------------------------
*/

function nextPage(){

    if(currentPage >= pdfDoc.numPages)
        return;

    currentPage++;

    renderPage(currentPage);

}

/*
|--------------------------------------------------------------------------
| PREVIOUS PAGE
|--------------------------------------------------------------------------
*/

function prevPage(){

    if(currentPage <= 1)
        return;

    currentPage--;

    renderPage(currentPage);

}

/*
|--------------------------------------------------------------------------
| RECOMMENDED BOOKS
|--------------------------------------------------------------------------
*/

async function renderRecommended(query = ''){

    const container =
        document.getElementById('recommendedBooks');

    container.innerHTML = '';

    const filtered =
        BOOKS.filter(book =>

            book.title
            .toLowerCase()
            .includes(query.toLowerCase())

        );

    for(const book of filtered){

        const index =
            BOOKS.indexOf(book);

        const card =
            document.createElement('div');

        card.className =
            'rec-book' +
            (index === currentBook ? ' active' : '');

        card.onclick = () =>
            loadBook(index);

        /*
        |--------------------------------------------------------------------------
        | PDF THUMB
        |--------------------------------------------------------------------------
        */

        const pdf =
            await pdfjsLib
            .getDocument(book.pdf)
            .promise;

        const page =
            await pdf.getPage(1);

        const viewport =
            page.getViewport({ scale: 0.25 });

        const thumb =
            document.createElement('canvas');

        const thumbCtx =
            thumb.getContext('2d');

        thumb.width =
            viewport.width;

        thumb.height =
            viewport.height;

        await page.render({
            canvasContext: thumbCtx,
            viewport: viewport
        }).promise;

        const thumbWrap =
            document.createElement('div');

        thumbWrap.className =
            'rec-thumb';

        thumbWrap.appendChild(thumb);

        const info =
            document.createElement('div');

        info.className =
            'rec-info';

        info.innerHTML = `
            <h4>${book.title}</h4>

            <div class="rec-author">
                ${book.author}
            </div>

            <div class="rec-slides">
                ${pdf.numPages} pages
            </div>
        `;

        card.appendChild(thumbWrap);
        card.appendChild(info);

        container.appendChild(card);

    }

}

/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

document
.getElementById('searchInput')
.addEventListener('input', function(){

    renderRecommended(this.value);

});

/*
|--------------------------------------------------------------------------
| SHARE
|--------------------------------------------------------------------------
*/

function shareBook(){

    if(navigator.share){

        navigator.share({
            title: BOOKS[currentBook].title,
            url: window.location.href
        });

    }

}

// function shareBook() {

//     const currentPdf = BOOKS[currentBook].pdf;

//     // FULL URL
//     const shareUrl = window.location.origin + currentPdf;

//     if (navigator.share) {

//         navigator.share({
//             title: BOOKS[currentBook].title,
//             text: 'Read this PDF book',
//             url: shareUrl
//         })
//         .catch(err => console.log(err));

//     } else {

//         navigator.clipboard.writeText(shareUrl)
//         .then(() => {

//             const btn = document.querySelector('.btn-act.ghost');

//             btn.innerHTML = `
//                 <i class="fas fa-check"></i>
//                 <span>Copied!</span>
//             `;

//             setTimeout(() => {

//                 btn.innerHTML = `
//                     <i class="fas fa-share-alt"></i>
//                     <span>Share</span>
//                 `;

//             }, 2000);

//         });

//     }

// }

/*
|--------------------------------------------------------------------------
| KEYBOARD
|--------------------------------------------------------------------------
*/

document.addEventListener('keydown', e => {

    if(e.key === 'ArrowRight')
        nextPage();

    if(e.key === 'ArrowLeft')
        prevPage();

});

/*
|--------------------------------------------------------------------------
| START
|--------------------------------------------------------------------------
*/

loadBook(0);

</script>

@endsection