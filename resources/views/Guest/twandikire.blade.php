<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<div class="chat-page">

  <!-- NAME SCREEN -->
  <div id="nameScreen" class="name-screen">
    <div class="name-card">
      <div class="logo-wrap">
        <div class="logo-ring">
          <span class="logo-icon">💬</span>
        </div>
        <span class="online-dot"></span>
      </div>
      <h2>Live Support</h2>
      <p>Our team typically replies in under a minute. Enter your name to get started.</p>
      <div class="input-wrap">
        <i class="fa fa-user input-icon"></i>
        <input type="text" id="guestName" placeholder="Your name" autocomplete="off" onkeypress="if(event.key==='Enter'){startChat()}">
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
    <div id="messages" class="chat-messages">
      <!-- JS-rendered messages go here -->
    </div>

    <!-- TYPING INDICATOR -->
    <div id="typingIndicator" class="typing-row" style="display:none;">
      <div class="typing-avatar">🎧</div>
      <div class="typing-bubble">
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
      </div>
    </div>

    <!-- INPUT BAR -->
    <div class="chat-input-bar">
      <input
        type="text"
        id="message"
        placeholder="Type a message…"
        autocomplete="off"
        onkeypress="if(event.key==='Enter'){sendMessage()}"
      >
      <button class="send-btn" onclick="sendMessage()">
        <i class="fa fa-paper-plane"></i>
      </button>
    </div>

  </div>
</div>

<style>
/* ============================
   RESET + BASE
============================ */
*, *::before, *::after {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html, body {
  width: 100%;
  height: 100%;
  overflow: hidden;
  font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
  background: #e5ddd5;
}

/* ============================
   FULL PAGE WRAPPER
============================ */
.chat-page {
  width: 100%;
  height: 100vh;
  display: flex;
  align-items: stretch;
  justify-content: center;
  background: #e5ddd5;
}

/* ============================
   NAME SCREEN
============================ */
.name-screen {
  position: fixed;
  inset: 0;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  background: linear-gradient(160deg, #075e54 0%, #128c7e 50%, #25d366 100%);
}

.name-card {
  width: 100%;
  max-width: 420px;
  background: #fff;
  border-radius: 20px;
  padding: 40px 30px 36px;
  text-align: center;
  box-shadow: 0 24px 64px rgba(0,0,0,.22);
}

.logo-wrap {
  position: relative;
  display: inline-block;
  margin-bottom: 22px;
}

.logo-ring {
  width: 76px;
  height: 76px;
  border-radius: 50%;
  background: linear-gradient(135deg, #25d366, #128c7e);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 34px;
  box-shadow: 0 6px 20px rgba(37,211,102,.35);
}

.online-dot {
  position: absolute;
  bottom: 4px;
  right: 4px;
  width: 16px;
  height: 16px;
  background: #25d366;
  border: 2.5px solid #fff;
  border-radius: 50%;
}

.name-card h2 {
  font-size: 26px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 10px;
  letter-spacing: -.4px;
}

.name-card p {
  color: #6b7280;
  font-size: 14.5px;
  line-height: 1.65;
  margin-bottom: 28px;
}

.input-wrap {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
  font-size: 15px;
}

.name-card input {
  width: 100%;
  height: 52px;
  border: 1.5px solid #e5e7eb;
  border-radius: 12px;
  padding: 0 16px 0 42px;
  font-size: 15px;
  outline: none;
  background: #f9fafb;
  color: #111827;
  transition: border-color .2s;
}

.name-card input:focus {
  border-color: #25d366;
  background: #fff;
}

.start-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  width: 100%;
  height: 52px;
  margin-top: 14px;
  border: none;
  border-radius: 12px;
  background: linear-gradient(135deg, #25d366, #128c7e);
  color: #fff;
  font-size: 15.5px;
  font-weight: 600;
  cursor: pointer;
  letter-spacing: .1px;
  box-shadow: 0 4px 14px rgba(37,211,102,.35);
  transition: opacity .18s, transform .18s;
}

.start-btn:hover {
  opacity: .92;
  transform: translateY(-1px);
}

.start-btn:active {
  transform: translateY(0);
  opacity: 1;
}

/* ============================
   CHAT CONTAINER
============================ */
.chat-container {
  width: 100%;
  max-width: 720px;
  height: 100vh;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  background: #e5ddd5;
  position: relative;
  overflow: hidden;
}

/* Subtle WhatsApp-style tile background */
.chat-container::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image:
    repeating-linear-gradient(
      45deg,
      rgba(0,0,0,.015) 0,
      rgba(0,0,0,.015) 1px,
      transparent 0,
      transparent 50%
    );
  background-size: 18px 18px;
  pointer-events: none;
  z-index: 0;
}

/* ============================
   HEADER
============================ */
.chat-header {
  position: relative;
  z-index: 10;
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 18px;
  background: #075e54;
  box-shadow: 0 2px 8px rgba(0,0,0,.18);
  flex-shrink: 0;
}

.header-avatar {
  position: relative;
  flex-shrink: 0;
}

.avatar-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: rgba(255,255,255,.15);
  font-size: 22px;
}

.header-online-dot {
  position: absolute;
  bottom: 1px;
  right: 1px;
  width: 12px;
  height: 12px;
  background: #25d366;
  border: 2px solid #075e54;
  border-radius: 50%;
}

.header-info h3 {
  font-size: 16px;
  font-weight: 600;
  color: #fff;
  letter-spacing: .1px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.status-text {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12.5px;
  color: rgba(255,255,255,.75);
  margin-top: 1px;
}

.status-dot {
  display: inline-block;
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #25d366;
  flex-shrink: 0;
}

/* Unread badge */
.badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 20px;
  height: 20px;
  padding: 0 5px;
  border-radius: 10px;
  background: #25d366;
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  line-height: 1;
}

/* ============================
   MESSAGES AREA
============================ */
.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 16px 14px 10px;
  display: flex;
  flex-direction: column;
  gap: 3px;
  position: relative;
  z-index: 1;
  scroll-behavior: smooth;
}

.chat-messages::-webkit-scrollbar { width: 4px; }
.chat-messages::-webkit-scrollbar-track { background: transparent; }
.chat-messages::-webkit-scrollbar-thumb { background: rgba(0,0,0,.2); border-radius: 4px; }

/* ---- Message rows ---- */
.message {
  display: flex;
  align-items: flex-end;
  gap: 6px;
  max-width: 80%;
  animation: msgPop .18s ease-out;
}

@keyframes msgPop {
  from { opacity: 0; transform: translateY(6px) scale(.97); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* Guest messages — right side */
.message.guest {
  align-self: flex-end;
  flex-direction: row-reverse;
}

/* Admin messages — left side */
.message.admin {
  align-self: flex-start;
}

/* Bubble */
.bubble {
  padding: 9px 13px;
  border-radius: 18px;
  font-size: 14.5px;
  line-height: 1.5;
  word-break: break-word;
  position: relative;
  max-width: 100%;
}

.message.guest .bubble {
  background: #dcf8c6;
  color: #111827;
  border-bottom-right-radius: 5px;
  box-shadow: 0 1px 2px rgba(0,0,0,.12);
}

.message.admin .bubble {
  background: #fff;
  color: #111827;
  border-bottom-left-radius: 5px;
  box-shadow: 0 1px 2px rgba(0,0,0,.10);
}

/* Timestamp */
.msg-time {
  font-size: 11px;
  color: #9ca3af;
  margin-top: 3px;
  padding: 0 4px;
}

.message.guest .msg-time { text-align: right; }
.message.admin .msg-time { text-align: left; }

/* ============================
   TYPING INDICATOR
============================ */
.typing-row {
  display: flex;
  align-items: flex-end;
  gap: 6px;
  padding: 4px 14px 6px;
  position: relative;
  z-index: 1;
}

.typing-avatar {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  box-shadow: 0 1px 3px rgba(0,0,0,.1);
  flex-shrink: 0;
}

.typing-bubble {
  background: #fff;
  border-radius: 18px;
  border-bottom-left-radius: 5px;
  padding: 11px 16px;
  display: flex;
  gap: 4px;
  align-items: center;
  box-shadow: 0 1px 2px rgba(0,0,0,.10);
}

.typing-bubble .dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #aaa;
  animation: typingBounce 1.2s infinite ease-in-out;
}

.typing-bubble .dot:nth-child(2) { animation-delay: .18s; }
.typing-bubble .dot:nth-child(3) { animation-delay: .36s; }

@keyframes typingBounce {
  0%, 60%, 100% { transform: translateY(0); opacity: .5; }
  30%            { transform: translateY(-5px); opacity: 1; }
}

/* ============================
   INPUT BAR
============================ */
.chat-input-bar {
  position: relative;
  z-index: 10;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  background: #f0f0f0;
  flex-shrink: 0;
  /* Prevent keyboard from hiding input on mobile */
  padding-bottom: max(10px, env(safe-area-inset-bottom));
}

.chat-input-bar input {
  flex: 1;
  height: 46px;
  border: none;
  border-radius: 24px;
  padding: 0 18px;
  font-size: 15px;
  outline: none;
  background: #fff;
  color: #111827;
  box-shadow: 0 1px 3px rgba(0,0,0,.08);
}

.chat-input-bar input::placeholder { color: #9ca3af; }

.send-btn {
  width: 46px;
  height: 46px;
  border: none;
  border-radius: 50%;
  background: #075e54;
  color: #fff;
  font-size: 17px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 2px 8px rgba(7,94,84,.35);
  transition: background .18s, transform .15s;
}

.send-btn:hover { background: #128c7e; }
.send-btn:active { transform: scale(.93); }

/* ============================
   RESPONSIVE
============================ */

/* Desktop / large tablet */
@media (min-width: 769px) {
  .chat-container {
    border-radius: 16px;
    margin: 20px auto;
    height: calc(100vh - 40px);
    box-shadow: 0 20px 60px rgba(0,0,0,.2);
  }
}

/* Tablet */
@media (max-width: 768px) {
  .chat-container { max-width: 100%; border-radius: 0; }
  .message { max-width: 85%; }
  .name-card { padding: 34px 24px 30px; }
}

/* Mobile */
@media (max-width: 480px) {
  .chat-header { padding: 10px 14px; }
  .chat-messages { padding: 12px 10px 8px; }
  .message { max-width: 90%; }
  .bubble { font-size: 14px; padding: 8px 11px; }
  .msg-time { font-size: 10.5px; }
  .chat-input-bar { padding: 8px 10px; padding-bottom: max(8px, env(safe-area-inset-bottom)); }
  .chat-input-bar input { height: 43px; font-size: 14.5px; }
  .send-btn { width: 43px; height: 43px; font-size: 15px; }
  .name-card { padding: 28px 18px 26px; border-radius: 16px; }
  .name-card h2 { font-size: 22px; }
  .logo-ring { width: 64px; height: 64px; font-size: 28px; }
}

/* Very small phones */
@media (max-width: 360px) {
  .name-card { padding: 22px 14px 20px; }
  .name-card h2 { font-size: 20px; }
  .name-card p { font-size: 13px; margin-bottom: 20px; }
  .name-card input, .start-btn { height: 48px; font-size: 14px; }
  .bubble { font-size: 13.5px; }
}

/* Landscape on small screens */
@media (max-height: 500px) and (max-width: 768px) {
  .chat-header { padding: 8px 14px; }
  .avatar-icon { width: 36px; height: 36px; font-size: 18px; }
  .header-info h3 { font-size: 14.5px; }
  .status-text { font-size: 11.5px; }
  .chat-messages { padding: 8px 10px 6px; }
  .chat-input-bar { padding: 6px 10px; }
}
</style>

<script>
let guestName = '';
let lastMessageCount = 0;

/* ── AUTO-OPEN ── */
window.onload = async function () {
  const guestID    = localStorage.getItem('guest_id');
  const storedName = localStorage.getItem('guest_name');
  if (!guestID || !storedName) return;
  guestName = storedName;
  showChat();
  loadMessages();
};

/* ── START CHAT ── */
function startChat() {
  const name = document.getElementById('guestName').value.trim();
  if (!name) { alert('Please enter your name'); return; }
  guestName = name;

  let guestID = localStorage.getItem('guest_id');
  if (!guestID) {
    guestID = window.crypto?.randomUUID ? window.crypto.randomUUID() : generateUUID();
    localStorage.setItem('guest_id', guestID);
  }
  localStorage.setItem('guest_name', name);
  showChat();
  loadMessages();
}

function showChat() {
  document.getElementById('nameScreen').style.display = 'none';
  document.getElementById('chatScreen').style.display  = 'flex';
}

/* ── UUID FALLBACK ── */
function generateUUID() {
  return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
    const r = Math.random() * 16 | 0;
    return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
  });
}

/* ── TIME AGO ── */
function timeAgo(dateString) {
  const secs = Math.floor((new Date() - new Date(dateString)) / 1000);
  if (secs < 60)    return 'Just now';
  if (secs < 3600)  return Math.floor(secs / 60)   + ' min ago';
  if (secs < 86400) return Math.floor(secs / 3600)  + ' hr ago';
  return Math.floor(secs / 86400) + ' day ago';
}

/* ── UNREAD BADGE ── */
function updateUnread(messages) {
  const badge = document.getElementById('unreadBadge');
  if (messages.length > lastMessageCount && lastMessageCount > 0) {
    const diff = messages.length - lastMessageCount;
    badge.innerText = diff;
    badge.style.display = 'inline-flex';
    setTimeout(() => { badge.style.display = 'none'; }, 4000);
  }
  lastMessageCount = messages.length;
}

/* ── LOAD MESSAGES ── */
async function loadMessages() {
  try {
    const guestID = localStorage.getItem('guest_id');
    if (!guestID) return;

    const response = await fetch(`/chat/messages/${guestID}`);
    if (!response.ok) { console.log('Load error'); return; }

    const messages = await response.json();
    updateUnread(messages);

    const box = document.getElementById('messages');
    const wasAtBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 80;

    let html = '';
    messages.forEach(msg => {
      html += `
        <div class="message ${msg.sender_type}">
          <div>
            <div class="bubble">${msg.message}</div>
            <div class="msg-time">${timeAgo(msg.created_at)}</div>
          </div>
        </div>`;
    });

    box.innerHTML = html;

    if (wasAtBottom || messages.length <= 1) {
      box.scrollTop = box.scrollHeight;
    }
  } catch(e) { console.log(e); }
}

/* ── SEND MESSAGE ── */
async function sendMessage() {
  const input  = document.getElementById('message');
  const text   = input.value.trim();
  if (!text) return;

  const guestID = localStorage.getItem('guest_id');
  if (!guestID) { alert('Session expired. Reload page.'); return; }

  try {
    const response = await fetch('/chat/send', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({
        sender_type: 'guest',
        sender_name: guestName,
        guest_id:    guestID,
        message:     text
      })
    });

    const result = await response.json();
    if (!response.ok) { console.log(result); alert('Message not sent.'); return; }

    input.value = '';
    loadMessages();
  } catch(e) { console.log(e); }
}

/* ── AUTO REFRESH (2 s) ── */
setInterval(() => {
  if (document.getElementById('chatScreen').style.display === 'flex') loadMessages();
}, 2000);

/* ── PRESENCE PING (10 s) ── */
setInterval(async () => {
  const guestID = localStorage.getItem('guest_id');
  if (!guestID) return;
  await fetch('/chat/presence', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({ guest_id: guestID })
  });
}, 10000);
</script>