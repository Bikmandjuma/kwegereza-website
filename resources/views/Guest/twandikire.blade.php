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

.name-screen{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:15px;
    background:linear-gradient(135deg,#4f46e5,#7c3aed);
    z-index:9999;
}

.name-card{
    width:100%;
    max-width:500px;
    background:#fff;
    border-radius:24px;
    padding:35px 25px;
    text-align:center;
    box-shadow:0 15px 40px rgba(0,0,0,.15);
}

.logo{
    width:80px;
    height:80px;
    margin:0 auto 20px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:50%;
    background:#eef2ff;
    font-size:36px;
}

.name-card h2{
    font-size:28px;
    color:#111827;
    margin-bottom:10px;
}

.name-card p{
    color:#64748b;
    font-size:15px;
    line-height:1.6;
    margin-bottom:25px;
}

.name-card input{
    width:100%;
    height:55px;
    border:1px solid #dbeafe;
    border-radius:14px;
    padding:0 16px;
    font-size:16px;
    outline:none;
    background:#f8fafc;
}

.name-card input:focus{
    border-color:#4f46e5;
}

.name-card button{
    width:100%;
    height:55px;
    margin-top:15px;
    border:none;
    border-radius:14px;
    background:#4f46e5;
    color:#fff;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
}

.name-card button:hover{
    background:#4338ca;
}

/* Tablet */

@media (max-width:768px){

    .name-card{
        max-width:95%;
        padding:30px 20px;
    }

    .name-card h2{
        font-size:24px;
    }

    .name-card p{
        font-size:14px;
    }
}

/* Mobile */

@media (max-width:480px){

    .name-screen{
        padding:10px;
    }

    .name-card{
        width:100%;
        max-width:none;
        border-radius:18px;
        padding:25px 15px;
    }

    .logo{
        width:65px;
        height:65px;
        font-size:30px;
        margin-bottom:15px;
    }

    .name-card h2{
        font-size:22px;
    }

    .name-card p{
        font-size:14px;
        margin-bottom:20px;
    }

    .name-card input,
    .name-card button{
        height:50px;
        font-size:15px;
    }
}

/* Extra Small Phones */

@media (max-width:360px){

    .name-card{
        padding:20px 12px;
    }

    .logo{
        width:55px;
        height:55px;
        font-size:24px;
    }

    .name-card h2{
        font-size:20px;
    }

    .name-card p{
        font-size:13px;
    }

    .name-card input,
    .name-card button{
        height:46px;
        font-size:14px;
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