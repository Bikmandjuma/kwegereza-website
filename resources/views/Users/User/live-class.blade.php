{{--
    Live Classroom (student side). This is the first real-time/WebRTC
    page the student side has ever had — every prior broadcasting
    feature (chat, presence, the owner-side classroom) was built only
    for the React admin app. This page mirrors the owner side's
    useLiveClassWebRTC.js hook logic in vanilla JS, since there is no
    student React bundle to share it with.

    Honest limitation: this is written to the real WebRTC spec and the
    same signaling channel/event contract the owner side uses, but
    actual two-browser audio was not verifiable in the sandbox this was
    built in (no real browser/microphone available). See the phase
    report for what was and wasn't tested.
--}}
@extends('Users.User.cover')
@section('title', $liveClass->title)

@section('content')
<div class="p-4 md:p-6">
    <div class="flex items-center justify-between p-5 mb-6 text-white shadow-lg bg-gradient-to-br from-[#0B6D20] to-[#0B3D2E] rounded-3xl">
        <div>
            <h1 class="text-lg font-bold">{{ $liveClass->title }}</h1>
            <p class="text-sm opacity-90">{{ __('Umuyobozi') }}: {{ $liveClass->host->firstname }} {{ $liveClass->host->lastname }}</p>
        </div>
        <span id="kiu-status-pill" class="px-3 py-1 text-xs font-semibold bg-white rounded-full" style="color:#0B3D2E">
            {{ $liveClass->status === 'live' ? 'Birimo gukorwa' : 'Ntabwo birimo gukorwa' }}
        </span>
    </div>

    @if($liveClass->status !== 'live')
        <div class="p-5 bg-white shadow-lg rounded-3xl">
            <p class="text-sm text-gray-500">Iri somo ntabwo ririmo gukorwa nonaha. Ongera ugerageze ryatangira.</p>
        </div>
    @else
        <div class="grid gap-3 mb-4 sm:grid-cols-3" id="kiu-remote-audios"></div>

        <div class="flex flex-wrap items-center gap-3 p-4 mb-4 bg-white shadow-lg rounded-3xl">
            <button id="kiu-mic-btn" disabled class="px-4 py-2 text-sm font-semibold text-white rounded-xl opacity-40 cursor-not-allowed" style="background:#0B6D20" title="Tegereza ko umuyobozi yakwemerera">
                Fungura ijwi
            </button>
            <button id="kiu-cam-btn" disabled class="px-4 py-2 text-sm font-semibold text-white rounded-xl opacity-40 cursor-not-allowed" style="background:#0B6D20" title="Tegereza ko umuyobozi yakwemerera">
                Fungura kamera
            </button>
            <button id="kiu-hand-btn" class="px-4 py-2 text-sm font-semibold text-white rounded-xl" style="background:#C9A227">
                Zamura ukuboko
            </button>
            <button id="kiu-leave-btn" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-xl">
                Sohoka
            </button>
            <span id="kiu-mic-status" class="ml-auto text-xs text-gray-400">Waciwe ijwi — zamura ukuboko usabe kuvuga</span>
        </div>

        <div class="grid gap-3 mb-4 sm:grid-cols-3" id="kiu-local-video-wrap" style="display:none;">
            <div class="relative overflow-hidden bg-black rounded-2xl aspect-video">
                <video id="kiu-local-video" autoplay muted playsinline class="object-cover w-full h-full"></video>
                <span class="absolute px-2 py-0.5 text-xs text-white rounded bg-black/50 bottom-2 left-2">Njye</span>
            </div>
        </div>

        <div class="p-5 bg-white shadow-lg rounded-3xl">
            <h2 class="mb-3 font-bold" style="color:#0B3D2E">Abari mu isomo (<span id="kiu-count">0</span>)</h2>
            <ul id="kiu-participants" class="space-y-2 text-sm"></ul>
        </div>
    @endif
</div>

@if($liveClass->status === 'live')
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
(function () {
    const CLASS_ID = {{ $liveClass->id }};
    const MY_KEY = 'student:{{ $student->id }}';
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const ICE_SERVERS = [{ urls: 'stun:stun.l.google.com:19302' }];

    const peers = {};
    let localStream = null;
    let micEnabled = false;
    let camEnabled = false;
    let handRaised = false;
    // Was completely unenforced client-side before — a student could
    // click "Fungura ijwi" and unmute themselves at any moment, whether
    // or not they'd ever raised a hand or been approved. The buttons now
    // start disabled and only unlock when a hand-approved event actually
    // names this participant.
    let speakingAllowed = false;

    function api(path, method = 'POST') {
        return fetch(path, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            credentials: 'same-origin',
        }).then((r) => r.json());
    }

    function sendSignal(to, signal) {
        return fetch(`/student/live-classes/${CLASS_ID}/signal`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ to, signal }),
        });
    }

    function createPeerConnection(peerKey) {
        const pc = new RTCPeerConnection({ iceServers: ICE_SERVERS });

        pc.onicecandidate = (e) => {
            if (e.candidate) sendSignal(peerKey, { type: 'ice-candidate', candidate: e.candidate });
        };

        pc.ontrack = (e) => {
            let el = document.getElementById('remote-' + peerKey.replace(':', '-'));
            if (!el) {
                el = document.createElement('audio');
                el.id = 'remote-' + peerKey.replace(':', '-');
                el.autoplay = true;
                el.controls = true;
                el.className = 'w-full';
                const wrap = document.createElement('div');
                wrap.className = 'p-3 bg-white shadow rounded-2xl';
                wrap.appendChild(el);
                document.getElementById('kiu-remote-audios').appendChild(wrap);
            }
            el.srcObject = e.streams[0];
        };

        if (localStream) {
            localStream.getTracks().forEach((track) => pc.addTrack(track, localStream));
        }

        peers[peerKey] = pc;
        return pc;
    }

    async function initiateCallTo(peerKey) {
        const pc = createPeerConnection(peerKey);
        const offer = await pc.createOffer();
        await pc.setLocalDescription(offer);
        sendSignal(peerKey, { type: 'offer', sdp: offer });
    }

    async function handleSignal(payload) {
        if (payload.to !== MY_KEY) return;
        const peerKey = payload.from;
        const signal = payload.signal;
        let pc = peers[peerKey];

        if (signal.type === 'offer') {
            pc = pc || createPeerConnection(peerKey);
            await pc.setRemoteDescription(new RTCSessionDescription(signal.sdp));
            const answer = await pc.createAnswer();
            await pc.setLocalDescription(answer);
            sendSignal(peerKey, { type: 'answer', sdp: answer });
        } else if (signal.type === 'answer' && pc) {
            await pc.setRemoteDescription(new RTCSessionDescription(signal.sdp));
        } else if (signal.type === 'ice-candidate' && pc) {
            try { await pc.addIceCandidate(new RTCIceCandidate(signal.candidate)); } catch (e) {}
        }
    }

    function renderParticipants(list) {
        document.getElementById('kiu-count').textContent = list.length;
        const ul = document.getElementById('kiu-participants');
        ul.innerHTML = '';
        list.forEach((p) => {
            const li = document.createElement('li');
            li.className = 'flex items-center justify-between';
            li.innerHTML = `<span>${p.name}${p.role === 'host' ? ' · Umuyobozi' : ''}${p.hand_raised ? ' ✋' : ''}</span>`;
            ul.appendChild(li);
        });
    }

    function loadParticipants() {
        fetch(`/student/live-classes/${CLASS_ID}/participants`, { headers: { Accept: 'application/json' } })
            .then((r) => r.json())
            .then((res) => {
                const list = res.data || [];
                renderParticipants(list);
                list.filter((p) => p.key !== MY_KEY).forEach((p) => {
                    if (!peers[p.key]) initiateCallTo(p.key);
                });

                // Reconcile speaking-permission state against the server's
                // record of THIS participant, not just live broadcast
                // events — otherwise a page refresh mid-class after being
                // approved would silently re-lock a student who was
                // already allowed to speak.
                const me = list.find((p) => p.key === MY_KEY);
                if (me && !me.is_muted && !speakingAllowed) {
                    unlockSpeaking();
                } else if (me && me.is_muted && speakingAllowed) {
                    lockSpeaking('Umuyobozi yaguciye ijwi.');
                }
            });
    }

    // Join first — the live-class.{id} channel's authorization check
    // requires an active (left_at IS NULL) participant record, so
    // subscribing before this call would fail auth.
    api(`/student/live-classes/${CLASS_ID}/join`).then(() => {
        loadParticipants();

        // Switched from Pusher to Laravel Reverb (matches the spec:
        // "Use Laravel Reverb... do not introduce a separate Node
        // backend"). Reverb speaks the same protocol, so only the
        // broadcaster name + connection target change here.
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

        const channel = echo.private(`live-class.${CLASS_ID}`);
        channel.listen('.signal', handleSignal);
        channel.listen('.state-changed', (payload) => {
            const refreshOn = ['participant-joined', 'participant-left', 'participant-removed', 'hand-raised', 'hand-approved', 'hand-rejected', 'participant-muted', 'muted-everyone'];
            if (refreshOn.includes(payload.type)) loadParticipants();

            if (payload.type === 'hand-approved' && payload.participant_key === MY_KEY) {
                unlockSpeaking();
            }
            if (payload.type === 'hand-rejected' && payload.participant_key === MY_KEY) {
                document.getElementById('kiu-mic-status').textContent = 'Umuyobozi ntiyakwemereye kuvuga ubu.';
            }
            if (payload.type === 'participant-muted' && payload.participant_key === MY_KEY) {
                lockSpeaking('Umuyobozi yaguciye ijwi.');
            }
            if (payload.type === 'muted-everyone') {
                lockSpeaking('Umuyobozi yahagaritse bose.');
            }
            if (payload.type === 'class-ended') {
                alert('Isomo ryarangiye.');
                window.location.href = '{{ route('student.dashboard') }}';
            }
        });

        window.addEventListener('beforeunload', () => {
            api(`/student/live-classes/${CLASS_ID}/leave`);
        });
    });

    function unlockSpeaking() {
        speakingAllowed = true;
        const micBtn = document.getElementById('kiu-mic-btn');
        const camBtn = document.getElementById('kiu-cam-btn');
        micBtn.disabled = false;
        camBtn.disabled = false;
        micBtn.classList.remove('opacity-40', 'cursor-not-allowed');
        camBtn.classList.remove('opacity-40', 'cursor-not-allowed');
        micBtn.title = '';
        camBtn.title = '';
        document.getElementById('kiu-mic-status').textContent = 'Umuyobozi yaguemereye kuvuga — fungura ijwi cyangwa kamera.';
    }

    function lockSpeaking(message) {
        speakingAllowed = false;
        // A mid-call mute/mute-all revokes the mic/camera immediately —
        // don't just disable the buttons, actually stop sending media.
        if (localStream) {
            localStream.getTracks().forEach((t) => t.stop());
            localStream = null;
        }
        micEnabled = false;
        camEnabled = false;
        const micBtn = document.getElementById('kiu-mic-btn');
        const camBtn = document.getElementById('kiu-cam-btn');
        micBtn.disabled = true;
        camBtn.disabled = true;
        micBtn.classList.add('opacity-40', 'cursor-not-allowed');
        camBtn.classList.add('opacity-40', 'cursor-not-allowed');
        micBtn.textContent = 'Fungura ijwi';
        camBtn.textContent = 'Fungura kamera';
        document.getElementById('kiu-local-video-wrap').style.display = 'none';
        document.getElementById('kiu-mic-status').textContent = message || 'Waciwe ijwi — zamura ukuboko usabe kuvuga';
    }

    async function ensureLocalStream(wantAudio, wantVideo) {
        if (localStream) {
            // Adding a track to an already-open stream (e.g. mic already on,
            // now also enabling camera) requires a fresh getUserMedia call
            // for the new kind and merging tracks in, since a MediaStream's
            // track list can't be extended by re-requesting the same stream.
            const extra = await navigator.mediaDevices.getUserMedia({ audio: wantAudio && !localStream.getAudioTracks().length, video: wantVideo && !localStream.getVideoTracks().length });
            extra.getTracks().forEach((t) => localStream.addTrack(t));
            return localStream;
        }
        localStream = await navigator.mediaDevices.getUserMedia({ audio: wantAudio, video: wantVideo });
        return localStream;
    }

    document.getElementById('kiu-mic-btn').addEventListener('click', async () => {
        if (!speakingAllowed) return; // guarded, but belt-and-suspenders against a stale disabled state
        if (!micEnabled) {
            try {
                await ensureLocalStream(true, camEnabled);
                micEnabled = true;
                document.getElementById('kiu-mic-btn').textContent = 'Hagarika ijwi';
                document.getElementById('kiu-mic-status').textContent = 'Ijwi ryafunguwe';
                Object.values(peers).forEach((pc) => {
                    localStream.getAudioTracks().forEach((track) => pc.addTrack(track, localStream));
                });
            } catch (e) {
                alert('Ntibishoboka gufungura mikoro. Reba uruhushya rwa microphone muri browser yawe.');
            }
        } else {
            localStream?.getAudioTracks().forEach((t) => { t.stop(); localStream.removeTrack(t); });
            micEnabled = false;
            document.getElementById('kiu-mic-btn').textContent = 'Fungura ijwi';
            document.getElementById('kiu-mic-status').textContent = camEnabled ? 'Ijwi ryahagaritswe' : 'Waciwe ijwi — zamura ukuboko usabe kuvuga';
        }
    });

    document.getElementById('kiu-cam-btn').addEventListener('click', async () => {
        if (!speakingAllowed) return;
        const localVideoWrap = document.getElementById('kiu-local-video-wrap');
        const localVideoEl = document.getElementById('kiu-local-video');
        if (!camEnabled) {
            try {
                await ensureLocalStream(micEnabled, true);
                camEnabled = true;
                localVideoEl.srcObject = localStream;
                localVideoWrap.style.display = 'grid';
                document.getElementById('kiu-cam-btn').textContent = 'Hagarika kamera';
                Object.values(peers).forEach((pc) => {
                    localStream.getVideoTracks().forEach((track) => pc.addTrack(track, localStream));
                });
            } catch (e) {
                alert('Ntibishoboka gufungura kamera. Reba uruhushya rwa kamera muri browser yawe.');
            }
        } else {
            localStream?.getVideoTracks().forEach((t) => { t.stop(); localStream.removeTrack(t); });
            camEnabled = false;
            localVideoWrap.style.display = 'none';
            document.getElementById('kiu-cam-btn').textContent = 'Fungura kamera';
        }
    });

    document.getElementById('kiu-hand-btn').addEventListener('click', () => {
        const endpoint = handRaised ? 'lower-hand' : 'raise-hand';
        api(`/student/live-classes/${CLASS_ID}/${endpoint}`).then(() => {
            handRaised = !handRaised;
            document.getElementById('kiu-hand-btn').textContent = handRaised ? 'Manura ukuboko' : 'Zamura ukuboko';
        });
    });

    document.getElementById('kiu-leave-btn').addEventListener('click', () => {
        api(`/student/live-classes/${CLASS_ID}/leave`).then(() => {
            window.location.href = '{{ route('student.dashboard') }}';
        });
    });
})();
</script>
@endif
@endsection
