<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- <h1>chat room page</h1> -->
<style>
	*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

html,
body{
    height:100%;
    overflow:hidden;
    font-family:Inter,sans-serif;
}

.admin-chat{
    display:flex;
    height:100vh;
    background:#f1f5f9;
}

/* SIDEBAR */

.sidebar{
    width:340px;
    background:white;
    border-right:1px solid #e5e7eb;
    display:flex;
    flex-direction:column;
}

.sidebar-header{
    padding:20px;
    border-bottom:1px solid #eee;
}

.sidebar-header h2{
    font-size:20px;
}

.search-box{
    padding:15px;
}

.search-box input{
    width:100%;
    border:none;
    outline:none;
    background:#f1f5f9;
    padding:14px;
    border-radius:12px;
}

.conversation-list{
    flex:1;
    overflow-y:auto;
}

/* CONVERSATION */

.conversation{
    padding:15px;
    cursor:pointer;
    border-bottom:1px solid #f1f5f9;
    transition:.2s;
}

.conversation:hover{
    background:#f8fafc;
}

.conversation.active{
    background:#eef2ff;
}

.conversation-name{
    font-weight:600;
    color:#111827;
}

.conversation-last{
    font-size:13px;
    color:#64748b;
    margin-top:4px;
}

/*.unread{
    background:#ef4444;
    color:white;
    padding:2px 8px;
    border-radius:999px;
    font-size:11px;
}*/

.unread{
    min-width:22px;
    height:22px;

    display:flex;
    align-items:center;
    justify-content:center;

    background:#22c55e;
    color:white;

    border-radius:999px;

    font-size:11px;
    font-weight:700;
}

/* CHAT AREA */

.chat-area{
    flex:1;
    display:flex;
    flex-direction:column;
}

/* HEADER */

/*.chat-header{
    height:70px;
    background:white;
    border-bottom:1px solid #e5e7eb;
    display:flex;
    align-items:center;
    padding:0 20px;
}
*/
.chat-user{
    display:flex;
    align-items:center;
    gap:12px;
}

.avatar{
    width:45px;
    height:45px;
    border-radius:50%;
    background:#eef2ff;
    display:flex;
    align-items:center;
    justify-content:center;
}

.online-status{
    font-size:12px;
    color:#22c55e;
}

/* MESSAGES */

.messages{
    flex:1;
    overflow-y:auto;
    padding:20px;
}

.empty-chat{
    text-align:center;
    color:#94a3b8;
    margin-top:100px;
}

/* MESSAGE */

.message{
    display:flex;
    margin-bottom:14px;
}

.message.admin{
    justify-content:flex-end;
}

.message.guest{
    justify-content:flex-start;
}

.message-wrap{
    max-width:75%;
}

.bubble{
    padding:12px 18px;
    border-radius:18px;
    word-break:break-word;
}

.admin .bubble{
    background:#4f46e5;
    color:white;
}

.guest .bubble{
    background:white;
    border:1px solid #e5e7eb;
}

.msg-time{
    font-size:11px;
    color:#94a3b8;
    margin-top:4px;
}

/* TYPING */

.typing-indicator{
    display:none;
    padding:10px 20px;
    font-size:13px;
    color:#64748b;
    font-style:italic;
}

.online-dot{
    width:10px;
    height:10px;
    background:#10b981;
    border-radius:50%;
    display:inline-block;
}

.offline-dot{
    width:10px;
    height:10px;
    background:#cbd5e1;
    border-radius:50%;
    display:inline-block;
}

/* INPUT */

.message-box{
    background:white;
    border-top:1px solid #e5e7eb;
    padding:15px;
    display:flex;
    gap:10px;
}

.message-box input{
    flex:1;
    border:none;
    outline:none;
    background:#f1f5f9;
    padding:15px;
    border-radius:14px;
}

.message-box button{
    border:none;
    background:#4f46e5;
    color:white;
    padding:15px 25px;
    border-radius:14px;
    cursor:pointer;
}

.chat-header{
    height:70px;
    background:white;
    border-bottom:1px solid #e5e7eb;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 20px;
}

.back-btn{
    display:none;
    border:none;
    background:none;
    font-size:20px;
    cursor:pointer;
    color:#334155;
}

.dashboard-btn{
    text-decoration:none;
    background:#4f46e5;
    color:white;
    padding:10px 18px;
    border-radius:10px;
    font-size:14px;
    font-weight:600;
}

.dashboard-btn:hover{
    opacity:.9;
}

/* MOBILE */

/*@media(max-width:768px){

    .sidebar{
        width:100%;
    }

    .chat-area{
        display:none;
    }

    .chat-area.mobile-show{
        display:flex;
        position:fixed;
        inset:0;
        z-index:1000;
        background:white;
    }
*/}

@media(max-width:768px){

    .sidebar{
        width:100%;
    }

    .chat-area{
        display:none;
    }

    .chat-area.mobile-show{
        display:flex;
        position:fixed;
        inset:0;
        z-index:9999;
        background:white;
    }

    .back-btn{
        display:block;
    }

    .dashboard-btn{
        padding:8px 12px;
        font-size:12px;
    }
}
</style>
<div class="admin-chat">

    <!-- SIDEBAR -->
    <div class="sidebar">

        <div class="sidebar-header">
            <h2>Live Chats</h2>
        </div>

        <div class="search-box">
            <input
                type="text"
                id="searchGuest"
                placeholder="Search guest..."
            >
        </div>

        <div
            id="conversationList"
            class="conversation-list"
        ></div>

    </div>

    <!-- CHAT AREA -->
    <div class="chat-area">

        <div class="chat-header">

            <!-- <div class="chat-user">

                <div class="avatar">
                    💬
                </div>

                <div>

                    <h3 id="guestTitle">
                        Select Conversation
                    </h3>

                    <span
                        id="guestStatus"
                        class="online-status"
                    >
                        Offline
                    </span>
                </div>

            </div> -->
            <div class="chat-header">

			    <div class="chat-user">

			        <button
			            id="backBtn"
			            class="back-btn"
			            onclick="closeMobileChat()"
			        >
			            <i class="fa fa-arrow-left"></i>
			        </button>

			        <div class="avatar">
			            💬
			        </div>

			        <div>

			            <h3 id="guestTitle">
			                Select Conversation
			            </h3>

			            <span
			                id="guestStatus"
			                class="online-status"
			            >
			                Offline
			            </span>

			        </div>

			    </div>

			    <a
			        href="{{ route('owner.dashboard') }}"
			        class="dashboard-btn"
			    >
			        Dashboard
			    </a>

			</div>

        </div>

        <div
            id="messages"
            class="messages"
        >

            <div class="empty-chat">
                Select a guest conversation
            </div>

        </div>

        <div
            id="typingIndicator"
            class="typing-indicator"
        >
            Guest is typing...
        </div>

        <div class="message-box">

            <input
                id="messageInput"
                type="text"
                placeholder="Type message..."
                onkeypress="
                    if(event.key==='Enter'){
                        sendAdminMessage();
                    }
                "
            >

            <button
                onclick="sendAdminMessage()"
            >
                Send&nbsp;<i class="fa fa-paper-plane"></i>
            </button>

        </div>

    </div>

</div>

<script>

let selectedGuest = null;
let selectedGuestName = null;

let lastMessageCount = 0;

/*
|--------------------------------------------------------------------------
| LOAD CONVERSATIONS
|--------------------------------------------------------------------------
*/

async function loadConversations()
{
    try{

        const response =
            await fetch('/owner/chat/conversations');

        const guests =
            await response.json();

        let html = '';

        guests.forEach(guest => {

            html += `
                <div
                    class="conversation
                    ${selectedGuest == guest.guest_id ? 'active' : ''}"
                    onclick="
						openConversation(
						    '${guest.guest_id}',
						    '${guest.sender_name}',
						    ${guest.online ? true : false}
						)
					"
                >

                    <div style="
                        display:flex;
                        justify-content:space-between;
                        align-items:center;
                    ">

                        <div>

            				<div
							    style="
							        display:flex;
							        align-items:center;
							        gap:8px;
							    "
							>

							    ${
							        guest.online
							        ?
							        '<span class="online-dot"></span>'
							        :
							        '<span class="offline-dot"></span>'
							    }

							    <div class="conversation-name">
							        ${guest.sender_name}
							    </div>

							</div>

                            <div class="conversation-last">
                                ${guest.last_message ?? ''}
                            </div>

                        </div>

                        ${
						    Number(guest.unread_count) > 0
						    ?
						    `<span class="unread">
						        ${guest.unread_count}
						    </span>`
						    :
						    ''
						}

                    </div>

                </div>
            `;
        });

        document
            .getElementById('conversationList')
            .innerHTML = html;

    }catch(error){

        console.log(error);

    }
}

/*
|--------------------------------------------------------------------------
| OPEN CONVERSATION
|--------------------------------------------------------------------------
*/

// async function openConversation(
//     guestID,
//     guestName
// )
// {
//     selectedGuest = guestID;
//     selectedGuestName = guestName;

//     document
//         .getElementById('guestTitle')
//         .innerText = guestName;

//     await markConversationRead();

//     loadMessages();

//     loadConversations();

//     if(window.innerWidth <= 768)
//     {
//         document
//             .querySelector('.chat-area')
//             .classList
//             .add('mobile-show');
//     }
// }

async function openConversation(
    guestID,
    guestName,
    online
)
{
    selectedGuest = guestID;

    document
        .getElementById('guestTitle')
        .innerText = guestName;

    document
        .getElementById('guestStatus')
        .innerText =
            online
            ? 'Online'
            : 'Offline';

    await markConversationRead();

    loadMessages();

    loadConversations();

    if(window.innerWidth <= 768)
    {
        document
        .querySelector('.chat-area')
        .classList
        .add('mobile-show');
    }
}

/*
|--------------------------------------------------------------------------
| LOAD MESSAGES
|--------------------------------------------------------------------------
*/

async function loadMessages()
{
    if(!selectedGuest) return;

    try{

        const response =
            await fetch(
                `/owner/chat/messages/${selectedGuest}`
            );

        const messages =
            await response.json();

        let html = '';

        messages.forEach(msg => {

            html += `
                <div class="message ${msg.sender_type}">

                    <div class="message-wrap">

                        <div class="bubble">
                            ${msg.message}
                        </div>

                        <div class="msg-time">
                            ${formatTime(
                                msg.created_at
                            )}
                        </div>

                    </div>

                </div>
            `;
        });

        const box =
            document.getElementById('messages');

        const shouldScroll =
            box.scrollHeight -
            box.scrollTop -
            box.clientHeight < 200;

        box.innerHTML = html;

        if(shouldScroll)
        {
            box.scrollTop =
                box.scrollHeight;
        }

        lastMessageCount =
            messages.length;

    }catch(error){

        console.log(error);

    }
}

/*
|--------------------------------------------------------------------------
| SEND MESSAGE
|--------------------------------------------------------------------------
*/

async function sendAdminMessage()
{
    if(!selectedGuest)
    {
        alert(
            'Select a conversation first'
        );
        return;
    }

    const input =
        document.getElementById(
            'messageInput'
        );

    const text =
        input.value.trim();

    if(!text) return;

    try{

        await fetch(
            '/owner/chat/send',
            {
                method:'POST',

                headers:{
                    'Content-Type':
                    'application/json',

                    'X-CSRF-TOKEN':
                    '{{ csrf_token() }}'
                },

                body:JSON.stringify({

                    guest_id:selectedGuest,

                    sender_type:'admin',

                    sender_name:'Admin',

                    message:text

                })
            }
        );

        input.value='';

        loadMessages();

        loadConversations();

    }catch(error){

        console.log(error);

    }
}

/*
|--------------------------------------------------------------------------
| MARK AS READ
|--------------------------------------------------------------------------
*/

async function markConversationRead()
{
    if(!selectedGuest) return;

    try{

        await fetch(
            '/owner/chat/read',
            {
                method:'POST',

                headers:{
                    'Content-Type':
                    'application/json',

                    'X-CSRF-TOKEN':
                    '{{ csrf_token() }}'
                },

                body:JSON.stringify({

                    guest_id:selectedGuest

                })
            }
        );

    }catch(error){

        console.log(error);

    }
}

/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

document
.getElementById('searchGuest')
.addEventListener('keyup', function(){

    const search =
        this.value.toLowerCase();

    document
    .querySelectorAll('.conversation')
    .forEach(item => {

        const text =
            item.innerText.toLowerCase();

        item.style.display =
            text.includes(search)
            ? 'block'
            : 'none';
    });

});

/*
|--------------------------------------------------------------------------
| TYPING INDICATOR
|--------------------------------------------------------------------------
*/

async function checkTyping()
{
    if(!selectedGuest) return;

    try{

        const response =
            await fetch(
                `/owner/chat/typing/${selectedGuest}`
            );

        const data =
            await response.json();

        document
        .getElementById(
            'typingIndicator'
        )
        .style.display =
            data.typing
            ? 'block'
            : 'none';

    }catch(error){

        console.log(error);

    }
}

/*
|--------------------------------------------------------------------------
| TIME FORMAT
|--------------------------------------------------------------------------
*/

function formatTime(dateString)
{
    const date =
        new Date(dateString);

    return date.toLocaleString(
        [],
        {
            hour:'2-digit',
            minute:'2-digit',
            day:'numeric',
            month:'short'
        }
    );
}

/*
|--------------------------------------------------------------------------
| MOBILE BACK BUTTON
|--------------------------------------------------------------------------
*/

function closeMobileChat()
{
    document
        .querySelector('.chat-area')
        .classList
        .remove('mobile-show');
}

/*
|--------------------------------------------------------------------------
| AUTO REFRESH
|--------------------------------------------------------------------------
*/

setInterval(() => {

    loadConversations();

    if(selectedGuest)
    {
        loadMessages();

        checkTyping();
    }

}, 2000);

/*
|--------------------------------------------------------------------------
| INITIAL LOAD
|--------------------------------------------------------------------------
*/

loadConversations();

</script>