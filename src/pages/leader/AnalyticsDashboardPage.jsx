import { useEffect, useState, useCallback } from "react";
import {
  Activity,
  ArrowUpRight,
  MessageCircle,
  Radio,
  Users,
  Wifi,
} from "lucide-react";
import { getAnalyticsOverview } from "../../api/analytics.js";
import AdminLayout from "../../layouts/AdminLayout.jsx";
import ActivityBarChart from "../../components/charts/ActivityBarChart.jsx";
import EngagementGauge from "../../components/charts/EngagementGauge.jsx";
import WeeklyAreaChart from "../../components/charts/WeeklyAreaChart.jsx";
import RecentActivityFeed from "../../components/RecentActivityFeed.jsx";

const RANGES = [
  { key: "daily", label: "Uyu munsi" },
  { key: "weekly", label: "Iki cyumweru" },
  { key: "monthly", label: "Uku kwezi" },
  { key: "yearly", label: "Uyu mwaka" },
  { key: "lifetime", label: "Byose" },
];

const EVENT_LABELS = {
  LOGIN: "Kwinjira",
  LOGOUT: "Gusohoka",
  PAGE_VIEW: "Amapaji yarebwe",
  CHAT_OPEN: "Chat yafunguwe",
  BOOK_DOWNLOAD: "Ibitabo byakuwe",
  CLASS_JOIN: "Kwinjira mu isomo",
  CLASS_LEAVE: "Gusohoka mu isomo",
};

function formatDuration(totalSeconds) {
  const h = Math.floor(totalSeconds / 3600);
  const m = Math.floor((totalSeconds % 3600) / 60);
  if (h > 0) return `${h}h ${m}min`;
  if (m > 0) return `${m}min`;
  return `${totalSeconds}s`;
}

const CARD_THEMES = {
  blue: { bg: "bg-blue-50", text: "text-blue-600" },
  gold: { bg: "bg-gold-100", text: "text-gold-600" },
  green: { bg: "bg-emerald-50", text: "text-emerald-600" },
  purple: { bg: "bg-violet-50", text: "text-violet-600" },
};

function StatCard({ icon: Icon, label, value, theme = "green" }) {
  const t = CARD_THEMES[theme];
  return (
    <div className="card">
      <div className={`w-11 h-11 rounded-xl ${t.bg} ${t.text} flex items-center justify-center mb-4`}>
        <Icon size={19} />
      </div>
      <div className="font-display text-[26px] font-bold text-green-950 leading-none">{value}</div>
      <div className="text-xs text-ink-soft font-semibold mt-2">{label}</div>
    </div>
  );
}

function Panel({ title, subtitle, className = "", children }) {
  return (
    <div className={`card ${className}`}>
      {title && (
        <div className="mb-1">
          <h3 className="font-display text-lg font-bold text-green-950">{title}</h3>
          {subtitle && <p className="text-xs text-ink-soft mt-0.5">{subtitle}</p>}
        </div>
      )}
      <div className={title ? "mt-4" : ""}>{children}</div>
    </div>
  );
}

export default function AnalyticsDashboardPage() {
  const [range, setRange] = useState("weekly");
  const [data, setData] = useState(null);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(true);

  const load = useCallback(async (r) => {
    setLoading(true);
    setError("");
    try {
      const res = await getAnalyticsOverview(r);
      setData(res.data);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    load(range);
  }, [range, load]);

  const eventEntries = data ? Object.entries(data.eventCounts).sort((a, b) => b[1] - a[1]) : [];
  const totalEvents = eventEntries.reduce((sum, [, c]) => sum + c, 0) || 1;
  const maxEventCount = Math.max(1, ...eventEntries.map(([, c]) => c));
  const dailySeries = data?.dailySeries ?? [];
  const recentActivity = data?.recentActivity ?? [];
  const engagementPct = data && data.totalStudents > 0 ? (data.activeStudents / data.totalStudents) * 100 : 0;

  return (
    <AdminLayout breadcrumb="Ahabanza / Dashboard" title="Dashboard">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div className="text-sm font-semibold text-ink-soft">Incamake y'Imibare</div>
        <div className="flex gap-1 bg-cream-2 rounded-full p-1">
          {RANGES.map((r) => (
            <button
              key={r.key}
              onClick={() => setRange(r.key)}
              className={`px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors ${
                range === r.key ? "bg-green-950 text-white" : "text-ink-soft"
              }`}
            >
              {r.label}
            </button>
          ))}
        </div>
      </div>

      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-5">{error}</div>
      )}

      {loading ? (
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-5">
          {Array.from({ length: 8 }).map((_, i) => (
            <div key={i} className="card animate-pulse h-[132px]">
              <div className="w-11 h-11 rounded-xl bg-cream-2 mb-4" />
              <div className="h-6 w-16 bg-cream-2 rounded mb-2" />
              <div className="h-3 w-24 bg-cream-2 rounded" />
            </div>
          ))}
        </div>
      ) : (
        data && (
          <>
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
              <StatCard icon={Wifi} label="Kuri interineti ubu" value={data.onlineNow} theme="blue" />
              <StatCard icon={Users} label="Abanyeshuri bakora" value={data.activeStudents} theme="gold" />
              <StatCard icon={Activity} label="Igihe cy'urubuga" value={formatDuration(data.platformTimeSeconds)} theme="green" />
              <StatCard icon={MessageCircle} label="Ubutumwa bwoherejwe" value={data.messagesSent} theme="purple" />
            </div>

            <div className="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
              <StatCard icon={Users} label="Abanyeshuri bose" value={data.totalStudents} theme="green" />
              <StatCard icon={Users} label="Abayobozi bose" value={data.totalLeaders} theme="blue" />
              <StatCard icon={Radio} label="Amasomo ya live" value={data.liveClassesHosted} theme="gold" />
              <StatCard icon={Radio} label="Igihe mu masomo ya live" value={formatDuration(data.liveClassAttendanceSeconds)} theme="purple" />
            </div>

            {/* Weekly activity bars + engagement gauge — TailAdmin-style hero row */}
            <div className="grid grid-cols-1 lg:grid-cols-[1.7fr_1fr] gap-5 mb-6">
              <Panel title="Ibikorwa by'Icyumweru" subtitle="Iminsi 7 ishize — amakuru nyayo y'ipfundo">
                <ActivityBarChart series={dailySeries} />
              </Panel>

              <Panel title="Kwitabira" subtitle="Abanyeshuri bakora ugereranyije n'abose">
                <EngagementGauge
                  value={engagementPct}
                  caption="bakora muri iki gihe"
                  footer={[
                    { label: "Bose", value: data.totalStudents },
                    { label: "Bakora", value: data.activeStudents },
                    { label: "Kuri net", value: data.onlineNow },
                  ]}
                />
              </Panel>
            </div>

            {/* Trend chart + recent activity feed */}
            <div className="grid grid-cols-1 lg:grid-cols-[1.7fr_1fr] gap-5 mb-6">
              <Panel title="Isesengura" subtitle="Kwinjira na ubutumwa, iminsi 7 ishize">
                <WeeklyAreaChart series={dailySeries} />
              </Panel>

              <Panel title="Ibikorwa Bishya" subtitle="Ibiheruka kuba kuri urubuga">
                <RecentActivityFeed items={recentActivity} />
              </Panel>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-[1.6fr_1fr] gap-5">
              <div className="card">
                <h3 className="font-display text-lg font-bold text-green-950 mb-5">Ibikorwa by'Abakoresha</h3>
                {eventEntries.length === 0 ? (
                  <div className="text-sm text-ink-soft">Nta bikorwa byanditswe muri iki gihe.</div>
                ) : (
                  <div className="space-y-4">
                    {eventEntries.map(([type, count]) => (
                      <div key={type}>
                        <div className="flex justify-between text-xs font-semibold text-ink-soft mb-1.5">
                          <span>{EVENT_LABELS[type] ?? type}</span>
                          <span>{count}</span>
                        </div>
                        <div className="h-2.5 bg-cream-2 rounded-full overflow-hidden">
                          <div
                            className="h-full bg-gradient-to-r from-green-700 to-gold-500 rounded-full"
                            style={{ width: `${(count / maxEventCount) * 100}%` }}
                          />
                        </div>
                      </div>
                    ))}
                  </div>
                )}
              </div>

              <div className="card !p-0 overflow-hidden">
                <h3 className="font-display text-lg font-bold text-green-950 px-5 pt-5 pb-4">Ibipimo</h3>
                {eventEntries.length === 0 ? (
                  <div className="px-5 pb-5 text-sm text-ink-soft">Nta makuru ahari.</div>
                ) : (
                  eventEntries.map(([type, count]) => (
                    <div
                      key={type}
                      className="flex items-center justify-between px-5 py-3 border-t border-line"
                    >
                      <span className="text-sm font-semibold text-green-950">{EVENT_LABELS[type] ?? type}</span>
                      <span className="flex items-center gap-1 text-xs font-bold text-emerald-600">
                        {((count / totalEvents) * 100).toFixed(1)}%
                        <ArrowUpRight size={12} />
                      </span>
                    </div>
                  ))
                )}
              </div>
            </div>

            <p className="text-xs text-ink-soft mt-5">
              Icyitonderwa: iyi mibare yose ifatwa ku bikorwa nyabyo byabaye kuri uru rubuga (nta na kimwe gifatwa
              cyangwa gikabya). Ukwezi/Umwaka bya "Isesengura" bizakora igihe backend izongerwaho aggregation
              y'ukwezi — ubu ni iminsi 7 ishize gusa ifatika.
            </p>
          </>
        )
      )}
    </AdminLayout>
  );
}
