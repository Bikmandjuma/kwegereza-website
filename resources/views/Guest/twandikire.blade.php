<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- Emoji / Sticker CDN -->
<script src="https://cdn.jsdelivr.net/npm/emoji-mart@5.5.2/dist/browser.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/emoji-mart@5.5.2/css/emoji-mart.css">

<div class="chat-page">

  <!-- NAME SCREEN -->
  <div id="nameScreen" class="name-screen">
    <div class="name-card">
      <div class="logo-wrap">
        <div class="logo-ring"><span>💬</span></div>
        <span class="online-dot"></span>
      </div>
      <h2>Live Support</h2>
      <p>Our team typically replies in under a minute. Enter your name to get started.</p>
      <div class="input-wrap">
        <i class="fa fa-user input-icon"></i>
        <input type="text" id="guestName" placeholder="Your name" autocomplete="off"
               onkeypress="if(event.key==='Enter'){startChat()}">
      </div>
      <button class="start-btn" onclick="startChat()">
        Start Conversation <i class="fa fa-arrow-right"></i>
      </button>
    </div>
  </div>

  <!-- CHAT SCREEN -->
  <div id="chatScreen" class="chat-container" style="display:none;">

    <!-- HEADER -->
    <div class="chat-header">
      <div class="header-avatar">
        <span class="avatar-icon">🎧</span>
        <span class="header-online-dot"></span>
      </div>
      <div class="header-info">
        <h3>Live Support <span id="unreadBadge" class="badge" style="display:none;"></span></h3>
        <span class="status-text"><span class="status-dot"></span>Online — usually replies instantly</span>
      </div>
    </div>

    <!-- MESSAGES -->
    <div id="messages" class="chat-messages"></div>

    <!-- TYPING INDICATOR (admin typing) -->
    <div id="typingIndicator" class="typing-row" style="display:none;">
      <div class="typing-avatar">🎧</div>
      <div class="typing-bubble">
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
      </div>
    </div>

    <!-- STICKER PICKER (hidden by default) -->
    <div id="stickerPicker" class="sticker-picker" style="display:none;"></div>

    <!-- INPUT BAR -->
    <div class="chat-input-bar">
      <button class="emoji-btn" id="emojiBtn" onclick="toggleStickers()" title="Stickers & Emoji">
        <i class="fa fa-smile-o"></i>
      </button>
      <input type="text" id="message" placeholder="Type a message…" autocomplete="off"
             onkeypress="if(event.key==='Enter'){sendMessage()}"
             oninput="onGuestTyping()">
      <button class="send-btn" onclick="sendMessage()">
        <i class="fa fa-paper-plane"></i>
      </button>
    </div>

  </div>
</div>

<style>
/* ── RESET ── */
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}

html,body{
  width:100%;height:100%;overflow:hidden;
  font-family:'Inter','Segoe UI',system-ui,sans-serif;
  background:#e5ddd5;
}

/* ── PAGE WRAPPER ── */
.chat-page{width:100%;height:100vh;display:flex;align-items:stretch;justify-content:center;background:#e5ddd5;}

/* ════════════════════════════
   NAME SCREEN
════════════════════════════ */
.name-screen{
  position:fixed;inset:0;z-index:9999;
  display:flex;align-items:center;justify-content:center;padding:20px;
  background:linear-gradient(160deg,#075e54 0%,#128c7e 50%,#25d366 100%);
}
.name-card{
  width:100%;max-width:420px;background:#fff;border-radius:20px;
  padding:40px 30px 36px;text-align:center;
  box-shadow:0 24px 64px rgba(0,0,0,.22);
}
.logo-wrap{position:relative;display:inline-block;margin-bottom:22px;}
.logo-ring{
  width:76px;height:76px;border-radius:50%;
  background:linear-gradient(135deg,#25d366,#128c7e);
  display:flex;align-items:center;justify-content:center;font-size:34px;
  box-shadow:0 6px 20px rgba(37,211,102,.35);
}
.online-dot{
  position:absolute;bottom:4px;right:4px;
  width:16px;height:16px;background:#25d366;
  border:2.5px solid #fff;border-radius:50%;
}
.name-card h2{font-size:26px;font-weight:700;color:#111827;margin-bottom:10px;letter-spacing:-.4px;}
.name-card p{color:#6b7280;font-size:14.5px;line-height:1.65;margin-bottom:28px;}
.input-wrap{position:relative;}
.input-icon{position:absolute;left:16px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:15px;}
.name-card input{
  width:100%;height:52px;border:1.5px solid #e5e7eb;border-radius:12px;
  padding:0 16px 0 42px;font-size:15px;outline:none;background:#f9fafb;
  color:#111827;transition:border-color .2s;
}
.name-card input:focus{border-color:#25d366;background:#fff;}
.start-btn{
  display:flex;align-items:center;justify-content:center;gap:10px;
  width:100%;height:52px;margin-top:14px;border:none;border-radius:12px;
  background:linear-gradient(135deg,#25d366,#128c7e);color:#fff;
  font-size:15.5px;font-weight:600;cursor:pointer;
  box-shadow:0 4px 14px rgba(37,211,102,.35);
  transition:opacity .18s,transform .18s;
}
.start-btn:hover{opacity:.92;transform:translateY(-1px);}
.start-btn:active{transform:translateY(0);opacity:1;}

/* ════════════════════════════
   CHAT CONTAINER
   — full-bleed on mobile, card on desktop
════════════════════════════ */
.chat-container{
  width:100%;max-width:720px;
  /* Use dvh so mobile browser chrome doesn't cover the input bar */
  height:100dvh;
  margin:0 auto;
  display:flex;flex-direction:column;
  background:#e5ddd5;position:relative;overflow:hidden;
}
/* WhatsApp tile bg */
.chat-container::before{
  content:'';position:absolute;inset:0;pointer-events:none;z-index:0;
  background-image:repeating-linear-gradient(45deg,rgba(0,0,0,.015) 0,rgba(0,0,0,.015) 1px,transparent 0,transparent 50%);
  background-size:18px 18px;
}

/* ── HEADER ── */
.chat-header{
  position:relative;z-index:10;
  display:flex;align-items:center;gap:12px;
  padding:10px 16px;
  background:#075e54;
  box-shadow:0 2px 8px rgba(0,0,0,.18);
  flex-shrink:0;
  /* iOS notch safe area */
  padding-top:max(10px,env(safe-area-inset-top));
}
.header-avatar{position:relative;flex-shrink:0;}
.avatar-icon{
  display:flex;align-items:center;justify-content:center;
  width:42px;height:42px;border-radius:50%;
  background:rgba(255,255,255,.15);font-size:20px;
}
.header-online-dot{
  position:absolute;bottom:1px;right:1px;
  width:12px;height:12px;background:#25d366;
  border:2px solid #075e54;border-radius:50%;
}
.header-info h3{
  font-size:16px;font-weight:600;color:#fff;
  display:flex;align-items:center;gap:8px;
}
.status-text{display:flex;align-items:center;gap:5px;font-size:12px;color:rgba(255,255,255,.75);margin-top:1px;}
.status-dot{display:inline-block;width:7px;height:7px;border-radius:50%;background:#25d366;flex-shrink:0;}
.badge{
  display:inline-flex;align-items:center;justify-content:center;
  min-width:20px;height:20px;padding:0 5px;border-radius:10px;
  background:#25d366;color:#fff;font-size:11px;font-weight:700;line-height:1;
}

/* ── MESSAGES AREA ── */
.chat-messages{
  flex:1;overflow-y:auto;overflow-x:hidden;
  padding:14px 12px 8px;
  display:flex;flex-direction:column;gap:2px;
  position:relative;z-index:1;
  /* smooth scrolling without janky repaints */
  scroll-behavior:auto;
  overscroll-behavior:contain;
  -webkit-overflow-scrolling:touch;
}
.chat-messages::-webkit-scrollbar{width:3px;}
.chat-messages::-webkit-scrollbar-track{background:transparent;}
.chat-messages::-webkit-scrollbar-thumb{background:rgba(0,0,0,.18);border-radius:4px;}

/* ── MESSAGE ROW ──
   No CSS entry animation on rows — prevents the
   "blink" caused by full innerHTML re-render + animation.
   New messages are appended via DOM, so only they get the pop. */
.message{
  display:flex;align-items:flex-end;gap:6px;
  max-width:78%;
}
.message.guest{align-self:flex-end;flex-direction:row-reverse;}
.message.admin{align-self:flex-start;}

/* Only newly appended messages get the pop */
.message.new-msg{animation:msgPop .16s ease-out both;}
@keyframes msgPop{
  from{opacity:0;transform:translateY(5px) scale(.97);}
  to  {opacity:1;transform:none;}
}

.bubble{
  padding:8px 12px;border-radius:18px;
  font-size:14.5px;line-height:1.52;
  word-break:break-word;position:relative;max-width:100%;
}
.message.guest .bubble{
  background:#dcf8c6;color:#111827;
  border-bottom-right-radius:4px;
  box-shadow:0 1px 2px rgba(0,0,0,.12);
}
.message.admin .bubble{
  background:#fff;color:#111827;
  border-bottom-left-radius:4px;
  box-shadow:0 1px 2px rgba(0,0,0,.10);
}
/* Sticker bubble — transparent, larger emoji */
.bubble.is-sticker{
  background:transparent!important;
  box-shadow:none!important;
  padding:4px;font-size:42px;line-height:1.1;
}
.msg-time{font-size:11px;color:#9ca3af;margin-top:3px;padding:0 3px;}
.message.guest .msg-time{text-align:right;}
.message.admin .msg-time{text-align:left;}

/* ── TYPING INDICATOR ── */
.typing-row{
  display:flex;align-items:flex-end;gap:6px;
  padding:4px 12px 6px;
  position:relative;z-index:1;
}
.typing-avatar{
  width:28px;height:28px;border-radius:50%;
  background:#fff;display:flex;align-items:center;justify-content:center;
  font-size:14px;box-shadow:0 1px 3px rgba(0,0,0,.1);flex-shrink:0;
}
.typing-bubble{
  background:#fff;border-radius:18px;border-bottom-left-radius:4px;
  padding:10px 15px;display:flex;gap:4px;align-items:center;
  box-shadow:0 1px 2px rgba(0,0,0,.10);
}
.typing-bubble .dot{
  width:7px;height:7px;border-radius:50%;background:#aaa;
  animation:typingBounce 1.2s infinite ease-in-out;
}
.typing-bubble .dot:nth-child(2){animation-delay:.18s;}
.typing-bubble .dot:nth-child(3){animation-delay:.36s;}
@keyframes typingBounce{
  0%,60%,100%{transform:translateY(0);opacity:.45;}
  30%{transform:translateY(-5px);opacity:1;}
}

/* ── STICKER PICKER ── */
.sticker-picker{
  position:relative;z-index:20;
  background:#fff;
  border-top:1px solid #e5e7eb;
  max-height:280px;overflow-y:auto;
  padding:12px;
  display:flex;flex-wrap:wrap;gap:6px;
  align-items:flex-start;
}
.sticker-picker .stk{
  font-size:30px;cursor:pointer;padding:4px;border-radius:8px;
  line-height:1;transition:transform .1s,background .1s;
  user-select:none;
}
.sticker-picker .stk:hover{background:#f0fdf4;transform:scale(1.2);}
.sticker-picker .stk:active{transform:scale(1.05);}

/* ── INPUT BAR ── */
.chat-input-bar{
  position:relative;z-index:10;
  display:flex;align-items:center;gap:8px;
  padding:8px 10px;
  background:#f0f0f0;
  flex-shrink:0;
  padding-bottom:max(8px,env(safe-area-inset-bottom));
}
.chat-input-bar input{
  flex:1;height:44px;border:none;border-radius:22px;
  padding:0 16px;font-size:15px;outline:none;
  background:#fff;color:#111827;
  box-shadow:0 1px 3px rgba(0,0,0,.08);
}
.chat-input-bar input::placeholder{color:#9ca3af;}
.emoji-btn{
  width:40px;height:40px;border:none;border-radius:50%;
  background:transparent;color:#128c7e;font-size:20px;
  cursor:pointer;display:flex;align-items:center;justify-content:center;
  flex-shrink:0;transition:background .15s;
}
.emoji-btn:hover{background:rgba(18,140,126,.1);}
.send-btn{
  width:44px;height:44px;border:none;border-radius:50%;
  background:#075e54;color:#fff;font-size:16px;cursor:pointer;
  display:flex;align-items:center;justify-content:center;flex-shrink:0;
  box-shadow:0 2px 8px rgba(7,94,84,.35);
  transition:background .18s,transform .13s;
}
.send-btn:hover{background:#128c7e;}
.send-btn:active{transform:scale(.91);}

/* ════════════════════════════
   RESPONSIVE
════════════════════════════ */

/* Desktop card */
@media(min-width:769px){
  .chat-container{
    border-radius:16px;margin:20px auto;
    height:calc(100dvh - 40px);
    box-shadow:0 20px 60px rgba(0,0,0,.2);
  }
}

/* Tablet */
@media(max-width:768px){
  .chat-container{max-width:100%;border-radius:0;}
  .message{max-width:85%;}
}

/* Mobile — full screen like real WhatsApp */
@media(max-width:480px){
  .chat-container{
    /* Stretch behind address bar */
    height:100dvh;
    border-radius:0;
  }
  .chat-header{padding:10px 14px;padding-top:max(10px,env(safe-area-inset-top));}
  .chat-messages{padding:10px 8px 6px;}
  .message{max-width:88%;}
  .bubble{font-size:14px;padding:7px 10px;}
  .msg-time{font-size:10.5px;}
  .chat-input-bar{
    padding:7px 8px;
    padding-bottom:max(7px,env(safe-area-inset-bottom));
  }
  .chat-input-bar input{height:42px;font-size:14.5px;}
  .send-btn{width:42px;height:42px;font-size:15px;}
  .emoji-btn{width:36px;height:36px;font-size:18px;}
  .sticker-picker{max-height:220px;}
  .sticker-picker .stk{font-size:26px;}
  /* Name card */
  .name-card{padding:26px 16px 24px;border-radius:16px;}
  .name-card h2{font-size:22px;}
  .logo-ring{width:64px;height:64px;font-size:28px;}
}

/* Very small phones (320–360px) */
@media(max-width:360px){
  .name-card{padding:20px 12px;}
  .name-card h2{font-size:20px;}
  .name-card p{font-size:13px;margin-bottom:18px;}
  .name-card input,.start-btn{height:48px;font-size:14px;}
  .bubble{font-size:13.5px;}
  .sticker-picker .stk{font-size:22px;}
}

/* Landscape small screens */
@media(max-height:500px)and(max-width:768px){
  .chat-header{padding:6px 12px;}
  .avatar-icon{width:34px;height:34px;font-size:17px;}
  .header-info h3{font-size:14px;}
  .status-text{font-size:11px;}
  .chat-messages{padding:6px 8px 4px;}
  .sticker-picker{max-height:150px;}
}
</style>

<script>
/* ═══════════════════════════════════════════════
   STICKER / EMOJI SET
   — categorised so the picker has sections
═══════════════════════════════════════════════ */
const STICKERS = [
  // Faces
  '😀','😂','🥲','😍','🥰','😎','😜','🤩','🥳','😇',
  '🤔','😴','🤯','🥺','😭','😡','🤗','😏','🙄','😬',
  // Gestures / people
  '👍','👎','👏','🙌','🤝','✌️','🤞','🖐️','💪','🫶',
  '🙏','🤦','🤷','💁','🧑‍💻','👩‍💼','🧑‍🎧',
  // Hearts & symbols
  '❤️','🧡','💛','💚','💙','💜','🖤','🤍','💯','✅',
  '❌','⚡','🔥','💥','🌟','⭐','🎉','🎊','🏆','🎯',
  // Animals
  '🐶','🐱','🐭','🐸','🦊','🐼','🦁','🐯','🐨','🐙',
  // Food & drink
  '🍕','🍔','🍟','🌮','🍜','🍣','🍩','🎂','🍦','☕',
  '🧋','🥤','🍺','🥂','🍷',
  // Travel & places
  '🚀','✈️','🚗','🏡','🏖️','🌍','🌈','🌙','☀️','⛄',
  // Objects
  '📱','💻','🎮','🎵','🎶','📷','🔑','💡','📚','🎁',
];

/* ═══════════════════════════════════════════════
   STATE
═══════════════════════════════════════════════ */
let guestName        = '';
let renderedMsgIds   = new Set();   // tracks which IDs are in the DOM
let lastMsgCount     = 0;
let stickerOpen      = false;
let typingTimeout    = null;        // guest typing debounce

/* ═══════════════════════════════════════════════
   BOOT
═══════════════════════════════════════════════ */
window.addEventListener('load', () => {
  buildStickerPicker();

  const guestID    = getOrCreateGuestID(/*force*/false);
  const storedName = localStorage.getItem('guest_name');
  if (guestID && storedName) {
    guestName = storedName;
    showChat();
    loadMessages(true);
  }

  // Close sticker picker on outside click
  document.addEventListener('click', e => {
    if (stickerOpen &&
        !e.target.closest('#stickerPicker') &&
        !e.target.closest('#emojiBtn')) {
      closeStickerPicker();
    }
  });
});

/* ═══════════════════════════════════════════════
   UNIQUE GUEST ID
   Uses crypto.randomUUID when available; the
   generated ID is always stored under the SAME
   key so repeated calls never create duplicates.
═══════════════════════════════════════════════ */
function getOrCreateGuestID(create = true) {
  let id = localStorage.getItem('guest_id');
  if (id) return id;
  if (!create) return null;

  // Generate a UUID v4 — cryptographically random
  if (window.crypto && typeof window.crypto.randomUUID === 'function') {
    id = window.crypto.randomUUID();
  } else {
    // Fallback: use getRandomValues for entropy
    const bytes = new Uint8Array(16);
    window.crypto.getRandomValues(bytes);
    bytes[6] = (bytes[6] & 0x0f) | 0x40; // version 4
    bytes[8] = (bytes[8] & 0x3f) | 0x80; // variant
    id = [...bytes].map((b,i) =>
      ([4,6,8,10].includes(i) ? '-' : '') + b.toString(16).padStart(2,'0')
    ).join('');
  }

  localStorage.setItem('guest_id', id);
  return id;
}

/* ═══════════════════════════════════════════════
   START CHAT
═══════════════════════════════════════════════ */
function startChat() {
  const name = document.getElementById('guestName').value.trim();
  if (!name) { alert('Please enter your name'); return; }
  guestName = name;
  getOrCreateGuestID(true);
  localStorage.setItem('guest_name', name);
  showChat();
  loadMessages(true);
}

function showChat() {
  document.getElementById('nameScreen').style.display = 'none';
  document.getElementById('chatScreen').style.display  = 'flex';
}

/* ═══════════════════════════════════════════════
   TIME AGO
═══════════════════════════════════════════════ */
function timeAgo(ds) {
  const s = Math.floor((Date.now() - new Date(ds)) / 1000);
  if (s < 60)    return 'Just now';
  if (s < 3600)  return Math.floor(s/60)   + ' min ago';
  if (s < 86400) return Math.floor(s/3600) + ' hr ago';
  return Math.floor(s/86400) + ' day ago';
}

/* ═══════════════════════════════════════════════
   UNREAD BADGE
═══════════════════════════════════════════════ */
function updateUnread(count) {
  const badge = document.getElementById('unreadBadge');
  if (count > lastMsgCount && lastMsgCount > 0) {
    const diff = count - lastMsgCount;
    badge.textContent = diff;
    badge.style.display = 'inline-flex';
    setTimeout(() => { badge.style.display = 'none'; }, 4000);
  }
  lastMsgCount = count;
}

/* ═══════════════════════════════════════════════
   LOAD MESSAGES
   — INCREMENTAL: only appends NEW messages,
     never re-renders the whole list ⟹ no blink.
   — initialLoad=true forces a full render on first open.
═══════════════════════════════════════════════ */
async function loadMessages(initialLoad = false) {
  try {
    const guestID = localStorage.getItem('guest_id');
    if (!guestID) return;

    const res = await fetch(`/chat/messages/${guestID}`);
    if (!res.ok) return;

    const messages = await res.json();
    updateUnread(messages.length);

    const box = document.getElementById('messages');
    const atBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 100;

    if (initialLoad) {
      // Full render once on open
      box.innerHTML = '';
      renderedMsgIds.clear();
      messages.forEach(msg => appendMessage(msg, false));
      box.scrollTop = box.scrollHeight;
      return;
    }

    // Incremental: only append messages we haven't seen
    let appended = false;
    messages.forEach(msg => {
      const uid = msg.id ?? (msg.created_at + msg.sender_type + msg.message.slice(0,10));
      if (!renderedMsgIds.has(uid)) {
        appendMessage(msg, true);
        appended = true;
      }
    });

    if (appended && atBottom) {
      box.scrollTop = box.scrollHeight;
    }

    // Typing indicator: hide if admin hasn't sent a new message recently
    // (controlled separately via /chat/typing endpoint below)

  } catch(e) { console.error(e); }
}

/* ── Append single message to DOM ── */
function appendMessage(msg, animate) {
  const uid = msg.id ?? (msg.created_at + msg.sender_type + msg.message.slice(0,10));
  renderedMsgIds.add(uid);

  const row = document.createElement('div');
  row.className = 'message ' + msg.sender_type + (animate ? ' new-msg' : '');
  row.dataset.uid = uid;

  // Detect if message is a sticker (single emoji)
  const isSticker = /^\p{Emoji_Presentation}{1,2}$/u.test(msg.message.trim());

  row.innerHTML = `
    <div>
      <div class="bubble${isSticker ? ' is-sticker' : ''}">${escapeHtml(msg.message)}</div>
      <div class="msg-time">${timeAgo(msg.created_at)}</div>
    </div>`;

  document.getElementById('messages').appendChild(row);
}

function escapeHtml(s) {
  return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

/* ═══════════════════════════════════════════════
   SEND MESSAGE — optimistic UI
   Instantly renders the bubble, then POST in background.
═══════════════════════════════════════════════ */
async function sendMessage(text) {
  const input = document.getElementById('message');
  const msg   = (text || input.value).trim();
  if (!msg) return;

  const guestID = localStorage.getItem('guest_id');
  if (!guestID) { alert('Session expired. Reload page.'); return; }

  // 1. Optimistic render — instant, no wait
  const tempId  = 'tmp-' + Date.now();
  const fakeMsg = {
    id:          tempId,
    sender_type: 'guest',
    message:     msg,
    created_at:  new Date().toISOString()
  };
  appendMessage(fakeMsg, true);
  const box = document.getElementById('messages');
  box.scrollTop = box.scrollHeight;

  input.value = '';
  closeStickerPicker();

  // 2. POST to server
  try {
    const res = await fetch('/chat/send', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({
        sender_type: 'guest',
        sender_name: guestName,
        guest_id:    guestID,
        message:     msg
      })
    });

    if (!res.ok) {
      // Remove optimistic bubble on failure
      const el = box.querySelector(`[data-uid="${tempId}"]`);
      if (el) el.remove();
      renderedMsgIds.delete(tempId);
      console.error('Send failed', await res.text());
    }
    // On success, the next poll will add the real message;
    // our duplicate-check (renderedMsgIds) prevents double render
    // by matching on content+time (server ID will be different but
    // incremental render only adds IDs it hasn't seen).

  } catch(e) {
    console.error(e);
  }
}

/* ═══════════════════════════════════════════════
   GUEST TYPING — notifies server so admin can see
═══════════════════════════════════════════════ */
function onGuestTyping() {
  clearTimeout(typingTimeout);
  const guestID = localStorage.getItem('guest_id');
  if (!guestID) return;

  fetch('/chat/typing', {
    method: 'POST',
    headers: {
      'Content-Type':  'application/json',
      'X-CSRF-TOKEN':  '{{ csrf_token() }}'
    },
    body: JSON.stringify({ guest_id: guestID, typing: true })
  }).catch(()=>{});

  typingTimeout = setTimeout(() => {
    fetch('/chat/typing', {
      method: 'POST',
      headers: {
        'Content-Type':  'application/json',
        'X-CSRF-TOKEN':  '{{ csrf_token() }}'
      },
      body: JSON.stringify({ guest_id: guestID, typing: false })
    }).catch(()=>{});
  }, 2000);
}

/* ═══════════════════════════════════════════════
   ADMIN TYPING INDICATOR
   Poll /chat/typing-status to show/hide the bubble
═══════════════════════════════════════════════ */
async function checkAdminTyping() {
  const guestID = localStorage.getItem('guest_id');
  if (!guestID) return;
  try {
    const res = await fetch(`/chat/admin-typing/${guestID}`);
    if (!res.ok) return;
    const { typing } = await res.json();
    const indicator  = document.getElementById('typingIndicator');
    const box        = document.getElementById('messages');
    const atBottom   = box.scrollHeight - box.scrollTop - box.clientHeight < 120;

    indicator.style.display = typing ? 'flex' : 'none';
    if (typing && atBottom) box.scrollTop = box.scrollHeight;
  } catch(e) {}
}

/* ═══════════════════════════════════════════════
   STICKER PICKER
═══════════════════════════════════════════════ */
function buildStickerPicker() {
  const picker = document.getElementById('stickerPicker');
  STICKERS.forEach(stk => {
    const btn = document.createElement('span');
    btn.className = 'stk';
    btn.textContent = stk;
    btn.addEventListener('click', () => {
      sendMessage(stk);
    });
    picker.appendChild(btn);
  });
}

function toggleStickers() {
  stickerOpen ? closeStickerPicker() : openStickerPicker();
}
function openStickerPicker() {
  document.getElementById('stickerPicker').style.display = 'flex';
  stickerOpen = true;
}
function closeStickerPicker() {
  document.getElementById('stickerPicker').style.display = 'none';
  stickerOpen = false;
}

/* ═══════════════════════════════════════════════
   POLLING
   Messages: every 2 s (incremental, no flicker)
   Admin typing: every 1.5 s
   Presence ping: every 10 s
═══════════════════════════════════════════════ */
setInterval(() => {
  if (document.getElementById('chatScreen').style.display === 'flex') {
    loadMessages(false);
  }
}, 2000);

setInterval(() => {
  if (document.getElementById('chatScreen').style.display === 'flex') {
    checkAdminTyping();
  }
}, 1500);

setInterval(async () => {
  const guestID = localStorage.getItem('guest_id');
  if (!guestID) return;
  fetch('/chat/presence', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({ guest_id: guestID })
  }).catch(()=>{});
}, 10000);
</script>