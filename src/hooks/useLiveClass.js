import { useEffect, useRef, useState, useCallback } from "react";
import { io } from "socket.io-client";

const SOCKET_URL = (import.meta.env.VITE_API_URL ?? "http://localhost:4000/api").replace(/\/api$/, "");

/**
 * Centralized realtime hook for the live classroom — the counterpart to
 * useChatSocket(). Same discipline: every socket.on() has a matching
 * socket.off() in cleanup, so leaving a classroom (unmount, navigation,
 * StrictMode remount) never leaves a stale listener reacting to events for
 * a class the user isn't in anymore.
 */
export function useLiveClass(token) {
  const socketRef = useRef(null);
  const [connected, setConnected] = useState(false);
  // Real connection-state machine for the "Connecting / Reconnecting /
  // Disconnected" states the spec asks for — separate from `connected`
  // (which callers already depend on as a plain boolean) so nothing that
  // reads `connected` needs to change.
  const [connectionState, setConnectionState] = useState("connecting");

  useEffect(() => {
    if (!token) return undefined;
    const socket = io(SOCKET_URL, {
      auth: { token },
      transports: ["websocket"],
      reconnection: true,
      reconnectionAttempts: Infinity,
      reconnectionDelay: 1000,
      reconnectionDelayMax: 5000,
    });
    socketRef.current = socket;
    setConnectionState("connecting");

    function onConnect() {
      setConnected(true);
      setConnectionState("connected");
    }
    function onDisconnect() {
      setConnected(false);
      setConnectionState("reconnecting"); // socket.io will auto-retry unless we called .disconnect() ourselves
    }
    function onReconnectAttempt() {
      setConnectionState("reconnecting");
    }
    function onReconnectFailed() {
      setConnectionState("disconnected");
    }
    socket.on("connect", onConnect);
    socket.on("disconnect", onDisconnect);
    socket.io.on("reconnect_attempt", onReconnectAttempt);
    socket.io.on("reconnect_failed", onReconnectFailed);

    return () => {
      socket.off("connect", onConnect);
      socket.off("disconnect", onDisconnect);
      socket.io.off("reconnect_attempt", onReconnectAttempt);
      socket.io.off("reconnect_failed", onReconnectFailed);
      socket.disconnect();
      socketRef.current = null;
    };
  }, [token]);

  function withAck(event) {
    return (payload) =>
      new Promise((resolve, reject) => {
        if (!socketRef.current) return reject(new Error("Socket not connected"));
        socketRef.current.emit(event, payload, (ack) => {
          if (ack?.ok) resolve(ack);
          else reject(new Error(ack?.error || `${event} failed`));
        });
      });
  }

  const joinClass = useCallback((liveClassId) => withAck("classroom:join")({ liveClassId }), []);
  const raiseHand = useCallback((liveClassId) => socketRef.current?.emit("classroom:raise-hand", { liveClassId }), []);
  const lowerHand = useCallback((liveClassId) => socketRef.current?.emit("classroom:lower-hand", { liveClassId }), []);
  const approveSpeaker = useCallback(
    (liveClassId, targetUserId) => withAck("classroom:approve-speaker")({ liveClassId, targetUserId }),
    []
  );
  const revokeSpeaker = useCallback(
    (liveClassId, targetUserId) => withAck("classroom:revoke-speaker")({ liveClassId, targetUserId }),
    []
  );
  const lowerAllHands = useCallback((liveClassId) => withAck("classroom:lower-all-hands")({ liveClassId }), []);
  const muteAll = useCallback((liveClassId) => withAck("classroom:mute-all")({ liveClassId }), []);
  const removeParticipant = useCallback(
    (liveClassId, targetUserId) => withAck("classroom:remove-participant")({ liveClassId, targetUserId }),
    []
  );
  const rejectSpeaker = useCallback(
    (liveClassId, targetUserId) => withAck("classroom:reject-speaker")({ liveClassId, targetUserId }),
    []
  );
  const disableAllCameras = useCallback(
    (liveClassId) => withAck("classroom:disable-all-cameras")({ liveClassId }),
    []
  );
  const lockClass = useCallback((liveClassId) => withAck("classroom:lock")({ liveClassId }), []);
  const unlockClass = useCallback((liveClassId) => withAck("classroom:unlock")({ liveClassId }), []);
  const endClass = useCallback((liveClassId) => withAck("classroom:end")({ liveClassId }), []);
  const sendChatMessage = useCallback(
    (liveClassId, body) => socketRef.current?.emit("classroom:chat-message", { liveClassId, body }),
    []
  );
  const revealNames = useCallback((liveClassId) => withAck("classroom:reveal-names")({ liveClassId }), []);
  const hideNames = useCallback((liveClassId) => withAck("classroom:hide-names")({ liveClassId }), []);
  const grantChatModerator = useCallback(
    (liveClassId, targetUserId) => withAck("classroom:grant-chat-moderator")({ liveClassId, targetUserId }),
    []
  );
  const revokeChatModerator = useCallback(
    (liveClassId, targetUserId) => withAck("classroom:revoke-chat-moderator")({ liveClassId, targetUserId }),
    []
  );

  function on(event) {
    return (handler) => {
      const socket = socketRef.current;
      if (!socket) return () => {};
      socket.on(event, handler);
      return () => socket.off(event, handler);
    };
  }

  const sendSignal = useCallback((event, liveClassId, toUserId, extra) => {
    socketRef.current?.emit(event, { liveClassId, toUserId, ...extra });
  }, []);

  return {
    connected,
    connectionState,
    joinClass,
    raiseHand,
    lowerHand,
    approveSpeaker,
    revokeSpeaker,
    rejectSpeaker,
    muteAll,
    lowerAllHands,
    removeParticipant,
    disableAllCameras,
    lockClass,
    unlockClass,
    endClass,
    sendChatMessage,
    revealNames,
    hideNames,
    grantChatModerator,
    revokeChatModerator,
    onParticipantJoined: on("classroom:participant-joined"),
    onParticipantUpdated: on("classroom:participant-updated"),
    onParticipantLeft: on("classroom:participant-left"),
    onHandRaised: on("classroom:hand-raised"),
    onSpeakerApproved: on("classroom:speaker-approved"),
    onSpeakerRevoked: on("classroom:speaker-revoked"),
    onSpeakerRejected: on("classroom:speaker-rejected"),
    onMutedAll: on("classroom:muted-all"),
    onParticipantsBulkUpdate: on("classroom:participants"),
    onCamerasDisabled: on("classroom:cameras-disabled"),
    onRemoved: on("classroom:removed"),
    onClassEnded: on("classroom:ended"),
    onHostDisconnected: on("classroom:host-disconnected"),
    onLocked: on("classroom:locked"),
    onUnlocked: on("classroom:unlocked"),
    onChatMessage: on("classroom:chat-message"),
    onNamesRevealed: on("classroom:names-revealed"),
    onNamesHidden: on("classroom:names-hidden"),
    onChatModeratorGranted: on("classroom:chat-moderator-granted"),
    onChatModeratorRevoked: on("classroom:chat-moderator-revoked"),
    onOffer: on("webrtc:offer"),
    onAnswer: on("webrtc:answer"),
    onIceCandidate: on("webrtc:ice-candidate"),
    sendOffer: (liveClassId, toUserId, sdp, kind) => sendSignal("webrtc:offer", liveClassId, toUserId, { sdp, kind }),
    sendAnswer: (liveClassId, toUserId, sdp, kind) => sendSignal("webrtc:answer", liveClassId, toUserId, { sdp, kind }),
    sendIceCandidate: (liveClassId, toUserId, candidate, kind) =>
      sendSignal("webrtc:ice-candidate", liveClassId, toUserId, { candidate, kind }),
  };
}
