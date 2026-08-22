import { useEffect, useState } from "react";
import { Users, Eye, Globe } from "lucide-react";
import { getPublicStats } from "../api/visits.js";

/** Refreshes every 30s — no need for a socket connection just for a footer
 * strip, and this stays honest (real DB-backed numbers, polled) without the
 * overhead of a public unauthenticated realtime channel. */
export default function SiteStatsBar() {
  const [stats, setStats] = useState(null);

  useEffect(() => {
    let mounted = true;
    function load() {
      getPublicStats()
        .then((res) => mounted && setStats(res.data))
        .catch(() => {});
    }
    load();
    const interval = setInterval(load, 30000);
    return () => {
      mounted = false;
      clearInterval(interval);
    };
  }, []);

  if (!stats) return null;

  return (
    <div className="flex flex-wrap items-center gap-5 py-4 text-[12px] text-[#9db4a7] border-t border-white/10">
      <span className="flex items-center gap-1.5">
        <Globe size={13} className="text-gold-400" /> Kuri interineti ubu: <b className="text-white">{stats.onlineNow}</b>
      </span>
      <span className="flex items-center gap-1.5">
        <Eye size={13} className="text-gold-400" /> Uyu munsi: <b className="text-white">{stats.todayVisits}</b>
      </span>
      <span className="flex items-center gap-1.5">
        <Users size={13} className="text-gold-400" /> Byose: <b className="text-white">{stats.totalVisits}</b>
      </span>
    </div>
  );
}
