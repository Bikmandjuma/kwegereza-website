{{-- Floating Guest FAQ Chat — guest-facing pages only, never inside authenticated dashboards --}}
<style>
:root{
  --kiu-chat-green: #058e48;
  --kiu-chat-green-deep: #094939;
  --kiu-chat-gold: #e2b45f;
}

#kiuFaqLauncher{
  position: fixed;
  bottom: 22px;
  right: 22px;
  width: 58px;
  height: 58px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--kiu-chat-green), var(--kiu-chat-green-deep));
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  cursor: pointer;
  box-shadow: 0 10px 24px rgba(9,73,57,0.35);
  z-index: 9998;
  border: none;
  transition: transform .2s;
}
#kiuFaqLauncher:hover{ transform: scale(1.06); }

#kiuFaqWindow{
  position: fixed;
  bottom: 92px;
  right: 22px;
  width: 340px;
  max-width: calc(100vw - 32px);
  height: 460px;
  max-height: calc(100vh - 140px);
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 20px 50px rgba(9,73,57,0.35);
  display: none;
  flex-direction: column;
  overflow: hidden;
  z-index: 9999;
}
#kiuFaqWindow.open{ display: flex; }

#kiuFaqHeader{
  background: linear-gradient(135deg, var(--kiu-chat-green), var(--kiu-chat-green-deep));
  color: #fff;
  padding: 14px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
#kiuFaqHeader strong{ font-size: 14px; }
#kiuFaqHeader p{ font-size: 11px; opacity: .85; margin: 2px 0 0; }
#kiuFaqClose{ background: none; border: none; color: #fff; font-size: 18px; cursor: pointer; }

#kiuFaqBody{
  flex: 1;
  padding: 14px;
  overflow-y: auto;
  background: #f6f8f7;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.kiu-faq-msg{
  max-width: 82%;
  padding: 9px 13px;
  border-radius: 14px;
  font-size: 13px;
  line-height: 1.45;
}
.kiu-faq-msg.bot{
  background: #fff;
  color: #1a1a1a;
  align-self: flex-start;
  border-bottom-left-radius: 4px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}
.kiu-faq-msg.user{
  background: var(--kiu-chat-green);
  color: #fff;
  align-self: flex-end;
  border-bottom-right-radius: 4px;
}
.kiu-faq-msg.typing{ font-style: italic; color: #888; background: transparent; box-shadow: none; }

#kiuFaqInputBar{
  display: flex;
  gap: 8px;
  padding: 10px;
  border-top: 1px solid #eee;
  background: #fff;
}
#kiuFaqInput{
  flex: 1;
  border: 1px solid #ddd;
  border-radius: 999px;
  padding: 9px 14px;
  font-size: 13px;
  outline: none;
}
#kiuFaqSend{
  background: var(--kiu-chat-green-deep);
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 38px;
  height: 38px;
  cursor: pointer;
  font-size: 14px;
}

@media (max-width: 480px){
  #kiuFaqWindow{ right: 16px; left: 16px; width: auto; }
}
</style>

<button id="kiuFaqLauncher" aria-label="Fungura ikiganiro">
  <i class="fa-solid fa-comment-dots"></i>
</button>

<div id="kiuFaqWindow" role="dialog" aria-label="Ikiganiro n'Umufasha">
  <div id="kiuFaqHeader">
    <div>
      <strong>Umufasha wa K.I.U</strong>
      <p>Baza ikibazo, tugufashe</p>
    </div>
    <button id="kiuFaqClose" aria-label="Funga">
      <i class="fa-solid fa-xmark"></i>
    </button>
  </div>

  <div id="kiuFaqBody">
    <div class="kiu-faq-msg bot">
      Assalam aalaikum warahmatullahi wabarakat <br/><br/>murakaza neza kuri Kwegereza Islam Umuryango. baza ikibazo, uri bubone igisubizo mumasegonda.
    </div>
  </div>

  <div id="kiuFaqInputBar">
    <input type="text" id="kiuFaqInput" placeholder="Andika ikibazo cyawe..." maxlength="1000">
    <button id="kiuFaqSend" aria-label="Ohereza">
      <i class="fa-solid fa-paper-plane"></i>
    </button>
  </div>
</div>

<script>
(function () {
  const launcher = document.getElementById('kiuFaqLauncher');
  const win = document.getElementById('kiuFaqWindow');
  const closeBtn = document.getElementById('kiuFaqClose');
  const body = document.getElementById('kiuFaqBody');
  const input = document.getElementById('kiuFaqInput');
  const sendBtn = document.getElementById('kiuFaqSend');

  launcher.addEventListener('click', () => win.classList.toggle('open'));
  closeBtn.addEventListener('click', () => win.classList.remove('open'));

  function addMessage(text, who) {
    const div = document.createElement('div');
    div.className = 'kiu-faq-msg ' + who;
    div.textContent = text;
    body.appendChild(div);
    body.scrollTop = body.scrollHeight;
    return div;
  }

  function send() {
    const question = input.value.trim();
    if (!question) return;

    addMessage(question, 'user');
    input.value = '';

    const typingEl = addMessage('...aravuga...', 'bot typing');

    fetch("{{ route('guest.faq.ask') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ question: question, page: window.location.pathname }),
    })
      .then(r => r.json())
      .then(data => {
        typingEl.remove();
        addMessage(data.answer, 'bot');
      })
      .catch(() => {
        typingEl.remove();
        addMessage("Habaye ikibazo. Ongera ugerageze.", 'bot');
      });
  }

  sendBtn.addEventListener('click', send);
  input.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') send();
  });
})();
</script>
