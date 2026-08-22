import { useEffect, useRef, useState, useCallback } from "react";
import { useParams } from "react-router-dom";
import {
  Calendar,
  Check,
  Copy,
  Eye,
  EyeOff,
  Hand,
  Lock,
  Unlock,
  Mic,
  MicOff,
  MessageSquare,
  MonitorUp,
  MonitorX,
  PhoneOff,
  Radio,
  ShieldCheck,
  ShieldPlus,
  UserMinus,
  Users,
  Video,
  VideoOff,
  Volume2,
  Wifi,
  WifiOff,
  X,
} from "lucide-react";
import { getToken } from "../api/client.js";
import { useAuth } from "../context/AuthContext.jsx";
import { useLiveClass } from "../hooks/useLiveClass.js";
import {
  createLiveClass,
  endLiveClass,
  getLiveClass,
  listActiveLiveClasses,
  listUpcomingLiveClasses,
  startScheduledLiveClass,
} from "../api/liveClasses.js";
import {
  closePeerConnection,
  createPeerConnection,
  getCameraStream,
  getMicrophoneStream,
  getScreenShareStream,
  watchActiveSpeaker,
} from "../webrtc/peer.js";

const MIC_LABEL = { HOST: "Umuyobozi", MUTED: "Yacecetse", APPROVED: "Yemerewe kuvuga" };

const CONNECTION_LABEL = {
  connecting: { text: "Turahuza...", tone: "text-amber-300 bg-amber-400/10" },
  connected: { text: "Byahujwe", tone: "text-emerald-300 bg-emerald-400/10" },
  reconnecting: { text: "Turongera guhuza...", tone: "text-amber-300 bg-amber-400/10" },
  disconnected: { text: "Ntabwo bihuye", tone: "text-red-300 bg-red-400/10" },
};

function toLocalInputValue(date) {
  const pad = (n) => String(n).padStart(2, "0");
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(
    date.getMinutes()
  )}`;
}

function formatDateTime(iso) {
  return new Date(iso).toLocaleString(undefined, { dateStyle: "medium", timeStyle: "short" });
}

function formatCountdown(scheduledFor, nowTick) {
  const diff = new Date(scheduledFor).getTime() - nowTick;
  if (diff <= 0) return "Igihe kigeze";
  const mins = Math.round(diff / 60000);
  if (mins < 60) return `Itangira mu minota ${mins}`;
  const hrs = Math.floor(mins / 60);
  if (hrs < 48) return `Itangira mu masaha ${hrs}`;
  return `Itangira mu minsi ${Math.floor(hrs / 24)}`;
}

function classLink(id) {
  return `${window.location.origin}/live-class/${id}`;
}

export default function LiveClassPage() {
  const { user } = useAuth();
  const { id: routeId } = useParams();
  const token = getToken();
  const rt = useLiveClass(token);

  const [activeClasses, setActiveClasses] = useState([]);
  const [upcomingClasses, setUpcomingClasses] = useState([]);
  const [newTitle, setNewTitle] = useState("");
  const [scheduleMode, setScheduleMode] = useState("now"); // "now" | "schedule"
  const [scheduledForInput, setScheduledForInput] = useState("");
  const [scheduleSuccess, setScheduleSuccess] = useState(null); // just-created scheduled class, for the link banner
  const [error, setError] = useState("");
  const [banner, setBanner] = useState("");
  const [copiedId, setCopiedId] = useState(null);
  const [nowTick, setNowTick] = useState(Date.now());

  // What a bare /live-class/:id link resolves to before joining — lets a
  // shared link show "this class starts at X" or "join now" without forcing
  // an automatic, silent join.
  const [routePreview, setRoutePreview] = useState(null);
  const [routePreviewError, setRoutePreviewError] = useState("");

  const [currentClass, setCurrentClass] = useState(null); // {id, title, hostId}
  const [self, setSelf] = useState(null); // {userId, role, micState, handRaised}
  const [participants, setParticipants] = useState([]);
  const [locked, setLocked] = useState(false);
  const [namesRevealed, setNamesRevealed] = useState(false);
  const [canModerateChat, setCanModerateChat] = useState(false);
  const [grantedModeratorIds, setGrantedModeratorIds] = useState(() => new Set());
  const [hostMicOn, setHostMicOn] = useState(false);
  const [myMicOn, setMyMicOn] = useState(false);
  const [hostCameraOn, setHostCameraOn] = useState(false);
  const [myCameraOn, setMyCameraOn] = useState(false);
  const [screenShareOn, setScreenShareOn] = useState(false);
  const [speakingUserIds, setSpeakingUserIds] = useState(() => new Set());
  const [amSpeaking, setAmSpeaking] = useState(false);

  const [chatOpen, setChatOpen] = useState(false);
  const [chatMessages, setChatMessages] = useState([]);
  const [chatInput, setChatInput] = useState("");
  const [unreadChat, setUnreadChat] = useState(0);

  const peersRef = useRef(new Map());
  const relayPeersRef = useRef(new Map());
  const speakerStreamsRef = useRef(new Map());
  const participantsRef = useRef([]);

  const hostStreamRef = useRef(null);
  const myStreamRef = useRef(null);
  const hostCameraStreamRef = useRef(null);
  const myCameraStreamRef = useRef(null);
  const screenStreamRef = useRef(null);

  const [remoteAudios, setRemoteAudios] = useState([]); // [{key, stream}]
  const [remoteVideos, setRemoteVideos] = useState([]); // [{key, stream, label}]

  const canHost = user?.role === "ADMIN" || (user?.permissions ?? []).includes("classroom.host");
  const isHost = self?.role === "HOST";

  useEffect(() => {
    participantsRef.current = participants;
  }, [participants]);

  useEffect(() => {
    const t = setInterval(() => setNowTick(Date.now()), 30000);
    return () => clearInterval(t);
  }, []);

  const loadLists = useCallback(async () => {
    try {
      const [liveRes, upcomingRes] = await Promise.all([listActiveLiveClasses(), listUpcomingLiveClasses()]);
      setActiveClasses(liveRes.data);
      setUpcomingClasses(upcomingRes.data);
    } catch (err) {
      setError(err.message);
    }
  }, []);

  useEffect(() => {
    loadLists();
  }, [loadLists]);

  // Resolve a shared /live-class/:id link before joining anything.
  useEffect(() => {
    if (!routeId || currentClass) {
      setRoutePreview(null);
      return;
    }
    setRoutePreviewError("");
    getLiveClass(routeId)
      .then((res) => setRoutePreview(res.data.liveClass))
      .catch((err) => setRoutePreviewError(err.message));
  }, [routeId, currentClass]);

  function addRemoteAudio(key, stream) {
    setRemoteAudios((prev) => [...prev.filter((a) => a.key !== key), { key, stream }]);
  }
  function removeRemoteAudio(key) {
    setRemoteAudios((prev) => prev.filter((a) => a.key !== key));
  }
  function addRemoteVideo(key, stream, label) {
    setRemoteVideos((prev) => [...prev.filter((v) => v.key !== key), { key, stream, label }]);
  }
  function removeRemoteVideo(key) {
    setRemoteVideos((prev) => prev.filter((v) => v.key !== key));
  }
  function participantName(userId) {
    if (userId === currentClass?.hostId) return "Umuyobozi";
    return participantsRef.current.find((p) => p.userId === userId)?.fullName ?? "Umwigishwa";
  }

  function cleanupAllPeers() {
    peersRef.current.forEach((pc) => closePeerConnection(pc));
    peersRef.current.clear();
    relayPeersRef.current.forEach((pc) => closePeerConnection(pc));
    relayPeersRef.current.clear();
    speakerStreamsRef.current.clear();
    [hostStreamRef, myStreamRef, hostCameraStreamRef, myCameraStreamRef, screenStreamRef].forEach((ref) => {
      ref.current?.getTracks().forEach((t) => t.stop());
      ref.current = null;
    });
    setRemoteAudios([]);
    setRemoteVideos([]);
    setHostMicOn(false);
    setMyMicOn(false);
    setHostCameraOn(false);
    setMyCameraOn(false);
    setScreenShareOn(false);
    setSpeakingUserIds(new Set());
    setChatMessages([]);
    setUnreadChat(0);
    setGrantedModeratorIds(new Set());
  }

  function leaveClassLocally(message) {
    cleanupAllPeers();
    setCurrentClass(null);
    setSelf(null);
    setParticipants([]);
    setLocked(false);
    setNamesRevealed(false);
    setCanModerateChat(false);
    if (message) setBanner(message);
    loadLists();
  }

  // ---- generic: open a peer connection carrying `stream` toward one target ----
  const openStreamTo = useCallback(
    async (targetUserId, stream, kind) => {
      if (!stream || !currentClass) return;
      const key = `${targetUserId}:${kind}`;
      if (peersRef.current.has(key)) return;
      const pc = createPeerConnection({
        onIceCandidate: (candidate) => rt.sendIceCandidate(currentClass.id, targetUserId, candidate, kind),
        onTrack: () => {},
      });
      stream.getTracks().forEach((t) => pc.addTrack(t, stream));
      peersRef.current.set(key, pc);
      const offer = await pc.createOffer();
      await pc.setLocalDescription(offer);
      rt.sendOffer(currentClass.id, targetUserId, offer, kind);
    },
    [currentClass, rt]
  );

  function stopBroadcastKind(kind) {
    participantsRef.current.forEach((p) => {
      if (p.userId === user.id) return;
      const key = `${p.userId}:${kind}`;
      closePeerConnection(peersRef.current.get(key));
      peersRef.current.delete(key);
    });
  }

  const relayStreamToOne = useCallback(
    async (targetUserId, sourceUserId, stream, mediaKind) => {
      if (!currentClass || targetUserId === sourceUserId) return;
      const relayKind = `relay:${sourceUserId}:${mediaKind}`;
      const key = `${targetUserId}:${relayKind}`;
      if (relayPeersRef.current.has(key)) return;
      const pc = createPeerConnection({
        onIceCandidate: (c) => rt.sendIceCandidate(currentClass.id, targetUserId, c, relayKind),
        onTrack: () => {},
      });
      stream.getTracks().forEach((t) => pc.addTrack(t, stream));
      relayPeersRef.current.set(key, pc);
      const offer = await pc.createOffer();
      await pc.setLocalDescription(offer);
      rt.sendOffer(currentClass.id, targetUserId, offer, relayKind);
    },
    [currentClass, rt]
  );

  function relayToAllOthers(sourceUserId, stream, mediaKind) {
    speakerStreamsRef.current.set(`${sourceUserId}:${mediaKind}`, stream);
    participantsRef.current.forEach((p) => {
      if (p.userId !== user.id) relayStreamToOne(p.userId, sourceUserId, stream, mediaKind);
    });
  }

  function stopRelaying(sourceUserId, mediaKind) {
    speakerStreamsRef.current.delete(`${sourceUserId}:${mediaKind}`);
    const suffix = `:relay:${sourceUserId}:${mediaKind}`;
    for (const [key, pc] of relayPeersRef.current.entries()) {
      if (key.endsWith(suffix)) {
        closePeerConnection(pc);
        relayPeersRef.current.delete(key);
      }
    }
  }

  // ---- host: mic ----
  async function handleHostEnableMic() {
    try {
      const stream = await getMicrophoneStream();
      hostStreamRef.current = stream;
      setHostMicOn(true);
      for (const p of participants) if (p.userId !== user.id) await openStreamTo(p.userId, stream, "broadcast");
    } catch {
      setError("Ntibyashobotse gukoresha mikoro. Emera uburenganzira bwa mikoro muri browser yawe.");
    }
  }

  async function handleEnableMyMic() {
    if (!currentClass) return;
    try {
      const stream = await getMicrophoneStream();
      myStreamRef.current = stream;
      setMyMicOn(true);
      await openStreamTo(currentClass.hostId, stream, "uplink");
    } catch {
      setError("Ntibyashobotse gukoresha mikoro. Emera uburenganzira bwa mikoro muri browser yawe.");
    }
  }

  // ---- host: camera ----
  function stopHostCameraLocal() {
    hostCameraStreamRef.current?.getTracks().forEach((t) => t.stop());
    hostCameraStreamRef.current = null;
    stopBroadcastKind("broadcast-video");
    setHostCameraOn(false);
  }
  async function handleToggleHostCamera() {
    if (hostCameraOn) {
      stopHostCameraLocal();
      return;
    }
    try {
      const stream = await getCameraStream();
      hostCameraStreamRef.current = stream;
      setHostCameraOn(true);
      for (const p of participants) if (p.userId !== user.id) await openStreamTo(p.userId, stream, "broadcast-video");
    } catch {
      setError("Ntibyashobotse gukoresha kamera. Emera uburenganzira bwa kamera muri browser yawe.");
    }
  }

  // ---- host: screen share ----
  function stopScreenShareLocal() {
    screenStreamRef.current?.getTracks().forEach((t) => t.stop());
    screenStreamRef.current = null;
    stopBroadcastKind("screenshare");
    setScreenShareOn(false);
  }
  async function handleToggleScreenShare() {
    if (screenShareOn) {
      stopScreenShareLocal();
      return;
    }
    try {
      const stream = await getScreenShareStream();
      screenStreamRef.current = stream;
      setScreenShareOn(true);
      stream.getVideoTracks()[0]?.addEventListener("ended", stopScreenShareLocal);
      for (const p of participants) if (p.userId !== user.id) await openStreamTo(p.userId, stream, "screenshare");
    } catch {
      setError("Ntibyashobotse gusangira ecran yawe.");
    }
  }

  // ---- any participant: own camera (relayed by host to everyone else) ----
  function stopMyCameraLocal() {
    myCameraStreamRef.current?.getTracks().forEach((t) => t.stop());
    myCameraStreamRef.current = null;
    if (currentClass) {
      const key = `${currentClass.hostId}:uplink-video`;
      closePeerConnection(peersRef.current.get(key));
      peersRef.current.delete(key);
    }
    setMyCameraOn(false);
  }
  async function handleToggleMyCamera() {
    if (myCameraOn) {
      stopMyCameraLocal();
      return;
    }
    try {
      const stream = await getCameraStream();
      myCameraStreamRef.current = stream;
      setMyCameraOn(true);
      await openStreamTo(currentClass.hostId, stream, "uplink-video");
    } catch {
      setError("Ntibyashobotse gukoresha kamera. Emera uburenganzira bwa kamera muri browser yawe.");
    }
  }

  // ---- join / create / schedule ----
  async function handleJoin(liveClassId, title, hostId) {
    setError("");
    try {
      const ack = await rt.joinClass(liveClassId);
      setCurrentClass({ id: liveClassId, title, hostId: hostId ?? (ack.self.role === "HOST" ? user.id : hostId) });
      setSelf(ack.self);
      setParticipants(ack.participants);
      setLocked(Boolean(ack.locked));
      setNamesRevealed(Boolean(ack.namesRevealed));
      setCanModerateChat(Boolean(ack.canModerateChat));
      setRoutePreview(null);
    } catch (err) {
      setError(err.message);
    }
  }

  async function handleCreate(e) {
    e.preventDefault();
    if (!newTitle.trim()) return;
    setError("");
    setScheduleSuccess(null);

    if (scheduleMode === "schedule") {
      if (!scheduledForInput) {
        setError("Hitamo itariki n'igihe isomo ritangira.");
        return;
      }
      try {
        const res = await createLiveClass(newTitle.trim(), new Date(scheduledForInput).toISOString());
        setNewTitle("");
        setScheduledForInput("");
        setScheduleSuccess(res.data.liveClass);
        loadLists();
      } catch (err) {
        setError(err.message);
      }
      return;
    }

    try {
      const res = await createLiveClass(newTitle.trim());
      setNewTitle("");
      await handleJoin(res.data.liveClass.id, res.data.liveClass.title, res.data.liveClass.hostId);
    } catch (err) {
      setError(err.message);
    }
  }

  async function handleStartScheduled(id, title, hostId) {
    setError("");
    try {
      await startScheduledLiveClass(id);
      await handleJoin(id, title, hostId);
    } catch (err) {
      setError(err.message);
    }
  }

  async function handleEndClass() {
    if (!currentClass) return;
    try {
      await rt.endClass(currentClass.id);
      await endLiveClass(currentClass.id).catch(() => {});
    } catch (err) {
      setError(err.message);
    }
  }

  async function handleToggleLock() {
    if (!currentClass) return;
    try {
      if (locked) await rt.unlockClass(currentClass.id);
      else await rt.lockClass(currentClass.id);
    } catch (err) {
      setError(err.message);
    }
  }

  async function handleToggleRevealNames() {
    if (!currentClass) return;
    try {
      if (namesRevealed) await rt.hideNames(currentClass.id);
      else await rt.revealNames(currentClass.id);
    } catch (err) {
      setError(err.message);
    }
  }

  async function handleToggleModerator(targetUserId) {
    if (!currentClass) return;
    const alreadyGranted = grantedModeratorIds.has(targetUserId);
    try {
      if (alreadyGranted) {
        await rt.revokeChatModerator(currentClass.id, targetUserId);
        setGrantedModeratorIds((prev) => {
          const next = new Set(prev);
          next.delete(targetUserId);
          return next;
        });
      } else {
        await rt.grantChatModerator(currentClass.id, targetUserId);
        setGrantedModeratorIds((prev) => new Set(prev).add(targetUserId));
      }
    } catch (err) {
      setError(err.message);
    }
  }

  function handleSendChat(e) {
    e.preventDefault();
    const text = chatInput.trim();
    if (!text || !currentClass) return;
    rt.sendChatMessage(currentClass.id, text);
    setChatInput("");
  }

  function handleCopyLink(id) {
    navigator.clipboard
      ?.writeText(classLink(id))
      .then(() => {
        setCopiedId(id);
        setTimeout(() => setCopiedId(null), 2000);
      })
      .catch(() => setError("Ntibyashobotse gukoporora link."));
  }

  // ---- realtime subscriptions: every one of these is unsubscribed on cleanup ----
  useEffect(() => {
    if (!currentClass) return undefined;
    const classId = currentClass.id;
    const amHost = self?.role === "HOST";

    const offJoined = rt.onParticipantJoined((p) => {
      setParticipants((prev) => (prev.some((x) => x.userId === p.userId) ? prev : [...prev, p]));
      if (amHost) {
        if (hostMicOn) openStreamTo(p.userId, hostStreamRef.current, "broadcast");
        if (hostCameraOn) openStreamTo(p.userId, hostCameraStreamRef.current, "broadcast-video");
        if (screenShareOn) openStreamTo(p.userId, screenStreamRef.current, "screenshare");
        speakerStreamsRef.current.forEach((stream, mapKey) => {
          const [sourceUserId, mediaKind] = mapKey.split(":");
          relayStreamToOne(p.userId, sourceUserId, stream, mediaKind);
        });
      }
    });

    const offUpdated = rt.onParticipantUpdated((p) => {
      setParticipants((prev) => prev.map((x) => (x.userId === p.userId ? p : x)));
      if (p.userId === user.id) setSelf((s) => (s ? { ...s, ...p } : s));
    });

    const offBulkUpdate = rt.onParticipantsBulkUpdate((list) => setParticipants(list));

    const offLeft = rt.onParticipantLeft(({ userId: leftId }) => {
      setParticipants((prev) => prev.filter((x) => x.userId !== leftId));
      ["broadcast", "uplink", "broadcast-video", "uplink-video", "screenshare"].forEach((kind) => {
        const key = `${leftId}:${kind}`;
        closePeerConnection(peersRef.current.get(key));
        peersRef.current.delete(key);
      });
      removeRemoteAudio(`${leftId}:uplink`);
      removeRemoteVideo(`${leftId}:broadcast-video`);
      removeRemoteVideo(`${leftId}:screenshare`);
      removeRemoteVideo(`${leftId}:uplink-video`);
      removeRemoteAudio(`${leftId}:relay-audio`);
      removeRemoteVideo(`${leftId}:relay-video`);
      stopRelaying(leftId, "audio");
      stopRelaying(leftId, "video");
      for (const [key, pc] of relayPeersRef.current.entries()) {
        if (key.startsWith(`${leftId}:relay:`)) {
          closePeerConnection(pc);
          relayPeersRef.current.delete(key);
        }
      }
      setSpeakingUserIds((prev) => {
        const next = new Set(prev);
        next.delete(leftId);
        return next;
      });
    });

    const offHandRaised = rt.onHandRaised(({ userId: raisedId }) => {
      setParticipants((prev) => prev.map((x) => (x.userId === raisedId ? { ...x, handRaised: true } : x)));
    });

    const offApproved = rt.onSpeakerApproved(() => {
      setSelf((s) => (s ? { ...s, micState: "APPROVED", handRaised: false } : s));
      setBanner("Wemerewe kuvuga! Kanda 'Fungura Mikoro' niba ushaka kuvuga.");
    });

    const offRejected = rt.onSpeakerRejected(() => {
      setSelf((s) => (s ? { ...s, handRaised: false } : s));
      setBanner("Ubusabe bwawe bwo kuvuga ntibwemejwe ubu.");
    });

    const offRevoked = rt.onSpeakerRevoked(() => {
      setSelf((s) => (s ? { ...s, micState: "MUTED" } : s));
      setMyMicOn(false);
      const key = `${currentClass.hostId}:uplink`;
      closePeerConnection(peersRef.current.get(key));
      peersRef.current.delete(key);
      myStreamRef.current?.getTracks().forEach((t) => t.stop());
      myStreamRef.current = null;
    });

    const offMutedAll = rt.onMutedAll(() => {
      if (self?.role !== "HOST") {
        setMyMicOn(false);
        const key = `${currentClass.hostId}:uplink`;
        closePeerConnection(peersRef.current.get(key));
        peersRef.current.delete(key);
        myStreamRef.current?.getTracks().forEach((t) => t.stop());
        myStreamRef.current = null;
      }
    });

    const offCamerasDisabled = rt.onCamerasDisabled(() => {
      stopMyCameraLocal();
      if (amHost) {
        stopHostCameraLocal();
        stopScreenShareLocal();
      }
    });

    const offLocked = rt.onLocked(() => {
      setLocked(true);
      if (!amHost) setBanner("Umuyobozi yafunze isomo — nta bandi bashobora kwinjira ubu.");
    });
    const offUnlocked = rt.onUnlocked(() => setLocked(false));

    const offNamesRevealed = rt.onNamesRevealed(() => setNamesRevealed(true));
    const offNamesHidden = rt.onNamesHidden(() => setNamesRevealed(false));
    const offModeratorGranted = rt.onChatModeratorGranted(() => {
      setCanModerateChat(true);
      setBanner("Wahawe ubushobozi bwo kuyobora ikiganiro cy'iri somo.");
    });
    const offModeratorRevoked = rt.onChatModeratorRevoked(() => setCanModerateChat(false));

    const offChat = rt.onChatMessage((msg) => {
      setChatMessages((prev) => [...prev.slice(-99), msg]);
      if (!chatOpen && msg.userId !== user.id) setUnreadChat((c) => c + 1);
    });

    const offRemoved = rt.onRemoved(() => leaveClassLocally("Wakuwe mu isomo n'umuyobozi."));
    const offEnded = rt.onClassEnded(() => leaveClassLocally("Isomo ryarangiye."));
    const offHostGone = rt.onHostDisconnected(() =>
      setBanner("Umuyobozi w'isomo yahagaritse guhuza. Tegereza cyangwa ugerageze nyuma gato.")
    );

    // ---- WebRTC signaling ----
    const offOffer = rt.onOffer(async ({ fromUserId, sdp, kind }) => {
      const pc = createPeerConnection({
        onIceCandidate: (candidate) => rt.sendIceCandidate(classId, fromUserId, candidate, kind),
        onTrack: (stream) => {
          if (kind === "broadcast") addRemoteAudio(`${fromUserId}:broadcast`, stream);
          else if (kind === "broadcast-video") addRemoteVideo(`${fromUserId}:broadcast-video`, stream, "Umuyobozi");
          else if (kind === "screenshare") addRemoteVideo(`${fromUserId}:screenshare`, stream, "Ecran y'umuyobozi");
          else if (kind === "uplink") {
            addRemoteAudio(`${fromUserId}:uplink`, stream);
            if (amHost) relayToAllOthers(fromUserId, stream, "audio");
          } else if (kind === "uplink-video") {
            addRemoteVideo(`${fromUserId}:uplink-video`, stream, participantName(fromUserId));
            if (amHost) relayToAllOthers(fromUserId, stream, "video");
          } else if (kind.startsWith("relay:")) {
            const [, sourceUserId, mediaKind] = kind.split(":");
            if (mediaKind === "audio") addRemoteAudio(`${sourceUserId}:relay-audio`, stream);
            else addRemoteVideo(`${sourceUserId}:relay-video`, stream, participantName(sourceUserId));
          }
        },
      });
      await pc.setRemoteDescription(new RTCSessionDescription(sdp));
      const answer = await pc.createAnswer();
      await pc.setLocalDescription(answer);
      peersRef.current.set(`${fromUserId}:${kind}`, pc);
      rt.sendAnswer(classId, fromUserId, answer, kind);
    });

    const offAnswer = rt.onAnswer(async ({ fromUserId, sdp, kind }) => {
      const pc = peersRef.current.get(`${fromUserId}:${kind}`) ?? relayPeersRef.current.get(`${fromUserId}:${kind}`);
      await pc?.setRemoteDescription(new RTCSessionDescription(sdp));
    });

    const offIce = rt.onIceCandidate(({ fromUserId, candidate, kind }) => {
      const pc = peersRef.current.get(`${fromUserId}:${kind}`) ?? relayPeersRef.current.get(`${fromUserId}:${kind}`);
      pc?.addIceCandidate(new RTCIceCandidate(candidate));
    });

    return () => {
      offJoined();
      offUpdated();
      offBulkUpdate();
      offLeft();
      offHandRaised();
      offApproved();
      offRejected();
      offRevoked();
      offMutedAll();
      offCamerasDisabled();
      offLocked();
      offUnlocked();
      offNamesRevealed();
      offNamesHidden();
      offModeratorGranted();
      offModeratorRevoked();
      offChat();
      offRemoved();
      offEnded();
      offHostGone();
      offOffer();
      offAnswer();
      offIce();
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [currentClass, self?.role, hostMicOn, hostCameraOn, screenShareOn, chatOpen]);

  // full teardown on unmount
  useEffect(() => () => cleanupAllPeers(), []);

  // ---- active-speaker detection on every remote audio stream ----
  useEffect(() => {
    const stopFns = remoteAudios.map(({ key, stream }) => {
      const uid = key.split(":")[0];
      return watchActiveSpeaker(stream, (isSpeaking) => {
        setSpeakingUserIds((prev) => {
          const next = new Set(prev);
          if (isSpeaking) next.add(uid);
          else next.delete(uid);
          return next;
        });
      });
    });
    return () => stopFns.forEach((stop) => stop());
  }, [remoteAudios]);

  // ---- active-speaker detection on MY OWN outgoing mic ----
  useEffect(() => {
    const stream = isHost ? hostStreamRef.current : myStreamRef.current;
    if (!stream) {
      setAmSpeaking(false);
      return undefined;
    }
    return watchActiveSpeaker(stream, setAmSpeaking);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [hostMicOn, myMicOn, isHost]);

  const raisedCount = participants.filter((p) => p.handRaised).length;
  const mutedCount = participants.filter((p) => p.micState === "MUTED").length;

  // ============================== NOT IN A CLASS ==============================
  if (!currentClass) {
    // A shared link to one specific class — show a focused card for it.
    if (routeId) {
      return (
        <div className="min-h-[70vh] bg-neutral-950 py-16 px-6">
          <div className="max-w-[560px] mx-auto">
            <div className="eyebrow !text-gold-400">KWEGEREZA LIVE CLASS</div>
            {routePreviewError ? (
              <div className="bg-red-500/10 border border-red-500/30 text-red-300 text-sm rounded-xl px-4 py-3 mt-4">
                {routePreviewError}
              </div>
            ) : !routePreview ? (
              <div className="text-neutral-400 text-sm mt-6">Turimo gupakira...</div>
            ) : (
              <div className="bg-neutral-900 border border-neutral-800 rounded-2xl p-6 mt-4">
                <h1 className="font-display text-2xl font-bold text-white mb-1">{routePreview.title}</h1>
                <p className="text-neutral-400 text-sm mb-4">Umuyobozi: {routePreview.hostName}</p>

                {routePreview.status === "LIVE" && (
                  <>
                    <div className="flex items-center gap-2 text-xs font-bold text-red-400 mb-4">
                      <span className="w-2 h-2 rounded-full bg-red-500 animate-pulse" /> ISOMO RIRI LIVE UBU
                    </div>
                    <button
                      onClick={() => handleJoin(routePreview.id, routePreview.title, routePreview.hostId)}
                      className="btn btn-gold w-full justify-center"
                    >
                      <Radio size={16} /> Injira mu isomo
                    </button>
                  </>
                )}

                {routePreview.status === "SCHEDULED" && (
                  <>
                    <div className="flex items-center gap-2 text-sm text-neutral-300 mb-1">
                      <Calendar size={14} /> {formatDateTime(routePreview.scheduledFor)}
                    </div>
                    <div className="text-xs font-bold text-amber-300 mb-4">
                      {formatCountdown(routePreview.scheduledFor, nowTick)}
                    </div>
                    {routePreview.hostId === user.id ? (
                      <button
                        onClick={() => handleStartScheduled(routePreview.id, routePreview.title, routePreview.hostId)}
                        className="btn btn-gold w-full justify-center"
                      >
                        <Radio size={16} /> Tangira ubu
                      </button>
                    ) : (
                      <p className="text-sm text-neutral-400">
                        Iri somo rizatangira ku itariki hejuru. Garuka kuri iyi paji icyo gihe kugira ngo winjire.
                      </p>
                    )}
                  </>
                )}

                {routePreview.status === "ENDED" && (
                  <p className="text-sm text-neutral-400">Iyi somo ryarangiye.</p>
                )}

                <button
                  onClick={() => handleCopyLink(routePreview.id)}
                  className="btn btn-outline !border-neutral-700 !text-neutral-200 w-full justify-center mt-3 !py-2 text-xs"
                >
                  {copiedId === routePreview.id ? <Check size={14} /> : <Copy size={14} />}
                  {copiedId === routePreview.id ? "Byakoporowe!" : "Koporora link"}
                </button>
              </div>
            )}
            <a href="/live-class" className="block text-center text-gold-400 text-sm font-bold mt-6">
              ← Reba andi masomo
            </a>
          </div>
        </div>
      );
    }

    return (
      <div className="min-h-screen bg-neutral-950 py-16">
        <div className="max-w-[760px] mx-auto px-6">
          <div className="eyebrow !text-gold-400">KWEGEREZA LIVE CLASS</div>
          <h1 className="font-display text-[32px] font-bold text-white mb-2">Amasomo ya Live</h1>
          <p className="text-neutral-400 text-sm mb-8">
            Injira mu isomo riri live ubu, cyangwa utangire irindi niba ufite uburenganzira — ubu cyangwa ku itariki
            uzahitamo.
          </p>

          {error && (
            <div className="bg-red-500/10 border border-red-500/30 text-red-300 text-sm rounded-xl px-4 py-3 mb-5">
              {error}
            </div>
          )}

          {scheduleSuccess && (
            <div className="bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm rounded-xl px-4 py-3 mb-5 flex items-center justify-between gap-3">
              <span>
                "{scheduleSuccess.title}" ryateganyijwe ku {formatDateTime(scheduleSuccess.scheduledFor)}.
              </span>
              <button
                onClick={() => handleCopyLink(scheduleSuccess.id)}
                className="flex-none text-xs font-bold bg-emerald-500/20 px-3 py-1.5 rounded-lg flex items-center gap-1"
              >
                {copiedId === scheduleSuccess.id ? <Check size={12} /> : <Copy size={12} />} Koporora link
              </button>
            </div>
          )}

          {canHost && (
            <form onSubmit={handleCreate} className="bg-neutral-900 border border-neutral-800 rounded-2xl p-5 mb-8">
              <input
                value={newTitle}
                onChange={(e) => setNewTitle(e.target.value)}
                placeholder="Umutwe w'isomo (urugero: Aqida — Ustadh Ahmad)"
                className="w-full rounded-xl border border-neutral-700 bg-neutral-950 text-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-gold-400 mb-3"
              />
              <div className="flex gap-2 mb-3">
                <button
                  type="button"
                  onClick={() => setScheduleMode("now")}
                  className={`flex-1 text-xs font-bold py-2 rounded-lg ${
                    scheduleMode === "now" ? "bg-gold-500 text-neutral-950" : "bg-neutral-800 text-neutral-300"
                  }`}
                >
                  Tangira ubu
                </button>
                <button
                  type="button"
                  onClick={() => {
                    setScheduleMode("schedule");
                    if (!scheduledForInput) {
                      setScheduledForInput(toLocalInputValue(new Date(Date.now() + 30 * 60000)));
                    }
                  }}
                  className={`flex-1 text-xs font-bold py-2 rounded-lg ${
                    scheduleMode === "schedule" ? "bg-gold-500 text-neutral-950" : "bg-neutral-800 text-neutral-300"
                  }`}
                >
                  Teganya igihe (nyuma y'amasaha/iminsi)
                </button>
              </div>
              {scheduleMode === "schedule" && (
                <input
                  type="datetime-local"
                  value={scheduledForInput}
                  min={toLocalInputValue(new Date(Date.now() + 5 * 60000))}
                  onChange={(e) => setScheduledForInput(e.target.value)}
                  className="w-full rounded-xl border border-neutral-700 bg-neutral-950 text-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-gold-400 mb-3"
                />
              )}
              <button type="submit" className="btn btn-gold w-full justify-center">
                <Radio size={16} /> {scheduleMode === "now" ? "Tangira Isomo" : "Teganya Isomo"}
              </button>
            </form>
          )}

          <h2 className="text-xs font-bold text-neutral-500 uppercase tracking-wide mb-2">Ariho ubu</h2>
          <div className="bg-neutral-900 border border-neutral-800 rounded-2xl overflow-hidden mb-8">
            {activeClasses.length === 0 ? (
              <div className="p-8 text-center text-neutral-500 text-sm">Nta somo riri live ubu.</div>
            ) : (
              activeClasses.map((c) => (
                <div key={c.id} className="flex items-center justify-between px-5 py-4 border-b border-neutral-800 last:border-0">
                  <div>
                    <div className="font-semibold text-white flex items-center gap-2">
                      <span className="w-2 h-2 rounded-full bg-red-500 animate-pulse" /> {c.title}
                      {c.locked && <Lock size={12} className="text-neutral-500" />}
                    </div>
                    <div className="text-xs text-neutral-500">{c.hostName}</div>
                  </div>
                  <div className="flex items-center gap-2">
                    <button
                      onClick={() => handleCopyLink(c.id)}
                      className="w-9 h-9 rounded-full border border-neutral-700 flex items-center justify-center text-neutral-300"
                      title="Koporora link"
                    >
                      {copiedId === c.id ? <Check size={14} /> : <Copy size={14} />}
                    </button>
                    <button onClick={() => handleJoin(c.id, c.title, c.hostId)} className="btn btn-gold !py-2 !px-4 text-xs">
                      Injira
                    </button>
                  </div>
                </div>
              ))
            )}
          </div>

          <h2 className="text-xs font-bold text-neutral-500 uppercase tracking-wide mb-2">Ateganyijwe</h2>
          <div className="bg-neutral-900 border border-neutral-800 rounded-2xl overflow-hidden">
            {upcomingClasses.length === 0 ? (
              <div className="p-8 text-center text-neutral-500 text-sm">Nta somo riteganyijwe ubu.</div>
            ) : (
              upcomingClasses.map((c) => (
                <div key={c.id} className="flex items-center justify-between px-5 py-4 border-b border-neutral-800 last:border-0">
                  <div>
                    <div className="font-semibold text-white">{c.title}</div>
                    <div className="text-xs text-neutral-500 flex items-center gap-1.5">
                      <Calendar size={11} /> {formatDateTime(c.scheduledFor)} · {c.hostName}
                    </div>
                    <div className="text-[11px] font-bold text-amber-300 mt-0.5">
                      {formatCountdown(c.scheduledFor, nowTick)}
                    </div>
                  </div>
                  <div className="flex items-center gap-2">
                    <button
                      onClick={() => handleCopyLink(c.id)}
                      className="w-9 h-9 rounded-full border border-neutral-700 flex items-center justify-center text-neutral-300"
                      title="Koporora link"
                    >
                      {copiedId === c.id ? <Check size={14} /> : <Copy size={14} />}
                    </button>
                    {c.hostId === user.id && (
                      <button
                        onClick={() => handleStartScheduled(c.id, c.title, c.hostId)}
                        className="btn btn-gold !py-2 !px-4 text-xs"
                      >
                        Tangira ubu
                      </button>
                    )}
                  </div>
                </div>
              ))
            )}
          </div>
        </div>
      </div>
    );
  }

  // ================================ IN A CLASS =================================
  const connectionInfo = CONNECTION_LABEL[rt.connectionState] ?? CONNECTION_LABEL.connecting;

  return (
    <div className="min-h-screen bg-neutral-950 py-10">
      <div className="max-w-[960px] mx-auto px-6">
        {banner && (
          <div className="bg-amber-400/10 border border-amber-400/30 text-amber-200 text-sm rounded-xl px-4 py-3 mb-4 flex items-center justify-between gap-3">
            <span>{banner}</span>
            <button onClick={() => setBanner("")} className="flex-none text-amber-300">
              <X size={14} />
            </button>
          </div>
        )}
        {error && (
          <div className="bg-red-500/10 border border-red-500/30 text-red-300 text-sm rounded-xl px-4 py-3 mb-4">
            {error}
          </div>
        )}

        <div className="flex items-center justify-between mb-4 gap-3 flex-wrap">
          <div>
            <div className="flex flex-wrap items-center gap-2 text-xs font-bold text-red-400 mb-1.5">
              <span className="w-2 h-2 rounded-full bg-red-500 animate-pulse" /> LIVE
              <span className={`px-2 py-0.5 rounded-full font-bold flex items-center gap-1 ${connectionInfo.tone}`}>
                {rt.connectionState === "disconnected" ? <WifiOff size={11} /> : <Wifi size={11} />}
                {connectionInfo.text}
              </span>
              {locked && (
                <span className="px-2 py-0.5 rounded-full font-bold bg-neutral-800 text-neutral-300 flex items-center gap-1">
                  <Lock size={11} /> Ryafunzwe
                </span>
              )}
              <span className="px-2 py-0.5 rounded-full font-bold bg-neutral-800 text-neutral-300 flex items-center gap-1">
                <Users size={11} /> {participants.length} bahari
              </span>
              {raisedCount > 0 && (
                <span className="px-2 py-0.5 rounded-full font-bold bg-gold-500/15 text-gold-300 flex items-center gap-1">
                  <Hand size={11} className="animate-hand-wave" /> {raisedCount} barashaka kuvuga
                </span>
              )}
              <span className="px-2 py-0.5 rounded-full font-bold bg-neutral-800 text-neutral-400 flex items-center gap-1">
                <MicOff size={11} /> {mutedCount} bacecetse
              </span>
            </div>
            <h1 className="font-display text-2xl font-bold text-white">{currentClass.title}</h1>
          </div>
          <div className="flex items-center gap-2 flex-none">
            <button
              onClick={() => handleCopyLink(currentClass.id)}
              className="btn btn-outline !border-neutral-700 !text-neutral-200 !py-2"
              title="Koporora link y'iri somo"
            >
              {copiedId === currentClass.id ? <Check size={16} /> : <Copy size={16} />}
            </button>
            <button
              onClick={() => {
                setChatOpen((o) => !o);
                setUnreadChat(0);
              }}
              className="btn btn-outline !border-neutral-700 !text-neutral-200 relative"
            >
              <MessageSquare size={16} /> Ubutumwa
              {unreadChat > 0 && (
                <span className="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
                  {unreadChat > 9 ? "9+" : unreadChat}
                </span>
              )}
            </button>
            {isHost ? (
              <button onClick={handleEndClass} className="btn !bg-red-600 !text-white">
                <PhoneOff size={16} /> Rangiza Isomo
              </button>
            ) : (
              <button onClick={() => leaveClassLocally(null)} className="btn btn-outline !border-neutral-700 !text-neutral-300">
                <PhoneOff size={16} /> Sohoka
              </button>
            )}
          </div>
        </div>

        {/* host controls row */}
        {isHost && (
          <div className="bg-neutral-900 border border-neutral-800 rounded-2xl flex flex-wrap items-center gap-2 p-3 mb-5">
            <button
              onClick={handleToggleLock}
              className={`btn !py-2 text-xs ${locked ? "!bg-neutral-700 !text-white" : "btn-outline !border-neutral-700 !text-neutral-200"}`}
            >
              {locked ? <Unlock size={14} /> : <Lock size={14} />} {locked ? "Fungura isomo" : "Funga isomo"}
            </button>
            <button
              onClick={handleToggleHostCamera}
              className={`btn !py-2 text-xs ${hostCameraOn ? "!bg-emerald-500/20 !text-emerald-300" : "btn-outline !border-neutral-700 !text-neutral-200"}`}
            >
              {hostCameraOn ? <Video size={14} /> : <VideoOff size={14} />} {hostCameraOn ? "Kamera irakora" : "Fungura kamera"}
            </button>
            <button
              onClick={handleToggleScreenShare}
              className={`btn !py-2 text-xs ${screenShareOn ? "!bg-emerald-500/20 !text-emerald-300" : "btn-outline !border-neutral-700 !text-neutral-200"}`}
            >
              {screenShareOn ? <MonitorX size={14} /> : <MonitorUp size={14} />}
              {screenShareOn ? "Hagarika gusangira ecran" : "Sangira ecran"}
            </button>
            <button
              onClick={() => rt.disableAllCameras(currentClass.id).catch((e) => setError(e.message))}
              className="btn btn-outline !border-neutral-700 !text-neutral-200 !py-2 text-xs"
            >
              <VideoOff size={14} /> Zimya kamera za bose
            </button>
            <button
              onClick={() => rt.lowerAllHands(currentClass.id).catch((e) => setError(e.message))}
              className="btn btn-outline !border-neutral-700 !text-neutral-200 !py-2 text-xs"
            >
              <Hand size={14} /> Manura amaboko yose
            </button>
          </div>
        )}

        {/* video tiles */}
        {remoteVideos.length > 0 && (
          <div className="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-5">
            {remoteVideos.map(({ key, stream, label }) => (
              <div key={key} className="rounded-xl overflow-hidden bg-neutral-900 border border-neutral-800 relative aspect-video">
                <video
                  autoPlay
                  playsInline
                  muted
                  ref={(el) => {
                    if (el && el.srcObject !== stream) el.srcObject = stream;
                  }}
                  className="w-full h-full object-cover"
                />
                <span className="absolute bottom-1.5 left-1.5 text-[10px] font-bold text-white bg-black/60 px-2 py-0.5 rounded-full">
                  {label}
                </span>
              </div>
            ))}
          </div>
        )}

        {/* my own controls */}
        <div className="bg-neutral-900 border border-neutral-800 rounded-2xl flex items-center justify-between p-4 mb-5 gap-3 flex-wrap">
          <div className="flex items-center gap-3">
            <div
              className={`w-10 h-10 rounded-full bg-gradient-to-br from-gold-500 to-gold-600 text-neutral-950 flex items-center justify-center font-display font-bold ${
                amSpeaking ? "ring-4 ring-emerald-400" : ""
              }`}
            >
              {user.fullName[0]}
            </div>
            <div>
              <div className="font-semibold text-white text-sm">{user.fullName} (wowe)</div>
              <div className="text-xs text-neutral-400">
                {amSpeaking ? "Ndavuga ubu" : MIC_LABEL[self?.micState] ?? "Yacecetse"}
              </div>
            </div>
          </div>

          <div className="flex items-center gap-2 flex-wrap">
            <button
              onClick={handleToggleMyCamera}
              className={`btn !py-2 text-xs ${myCameraOn ? "!bg-emerald-500/20 !text-emerald-300" : "btn-outline !border-neutral-700 !text-neutral-200"}`}
            >
              {myCameraOn ? <Video size={14} /> : <VideoOff size={14} />}
            </button>

            {isHost ? (
              <button
                onClick={handleHostEnableMic}
                disabled={hostMicOn}
                className={`btn ${hostMicOn ? "!bg-emerald-500/20 !text-emerald-300" : "btn-gold"} !py-2`}
              >
                {hostMicOn ? <Mic size={16} /> : <MicOff size={16} />}
                {hostMicOn ? "Mikoro Irakora" : "Fungura Mikoro"}
              </button>
            ) : self?.micState === "APPROVED" ? (
              <button
                onClick={handleEnableMyMic}
                disabled={myMicOn}
                className={`btn ${myMicOn ? "!bg-emerald-500/20 !text-emerald-300" : "btn-gold"} !py-2`}
              >
                {myMicOn ? <Mic size={16} /> : <MicOff size={16} />}
                {myMicOn ? "Mikoro Irakora" : "Fungura Mikoro"}
              </button>
            ) : self?.handRaised ? (
              <button
                onClick={() => rt.lowerHand(currentClass.id)}
                className="btn !bg-gold-500/15 !text-gold-300 !py-2"
              >
                <Hand size={16} className="animate-hand-wave" /> Manura ukuboko
              </button>
            ) : (
              <button
                onClick={() => rt.raiseHand(currentClass.id)}
                className="btn btn-outline !border-neutral-700 !text-neutral-200 !py-2"
              >
                <Hand size={16} /> Zamura ukuboko
              </button>
            )}
          </div>
        </div>

        {/* participants — everybody online, by name */}
        <div className="bg-neutral-900 border border-neutral-800 rounded-2xl overflow-hidden">
          <div className="px-5 py-3 border-b border-neutral-800 flex items-center gap-2 text-xs font-bold text-neutral-400 uppercase tracking-wide">
            <Users size={14} /> Abari mu ishuri ({participants.length})
          </div>
          {isHost && (
            <div className="px-5 py-2.5 border-b border-neutral-800">
              <button
                onClick={() => rt.muteAll(currentClass.id)}
                className="text-xs font-bold text-neutral-400 flex items-center gap-1.5"
              >
                <Volume2 size={13} /> Ucecekeshe bose
              </button>
            </div>
          )}
          {participants
            .filter((p) => p.userId !== user.id)
            .map((p) => (
              <div key={p.userId} className="flex items-center justify-between px-5 py-3 border-b border-neutral-800 last:border-0">
                <div className="flex items-center gap-3">
                  <div
                    className={`w-9 h-9 rounded-full bg-gradient-to-br from-gold-500 to-gold-600 text-neutral-950 flex items-center justify-center font-display font-bold text-sm ${
                      speakingUserIds.has(p.userId) ? "ring-4 ring-emerald-400" : ""
                    }`}
                  >
                    {p.fullName[0]}
                  </div>
                  <div>
                    <div className="font-semibold text-sm text-white flex items-center gap-2">
                      {p.fullName}
                      {p.handRaised && (
                        <span className="text-[10px] font-bold bg-gold-500/15 text-gold-300 px-2 py-0.5 rounded-full flex items-center gap-1">
                          <Hand size={10} className="animate-hand-wave" /> arashaka kuvuga
                        </span>
                      )}
                    </div>
                    <div className="text-xs text-neutral-500">
                      {speakingUserIds.has(p.userId) ? "Ariko avuga" : MIC_LABEL[p.micState] ?? p.micState}
                    </div>
                  </div>
                </div>
                {isHost && (
                  <div className="flex gap-2 flex-wrap justify-end">
                    {["LEADER", "ADMIN"].includes(p.accountRole) && (
                      <button
                        onClick={() => handleToggleModerator(p.userId)}
                        title="Ongera/Kuraho ubushobozi bwo kuyobora ikiganiro"
                        className={`text-xs font-bold px-3 py-1.5 rounded-lg flex items-center gap-1 ${
                          grantedModeratorIds.has(p.userId)
                            ? "bg-gold-500/20 text-gold-300"
                            : "bg-neutral-800 text-neutral-300"
                        }`}
                      >
                        <ShieldPlus size={12} /> Umuyobozi w'ikiganiro
                      </button>
                    )}
                    {p.handRaised ? (
                      <>
                        <button
                          onClick={() => rt.approveSpeaker(currentClass.id, p.userId)}
                          className="text-xs font-bold bg-gold-500 text-neutral-950 px-3 py-1.5 rounded-lg flex items-center gap-1"
                        >
                          <ShieldCheck size={12} /> Emerera
                        </button>
                        <button
                          onClick={() => rt.rejectSpeaker(currentClass.id, p.userId)}
                          className="text-xs font-bold bg-neutral-800 text-neutral-300 px-3 py-1.5 rounded-lg flex items-center gap-1"
                        >
                          <X size={12} /> Anga
                        </button>
                      </>
                    ) : p.micState === "MUTED" ? (
                      <button
                        onClick={() => rt.approveSpeaker(currentClass.id, p.userId)}
                        className="text-xs font-bold bg-gold-500 text-neutral-950 px-3 py-1.5 rounded-lg flex items-center gap-1"
                      >
                        <ShieldCheck size={12} /> Emerera
                      </button>
                    ) : (
                      <button
                        onClick={() => rt.revokeSpeaker(currentClass.id, p.userId)}
                        className="text-xs font-bold bg-neutral-800 text-neutral-300 px-3 py-1.5 rounded-lg flex items-center gap-1"
                      >
                        <MicOff size={12} /> Hagarika
                      </button>
                    )}
                    <button
                      onClick={() => rt.removeParticipant(currentClass.id, p.userId)}
                      className="text-xs font-bold bg-neutral-800 text-red-400 px-3 py-1.5 rounded-lg flex items-center gap-1"
                    >
                      <UserMinus size={12} /> Kura
                    </button>
                  </div>
                )}
              </div>
            ))}
        </div>

        {/* classroom chat panel */}
        {chatOpen && (
          <div className="bg-neutral-900 border border-neutral-800 rounded-2xl overflow-hidden mt-5 flex flex-col h-[380px]">
            <div className="px-4 py-3 border-b border-neutral-800 flex items-center justify-between">
              <div className="font-bold text-sm text-white">Ubutumwa bw'ishuri</div>
              <div className="flex items-center gap-2">
                <span className="text-[10px] font-bold text-neutral-400 flex items-center gap-1">
                  {namesRevealed ? <Eye size={11} /> : <EyeOff size={11} />}
                  {namesRevealed ? "Amazina aragaragara" : "Amazina arahishe"}
                </span>
                {canModerateChat && (
                  <button
                    onClick={handleToggleRevealNames}
                    className="text-[10px] font-bold bg-neutral-800 text-neutral-200 px-2.5 py-1 rounded-full"
                  >
                    {namesRevealed ? "Hisha amazina" : "Erekana amazina"}
                  </button>
                )}
              </div>
            </div>
            <div className="flex-1 overflow-y-auto px-4 py-3 space-y-2">
              {chatMessages.length === 0 ? (
                <div className="text-center text-neutral-500 text-xs py-6">Nta butumwa buriho ubu.</div>
              ) : (
                chatMessages.map((m) => (
                  <div key={m.id} className={`flex ${m.userId === user.id ? "justify-end" : "justify-start"}`}>
                    <div
                      className={`max-w-[75%] rounded-xl px-3 py-2 text-xs ${
                        m.userId === user.id ? "bg-gold-500 text-neutral-950" : "bg-neutral-800 text-neutral-100"
                      }`}
                    >
                      {m.userId !== user.id && (
                        <div className="font-bold mb-0.5 flex items-center gap-1">
                          {m.fullName} {m.role === "HOST" && "· Umuyobozi"}
                          {m.anonymous && <EyeOff size={9} className="opacity-60" />}
                        </div>
                      )}
                      {m.body}
                    </div>
                  </div>
                ))
              )}
            </div>
            <form onSubmit={handleSendChat} className="p-3 border-t border-neutral-800 flex gap-2">
              <input
                value={chatInput}
                onChange={(e) => setChatInput(e.target.value)}
                placeholder="Andika ubutumwa..."
                className="flex-1 rounded-full border border-neutral-700 bg-neutral-950 text-white px-4 py-2 text-xs outline-none focus:ring-2 focus:ring-gold-400"
              />
              <button type="submit" className="btn btn-gold !py-2 !px-4 text-xs">
                Ohereza
              </button>
            </form>
          </div>
        )}

        {/* remote audio elements — invisible, just plays sound */}
        {remoteAudios.map(({ key, stream }) => (
          <audio
            key={key}
            autoPlay
            ref={(el) => {
              if (el && el.srcObject !== stream) el.srcObject = stream;
            }}
          />
        ))}

        <p className="text-xs text-neutral-600 mt-4">
          Icyitonderwa: iyi si simulation — ikoresha WebRTC nyayo binyuze muri mikoro/kamera yawe nyayo. Niba
          udashaka guhuza mikoro cyangwa kamera, ntugomba kubikora — nta na rimwe ibikoresho byawe bifungurwa
          utabizi. Ubutumwa bw'ishuri ntibwerekana amazina keretse umuyobozi abyemeje.
        </p>
      </div>
    </div>
  );
}
