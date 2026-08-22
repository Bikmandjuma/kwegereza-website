import { useEffect, useRef, useState, useCallback } from "react";
import { io } from "socket.io-client";

const SOCKET_URL = (import.meta.env.VITE_API_URL ?? "http://localhost:4000/api").replace(/\/api$/, "");

/**
 * Centralized realtime hook for chat. One socket connection per mounted
 * consumer, and — critically — every listener registered here is removed
 * in the effect's cleanup function. This is what prevents the classic bug
 * of duplicate message rendering: a stale listener from a previous
 * mount/navigation/StrictMode double-invoke firing alongside a fresh one.
 */
export function useChatSocket(token) {
  const socketRef = useRef(null);
  const [connected, setConnected] = useState(false);

  useEffect(() => {
    if (!token) return undefined;

    const socket = io(SOCKET_URL, { auth: { token }, transports: ["websocket"] });
    socketRef.current = socket;

    function onConnect() {
      setConnected(true);
    }
    function onDisconnect() {
      setConnected(false);
    }

    socket.on("connect", onConnect);
    socket.on("disconnect", onDisconnect);

    return () => {
      // Explicit teardown — mirrors socket.on() 1:1 with socket.off(), then
      // disconnects. Without this, remounting (or React StrictMode's
      // mount→unmount→mount in dev) would leave a second live connection
      // whose listeners double-fire every future event.
      socket.off("connect", onConnect);
      socket.off("disconnect", onDisconnect);
      socket.disconnect();
      socketRef.current = null;
    };
  }, [token]);

  const joinConversation = useCallback((conversationId) => {
    return new Promise((resolve, reject) => {
      if (!socketRef.current) return reject(new Error("Socket not connected"));
      socketRef.current.emit("conversation:join", { conversationId }, (ack) => {
        if (ack?.ok) resolve(ack);
        else reject(new Error(ack?.error || "Ntibyakunze kwinjira mu kiganiro."));
      });
    });
  }, []);

  const sendMessage = useCallback((conversationId, body) => {
    return new Promise((resolve, reject) => {
      if (!socketRef.current) return reject(new Error("Socket not connected"));
      // Generated ONCE per logical send, client-side. If this exact call gets
      // retried (network hiccup, double click before the button disables),
      // the server's unique constraint guarantees only one message is ever
      // stored — see backend clientMessageId documentation.
      const clientMessageId = crypto.randomUUID();
      socketRef.current.emit(
        "message:send",
        { conversationId, clientMessageId, body },
        (ack) => {
          if (ack?.ok) resolve(ack);
          else reject(new Error(ack?.error || "Ntibyakunze kohereza ubutumwa."));
        }
      );
    });
  }, []);

  const onNewMessage = useCallback((handler) => {
    const socket = socketRef.current;
    if (!socket) return () => {};
    socket.on("message:new", handler);
    return () => socket.off("message:new", handler);
  }, []);

  const onTyping = useCallback((handler) => {
    const socket = socketRef.current;
    if (!socket) return () => {};
    socket.on("typing:update", handler);
    return () => socket.off("typing:update", handler);
  }, []);

  const onPresence = useCallback((handler) => {
    const socket = socketRef.current;
    if (!socket) return () => {};
    socket.on("presence:update", handler);
    return () => socket.off("presence:update", handler);
  }, []);

  const emitTyping = useCallback((conversationId, isTyping) => {
    socketRef.current?.emit(isTyping ? "typing:start" : "typing:stop", { conversationId });
  }, []);

  return { connected, joinConversation, sendMessage, onNewMessage, onTyping, onPresence, emitTyping };
}
