import { LogIn, LogOut, Eye, MessageCircle, BookOpen, Radio, User } from "lucide-react";

const TYPE_META = {
  LOGIN: { icon: LogIn, label: "yinjiye", color: "text-emerald-600 bg-emerald-50" },
  LOGOUT: { icon: LogOut, label: "yasohotse", color: "text-ink-soft bg-cream-2" },
  PAGE_VIEW: { icon: Eye, label: "yareba urupapuro", color: "text-blue-600 bg-blue-50" },
  CHAT_OPEN: { icon: MessageCircle, label: "yafunguye chat", color: "text-violet-600 bg-violet-50" },
  BOOK_DOWNLOAD: { icon: BookOpen, label: "yakuye igitabo", color: "text-gold-600 bg-gold-100" },
  CLASS_JOIN: { icon: Radio, label: "yinjiye mu isomo", color: "text-emerald-600 bg-emerald-50" },
  CLASS_LEAVE: { icon: Radio, label: "yavuye mu isomo", color: "text-ink-soft bg-cream-2" },
};

function timeAgo(iso) {
  const diffMs = Date.now() - new Date(iso).getTime();
  const s = Math.max(0, Math.floor(diffMs / 1000));
  if (s < 60) return "vuba aha";
  const m = Math.floor(s / 60);
  if (m < 60) return `${m} min.`;
  const h = Math.floor(m / 60);
  if (h < 24) return `${h}h`;
  const d = Math.floor(h / 24);
  return `${d}d`;
}

export default function RecentActivityFeed({ items = [] }) {
  if (items.length === 0) {
    return <div className="text-sm text-ink-soft py-10 text-center">Nta bikorwa vuba aha.</div>;
  }

  return (
    <div className="space-y-1">
      {items.map((a) => {
        const meta = TYPE_META[a.type] ?? { icon: User, label: a.type, color: "text-ink-soft bg-cream-2" };
        const Icon = meta.icon;
        return (
          <div key={a.id} className="flex items-center gap-3 py-2.5 border-b border-line last:border-0">
            <div className={`w-9 h-9 rounded-full flex items-center justify-center flex-none ${meta.color}`}>
              <Icon size={15} />
            </div>
            <div className="min-w-0 flex-1">
              <div className="text-sm font-semibold text-green-950 truncate">
                {a.userName} <span className="font-normal text-ink-soft">{meta.label}</span>
              </div>
              <div className="text-[11px] text-ink-soft">{a.userRole === "STUDENT" ? "Umunyeshuri" : "Umuyobozi"}</div>
            </div>
            <div className="text-[11px] text-ink-soft font-semibold flex-none">{timeAgo(a.createdAt)}</div>
          </div>
        );
      })}
    </div>
  );
}
