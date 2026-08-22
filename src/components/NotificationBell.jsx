import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { Bell, BellRing, Check } from "lucide-react";
import { useNotifications } from "../context/NotificationContext.jsx";
import { enablePushNotifications, notificationPermission, pushSupported } from "../utils/push.js";

function timeAgo(iso) {
  const diffMs = Date.now() - new Date(iso).getTime();
  const mins = Math.floor(diffMs / 60000);
  if (mins < 1) return "ubu";
  if (mins < 60) return `${mins}min`;
  const hrs = Math.floor(mins / 60);
  if (hrs < 24) return `${hrs}h`;
  return `${Math.floor(hrs / 24)}d`;
}

export default function NotificationBell() {
  const { notifications, unreadCount, markRead, markAllRead } = useNotifications();
  const [open, setOpen] = useState(false);
  const [pushState, setPushState] = useState(notificationPermission());
  const [pushError, setPushError] = useState("");
  const navigate = useNavigate();

  async function handleEnablePush() {
    setPushError("");
    try {
      await enablePushNotifications();
      setPushState("granted");
    } catch (err) {
      setPushError(err.message);
      setPushState(notificationPermission());
    }
  }

  function handleClickNotification(n) {
    if (!n.read) markRead(n.id);
    setOpen(false);
    if (n.url) navigate(n.url);
  }

  return (
    <div className="relative">
      <button
        onClick={() => setOpen((o) => !o)}
        className="relative w-9 h-9 rounded-full bg-cream-2 flex items-center justify-center text-green-950"
      >
        <Bell size={16} />
        {unreadCount > 0 && (
          <span className="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
            {unreadCount > 9 ? "9+" : unreadCount}
          </span>
        )}
      </button>

      {open && (
        <div className="absolute right-0 top-11 w-[340px] bg-surface rounded-2xl shadow-xl border border-line overflow-hidden z-[200]">
          <div className="px-4 py-3 border-b border-line flex items-center justify-between">
            <span className="font-bold text-sm text-green-950">Ubutumwa</span>
            {unreadCount > 0 && (
              <button onClick={markAllRead} className="text-xs font-bold text-gold-600 flex items-center gap-1">
                <Check size={12} /> Byose byasomwe
              </button>
            )}
          </div>

          {pushSupported() && pushState !== "granted" && (
            <button
              onClick={handleEnablePush}
              className="w-full flex items-center gap-2 px-4 py-3 bg-cream-2 text-xs font-semibold text-green-950 border-b border-line"
            >
              <BellRing size={14} className="flex-none" />
              Emeza ubutumwa bwa push kuri iyi terefone/mudasobwa
            </button>
          )}
          {pushError && <div className="px-4 py-2 text-xs text-red-600">{pushError}</div>}

          <div className="max-h-[360px] overflow-y-auto">
            {notifications.length === 0 ? (
              <div className="p-6 text-center text-ink-soft text-sm">Nta butumwa ufite.</div>
            ) : (
              notifications.map((n) => (
                <button
                  key={n.id}
                  onClick={() => handleClickNotification(n)}
                  className={`w-full text-left px-4 py-3 border-b border-line last:border-0 hover:bg-cream-2 flex gap-2 ${
                    n.read ? "" : "bg-gold-100/40"
                  }`}
                >
                  <span className={`w-1.5 h-1.5 rounded-full mt-1.5 flex-none ${n.read ? "bg-transparent" : "bg-gold-500"}`} />
                  <span className="flex-1 min-w-0">
                    <span className="block text-sm font-semibold text-green-950 truncate">{n.title}</span>
                    <span className="block text-xs text-ink-soft truncate">{n.body}</span>
                  </span>
                  <span className="text-[10px] text-ink-soft flex-none">{timeAgo(n.createdAt)}</span>
                </button>
              ))
            )}
          </div>

          <button
            onClick={() => {
              setOpen(false);
              navigate("/notifications");
            }}
            className="w-full text-center text-xs font-bold text-green-950 py-3 border-t border-line hover:bg-cream-2"
          >
            Reba ubutumwa bwose n'ihitamo →
          </button>
        </div>
      )}
    </div>
  );
}
