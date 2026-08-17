@extends('Users.User.cover')
@section('title', "Itsinda ry'Abanyeshuri")

@section('content')
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

<style>
#kgcRoot{ display:flex; flex-direction:column; height:calc(100dvh - 4.5rem); }
@media(min-width:1024px){ #kgcRoot{ height:100dvh; } }

#kgcMessages{ flex:1; overflow-y:auto; padding:14px; display:flex; flex-direction:column; gap:6px; background:#f6f8f7; }
.kgc-msg{ display:flex; max-width:80%; }
.kgc-msg.mine{ align-self:flex-end; flex-direction:row-reverse; }
.kgc-bubble{ border-radius:16px; padding:8px 12px; font-size:14px; }
.kgc-msg.mine .kgc-bubble{ background:#0B6D20; color:#fff; border-bottom-right-radius:4px; }
.kgc-msg:not(.mine) .kgc-bubble{ background:#fff; color:#111827; border-bottom-left-radius:4px; box-shadow:0 1px 2px rgba(0,0,0,.08); }
.kgc-sender{ font-size:11px; font-weight:700; opacity:.7; margin-bottom:2px; }
.kgc-time{ font-size:10px; color:#9ca3af; margin-top:3px; }
.kgc-reply-preview{ font-size:11px; border-left:2px solid #C9A227; padding:2px 8px; margin-bottom:4px; background:rgba(0,0,0,.03); border-radius:6px; }
.kgc-reactions{ display:flex; gap:4px; margin-top:4px; flex-wrap:wrap; }
.kgc-reaction-pill{ font-size:11px; background:#fff; border:1px solid #e5e7eb; border-radius:999px; padding:1px 8px; cursor:pointer; }
.kgc-actions{ display:flex; gap:10px; font-size:11px; color:#9ca3af; margin-top:2px; opacity:0; transition:opacity .15s; }
.kgc-msg:hover .kgc-actions{ opacity:1; }
.kgc-actions button{ cursor:pointer; }
.kgc-pinned-bar{ background:#fff8e6; border-bottom:1px solid #f1e2b0; padding:8px 14px; font-size:12px; color:#8a6d1a; display:none; }
.kgc-pinned-bar.show{ display:block; }
#kgcInputBar{ display:flex; gap:8px; padding:10px; background:#fff; border-top:1px solid #e5e7eb; }
#kgcInput{ flex:1; border:1px solid #e5e7eb; border-radius:999px; padding:10px 16px; outline:none; font-size:14px; }
#kgcInput:focus{ border-color:#0B6D20; box-shadow:0 0 0 3px rgba(11,109,32,.1); }
#kgcSendBtn{ background:#0B6D20; color:#fff; border:none; border-radius:50%; width:42px; height:42px; cursor:pointer; }
.kgc-reply-bar{ display:none; align-items:center; justify-content:space-between; background:#f6f8f7; border-top:1px solid #e5e7eb; padding:6px 14px; font-size:12px; }
.kgc-reply-bar.show{ display:flex; }
.kgc-emoji-picker{ position:absolute; bottom:100%; background:#fff; border:1px solid #e5e7eb; border-radius:999px; padding:4px 8px; display:none; gap:4px; box-shadow:0 4px 12px rgba(0,0,0,.1); z-index:5; }
.kgc-emoji-picker.show{ display:flex; }
.kgc-online-dot{ width:8px; height:8px; border-radius:50%; background:#22c55e; display:inline-block; }

@media(max-width:480px){
  .kgc-msg{ max-width:90%; }
}
</style>

<div id="kgcRoot">
  <div class="flex items-center gap-2 px-4 py-3 bg-white border-b">
    <span class="kgc-online-dot"></span>
    <div>
      <h1 class="text-sm font-bold" style="color:#0B3D2E">
        {{ $group === 'male_students' ? "Itsinda ry'Abahungu" : "Itsinda ry'Abakobwa" }}
      </h1>
      <p class="text-[11px] text-gray-400">Kwegereza Chat &middot; Itsinda ryawe</p>
    </div>
  </div>

  <div id="kgcPinnedBar" class="kgc-pinned-bar"></div>

  <div id="kgcMessages"></div>

  <div id="kgcReplyBar" class="kgc-reply-bar">
    <span id="kgcReplyText"></span>
    <button onclick="kgcCancelReply()" class="font-bold text-red-600">&times;</button>
  </div>

  <div id="kgcInputBar">
    <input id="kgcInput" type="text" placeholder="Andika ubutumwa..." autocomplete="off">
    <button id="kgcSendBtn" onclick="kgcSend()"><i class="fa-solid fa-paper-plane"></i></button>
  </div>
</div>

<script>
const KGC_STUDENT_ID = {{ auth('student')->id() }};
let kgcReplyTo = null;

function kgcEscape(s) {
  return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function kgcRenderReactions(msg) {
  const reactions = msg.reactions || {};
  const entries = Object.entries(reactions);
  if (!entries.length) return '';
  return `<div class="kgc-reactions">${entries.map(([emoji, count]) =>
    `<span class="kgc-reaction-pill" onclick="kgcReact(${msg.id}, '${emoji}')">${emoji} ${count}</span>`
  ).join('')}</div>`;
}

function kgcRenderMessage(msg) {
  const mine = msg.sender_id === KGC_STUDENT_ID;
  const div = document.createElement('div');
  div.className = 'kgc-msg' + (mine ? ' mine' : '');
  div.dataset.id = msg.id;

  const replyHtml = msg.parent ? `<div class="kgc-reply-preview">${kgcEscape(msg.parent.sender_name)}: ${kgcEscape(msg.parent.message.slice(0,50))}</div>` : '';
  const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

  div.innerHTML = `
    <div style="max-width:100%;">
      <div class="kgc-bubble">
        ${!mine ? `<div class="kgc-sender">${kgcEscape(msg.sender_name)}</div>` : ''}
        ${msg.is_pinned ? `<div style="font-size:10px;opacity:.7;">📌 Byomekwe</div>` : ''}
        ${replyHtml}
        <div>${kgcEscape(msg.message)}</div>
        <div class="kgc-time">${time}</div>
      </div>
      ${kgcRenderReactions(msg)}
      <div class="kgc-actions" style="position:relative;">
        <button onclick="kgcToggleEmojiPicker(${msg.id})">😊</button>
        <div class="kgc-emoji-picker" id="kgc-emoji-${msg.id}">
          ${['👍','❤️','😂','🙏','🎉'].map(e => `<span onclick="kgcReact(${msg.id}, '${e}')" style="cursor:pointer;">${e}</span>`).join('')}
        </div>
        <button onclick='kgcStartReply(${msg.id}, ${JSON.stringify(msg.sender_name)}, ${JSON.stringify(msg.message)})'>↩ Subiza</button>
        ${mine ? `<button onclick="kgcDelete(${msg.id})" style="color:#dc2626;">🗑 Siba</button>` : ''}
        <button onclick="kgcReport(${msg.id})">⚑ Raporo</button>
      </div>
    </div>`;
  return div;
}

function kgcToggleEmojiPicker(id) {
  document.querySelectorAll('.kgc-emoji-picker').forEach(el => {
    if (el.id !== `kgc-emoji-${id}`) el.classList.remove('show');
  });
  document.getElementById(`kgc-emoji-${id}`)?.classList.toggle('show');
}

async function kgcReact(id, emoji) {
  document.getElementById(`kgc-emoji-${id}`)?.classList.remove('show');
  try {
    const res = await fetch(`/student/group-chat/messages/${id}/react`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: JSON.stringify({ emoji }),
    });
    const data = await res.json();
    const row = document.querySelector(`.kgc-msg[data-id="${id}"]`);
    if (row && data.data) {
      const pillsHost = row.querySelector('.kgc-reactions');
      const newHtml = kgcRenderReactions({ id, reactions: data.data.reactions });
      if (pillsHost) pillsHost.outerHTML = newHtml || '<div class="kgc-reactions"></div>';
    }
  } catch (e) { console.error(e); }
}

function kgcStartReply(id, senderName, message) {
  kgcReplyTo = id;
  document.getElementById('kgcReplyText').textContent = `Usubiza ${senderName}: ${message.slice(0, 40)}`;
  document.getElementById('kgcReplyBar').classList.add('show');
  document.getElementById('kgcInput').focus();
}
function kgcCancelReply() {
  kgcReplyTo = null;
  document.getElementById('kgcReplyBar').classList.remove('show');
}

async function kgcDelete(id) {
  if (!confirm('Wemeza gusiba ubu butumwa?')) return;
  await fetch(`/student/group-chat/messages/${id}`, {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
  });
  document.querySelector(`.kgc-msg[data-id="${id}"]`)?.remove();
}

async function kgcReport(id) {
  const reason = prompt('Impamvu (si ngombwa):') ?? '';
  await fetch(`/student/group-chat/messages/${id}/report`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({ reason }),
  });
  alert('Raporo yatanzwe.');
}

async function kgcLoad() {
  const res = await fetch('{{ route("student.groupChat.messages") }}');
  const data = await res.json();
  if (!data.success) {
    document.getElementById('kgcMessages').innerHTML = `<p class="p-4 text-sm text-center text-gray-400">${data.message}</p>`;
    return;
  }
  const box = document.getElementById('kgcMessages');
  box.innerHTML = '';
  data.data.forEach(msg => box.appendChild(kgcRenderMessage(msg)));
  box.scrollTop = box.scrollHeight;

  const pinnedBar = document.getElementById('kgcPinnedBar');
  if (data.pinned && data.pinned.length) {
    pinnedBar.innerHTML = '📌 ' + data.pinned.map(m => kgcEscape(m.message.slice(0, 60))).join(' &middot; ');
    pinnedBar.classList.add('show');
  }
}

async function kgcSend() {
  const input = document.getElementById('kgcInput');
  const message = input.value.trim();
  if (!message) return;
  input.value = '';

  const res = await fetch('{{ route("student.groupChat.send") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({ message, parent_id: kgcReplyTo }),
  });
  const data = await res.json();
  if (data.success) {
    const box = document.getElementById('kgcMessages');
    box.appendChild(kgcRenderMessage(data.data));
    box.scrollTop = box.scrollHeight;
  } else {
    alert(data.message || 'Ntibyakunze.');
  }
  kgcCancelReply();
}

document.getElementById('kgcInput').addEventListener('keydown', (e) => {
  if (e.key === 'Enter') kgcSend();
});

kgcLoad();

// Realtime — new messages + reaction/pin/delete updates
@if($group)
try {
  const echo = new Echo({
    broadcaster: 'reverb',
    key: '{{ config('broadcasting.connections.reverb.key') }}',
    wsHost: '{{ config('broadcasting.connections.reverb.options.host') }}',
    wsPort: {{ config('broadcasting.connections.reverb.options.port', 443) }},
    wssPort: {{ config('broadcasting.connections.reverb.options.port', 443) }},
    forceTLS: {{ config('broadcasting.connections.reverb.options.scheme') === 'https' ? 'true' : 'false' }},
    enabledTransports: ['ws', 'wss'],
    authEndpoint: '/student/broadcasting/auth',
  });

  const channelName = 'group.' + '{{ str_replace("_", "-", $group ?? "") }}';
  const channel = echo.private(channelName);

  channel.listen('.group-message.sent', (payload) => {
    if (payload.sender_id === KGC_STUDENT_ID) return; // already rendered optimistically
    const box = document.getElementById('kgcMessages');
    box.appendChild(kgcRenderMessage(payload));
    box.scrollTop = box.scrollHeight;
  });

  channel.listen('.group-message.updated', (payload) => {
    if (payload.type === 'deleted') {
      document.querySelector(`.kgc-msg[data-id="${payload.message_id}"]`)?.remove();
    } else if (payload.type === 'reacted' || payload.type === 'unreacted') {
      const row = document.querySelector(`.kgc-msg[data-id="${payload.message_id}"]`);
      const pillsHost = row?.querySelector('.kgc-reactions');
      const newHtml = kgcRenderReactions({ reactions: payload.reactions });
      if (pillsHost) pillsHost.outerHTML = newHtml || '<div class="kgc-reactions"></div>';
    } else if (payload.type === 'pinned' || payload.type === 'unpinned') {
      kgcLoad();
    }
  });
} catch (e) {
  console.warn('Realtime unavailable for group chat, staying on manual refresh.', e);
}
@endif
</script>
@endsection
