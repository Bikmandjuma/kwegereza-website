import { useCallback, useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import {
  BellOff,
  Check,
  ChevronLeft,
  ChevronRight,
  MessageCircle,
  Radio,
  Settings2,
  Volume2,
  VolumeX,
} from "lucide-react";
import {
  getNotificationHistory,
  getNotificationPreferences,
  markAllNotificationsRead,
  markNotificationRead,
  updateNotificationPreference,
} from "../api/notifications.js";
import { useNotifications } from "../context/NotificationContext.jsx";
import { isNotificationSoundEnabled, setNotificationSoundEnabled } from "../utils/notificationSound.js";

// Every configurable category from the spec, in the order it lists them.
// ACCOUNT and SYSTEM are shown as filter tabs (so users can find those
// notifications) but never appear in the preferences panel below — they
// can't be turned off.
const CATEGORY_TABS = [
  { key: "", label: "Byose" },
  { key: "CHAT", label: "Chat" },
  { key: "DARS", label: "Dars" },
  { key: "IFAIDA", label: "Ifaida" },
  { key: "LIVE_CLASS", label: "Live Class" },
  { key: "BOOKS", label: "Ibitabo" },
  { key: "EXAMS", label: "Ibizamini" },
  { key: "ANNOUNCEMENTS", label: "Amatangazo" },
  { key: "ACCOUNT", label: "Konti" },
  { key: "SYSTEM", label: "Sisitemu" },
];

const CONFIGURABLE_CATEGORIES = [
  { key: "CHAT", label: "Chat" },
  { key: "DARS", label: "Dars" },
  { key: "IFAIDA", label: "Ifaida" },
  { key: "LIVE_CLASS", label: "Amasomo ya Live" },
  { key: "BOOKS", label: "Ibitabo" },
  { key: "EXAMS", label: "Ibizamini" },
  { key: "ANNOUNCEMENTS", label: "Amatangazo" },
];

function timeAgo(iso) {
  const diffMs = Date.now() - new Date(iso).getTime();
  const mins = Math.floor(diffMs / 60000);
  if (mins < 1) return "ubu";
  if (mins < 60) return `${mins}min`;
  const hrs = Math.floor(mins / 60);
  if (hrs < 24) return `${hrs}h`;
  return `${Math.floor(hrs / 24)}d`;
}

export default function NotificationCenterPage() {
  const navigate = useNavigate();
  const { refresh: refreshBell } = useNotifications();

  const [category, setCategory] = useState("");
  const [items, setItems] = useState([]);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [unreadCount, setUnreadCount] = useState(0);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  const [showPreferences, setShowPreferences] = useState(false);
  const [preferences, setPreferences] = useState(null); // { CATEGORY: {enabled, push} }
  const [prefsLoading, setPrefsLoading] = useState(false);
  const [soundOn, setSoundOn] = useState(isNotificationSoundEnabled());

  const load = useCallback(async () => {
    setLoading(true);
    setError("");
    try {
      const res = await getNotificationHistory({ page, perPage: 15, category });
      setItems(res.data);
      setTotalPages(res.meta.totalPages);
      setUnreadCount(res.meta.unreadCount);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }, [page, category]);

  useEffect(() => {
    load();
  }, [load]);

  useEffect(() => {
    setPage(1);
  }, [category]);

  async function handleOpenPreferences() {
    setShowPreferences(true);
    if (preferences) return;
    setPrefsLoading(true);
    try {
      const res = await getNotificationPreferences();
      setPreferences(res.data.categories);
    } catch (err) {
      setError(err.message);
    } finally {
      setPrefsLoading(false);
    }
  }

  async function handleTogglePreference(catKey, field) {
    const current = preferences[catKey];
    const next = { ...current, [field]: !current[field] };
    setPreferences((prev) => ({ ...prev, [catKey]: next }));
    try {
      await updateNotificationPreference(catKey, next);
    } catch (err) {
      setError(err.message);
      setPreferences((prev) => ({ ...prev, [catKey]: current })); // revert on failure
    }
  }

  function handleToggleSound() {
    const next = !soundOn;
    setNotificationSoundEnabled(next);
    setSoundOn(next);
  }

  async function handleMarkAllRead() {
    setItems((prev) => prev.map((n) => ({ ...n, read: true })));
    setUnreadCount(0);
    try {
      await markAllNotificationsRead();
      refreshBell();
    } catch {
      load();
    }
  }

  async function handleClickItem(n) {
    if (!n.read) {
      setItems((prev) => prev.map((x) => (x.id === n.id ? { ...x, read: true } : x)));
      setUnreadCount((c) => Math.max(0, c - 1));
      try {
        await markNotificationRead(n.id);
        refreshBell();
      } catch {
        load();
      }
    }
    if (n.url) navigate(n.url);
  }

  return (
    <div className="py-10 max-w-[760px] mx-auto px-6">
      <div className="flex items-center justify-between mb-1">
        <div className="eyebrow">🔔 UBUTUMWA</div>
        <button
          onClick={handleOpenPreferences}
          className="btn btn-outline !border-line !text-green-950 !py-2 text-xs"
        >
          <Settings2 size={14} /> Ihitamo
        </button>
      </div>
      <h1 className="font-display text-[28px] font-bold text-green-950 mb-1">Ubutumwa bwawe</h1>
      <p className="text-ink-soft text-sm mb-6">
        {unreadCount > 0 ? `${unreadCount} butarasomwa` : "Byose byasomwe"}
      </p>

      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-5">{error}</div>
      )}

      {/* category tabs */}
      <div className="flex gap-2 overflow-x-auto pb-2 mb-4">
        {CATEGORY_TABS.map((tab) => (
          <button
            key={tab.key || "all"}
            onClick={() => setCategory(tab.key)}
            className={`px-3 py-1.5 rounded-full text-xs font-bold whitespace-nowrap flex-none ${
              category === tab.key ? "bg-green-950 text-white" : "bg-cream-2 text-ink-soft"
            }`}
          >
            {tab.label}
          </button>
        ))}
      </div>

      {unreadCount > 0 && (
        <div className="flex justify-end mb-3">
          <button onClick={handleMarkAllRead} className="text-xs font-bold text-gold-600 flex items-center gap-1">
            <Check size={12} /> Byose byasomwe
          </button>
        </div>
      )}

      {/* history list */}
      <div className="card !p-0 overflow-hidden mb-4">
        {loading ? (
          <div className="p-10 text-center text-ink-soft text-sm">Turimo gupakira...</div>
        ) : items.length === 0 ? (
          <div className="p-10 text-center text-ink-soft text-sm">Nta butumwa buriho muri iyi category.</div>
        ) : (
          items.map((n) => (
            <button
              key={n.id}
              onClick={() => handleClickItem(n)}
              className={`w-full text-left px-5 py-4 border-b border-line last:border-0 hover:bg-cream-2 flex gap-3 ${
                n.read ? "" : "bg-gold-100/40"
              }`}
            >
              <span className={`w-1.5 h-1.5 rounded-full mt-2 flex-none ${n.read ? "bg-transparent" : "bg-gold-500"}`} />
              <span className="flex-1 min-w-0">
                <span className="block text-sm font-semibold text-green-950">{n.title}</span>
                <span className="block text-xs text-ink-soft mt-0.5">{n.body}</span>
              </span>
              <span className="text-[10px] text-ink-soft flex-none">{timeAgo(n.createdAt)}</span>
            </button>
          ))
        )}
      </div>

      {!loading && items.length > 0 && (
        <div className="flex items-center justify-center gap-3 text-xs text-ink-soft">
          <button
            onClick={() => setPage((p) => Math.max(1, p - 1))}
            disabled={page <= 1}
            className="w-8 h-8 rounded-lg border border-line flex items-center justify-center disabled:opacity-40"
          >
            <ChevronLeft size={14} />
          </button>
          <span className="font-semibold">
            Paji {page} / {totalPages}
          </span>
          <button
            onClick={() => setPage((p) => Math.min(totalPages, p + 1))}
            disabled={page >= totalPages}
            className="w-8 h-8 rounded-lg border border-line flex items-center justify-center disabled:opacity-40"
          >
            <ChevronRight size={14} />
          </button>
        </div>
      )}

      {/* preferences modal */}
      {showPreferences && (
        <div className="fixed inset-0 bg-black/40 z-[300] flex items-center justify-center p-4">
          <div className="bg-surface rounded-2xl shadow-xl w-full max-w-[440px] max-h-[85vh] overflow-y-auto">
            <div className="px-5 py-4 border-b border-line flex items-center justify-between">
              <span className="font-bold text-green-950">Ihitamo ry'ubutumwa</span>
              <button onClick={() => setShowPreferences(false)} className="text-ink-soft text-sm font-bold">
                Funga
              </button>
            </div>

            <div className="px-5 py-4 border-b border-line flex items-center justify-between">
              <div className="flex items-center gap-2 text-sm font-semibold text-green-950">
                {soundOn ? <Volume2 size={16} /> : <VolumeX size={16} />} Ijwi ry'ubutumwa
              </div>
              <button
                onClick={handleToggleSound}
                className={`w-11 h-6 rounded-full relative transition-colors ${soundOn ? "bg-green-950" : "bg-cream-2"}`}
              >
                <span
                  className={`absolute top-0.5 w-5 h-5 bg-white rounded-full transition-transform ${
                    soundOn ? "translate-x-[22px]" : "translate-x-0.5"
                  }`}
                />
              </button>
            </div>

            {prefsLoading || !preferences ? (
              <div className="p-8 text-center text-ink-soft text-sm">Turimo gupakira...</div>
            ) : (
              <div className="divide-y divide-line">
                {CONFIGURABLE_CATEGORIES.map((cat) => {
                  const pref = preferences[cat.key] ?? { enabled: true, push: true };
                  return (
                    <div key={cat.key} className="px-5 py-4">
                      <div className="flex items-center justify-between mb-2">
                        <span className="text-sm font-semibold text-green-950">{cat.label}</span>
                        <button
                          onClick={() => handleTogglePreference(cat.key, "enabled")}
                          className={`w-11 h-6 rounded-full relative transition-colors ${
                            pref.enabled ? "bg-green-950" : "bg-cream-2"
                          }`}
                        >
                          <span
                            className={`absolute top-0.5 w-5 h-5 bg-white rounded-full transition-transform ${
                              pref.enabled ? "translate-x-[22px]" : "translate-x-0.5"
                            }`}
                          />
                        </button>
                      </div>
                      {pref.enabled && (
                        <label className="flex items-center gap-2 text-xs text-ink-soft">
                          <input
                            type="checkbox"
                            checked={pref.push}
                            onChange={() => handleTogglePreference(cat.key, "push")}
                            className="w-3.5 h-3.5"
                          />
                          Kohereza kuri push notification (bell + telefone)
                        </label>
                      )}
                    </div>
                  );
                })}
                <div className="px-5 py-4 text-xs text-ink-soft flex items-center gap-2">
                  <BellOff size={13} className="flex-none" />
                  Ubutumwa bwa Konti n'ubwa Sisitemu ntibushobora kuzimwa — ni ingenzi ku mutekano wa konti yawe.
                </div>
              </div>
            )}
          </div>
        </div>
      )}

      <p className="text-xs text-ink-soft mt-6 flex items-center gap-1.5">
        <Radio size={12} /> Amatangazo mashya araboneka ako kanya, kandi <MessageCircle size={12} className="inline" /> ubutumwa bwa chat bwifashisha ubu bumenyi bumwe.
      </p>
    </div>
  );
}
