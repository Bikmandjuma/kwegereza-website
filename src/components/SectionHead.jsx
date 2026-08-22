export default function SectionHead({ eyebrow, title, desc, center = true }) {
  return (
    <div className={`max-w-[640px] mb-11 ${center ? "mx-auto text-center" : ""}`}>
      <div className={`eyebrow ${center ? "justify-center" : ""}`}>{eyebrow}</div>
      <h2 className="font-display text-[38px] font-bold text-green-950 mb-2.5">{title}</h2>
      {desc && <p className="text-ink-soft text-[15.5px] leading-relaxed mx-auto">{desc}</p>}
    </div>
  );
}
