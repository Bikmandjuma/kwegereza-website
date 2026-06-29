<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<!-- ══════════════════════════════════════════
     ADMIN CHAT — WhatsApp-style
══════════════════════════════════════════ -->
<div class="admin-chat">

  <!-- ── SIDEBAR ── -->
  <div class="sidebar" id="sidebar">

    <div class="sidebar-header">
      <div class="sidebar-brand">
        <span class="brand-icon">🎧</span>
        <div>
          <h2>Live Chats</h2>
          <span id="totalBadge" class="total-badge"></span>
        </div>
      </div>
    </div>

    <div class="search-wrap">
      <i class="fa fa-search search-icon"></i>
      <input type="text" id="searchGuest" placeholder="Search conversations…" autocomplete="off">
    </div>

    <div id="conversationList" class="conversation-list"></div>

  </div>

  <!-- ── CHAT AREA ── -->
  <div class="chat-area" id="chatArea">

    <!-- Header -->
    <div class="chat-header">
      <div class="header-left">
        <button class="back-btn" id="backBtn" onclick="closeMobileChat()">
          <i class="fa fa-arrow-left"></i>
        </button>
        <div class="header-avatar" id="headerAvatar">
          <span id="avatarInitial">?</span>
          <span class="hav-dot" id="headerDot"></span>
        </div>
        <div class="header-info">
          <h3 id="guestTitle">Select a conversation</h3>
          <span id="guestStatus" class="guest-status offline-text">Select a conversation to start</span>
        </div>
      </div>
      <a href="{{ route('owner.dashboard') }}" class="dashboard-btn">
        <i class="fa fa-th-large"></i><span> Dashboard</span>
      </a>
    </div>

    <!-- Messages -->
    <div id="messages" class="messages">
      <div class="empty-chat">
        <div class="empty-icon">💬</div>
        <p>Select a guest conversation<br>to start chatting</p>
      </div>
    </div>

    <!-- Typing indicator -->
    <div id="typingIndicator" class="typing-row" style="display:none;">
      <div class="typing-bubble">
        <span class="dot"></span><span class="dot"></span><span class="dot"></span>
      </div>
      <span class="typing-label">Guest is typing</span>
    </div>

    <!-- Input bar -->
    <div class="message-box">
      <input
        id="messageInput"
        type="text"
        placeholder="Type a message…"
        autocomplete="off"
        onkeypress="if(event.key==='Enter'){sendAdminMessage()}"
      >
      <button class="send-btn" onclick="sendAdminMessage()">
        <i class="fa fa-paper-plane"></i>
      </button>
    </div>

  </div>
</div>

<!-- ══════════════════════════════════════════
     STYLES
══════════════════════════════════════════ -->
<style>
/* ── RESET ── */
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
html,body{height:100%;overflow:hidden;font-family:'Inter','Segoe UI',system-ui,sans-serif;background:#e5ddd5;}

/* ══════════════════════
   LAYOUT
══════════════════════ */
.admin-chat{display:flex;height:100vh;overflow:hidden;}

/* ══════════════════════
   SIDEBAR
══════════════════════ */
.sidebar{
  width:340px;min-width:300px;
  display:flex;flex-direction:column;
  background:#fff;
  border-right:1px solid #e2e8f0;
  flex-shrink:0;
  overflow:hidden;
}

.sidebar-header{
  padding:14px 18px 12px;
  background:#075e54;
  flex-shrink:0;
}
.sidebar-brand{display:flex;align-items:center;gap:12px;}
.brand-icon{
  width:40px;height:40px;border-radius:50%;
  background:rgba(255,255,255,.15);
  display:flex;align-items:center;justify-content:center;font-size:20px;
}
.sidebar-header h2{font-size:17px;font-weight:700;color:#fff;line-height:1.2;}
.total-badge{
  display:inline-block;font-size:11px;color:rgba(255,255,255,.7);
  margin-top:2px;
}

/* Search */
.search-wrap{
  position:relative;padding:10px 14px;
  background:#f0f2f5;border-bottom:1px solid #e2e8f0;
  flex-shrink:0;
}
.search-icon{position:absolute;left:26px;top:50%;transform:translateY(-50%);color:#aaa;font-size:13px;}
#searchGuest{
  width:100%;height:38px;border:none;outline:none;
  background:#fff;border-radius:20px;
  padding:0 14px 0 34px;font-size:14px;color:#111827;
  box-shadow:0 1px 3px rgba(0,0,0,.07);
}

/* Conversation list */
.conversation-list{flex:1;overflow-y:auto;overflow-x:hidden;}
.conversation-list::-webkit-scrollbar{width:4px;}
.conversation-list::-webkit-scrollbar-thumb{background:rgba(0,0,0,.12);border-radius:4px;}

/* Conversation item */
.conversation{
  display:flex;align-items:center;gap:12px;
  padding:12px 16px;cursor:pointer;
  border-bottom:1px solid #f1f5f9;
  transition:background .15s;
  position:relative;
}
.conversation:hover{background:#f8fafc;}
.conversation.active{background:#e8f5e9;}

/* Guest avatar in list */
.conv-avatar{
  width:46px;height:46px;border-radius:50%;
  background:linear-gradient(135deg,#128c7e,#075e54);
  display:flex;align-items:center;justify-content:center;
  font-size:18px;font-weight:700;color:#fff;
  flex-shrink:0;position:relative;
}
.conv-dot{
  position:absolute;bottom:1px;right:1px;
  width:12px;height:12px;border-radius:50%;
  border:2px solid #fff;
}
.conv-dot.online{background:#25d366;}
.conv-dot.offline{background:#cbd5e1;}

.conv-body{flex:1;min-width:0;}
.conv-top{display:flex;justify-content:space-between;align-items:center;gap:8px;}
.conv-name{
  font-size:14.5px;font-weight:600;color:#111827;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.conv-time{font-size:11px;color:#94a3b8;flex-shrink:0;}
.conv-last{
  font-size:13px;color:#64748b;margin-top:2px;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.conv-last.has-unread{color:#111827;font-weight:500;}

.unread-badge{
  min-width:20px;height:20px;padding:0 5px;
  border-radius:10px;background:#25d366;
  color:#fff;font-size:11px;font-weight:700;
  display:inline-flex;align-items:center;justify-content:center;
  flex-shrink:0;
}

/* ══════════════════════
   CHAT AREA
══════════════════════ */
.chat-area{
  flex:1;display:flex;flex-direction:column;
  background:#e5ddd5;
  position:relative;overflow:hidden;
  min-width:0;
}
/* tile bg */
.chat-area::before{
  content:'';position:absolute;inset:0;pointer-events:none;z-index:0;
  background-image:repeating-linear-gradient(45deg,rgba(0,0,0,.015) 0,rgba(0,0,0,.015) 1px,transparent 0,transparent 50%);
  background-size:18px 18px;
}

/* ── Header ── */
.chat-header{
  position:relative;z-index:10;
  display:flex;align-items:center;justify-content:space-between;
  padding:10px 18px;
  background:#075e54;
  box-shadow:0 2px 6px rgba(0,0,0,.2);
  flex-shrink:0;
}
.header-left{display:flex;align-items:center;gap:10px;min-width:0;}

.back-btn{
  display:none;border:none;background:none;
  color:#fff;font-size:18px;cursor:pointer;
  padding:4px 8px 4px 0;flex-shrink:0;
}

.header-avatar{
  width:40px;height:40px;border-radius:50%;
  background:rgba(255,255,255,.18);
  display:flex;align-items:center;justify-content:center;
  font-size:17px;font-weight:700;color:#fff;
  flex-shrink:0;position:relative;
}
.hav-dot{
  position:absolute;bottom:1px;right:1px;
  width:10px;height:10px;border-radius:50%;
  border:2px solid #075e54;background:#cbd5e1;
}
.hav-dot.online{background:#25d366;}

.header-info h3{font-size:15.5px;font-weight:600;color:#fff;line-height:1.2;}
.guest-status{font-size:12px;margin-top:1px;}
.online-text{color:#9be7c4;}
.offline-text{color:rgba(255,255,255,.55);}

.dashboard-btn{
  display:flex;align-items:center;gap:6px;
  text-decoration:none;background:rgba(255,255,255,.15);
  color:#fff;padding:8px 14px;border-radius:10px;
  font-size:13px;font-weight:600;white-space:nowrap;flex-shrink:0;
  transition:background .15s;
}
.dashboard-btn:hover{background:rgba(255,255,255,.25);}
.dashboard-btn span{display:inline;}

/* ── Messages ── */
.messages{
  flex:1;overflow-y:auto;overflow-x:hidden;
  padding:14px 14px 8px;
  display:flex;flex-direction:column;gap:3px;
  position:relative;z-index:1;
  overscroll-behavior:contain;
  -webkit-overflow-scrolling:touch;
}
.messages::-webkit-scrollbar{width:4px;}
.messages::-webkit-scrollbar-thumb{background:rgba(0,0,0,.15);border-radius:4px;}

.empty-chat{
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  flex:1;gap:12px;color:#94a3b8;text-align:center;padding:40px;
  min-height:200px;
}
.empty-icon{font-size:52px;opacity:.4;}
.empty-chat p{font-size:14px;line-height:1.6;opacity:.7;}

/* Message rows — NO animation on existing, only new */
.message{display:flex;align-items:flex-end;gap:6px;max-width:76%;}
.message.admin{align-self:flex-end;flex-direction:row-reverse;}
.message.guest{align-self:flex-start;}

.message.new-msg{animation:msgPop .15s ease-out both;}
@keyframes msgPop{from{opacity:0;transform:translateY(5px) scale(.97);}to{opacity:1;transform:none;}}

.bubble{
  padding:8px 13px;border-radius:18px;
  font-size:14.5px;line-height:1.52;
  word-break:break-word;position:relative;max-width:100%;
}
.message.admin .bubble{
  background:#075e54;color:#fff;
  border-bottom-right-radius:4px;
  box-shadow:0 1px 2px rgba(0,0,0,.18);
}
.message.guest .bubble{
  background:#fff;color:#111827;
  border-bottom-left-radius:4px;
  box-shadow:0 1px 2px rgba(0,0,0,.10);
}
/* Sticker / emoji-only */
.bubble.is-sticker{background:transparent!important;box-shadow:none!important;padding:4px;font-size:38px;line-height:1.1;}

.msg-time{font-size:11px;color:#9ca3af;margin-top:3px;padding:0 3px;}
.message.admin .msg-time{text-align:right;}
.message.guest .msg-time{text-align:left;}

/* Double-tick for admin */
.msg-tick{font-size:12px;margin-left:3px;opacity:.75;}

/* ── Typing indicator ── */
.typing-row{
  display:flex;align-items:center;gap:8px;
  padding:6px 16px 8px;
  position:relative;z-index:1;
}
.typing-bubble{
  background:#fff;border-radius:18px;border-bottom-left-radius:4px;
  padding:10px 14px;display:flex;gap:4px;align-items:center;
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
.typing-label{font-size:12px;color:#64748b;font-style:italic;}

/* ── Input bar ── */
.message-box{
  position:relative;z-index:10;
  display:flex;align-items:center;gap:10px;
  padding:10px 14px;
  background:#f0f0f0;
  flex-shrink:0;
  padding-bottom:max(10px,env(safe-area-inset-bottom));
}
.message-box input{
  flex:1;height:44px;border:none;border-radius:22px;
  padding:0 18px;font-size:15px;outline:none;
  background:#fff;color:#111827;
  box-shadow:0 1px 3px rgba(0,0,0,.08);
}
.message-box input::placeholder{color:#9ca3af;}
.send-btn{
  width:44px;height:44px;border:none;border-radius:50%;
  background:#075e54;color:#fff;font-size:16px;cursor:pointer;
  display:flex;align-items:center;justify-content:center;flex-shrink:0;
  box-shadow:0 2px 8px rgba(7,94,84,.3);
  transition:background .15s,transform .12s;
}
.send-btn:hover{background:#128c7e;}
.send-btn:active{transform:scale(.91);}

/* ══════════════════════
   RESPONSIVE
══════════════════════ */
@media(max-width:768px){
  .sidebar{width:100%;min-width:unset;}
  .chat-area{display:none;position:fixed;inset:0;z-index:9999;background:#e5ddd5;}
  .chat-area.mobile-show{display:flex;}
  .back-btn{display:block;}
  .dashboard-btn span{display:none;}
  .dashboard-btn{padding:8px 10px;}
  .message{max-width:85%;}
}
@media(max-width:480px){
  .chat-header{padding:8px 12px;}
  .messages{padding:10px 8px 6px;}
  .message{max-width:90%;}
  .bubble{font-size:14px;padding:7px 11px;}
  .message-box{padding:8px 10px;padding-bottom:max(8px,env(safe-area-inset-bottom));}
}
@media(max-height:500px)and(max-width:768px){
  .chat-header{padding:6px 12px;}
}
</style>

<!-- ══════════════════════════════════════════
     SCRIPT
══════════════════════════════════════════ -->
<script>
/* ═══════════════════════ STATE ═══════════════════════ */
let selectedGuest     = null;
let renderedMsgIds    = new Set();   // incremental render — no blink
let convLastSeen      = {};          // guestID → last_message for bubble subtitle
let convOrder         = [];          // ordered list of guestIDs (newest first)
let convData          = {};          // guestID → full guest object

/* ═══════════════════════ TIME AGO ═══════════════════════ */
function timeAgo(ds) {
  const s = Math.floor((Date.now() - new Date(ds)) / 1000);
  if (s < 10)     return 'Just now';
  if (s < 60)     return s + 's ago';
  if (s < 3600)   return Math.floor(s / 60) + ' min ago';
  if (s < 86400)  return Math.floor(s / 3600) + ' hr ago';
  if (s < 604800) return Math.floor(s / 86400) + ' day' + (Math.floor(s/86400)>1?'s':'') + ' ago';
  if (s < 2592000)return Math.floor(s / 604800) + ' wk ago';
  return Math.floor(s / 2592000) + ' mo ago';
}

function shortTime(ds) {
  const d = new Date(ds);
  const now = new Date();
  const diff = (now - d) / 1000;
  if (diff < 60)     return 'Just now';
  if (diff < 3600)   return Math.floor(diff/60) + 'm';
  if (diff < 86400)  return d.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});
  if (diff < 604800) return d.toLocaleDateString([],{weekday:'short'});
  return d.toLocaleDateString([],{day:'numeric',month:'short'});
}

/* ═══════════════════════ AVATAR INITIAL ═══════════════════════ */
function getInitial(name) {
  return (name || '?').trim().charAt(0).toUpperCase();
}

/* ═══════════════════════ ESCAPE HTML ═══════════════════════ */
function esc(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

/* ═══════════════════════ CONVERSATIONS ═══════════════════════
   — Sorted so most-recent message is always at top
   — Guest IDs are deduped via convData map
═══════════════════════════════════════════════════════════════ */
async function loadConversations() {
  try {
    const res    = await fetch('/owner/chat/conversations');
    if (!res.ok) return;
    const guests = await res.json();

    // Merge into convData (dedup by guest_id)
    guests.forEach(g => { convData[g.guest_id] = g; });

    // Sort by latest_at descending (newest first)
    convOrder = Object.values(convData)
      .sort((a, b) => new Date(b.latest_at ?? b.created_at ?? 0) - new Date(a.latest_at ?? a.created_at ?? 0))
      .map(g => g.guest_id);

    renderConversationList();

    // Update total badge
    const totalUnread = Object.values(convData).reduce((n, g) => n + (Number(g.unread_count) || 0), 0);
    const tb = document.getElementById('totalBadge');
    tb.textContent = totalUnread > 0 ? totalUnread + ' unread' : Object.keys(convData).length + ' active';

  } catch(e) { console.error(e); }
}

function renderConversationList() {
  const search = document.getElementById('searchGuest').value.toLowerCase();
  let html = '';

  convOrder.forEach(gid => {
    const g = convData[gid];
    if (!g) return;
    if (search && !g.sender_name.toLowerCase().includes(search) && !(g.last_message||'').toLowerCase().includes(search)) return;

    const isActive  = gid === selectedGuest;
    const hasUnread = Number(g.unread_count) > 0;
    const initial   = getInitial(g.sender_name);
    const lastTime  = g.latest_at ? shortTime(g.latest_at) : '';

    html += `
      <div class="conversation${isActive ? ' active' : ''}"
           onclick="openConversation('${esc(gid)}','${esc(g.sender_name)}',${!!g.online})">
        <div class="conv-avatar">
          ${initial}
          <span class="conv-dot ${g.online ? 'online' : 'offline'}"></span>
        </div>
        <div class="conv-body">
          <div class="conv-top">
            <span class="conv-name">${esc(g.sender_name)}</span>
            <span class="conv-time">${lastTime}</span>
          </div>
          <div class="conv-last${hasUnread ? ' has-unread' : ''}">
            ${esc(g.last_message ?? '')}
          </div>
        </div>
        ${hasUnread ? `<span class="unread-badge">${g.unread_count}</span>` : ''}
      </div>`;
  });

  document.getElementById('conversationList').innerHTML = html || '<div style="padding:20px;text-align:center;color:#94a3b8;font-size:13px;">No conversations yet</div>';
}

/* Search live filter */
document.getElementById('searchGuest').addEventListener('input', renderConversationList);

/* ═══════════════════════ OPEN CONVERSATION ═══════════════════════ */
async function openConversation(guestID, guestName, online) {
  selectedGuest   = guestID;
  renderedMsgIds  = new Set();

  // Header
  document.getElementById('guestTitle').textContent = guestName;
  document.getElementById('avatarInitial').textContent = getInitial(guestName);
  const dot  = document.getElementById('headerDot');
  const stat = document.getElementById('guestStatus');
  if (online) {
    dot.className  = 'hav-dot online';
    stat.textContent = 'Online';
    stat.className   = 'guest-status online-text';
  } else {
    dot.className  = 'hav-dot';
    stat.textContent = 'Offline';
    stat.className   = 'guest-status offline-text';
  }

  await markConversationRead();
  loadMessages(true);
  renderConversationList();

  if (window.innerWidth <= 768) {
    document.getElementById('chatArea').classList.add('mobile-show');
  }
}

/* ═══════════════════════ LOAD MESSAGES ═══════════════════════
   Incremental DOM append — never wipes existing bubbles ⟹ no blink
═══════════════════════════════════════════════════════════════ */
async function loadMessages(initialLoad = false) {
  if (!selectedGuest) return;
  try {
    const res = await fetch(`/owner/chat/messages/${selectedGuest}`);
    if (!res.ok) return;
    const messages = await res.json();

    const box      = document.getElementById('messages');
    const atBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 120;

    if (initialLoad) {
      box.innerHTML = '';
      renderedMsgIds.clear();
      messages.forEach(m => appendMessage(m, false));
      box.scrollTop = box.scrollHeight;
      return;
    }

    let appended = false;
    messages.forEach(m => {
      const uid = m.id ?? (m.created_at + m.sender_type + String(m.message).slice(0, 12));
      if (!renderedMsgIds.has(uid)) {
        appendMessage(m, true);
        appended = true;
      }
    });
    if (appended && atBottom) box.scrollTop = box.scrollHeight;

  } catch(e) { console.error(e); }
}

function appendMessage(msg, animate) {
  const uid = msg.id ?? (msg.created_at + msg.sender_type + String(msg.message).slice(0, 12));
  renderedMsgIds.add(uid);

  const isSticker = /^\p{Emoji_Presentation}{1,2}$/u.test(String(msg.message).trim());

  const row = document.createElement('div');
  row.className = `message ${msg.sender_type}${animate ? ' new-msg' : ''}`;
  row.dataset.uid = uid;

  const tick = msg.sender_type === 'admin' ? '<span class="msg-tick">✓✓</span>' : '';

  row.innerHTML = `
    <div>
      <div class="bubble${isSticker ? ' is-sticker' : ''}">${esc(msg.message)}</div>
      <div class="msg-time">${timeAgo(msg.created_at)}${tick}</div>
    </div>`;

  const box = document.getElementById('messages');

  // Remove placeholder if present
  const empty = box.querySelector('.empty-chat');
  if (empty) empty.remove();

  box.appendChild(row);
}

/* ═══════════════════════ SEND — OPTIMISTIC UI ═══════════════════════ */
async function sendAdminMessage() {
  if (!selectedGuest) { alert('Select a conversation first'); return; }

  const input = document.getElementById('messageInput');
  const text  = input.value.trim();
  if (!text) return;

  // 1. Instant optimistic render
  const tempId  = 'tmp-' + Date.now();
  appendMessage({ id: tempId, sender_type: 'admin', message: text, created_at: new Date().toISOString() }, true);
  const box = document.getElementById('messages');
  box.scrollTop = box.scrollHeight;
  input.value = '';

  // 2. POST in background
  try {
    const res = await fetch('/owner/chat/send', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify({ guest_id: selectedGuest, sender_type: 'admin', sender_name: 'Admin', message: text })
    });
    if (!res.ok) {
      // Remove optimistic bubble on fail
      const el = box.querySelector(`[data-uid="${tempId}"]`);
      if (el) el.remove();
      renderedMsgIds.delete(tempId);
    } else {
      // Refresh conversation list to update last-message & bump to top
      loadConversations();
    }
  } catch(e) { console.error(e); }
}

/* ═══════════════════════ MARK READ ═══════════════════════ */
async function markConversationRead() {
  if (!selectedGuest) return;
  try {
    await fetch('/owner/chat/read', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify({ guest_id: selectedGuest })
    });
    if (convData[selectedGuest]) convData[selectedGuest].unread_count = 0;
  } catch(e) {}
}

/* ═══════════════════════ TYPING (guest → bouncing dots) ═══════════════════════ */
async function checkTyping() {
  if (!selectedGuest) return;
  try {
    const res  = await fetch(`/owner/chat/typing/${selectedGuest}`);
    if (!res.ok) return;
    const data = await res.json();
    const ind  = document.getElementById('typingIndicator');
    const box  = document.getElementById('messages');
    const atBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 120;
    ind.style.display = data.typing ? 'flex' : 'none';
    if (data.typing && atBottom) box.scrollTop = box.scrollHeight;
  } catch(e) {}
}

/* ═══════════════════════ MOBILE BACK ═══════════════════════ */
function closeMobileChat() {
  document.getElementById('chatArea').classList.remove('mobile-show');
}

/* ═══════════════════════ POLLING ═══════════════════════ */
// Conversations + messages: 2 s
setInterval(() => {
  loadConversations();
  if (selectedGuest) loadMessages(false);
}, 2000);

// Typing: 1.5 s
setInterval(() => {
  if (selectedGuest) checkTyping();
}, 1500);

/* ═══════════════════════ BOOT ═══════════════════════ */
loadConversations();
</script>