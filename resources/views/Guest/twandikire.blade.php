<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div class="chat-page">
    <!-- NAME SCREEN -->
   <!--  <div id="nameScreen" class="name-screen">
        <div class="name-card">
            <div class="logo">💬</div>
            <h2>Welcome to Live Chat</h2>
            <p>Enter your name to start chatting with support.</p>
            <input type="text" id="guestName" placeholder="Your name">
            <button onclick="startChat()">Start Chat</button>
        </div>
    </div>
 -->
    <div id="nameScreen" class="name-screen">
        <div class="name-card">
            <div class="logo">
                💬
            </div>

            <h2>Live Support Chat</h2>

            <p>
                Connect instantly with our support team.
                Enter your name below to start chatting.
            </p>

            <input
                type="text"
                id="guestName"
                placeholder="Enter your name"
            >

            <button onclick="startChat()">
                Start Conversation
            </button>
        </div>
    </div>

    <!-- CHAT SCREEN -->
    <div id="chatScreen" class="chat-container">
        <div class="chat-header">
            <div>
                <!-- <h3>Live Support</h3> -->
                <h3>
                    Live Support
                    <span id="unreadBadge"
                          class="badge">
                    </span>
                </h3>
                <span>Online</span>
            </div>
        </div>

        <div id="messages" class="chat-messages"></div>

        <div id="typingIndicator"
             class="typing-indicator"
             style="display:none;">
            Admin is typing...
        </div>

        <div class="chat-input">
            <input type="text" id="message" placeholder="Type a message..." onkeypress="if(event.key==='Enter'){sendMessage()}">
            <button onclick="sendMessage()">
                Send&nbsp;<i class="fa fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

html,
body{
    width:100%;
    height:100%;
    overflow:hidden;
    font-family:Inter,Segoe UI,sans-serif;
    background:#f8fafc;
}

.chat-page{
    width:100%;
    height:100vh;
}

/* ==========================
   NAME SCREEN
========================== */
/*
.name-screen{
    width:100%;
    height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:20px;
    background:linear-gradient(
        135deg,
        #4f46e5,
        #6366f1
    );
}

.name-card{
    width:100%;
    max-width:420px;
    background:#ffffff;
    border-radius:30px;
    padding:35px 30px;
    text-align:center;
    box-shadow:
        0 20px 60px rgba(0,0,0,.15);
}
*/

.name-screen{
    position:fixed;
    inset:0;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:15px;
    background:linear-gradient(135deg,#4f46e5,#7c3aed);
    z-index:9999;
}

.name-card{
    width:100%;
    max-width:500px;
    background:#fff;
    border-radius:24px;
    padding:40px 30px;
    text-align:center;
    box-shadow:0 20px 50px rgba(0,0,0,.15);
}

.logo{
    width:90px;
    height:90px;
    margin:0 auto 20px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:42px;
    background:linear-gradient(135deg,#4f46e5,#7c3aed);
}

.name-card h2{
    font-size:30px;
    margin-bottom:12px;
    color:#111827;
}

.name-card p{
    font-size:17px;
    line-height:1.6;
    color:#6b7280;
    margin-bottom:25px;
}

.name-card input{
    width:100%;
    height:60px;
    border:2px solid #e5e7eb;
    border-radius:14px;
    padding:0 18px;
    font-size:17px;
    outline:none;
    box-sizing:border-box;
}

.name-card button{
    width:100%;
    height:60px;
    margin-top:15px;
    border:none;
    border-radius:14px;
    background:linear-gradient(135deg,#4f46e5,#7c3aed);
    color:white;
    font-size:18px;
    font-weight:600;
    cursor:pointer;
}

/* Tablets */
@media (max-width:768px){
    .name-card{
        max-width:95%;
        padding:35px 25px;
    }
}

/* Phones */
@media (max-width:480px){

    .name-screen{
        padding:10px;
    }

    .name-card{
        width:100%;
        max-width:100%;
        padding:30px 20px;
        border-radius:20px;
    }

    .logo{
        width:80px;
        height:80px;
        font-size:36px;
    }

    .name-card h2{
        font-size:26px;
    }

    .name-card p{
        font-size:16px;
    }

    .name-card input,
    .name-card button{
        height:56px;
        font-size:16px;
    }
}

/* Very small devices */
@media (max-width:360px){

    .name-card{
        width: 400px;
        height: 400px;
        padding:25px 15px;
    }

    .name-card h2{
        font-size:22px;
    }

    .name-card p{
        font-size:14px;
    }

    .logo{
        width:70px;
        height:70px;
        font-size:30px;
    }
}
.logo{
    width:85px;
    height:85px;
    margin:auto;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:50%;
    background:#eef2ff;
    font-size:38px;
}

.name-card h2{
    margin-top:18px;
    font-size:28px;
    color:#111827;
}

.name-card p{
    margin-top:8px;
    color:#64748b;
    line-height:1.6;
}

.name-card input{
    width:100%;
    margin-top:22px;
    padding:15px;
    border:none;
    outline:none;
    border-radius:15px;
    background:#f1f5f9;
    font-size:15px;
}

.name-card button{
    width:100%;
    margin-top:15px;
    padding:15px;
    border:none;
    border-radius:15px;
    background:#4f46e5;
    color:white;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
    transition:.2s;
}

.name-card button:hover{
    background:#4338ca;
}

/* ==========================
   CHAT SCREEN
========================== */

.chat-container{
    display:none;
    flex-direction:column;
    width:100%;
    height:100vh;
    background:#f8fafc;
}

/* HEADER */

.chat-header{
    background:#4f46e5;
    color:white;
    padding:18px 20px;
    box-shadow:
        0 2px 10px rgba(0,0,0,.08);
}

.chat-header h3{
    display:flex;
    align-items:center;
    gap:8px;
    font-size:18px;
    font-weight:700;
}

.chat-header span{
    font-size:13px;
    opacity:.85;
}

/* UNREAD BADGE */

.badge{
    display:none;
    min-width:22px;
    height:22px;
    padding:0 8px;
    border-radius:999px;
    background:#ef4444;
    color:white;
    font-size:11px;
    font-weight:700;
    align-items:center;
    justify-content:center;
}

/* CHAT BODY */

.chat-messages{
    flex:1;
    overflow-y:auto;
    padding:20px;
    display:flex;
    flex-direction:column;
}

/* MESSAGE */

.message{
    display:flex;
    margin-bottom:12px;
}

.message.guest{
    justify-content:flex-end;
}

.message.admin{
    justify-content:flex-start;
}

.message > div{
    max-width:80%;
}

/* BUBBLE */

.bubble{
    padding:12px 16px;
    border-radius:18px;
    word-break:break-word;
    line-height:1.5;
    font-size:14px;
}

.guest .bubble{
    background:#4f46e5;
    color:white;
    border-bottom-right-radius:6px;
}

.admin .bubble{
    background:white;
    color:#111827;
    border:1px solid #e5e7eb;
    border-bottom-left-radius:6px;
}

/* TIME */

.msg-time{
    margin-top:4px;
    font-size:11px;
    color:#94a3b8;
}

.message.guest .msg-time{
    text-align:right;
    padding-right:6px;
}

.message.admin .msg-time{
    text-align:left;
    padding-left:6px;
}

/* TYPING */

.typing-indicator{
    display:none;
    padding:10px 20px;
    color:#64748b;
    font-size:13px;
    font-style:italic;
    background:#ffffff;
    border-top:1px solid #e5e7eb;
}

/* INPUT AREA */

.chat-input{
    display:flex;
    align-items:center;
    gap:10px;
    padding:15px;
    background:white;
    border-top:1px solid #e5e7eb;
}

.chat-input input{
    flex:1;
    border:none;
    outline:none;
    background:#f1f5f9;
    padding:14px 16px;
    border-radius:15px;
    font-size:15px;
}

.chat-input button{
    border:none;
    background:#4f46e5;
    color:white;
    padding:14px 22px;
    border-radius:15px;
    font-weight:600;
    cursor:pointer;
    transition:.2s;
}

.chat-input button:hover{
    background:#4338ca;
}

/* SCROLLBAR */

.chat-messages::-webkit-scrollbar{
    width:6px;
}

.chat-messages::-webkit-scrollbar-thumb{
    background:#cbd5e1;
    border-radius:999px;
}

/* ==========================
   MOBILE
========================== */

@media(max-width:768px){

    .name-card{
        padding:25px;
        border-radius:22px;
    }

    .chat-header{
        padding:15px;
    }

    .chat-messages{
        padding:15px;
    }

    .message > div{
        max-width:88%;
    }

    .bubble{
        font-size:14px;
        padding:11px 14px;
    }

    .chat-input{
        padding:12px;
    }

    .chat-input input{
        padding:13px 14px;
    }

    .chat-input button{
        padding:13px 18px;
    }
}
</style>

<script>

let guestName = '';
let lastMessageCount = 0;

/*
|--------------------------------------------------------------------------
| AUTO OPEN CHAT
|--------------------------------------------------------------------------
*/
window.onload = async function () {

    const guestID = localStorage.getItem('guest_id');
    const storedName = localStorage.getItem('guest_name');

    if (!guestID || !storedName) {
        return;
    }

    guestName = storedName;

    document.getElementById('nameScreen').style.display = 'none';
    document.getElementById('chatScreen').style.display = 'flex';

    loadMessages();
};

/*
|--------------------------------------------------------------------------
| START CHAT
|--------------------------------------------------------------------------
*/
function startChat() {

    const name = document
        .getElementById('guestName')
        .value
        .trim();

    if (!name) {
        alert('Please enter your name');
        return;
    }

    guestName = name;

    let guestID = localStorage.getItem('guest_id');

    if (!guestID) {

        guestID =
            window.crypto &&
            window.crypto.randomUUID
                ? window.crypto.randomUUID()
                : generateUUID();

        localStorage.setItem(
            'guest_id',
            guestID
        );
    }

    localStorage.setItem(
        'guest_name',
        name
    );

    document.getElementById('nameScreen').style.display = 'none';
    document.getElementById('chatScreen').style.display = 'flex';

    loadMessages();
}

/*
|--------------------------------------------------------------------------
| UUID FALLBACK
|--------------------------------------------------------------------------
*/
function generateUUID() {

    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'
        .replace(/[xy]/g, function(c) {

            const r = Math.random() * 16 | 0;
            const v = c === 'x'
                ? r
                : (r & 0x3 | 0x8);

            return v.toString(16);
        });
}

/*
|--------------------------------------------------------------------------
| TIME AGO
|--------------------------------------------------------------------------
*/
function timeAgo(dateString) {

    const seconds = Math.floor(
        (new Date() - new Date(dateString)) / 1000
    );

    if (seconds < 60) {
        return 'Just now';
    }

    if (seconds < 3600) {
        return Math.floor(seconds / 60) + ' min ago';
    }

    if (seconds < 86400) {
        return Math.floor(seconds / 3600) + ' hr ago';
    }

    return Math.floor(seconds / 86400) + ' day ago';
}

/*
|--------------------------------------------------------------------------
| UNREAD BADGE
|--------------------------------------------------------------------------
*/
function updateUnread(messages) {

    const badge =
        document.getElementById('unreadBadge');

    if (messages.length > lastMessageCount) {

        const diff =
            messages.length - lastMessageCount;

        if (lastMessageCount > 0) {

            badge.innerText = diff;
            badge.style.display = 'inline-block';
        }
    }

    lastMessageCount = messages.length;
}

/*
|--------------------------------------------------------------------------
| LOAD MESSAGES
|--------------------------------------------------------------------------
*/
async function loadMessages() {

    try {

        const guestID =
            localStorage.getItem('guest_id');

        if (!guestID) {
            return;
        }

        const response =
            await fetch(
                `/chat/messages/${guestID}`
            );

        if (!response.ok) {
            console.log('Load error');
            return;
        }

        const messages =
            await response.json();

        updateUnread(messages);

        let html = '';

        messages.forEach(msg => {

            html += `
                <div class="message ${msg.sender_type}">
                    <div>

                        <div class="bubble">
                            ${msg.message}
                        </div>

                        <div class="msg-time">
                            ${timeAgo(msg.created_at)}
                        </div>

                    </div>
                </div>
            `;
        });

        const box =
            document.getElementById('messages');

        box.innerHTML = html;

        const shouldScroll =
            box.scrollHeight -
            box.scrollTop -
            box.clientHeight < 100;

        if (shouldScroll) {
            box.scrollTop =
                box.scrollHeight;
        }

    } catch(error) {

        console.log(error);
    }
}

/*
|--------------------------------------------------------------------------
| SEND MESSAGE
|--------------------------------------------------------------------------
*/
async function sendMessage() {

    const text =
        document
        .getElementById('message')
        .value
        .trim();

    if (!text) {
        return;
    }

    const guestID =
        localStorage.getItem('guest_id');

    if (!guestID) {

        alert(
            'Session expired. Reload page.'
        );

        return;
    }

    try {

        const response =
            await fetch('/chat/send', {

                method: 'POST',

                headers: {
                    'Content-Type':
                        'application/json',

                    'X-CSRF-TOKEN':
                        '{{ csrf_token() }}'
                },

                body: JSON.stringify({

                    sender_type: 'guest',

                    sender_name: guestName,

                    guest_id: guestID,

                    message: text
                })
            });

        const result =
            await response.json();

        if (!response.ok) {

            console.log(result);

            alert(
                'Message not sent.'
            );

            return;
        }

        document
            .getElementById('message')
            .value = '';

        loadMessages();

    } catch(error) {

        console.log(error);
    }
}

/*
|--------------------------------------------------------------------------
| AUTO REFRESH
|--------------------------------------------------------------------------
*/
setInterval(() => {

    if (
        document.getElementById('chatScreen')
        .style.display === 'flex'
    ) {

        loadMessages();
    }

}, 2000);


//last seen
setInterval(async()=>{

    const guestID = localStorage.getItem('guest_id');

    if(!guestID) return;

    await fetch('/chat/presence',{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body:JSON.stringify({
            guest_id:guestID
        })
    });

},10000);

</script>