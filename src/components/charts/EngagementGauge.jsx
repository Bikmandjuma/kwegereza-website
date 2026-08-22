// Semi-circular gauge, styled after a "target" dial — driven by a single
// real ratio (active students ÷ total students) rather than an invented number.

function polarToCartesian(cx, cy, r, angleDeg) {
  const rad = ((angleDeg - 180) * Math.PI) / 180;
  return { x: cx + r * Math.cos(rad), y: cy + r * Math.sin(rad) };
}

function arcPath(cx, cy, r, startDeg, endDeg) {
  const start = polarToCartesian(cx, cy, r, endDeg);
  const end = polarToCartesian(cx, cy, r, startDeg);
  const largeArc = endDeg - startDeg <= 180 ? 0 : 1;
  return `M ${start.x} ${start.y} A ${r} ${r} 0 ${largeArc} 0 ${end.x} ${end.y}`;
}

export default function EngagementGauge({ value = 0, caption, footer = [] }) {
  const pct = Math.max(0, Math.min(100, value));
  const cx = 130;
  const cy = 118;
  const r = 92;
  const sweep = (pct / 100) * 180;

  return (
    <div className="flex flex-col h-full">
      <svg viewBox="0 0 260 140" className="w-full h-auto">
        <path d={arcPath(cx, cy, r, 0, 180)} fill="none" stroke="currentColor" className="text-cream-2" strokeWidth="16" strokeLinecap="round" />
        {sweep > 0 && (
          <path d={arcPath(cx, cy, r, 0, sweep)} fill="none" stroke="url(#gaugeGradient)" strokeWidth="16" strokeLinecap="round" />
        )}
        <defs>
          <linearGradient id="gaugeGradient" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0%" stopColor="#0b3d2e" />
            <stop offset="100%" stopColor="#cf9d3f" />
          </linearGradient>
        </defs>
        <text x={cx} y={cy - 6} textAnchor="middle" fontSize="34" fontWeight="800" className="fill-green-950 font-display">
          {pct.toFixed(1)}%
        </text>
        <text x={cx} y={cy + 16} textAnchor="middle" fontSize="10.5" fontWeight="700" className="fill-ink-soft">
          {caption}
        </text>
      </svg>

      {footer.length > 0 && (
        <div className="grid grid-cols-3 gap-2 mt-auto pt-5 border-t border-line">
          {footer.map((f) => (
            <div key={f.label} className="text-center">
              <div className="font-display text-base font-bold text-green-950">{f.value}</div>
              <div className="text-[10.5px] text-ink-soft font-semibold mt-0.5">{f.label}</div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
