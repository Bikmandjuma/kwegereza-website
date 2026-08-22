// Grouped bar chart for the last 7 real days of activity (logins / class
// joins / messages). Pure SVG — no charting dependency — so it stays light
// and matches the site's existing hand-rolled component style.

const SERIES = [
  { key: "logins", label: "Kwinjira", color: "#0f4d3a" },
  { key: "classJoins", label: "Amasomo ya live", color: "#cf9d3f" },
  { key: "messages", label: "Ubutumwa", color: "#7c6fe0" },
];

function niceMax(max) {
  if (max <= 0) return 4;
  const magnitude = 10 ** Math.floor(Math.log10(max));
  const residual = max / magnitude;
  let niceResidual;
  if (residual <= 1) niceResidual = 1;
  else if (residual <= 2) niceResidual = 2;
  else if (residual <= 5) niceResidual = 5;
  else niceResidual = 10;
  return niceResidual * magnitude;
}

export default function ActivityBarChart({ series = [] }) {
  const W = 640;
  const H = 260;
  const padL = 34;
  const padR = 8;
  const padT = 12;
  const padB = 30;
  const innerW = W - padL - padR;
  const innerH = H - padT - padB;

  const max = Math.max(1, ...series.flatMap((d) => [d.logins, d.classJoins, d.messages]));
  const top = niceMax(max);
  const ticks = [0, top / 4, top / 2, (3 * top) / 4, top];

  const groupW = innerW / Math.max(1, series.length);
  const barW = Math.min(14, groupW / 5);
  const gap = 3;

  const totals = SERIES.reduce((acc, s) => {
    acc[s.key] = series.reduce((sum, d) => sum + (d[s.key] || 0), 0);
    return acc;
  }, {});

  return (
    <div>
      <div className="flex flex-wrap items-center gap-4 mb-3">
        {SERIES.map((s) => (
          <div key={s.key} className="flex items-center gap-1.5 text-xs font-semibold text-ink-soft">
            <span className="w-2.5 h-2.5 rounded-full flex-none" style={{ background: s.color }} />
            {s.label}
            <span className="text-ink-soft/70 font-bold">({totals[s.key]})</span>
          </div>
        ))}
      </div>

      <svg viewBox={`0 0 ${W} ${H}`} className="w-full h-auto" role="img" aria-label="Ibikorwa mu byumweru">
        {ticks.map((t, i) => {
          const y = padT + innerH - (t / top) * innerH;
          return (
            <g key={i}>
              <line x1={padL} x2={W - padR} y1={y} y2={y} stroke="currentColor" className="text-line" strokeWidth="1" />
              <text x={padL - 8} y={y + 3} textAnchor="end" fontSize="9" className="fill-ink-soft">
                {Math.round(t)}
              </text>
            </g>
          );
        })}

        {series.map((d, i) => {
          const groupX = padL + i * groupW + groupW / 2 - ((barW + gap) * SERIES.length - gap) / 2;
          return (
            <g key={d.date}>
              {SERIES.map((s, si) => {
                const val = d[s.key] || 0;
                const h = (val / top) * innerH;
                const x = groupX + si * (barW + gap);
                const y = padT + innerH - h;
                return (
                  <rect
                    key={s.key}
                    x={x}
                    y={h === 0 ? y - 1 : y}
                    width={barW}
                    height={h === 0 ? 1 : h}
                    rx={3}
                    fill={s.color}
                    opacity={val === 0 ? 0.25 : 1}
                  >
                    <title>{`${s.label}: ${val}`}</title>
                  </rect>
                );
              })}
              <text
                x={padL + i * groupW + groupW / 2}
                y={H - 8}
                textAnchor="middle"
                fontSize="10"
                fontWeight="700"
                className="fill-ink-soft"
              >
                {d.label}
              </text>
            </g>
          );
        })}
      </svg>
    </div>
  );
}
