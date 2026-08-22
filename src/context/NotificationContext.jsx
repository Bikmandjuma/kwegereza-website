import { createContext, useContext, useEffect, useState, useCallback, useRef } from "react";
import { io } from "socket.io-client";
import { getToken } from "../api/client.js";
import { listNotifications, markAllNotificationsRead, markNotificationRead } from "../api/notifications.js";
import { useAuth } from "./AuthContext.jsx";

const SOCKET_URL = (import.meta.env.VITE_API_URL ?? "http://localhost:4000/api").replace(/\/api$/, "");

const NotificationContext = createContext(null);

export function NotificationProvider({ children }) {
  const { user, refreshUser } = useAuth();
  const [notifications, setNotifications] = useState([]);
  const [unreadCount, setUnreadCount] = useState(0);
  const [banner, setBanner] = useState(null); // the top-of-screen sliding banner
  const socketRef = useRef(null);

  const refresh = useCallback(async () => {
    if (!user) return;
    try {
      const res = await listNotifications();
      setNotifications(res.data);
      setUnreadCount(res.meta.unreadCount);
    } catch {
      // Silent — the notification center is a convenience, not core navigation.
    }
  }, [user]);

  useEffect(() => {
    refresh();
  }, [refresh]);

  // Dedicated realtime connection for `notification:new`. Every connected,
  // authenticated socket automatically joins its own `user:{id}` room
  // server-side, so no explicit join call is needed here — just listen.
  useEffect(() => {
    if (!user) return undefined;
    const token = getToken();
    if (!token) return undefined;

    const socket = io(SOCKET_URL, { auth: { token }, transports: ["websocket"] });
    socketRef.current = socket;

    function onNewNotification(notification) {
      setNotifications((prev) => [notification, ...prev].slice(0, 30));
      setUnreadCount((c) => c + 1);
      setBanner(notification);
    }
    socket.on("notification:new", onNewNotification);

    // When an admin changes this user's role/permissions (or blocks them),
    // the backend already enforces the new rule on the very next request —
    // this just makes the browser's own UI (sidebar, buttons) catch up
    // immediately instead of only after a manual refresh or re-login.
    function onAccountUpdated() {
      refreshUser();
    }
    socket.on("account:updated", onAccountUpdated);

    return () => {
      socket.off("notification:new", onNewNotification);
      socket.off("account:updated", onAccountUpdated);
      socket.disconnect();
      socketRef.current = null;
    };
  }, [user, refreshUser]);

  const markRead = useCallback(async (id) => {
    setNotifications((prev) => prev.map((n) => (n.id === id ? { ...n, read: true } : n)));
    setUnreadCount((c) => Math.max(0, c - 1));
    try {
      await markNotificationRead(id);
    } catch {
      refresh(); // reconcile with the server if the optimistic update was wrong
    }
  }, [refresh]);

  const markAllRead = useCallback(async () => {
    setNotifications((prev) => prev.map((n) => ({ ...n, read: true })));
    setUnreadCount(0);
    try {
      await markAllNotificationsRead();
    } catch {
      refresh();
    }
  }, [refresh]);

  const dismissBanner = useCallback(() => setBanner(null), []);

  return (
    <NotificationContext.Provider
      value={{ notifications, unreadCount, banner, markRead, markAllRead, dismissBanner, refresh }}
    >
      {children}
    </NotificationContext.Provider>
  );
}

export function useNotifications() {
  const ctx = useContext(NotificationContext);
  if (!ctx) throw new Error("useNotifications must be used inside <NotificationProvider>");
  return ctx;
}
