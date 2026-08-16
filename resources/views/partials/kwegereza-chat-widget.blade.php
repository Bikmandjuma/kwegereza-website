{{-- Kwegereza Chat — floating guest↔leader chat, everywhere on the
     public site. Replaces the old automated FAQ bot on this same
     bottom-right icon: this opens a REAL conversation with a leader,
     not a keyword-matched bot. Shares the exact same backend
     (/chat/* endpoints) and localStorage keys (guest_id, guest_name)
     as the full-page version at twandikire.blade.php, so a guest who
     started chatting one way sees the identical conversation the
     other way — same person, same thread, just two entry points. --}}
<style>
:root{ --kgw-radius:20px; }

#kgwLauncher{
  position: fixed; bottom: 22px; right: 22px; width: 58px; height: 58px;
  border-radius: 50%; background: var(--grad-green, linear-gradient(135deg,#0B6D20,#0B3D2E));
  color: #fff; display: flex; align-items: center; justify-content: center;
  font-size: 24px; cursor: pointer; box-shadow: 0 10px 24px rgba(11,61,46,.35);
  z-index: 9997; border: none;
  transition: transform .25s cubic-bezier(0.16,1,0.3,1), box-shadow .25s;
  animation: kgwPulse 2.6s ease-in-out infinite;
}
#kgwLauncher:hover{ transform: scale(1.08); }
#kgwLauncher.has-opened{ animation: none; }
#kgwLauncher .kgw-unread{
  position:absolute; top:-2px; right:-2px; background:#e11d48; color:#fff;
  font-size:11px; font-weight:700; border-radius:999px; min-width:18px; height:18px;
  display:none; align-items:center; justify-content:center; padding:0 4px;
}
@keyframes kgwPulse{
  0%,100%{ box-shadow:0 10px 24px rgba(11,61,46,.35), 0 0 0 0 rgba(11,109,32,.35); }
  50%{ box-shadow:0 10px 24px rgba(11,61,46,.35), 0 0 0 10px rgba(11,109,32,0); }
}
@media (prefers-reduced-motion: reduce){ #kgwLauncher{ animation:none; } }

#kgwOverlay{
  position: fixed; inset: 0; z-index: 9998; background: rgba(0,0,0,.45);
  display: none; align-items: flex-end; justify-content: flex-end; padding: 0;
}
#kgwOverlay.open{ display: flex; }

#kgwModal{
  width: 380px; max-width: 100vw; height: 600px; max-height: 100dvh;
  background: #fff; display: flex; flex-direction: column; overflow: hidden;
  margin: 22px; border-radius: var(--kgw-radius); box-shadow: 0 24px 64px rgba(0,0,0,.3);
  opacity: 0; transform: translateY(16px) scale(.97);
  transition: opacity .2s ease-out, transform .2s cubic-bezier(0.16,1,0.3,1);
}
#kgwOverlay.open #kgwModal{ opacity:1; transform:translateY(0) scale(1); }

@media (max-width: 480px){
  #kgwModal{ margin:0; width:100vw; height:100dvh; max-height:100dvh; border-radius:0; }
}

/* ── Name capture screen ── */
#kgwNameScreen{ flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:28px 24px; text-align:center; background:var(--grad-green,linear-gradient(160deg,#0B3D2E,#0B6D20)); }
#kgwNameScreen .kgw-logo{ width:64px; height:64px; border-radius:50%; background:rgba(255,255,255,.15); display:flex; align-items:center; justify-content:center; font-size:28px; margin-bottom:16px; }
#kgwNameScreen h2{ color:#fff; font-size:20px; font-weight:700; margin-bottom:8px; }
#kgwNameScreen p{ color:rgba(255,255,255,.8); font-size:13px; margin-bottom:20px; }
#kgwNameInput{ width:100%; height:46px; border:none; border-radius:12px; padding:0 16px; font-size:14px; outline:none; margin-bottom:12px; }
#kgwStartBtn{ width:100%; height:46px; border:none; border-radius:12px; background:var(--gold,#C9A227); color:#0B3D2E; font-weight:700; cursor:pointer; }

/* ── Chat screen ── */
#kgwChatScreen{ flex:1; display:none; flex-direction:column; min-height:0; }
#kgwHeader{ display:flex; align-items:center; gap:10px; padding:12px 14px; background:var(--green-dark,#0B3D2E); color:#fff; flex-shrink:0; }
#kgwHeader .kgw-avatar{ width:38px; height:38px; border-radius:50%; background:rgba(255,255,255,.15); display:flex; align-items:center; justify-content:center; font-size:18px; }
#kgwHeader h3{ font-size:14px; font-weight:600; margin:0; }
#kgwHeader .kgw-status{ font-size:11px; color:rgba(255,255,255,.75); display:flex; align-items:center; gap:5px; }
#kgwHeader .kgw-status-dot{ width:7px; height:7px; border-radius:50%; background:#22c55e; }
#kgwCloseBtn{ margin-left:auto; background:none; border:none; color:#fff; font-size:18px; cursor:pointer; }

#kgwMessages{ flex:1; overflow-y:auto; padding:12px; display:flex; flex-direction:column; gap:8px; background:#f6f8f7; }
.kgw-msg{ display:flex; align-items:flex-end; gap:6px; max-width:82%; }
.kgw-msg.guest{ align-self:flex-end; flex-direction:row-reverse; }
.kgw-bubble{ padding:8px 12px; border-radius:16px; font-size:13.5px; line-height:1.45; }
.kgw-msg.guest .kgw-bubble{ background:var(--gold-light,#e8c870); color:#111827; border-bottom-right-radius:4px; }
.kgw-msg.admin .kgw-bubble{ background:#fff; color:#111827; border-bottom-left-radius:4px; box-shadow:0 1px 2px rgba(0,0,0,.1); }
.kgw-time{ font-size:10px; color:#9ca3af; margin-top:2px; display:flex; align-items:center; gap:3px; }
.kgw-msg.guest .kgw-time{ justify-content:flex-end; }
.kgw-tick{ color:#9ca3af; display:inline-flex; }
.kgw-tick.read{ color:#0B6D20; }

#kgwTyping{ display:none; align-items:center; gap:6px; padding:0 12px 6px; font-size:11px; color:#6b7280; }
#kgwTyping .kgw-dot{ width:5px; height:5px; border-radius:50%; background:#9ca3af; animation:kgwBlink 1s infinite; }
#kgwTyping .kgw-dot:nth-child(2){ animation-delay:.2s; }
#kgwTyping .kgw-dot:nth-child(3){ animation-delay:.4s; }
@keyframes kgwBlink{ 0%,100%{opacity:.3;} 50%{opacity:1;} }

#kgwInputBar{ display:flex; gap:8px; padding:10px; border-top:1px solid #eee; background:#fff; flex-shrink:0; }
#kgwInput{ flex:1; border:1px solid #ddd; border-radius:999px; padding:9px 14px; font-size:13.5px; outline:none; }
#kgwInput:focus{ border-color:#0B6D20; }
#kgwSendBtn{ background:var(--green-dark,#0B3D2E); color:#fff; border:none; border-radius:50%; width:38px; height:38px; cursor:pointer; flex-shrink:0; }
#kgwQuickEmojis{ display:flex; gap:4px; padding:0 10px 8px; }
#kgwQuickEmojis span{ cursor:pointer; font-size:18px; }
</style>

<button id="kgwLauncher" aria-label="Fungura ikiganiro na Kwegereza">
  <i class="fa-solid fa-comment-dots"></i>
  <span class="kgw-unread" id="kgwUnread"></span>
</button>

<div id="kgwOverlay">
  <div id="kgwModal" role="dialog" aria-label="Kwegereza Chat">

    <div id="kgwNameScreen">
      <div class="kgw-logo">💬</div>
      <h2>Kwegereza Chat</h2>
      <p>Ababishinzwe bari bugusubize mu gihe gito. Andika izina ryawe utangire.</p>
      <input type="text" id="kgwNameInput" placeholder="Izina ryawe, urugero: Umm Raslaan" onkeydown="if(event.key==='Enter') kgwStartChat()">
      <button id="kgwStartBtn" onclick="kgwStartChat()">Tangira Ikiganiro &rarr;</button>
      <button id="kgwCloseBtn" onclick="kgwCloseModal()" aria-label="Funga" class="text-red-600 text-align-center mt-2 mr-2">Close</button>

    </div>

    <div id="kgwChatScreen">
      <div id="kgwHeader">
        <div class="kgw-avatar">🎧</div>
        <div>
          <h3>Kwegereza Chat</h3>
          <div class="kgw-status"><span class="kgw-status-dot"></span> Turi kuri interineti</div>
        </div>
        <button id="kgwCloseBtn" onclick="kgwCloseModal()" aria-label="Funga"><i class="fa-solid fa-xmark"></i></button>
      </div>

      <div id="kgwMessages"></div>

      <div id="kgwTyping">
        <span>Umufasha arimo kwandika</span>
        <span class="kgw-dot"></span><span class="kgw-dot"></span><span class="kgw-dot"></span>
      </div>

      <div id="kgwQuickEmojis">
        <span onclick="kgwSendMessage('👍')">👍</span>
        <span onclick="kgwSendMessage('❤️')">❤️</span>
        <span onclick="kgwSendMessage('🙏')">🙏</span>
        <span onclick="kgwSendMessage('😂')">😂</span>
      </div>

      <div id="kgwInputBar">
        <input type="text" id="kgwInput" placeholder="Andika ubutumwa..." autocomplete="off">
        <button id="kgwSendBtn" onclick="kgwSendMessage()" aria-label="Ohereza"><i class="fa-solid fa-paper-plane"></i></button>
      </div>
    </div>
    
  </div>
</div>

<script>
(function () {
  let kgwGuestName = '';
  let kgwRenderedIds = new Set();
  let kgwLastCount = 0;
  let kgwTypingTimeout = null;
  let kgwPollInterval = null;
  let kgwTypingInterval = null;
  let kgwPresenceInterval = null;

  function kgwGetOrCreateGuestID(create) {
    let id = localStorage.getItem('guest_id');
    if (id) return id;
    if (!create) return null;
    id = (window.crypto && crypto.randomUUID) ? crypto.randomUUID()
      : 'g-' + Date.now() + '-' + Math.random().toString(16).slice(2);
    localStorage.setItem('guest_id', id);
    return id;
  }

  function kgwEscape(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }

  function kgwTickSvg(isRead) {
    if (isRead) {
      return '<span class="kgw-tick read" title="Yasomwe"><svg width="13" height="9" viewBox="0 0 16 11" fill="none"><path d="M1 5.5L5 9.5L11 1.5M6 5.5L10 9.5L15 1.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></span>';
    }
    return '<span class="kgw-tick" title="Yoherejwe"><svg width="11" height="9" viewBox="0 0 12 11" fill="none"><path d="M1 5.5L5 9.5L11 1.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></span>';
  }

  function kgwAppendMessage(msg, animate) {
    const uid = msg.id ?? (msg.created_at + msg.sender_type + msg.message.slice(0,10));
    kgwRenderedIds.add(uid);

    const row = document.createElement('div');
    row.className = 'kgw-msg ' + msg.sender_type;
    row.dataset.uid = uid;
    if (msg.id) row.dataset.msgId = msg.id;

    const tick = msg.sender_type === 'guest' ? kgwTickSvg(msg.is_read) : '';
    const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    row.innerHTML = `<div><div class="kgw-bubble">${kgwEscape(msg.message)}</div><div class="kgw-time">${time}${tick}</div></div>`;
    document.getElementById('kgwMessages').appendChild(row);
    if (animate) row.style.animation = 'none';
  }

  function kgwUpdateStatuses(messages) {
    messages.forEach(msg => {
      if (msg.sender_type !== 'guest' || !msg.id) return;
      const row = document.querySelector(`.kgw-msg[data-msg-id="${msg.id}"]`);
      const tickEl = row?.querySelector('.kgw-tick');
      if (tickEl && msg.is_read && !tickEl.classList.contains('read')) {
        tickEl.outerHTML = kgwTickSvg(true);
      }
    });
  }

  async function kgwLoadMessages(initial) {
    const guestID = localStorage.getItem('guest_id');
    if (!guestID) return;
    try {
      const res = await fetch(`/chat/messages/${guestID}`);
      if (!res.ok) return;
      const messages = await res.json();

      if (messages.length > kgwLastCount && kgwLastCount > 0 && document.getElementById('kgwOverlay').classList.contains('open') === false) {
        document.getElementById('kgwUnread').textContent = messages.length - kgwLastCount;
        document.getElementById('kgwUnread').style.display = 'flex';
      }
      kgwLastCount = messages.length;

      const box = document.getElementById('kgwMessages');
      if (initial) {
        box.innerHTML = '';
        kgwRenderedIds.clear();
        messages.forEach(m => kgwAppendMessage(m, false));
        box.scrollTop = box.scrollHeight;
        return;
      }

      let appended = false;
      messages.forEach(m => {
        const uid = m.id ?? (m.created_at + m.sender_type + m.message.slice(0,10));
        if (!kgwRenderedIds.has(uid)) { kgwAppendMessage(m, true); appended = true; }
      });
      kgwUpdateStatuses(messages);
      if (appended) box.scrollTop = box.scrollHeight;
    } catch (e) { console.error(e); }
  }

  window.kgwSendMessage = async function (fixedText) {
    const input = document.getElementById('kgwInput');
    const msg = (fixedText || input.value).trim();
    if (!msg) return;
    const guestID = localStorage.getItem('guest_id');
    if (!guestID) return;

    const tempId = 'tmp-' + Date.now();
    kgwAppendMessage({ id: tempId, sender_type: 'guest', message: msg, created_at: new Date().toISOString(), is_read: false }, true);
    document.getElementById('kgwMessages').scrollTop = 999999;
    if (!fixedText) input.value = '';

    try {
      await fetch('/chat/send', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ sender_type: 'guest', sender_name: kgwGuestName, guest_id: guestID, message: msg }),
      });
    } catch (e) { console.error(e); }
  };

  function kgwOnTyping() {
    clearTimeout(kgwTypingTimeout);
    const guestID = localStorage.getItem('guest_id');
    if (!guestID) return;
    fetch('/chat/typing', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify({ guest_id: guestID, typing: true }),
    }).catch(() => {});
    kgwTypingTimeout = setTimeout(() => {
      fetch('/chat/typing', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ guest_id: guestID, typing: false }),
      }).catch(() => {});
    }, 2000);
  }

  async function kgwCheckAdminTyping() {
    const guestID = localStorage.getItem('guest_id');
    if (!guestID) return;
    try {
      const res = await fetch(`/chat/admin-typing/${guestID}`);
      if (!res.ok) return;
      const { typing } = await res.json();
      document.getElementById('kgwTyping').style.display = typing ? 'flex' : 'none';
    } catch (e) {}
  }

  function kgwPing() {
    const guestID = localStorage.getItem('guest_id');
    if (!guestID) return;
    fetch('/chat/presence', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify({ guest_id: guestID }),
    }).catch(() => {});
  }

  function kgwStartPolling() {
    if (kgwPollInterval) return;
    kgwPollInterval = setInterval(() => kgwLoadMessages(false), 2500);
    kgwTypingInterval = setInterval(kgwCheckAdminTyping, 2000);
    kgwPresenceInterval = setInterval(kgwPing, 10000);
    kgwPing();
  }

  window.kgwStartChat = function () {
    const name = document.getElementById('kgwNameInput').value.trim();
    if (!name) { document.getElementById('kgwNameInput').focus(); return; }
    kgwGuestName = name;
    localStorage.setItem('guest_name', name);
    kgwGetOrCreateGuestID(true);
    document.getElementById('kgwNameScreen').style.display = 'none';
    document.getElementById('kgwChatScreen').style.display = 'flex';
    kgwLoadMessages(true);
    kgwStartPolling();
  };

  window.openKwegerezaChat = function () {
    const overlay = document.getElementById('kgwOverlay');
    overlay.classList.add('open');
    document.getElementById('kgwLauncher').classList.add('has-opened');
    document.getElementById('kgwUnread').style.display = 'none';

    const guestID = localStorage.getItem('guest_id');
    const storedName = localStorage.getItem('guest_name');
    if (guestID && storedName) {
      kgwGuestName = storedName;
      document.getElementById('kgwNameScreen').style.display = 'none';
      document.getElementById('kgwChatScreen').style.display = 'flex';
      kgwLoadMessages(true);
      kgwStartPolling();
    } else {
      document.getElementById('kgwNameScreen').style.display = 'flex';
      document.getElementById('kgwChatScreen').style.display = 'none';
      setTimeout(() => document.getElementById('kgwNameInput').focus(), 100);
    }
  };

  window.kgwCloseModal = function () {
    document.getElementById('kgwOverlay').classList.remove('open');
  };

  document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('kgwLauncher').addEventListener('click', window.openKwegerezaChat);
    document.getElementById('kgwOverlay').addEventListener('click', function (e) {
      if (e.target === this) window.kgwCloseModal();
    });
    document.getElementById('kgwInput').addEventListener('keydown', function (e) {
      if (e.key === 'Enter') window.kgwSendMessage();
    });
    document.getElementById('kgwInput').addEventListener('input', kgwOnTyping);

    // Intercept every "Twandikire" / "Twiyungeho" link site-wide so they
    // open this modal instead of navigating to the full chat page —
    // the full page at /twandikire still works fine on its own for
    // anyone who lands there directly (bookmark, search engine, no-JS).
    document.querySelectorAll('a[href="{{ route('guest.twandikire') }}"]').forEach(function (link) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        window.openKwegerezaChat();
      });
    });

    // If a guest already has an open conversation, keep the badge fed
    // even before they open the widget for the first time this visit.
    if (localStorage.getItem('guest_id')) {
      kgwLoadMessages(true).then(() => { kgwLastCount = 0; });
      kgwPing();
      setInterval(function () {
        if (!document.getElementById('kgwOverlay').classList.contains('open')) kgwLoadMessages(false);
      }, 4000);
    }
  });
})();
</script>
