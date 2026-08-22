import { useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { Bell, X } from "lucide-react";
import { useNotifications } from "../context/NotificationContext.jsx";
import { playNotificationSound } from "../utils/notificationSound.js";

export default function TopBanner() {
  const { banner, dismissBanner, markRead } = useNotifications();
  const navigate = useNavigate();

  useEffect(() => {
    if (!banner) return undefined;
    playNotificationSound();
    const t = setTimeout(dismissBanner, 7000); // auto-dismiss, never spam-stacks
    return () => clearTimeout(t);
  }, [banner, dismissBanner]);

  if (!banner) return null;

  function handleOpen() {
    markRead(banner.id);
    dismissBanner();
    if (banner.url) navigate(banner.url);
  }

  return (
    <div className="fixed top-4 left-1/2 -translate-x-1/2 z-[100] w-[92%] max-w-[420px] animate-[slideDown_0.25s_ease-out]">
      <div className="bg-green-950 text-white rounded-2xl shadow-[0_20px_45px_-15px_rgba(8,37,28,0.5)] px-4 py-3.5 flex items-start gap-3">
        <div className="w-9 h-9 rounded-full bg-gold-500/20 text-gold-400 flex items-center justify-center flex-none">
          <Bell size={16} />
        </div>
        <button onClick={handleOpen} className="flex-1 text-left">
          <div className="font-semibold text-sm">{banner.title}</div>
          <div className="text-xs text-white/70 mt-0.5 line-clamp-2">{banner.body}</div>
          {banner.url && <div className="text-xs text-gold-400 font-bold mt-1.5">Fungura →</div>}
        </button>
        <button onClick={dismissBanner} className="text-white/50 hover:text-white flex-none">
          <X size={16} />
        </button>
      </div>
      <style>{`
        @keyframes slideDown {
          from { transform: translate(-50%, -20px); opacity: 0; }
          to { transform: translate(-50%, 0); opacity: 1; }
        }
      `}</style>
    </div>
  );
}
