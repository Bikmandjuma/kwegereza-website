<!DOCTYPE html>
<html lang="rw">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kwegereza Islam Umuryango – E-Learning</title>
<!-- <meta http-equiv="refresh" content="3"> -->
<link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Playfair+Display:wght@600;800&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="{{ URL::to('/') }}/Guest/assets/style.css" />
<style>
  .logo-box-x { display:flex; align-items:center; gap:10px;align-content:center;text-align:center; }
  .logo-box-x img {
    width: 54px; height: 54px;
    border-radius: 50%;
    border: 2px solid var(--gold);
    object-fit: contain;
    padding: 4px;
  }
</style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
  <div class="nav-inner">
    <div class="logo-box-x" style="">
      <img src="{{ URL::to('/') }}/Guest/images/logo.png" alt="K.I.U Logo"
           onerror="this.style.background='#0B3D2E';this.src='';this.alt='KIU'">
      
        <!-- <h2>K.I.U</h2> -->
        <p>Kwegereza Islam Umuryango</p>

    </div>

  </div>

</div>


<!-- DAILY AYAH + HADITH -->
<!-- <div class="daily-strip container" style="padding-top:40px;">
  <div class="daily-card">
    <h4><i class="fas fa-users"></i>,<i class="fas fa-eye"></i> &nbsp;System status</h4>
    <div class="trans">System-users : 0</div>
    <div class="trans">Today's visit : 0</div>
    <div class="trans">All-visits : 0</div>    
  </div>

</div> -->

<!-- Modal -->
<div id="classModal" class="modal">
  <div class="modal-content">

    <span class="closeBtn" style="margin-top:-3px;color: red;">&times;</span>

    <h2>📚 Mwinjire twige</h2>
    <hr>
    <p>
      Isomo turibwige uyumunsi ni Hadith , turi bugezweho na <b>Sheikh IRADUKUNDA ABOUBAKAR ABUU ABDILRAHMAN</b>
    </p>

    <button class="join-btn">Mukanya Saa 21:30 PM</button>

  </div>
</div>

  @yield('content')

<!-- FOOTER -->
@if(!isset($hideFooter) || !$hideFooter)
<div class="footer-gold"></div>
<footer>
  <div class="footer-inner">
    <div>
      <h4>Twandikire</h4>
      <p><i class="fa fa-phone"></i> (+250) 723061482</p>
      <p><i class="fa fa-envelope"></i> umuryangok@gmail.com</p>
      <p><i class="fa fa-location-dot"></i> Rwanda</p>
    </div>
    <div>
      <h4>Aho tugana</h4>
      <a href="#ahabanza"><i class="fas fa-home"></i> Ahabanza</a>
      <a href="#abo-turi-bo"><i class="fas fa-info-circle"></i> Abo-turibo</a>
      <a href="#amasomo"><i class="fas fa-graduation-cap"></i> Amasomo</a>
      <a href="#ibitabo"><i class="fas fa-book"></i> Ibitabo</a>
    </div>
    <div>
      <h4>Imbuga nkoranya mbaga</h4>
      <a href="https://t.me/kwegereza" target="_blank"><i class="fab fa-telegram"></i> Telegram</a>
      <a href="https://www.facebook.com/groups/171182813976386/" target="_blank"><i class="fab fa-facebook"></i> Facebook</a>
      <a href="https://chat.whatsapp.com/G87ZLng06dJJA4rqV7DQsS" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a>
      <a href="https://youtube.com/@kwegereza" target="_blank"><i class="fab fa-youtube"></i> YouTube</a>
    </div>
  </div>
  <div class="footer-bottom">
    <p><strong>KWEGEREZA ISLAM UMURYANGO</strong> &nbsp;|&nbsp; تقريب السنة بين يدي الأمة</p>
    <p style="margin-top:6px;">&copy; 2026 K.I.U </p>
  </div>
</footer>
@endif

<script>
const chatBtn = document.getElementById("chatBtn");
const modal = document.getElementById("classModal");
const closeBtn = document.querySelector(".closeBtn");
const mobileMenu = document.getElementById("mobileMenu");
const dropdown = document.querySelector(".dropdown > a");
const menu = document.querySelector(".dropdown-menu");


chatBtn.onclick = () => {
  modal.style.display = "flex";
};

closeBtn.onclick = () => {
  modal.style.display = "none";
};

window.onclick = (e) => {
  if (e.target === modal) {
    modal.style.display = "none";
  }
};

// ✅ CLOSE MOBILE MENU WHEN CLICKING OUTSIDE
document.addEventListener("click", function (e) {
    if (
        mobileMenu &&
        menuBtn &&
        !mobileMenu.contains(e.target) &&
        !menuBtn.contains(e.target)
    ) {
        mobileMenu.style.display = "none";
    }
});

dropdown.addEventListener("click", (e) => {
  e.preventDefault();
  menu.style.display = (menu.style.display === "flex") ? "none" : "flex";
});

// SELECT ALL SHEIKH LINKS
const sheikhLinks = document.querySelectorAll(".sheikh-link");

// LOOP THROUGH THEM
sheikhLinks.forEach(link => {
  link.addEventListener("click", function () {

    const name = this.getAttribute("data-name");

    // SAVE TO LOCAL STORAGE
    localStorage.setItem("sheikh_name", name);

  });
});

</script>
<!-- <script src="assets/script.js"></script> -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="{{ URL::to('/') }}/Guest/assets/script.js"></script>

</body>
</html>
