// Dual-line area chart over the real 7-day series. "Ukwezi"/"Umwaka" tabs
// are shown but disabled with a "vuba" (soon) badge — the backend only
// aggregates day-by-day right now, and showing a working-looking tab that
// silently returns nothing would be worse than being upfront about it.

const RANGE_TABS = [
  { key: "week", label: "Iki cyumweru", enabled: true },
  { key: "month", label: "Ukwezi", enabled: false },
  { key: "year", label: "Umwaka", enabled: false },
];

const LINES = [
  { key: "logins", label: "Kwinjira", color: "#0b3d2e" },
  { key: "messages", label: "Ubutumwa", color: "#cf9d3f" },
];

export default function WeeklyAreaChart({ series = [] }) {
  const W = 640;
  const H = 240;
  const padL = 30;
  const padR = 8;
  const padT = 14;
  const padB = 26;
  const innerW = W - padL - padR;
  const innerH = H - padT - padB;

  const max = Math.max(1, ...series.flatMap((d) => [d.logins, d.messages]));
  const step = series.length > 1 ? innerW / (series.length - 1) : innerW;

  const linePoints = (key) =>
    series.map((d, i) => {
      const x = padL + i * step;
      const y = padT + innerH - (d[key] / max) * innerH;
      return { x, y };
    });

  const toPath = (pts) => pts.map((p, i) => `${i === 0 ? "M" : "L"} ${p.x} ${p.y}`).join(" ");
  const toAreaPath = (pts) =>
    `${toPath(pts)} L ${pts[pts.length - 1].x} ${padT + innerH} L ${pts[0].x} ${padT + innerH} Z`;

  const rangeLabel =
    series.length > 0 ? `${series[0].label} – ${series[series.length - 1].label}` : "";

  return (
    <div>
      <div className="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div className="flex items-center gap-3">
          {LINES.map((l) => (
            <div key={l.key} className="flex items-center gap-1.5 text-xs font-semibold text-ink-soft">
              <span className="w-2.5 h-2.5 rounded-full flex-none" style={{ background: l.color }} />
              {l.label}
            </div>
          ))}
        </div>
        <div className="flex items-center gap-1 bg-cream-2 rounded-full p-1">
          {RANGE_TABS.map((t) => (
            <button
              key={t.key}
              disabled={!t.enabled}
              title={t.enabled ? undefined : "Bizaza vuba"}
              className={`relative px-3 py-1.5 rounded-full text-[11.5px] font-bold transition-colors ${
                t.enabled ? "bg-green-950 text-white" : "text-ink-soft/50 cursor-not-allowed"
              }`}
            >
              {t.label}
              {!t.enabled && (
                <span className="absolute -top-1.5 -right-1.5 bg-gold-500 text-[7.5px] text-[#1a1206] font-extrabold px-1 rounded-full leading-tight">
                  vuba
                </span>
              )}
            </button>
          ))}
        </div>
      </div>

      {series.length === 0 ? (
        <div className="text-sm text-ink-soft py-10 text-center">Nta makuru ahari.</div>
      ) : (
        <>
          <svg viewBox={`0 0 ${W} ${H}`} className="w-full h-auto" role="img" aria-label="Isesengura ry'ibyumweru">
            <defs>
              {LINES.map((l) => (
                <linearGradient key={l.key} id={`area-${l.key}`} x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stopColor={l.color} stopOpacity="0.28" />
                  <stop offset="100%" stopColor={l.color} stopOpacity="0" />
                </linearGradient>
              ))}
            </defs>

            {[0, 0.25, 0.5, 0.75, 1].map((f) => (
              <line
                key={f}
                x1={padL}
                x2={W - padR}
                y1={padT + innerH * f}
                y2={padT + innerH * f}
                stroke="currentColor"
                className="text-line"
              />
            ))}

            {LINES.map((l) => {
              const pts = linePoints(l.key);
              return (
                <g key={l.key}>
                  <path d={toAreaPath(pts)} fill={`url(#area-${l.key})`} />
                  <path d={toPath(pts)} fill="none" stroke={l.color} strokeWidth="2.5" strokeLinejoin="round" strokeLinecap="round" />
                  {pts.map((p, i) => (
                    <circle key={i} cx={p.x} cy={p.y} r="3" fill={l.color} />
                  ))}
                </g>
              );
            })}

            {series.map((d, i) => (
              <text
                key={d.date}
                x={padL + i * step}
                y={H - 6}
                textAnchor="middle"
                fontSize="10"
                fontWeight="700"
                className="fill-ink-soft"
              >
                {d.label}
              </text>
            ))}
          </svg>
          <div className="text-[11px] text-ink-soft mt-1">{rangeLabel}</div>
        </>
      )}
    </div>
  );
}
