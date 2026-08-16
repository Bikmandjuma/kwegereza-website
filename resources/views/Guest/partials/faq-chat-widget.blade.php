{{-- Floating Guest FAQ Chat — guest-facing pages only, never inside authenticated dashboards --}}
<style>
/* Uses the shared brand variables (--green/--green-dark/--gold) from
   Guest/assets/style.css instead of a third, slightly different local
   green (this file previously defined its own --kiu-chat-green). */

#kiuFaqLauncher{
  position: fixed;
  bottom: 22px;
  right: 22px;
  width: 58px;
  height: 58px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--green, #0B6D20), var(--green-dark, #094939));
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  cursor: pointer;
  box-shadow: 0 10px 24px rgba(9,73,57,0.35);
  z-index: 9998;
  border: none;
  transition: transform .25s cubic-bezier(0.16,1,0.3,1), box-shadow .25s;
  animation: kiuFaqPulse 2.6s ease-in-out infinite;
}
#kiuFaqLauncher:hover{ transform: scale(1.08); box-shadow: 0 14px 32px rgba(9,73,57,0.45); }
#kiuFaqLauncher:focus-visible{ outline: 2px solid var(--gold-light, #e2b45f); outline-offset: 3px; }
#kiuFaqLauncher.has-opened{ animation: none; }

@keyframes kiuFaqPulse {
  0%, 100% { box-shadow: 0 10px 24px rgba(9,73,57,0.35), 0 0 0 0 rgba(11,109,32,0.35); }
  50% { box-shadow: 0 10px 24px rgba(9,73,57,0.35), 0 0 0 10px rgba(11,109,32,0); }
}

@media (prefers-reduced-motion: reduce) {
  #kiuFaqLauncher { animation: none; }
}

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
  opacity: 0;
  transform: translateY(12px) scale(0.97);
  transition: opacity .22s ease-out, transform .22s cubic-bezier(0.16,1,0.3,1);
}
#kiuFaqWindow.open{ display: flex; opacity: 1; transform: translateY(0) scale(1); }

#kiuFaqHeader{
  background: linear-gradient(135deg, var(--green, #0B6D20), var(--green-dark, #094939));
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
  background: var(--green, #0B6D20);
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
  transition: border-color .2s, box-shadow .2s;
}
#kiuFaqInput:focus{ border-color: var(--green, #0B6D20); box-shadow: 0 0 0 3px rgba(11,109,32,0.12); }
#kiuFaqSend{
  background: var(--green-dark, #094939);
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 38px;
  height: 38px;
  cursor: pointer;
  font-size: 14px;
  transition: transform .2s cubic-bezier(0.16,1,0.3,1), filter .2s;
}
#kiuFaqSend:hover{ filter: brightness(1.1); transform: scale(1.06); }
#kiuFaqSend:focus-visible, #kiuFaqClose:focus-visible{ outline: 2px solid var(--gold-light, #e2b45f); outline-offset: 2px; }

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

  launcher.addEventListener('click', () => {
    win.classList.toggle('open');
    launcher.classList.add('has-opened');
  });
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
