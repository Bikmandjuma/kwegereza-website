import { Link } from "react-router-dom";
import { GraduationCap, Users } from "lucide-react";

export default function Hero() {
  return (
    <div>
      <div
        className="relative overflow-hidden text-white"
        style={{
          background:
            "linear-gradient(115deg, rgba(8,37,28,.94) 0%, rgba(11,61,46,.86) 38%, rgba(151,110,38,.72) 78%, rgba(207,157,63,.62) 100%), linear-gradient(180deg,#0c3f2f,#123f2c)",
        }}
      >
        <svg
          className="absolute inset-0 opacity-50 pointer-events-none w-full h-full"
          viewBox="0 0 1400 480"
          preserveAspectRatio="xMidYMax slice"
        >
          <g fill="#ffffff" opacity="0.10">
            <path d="M0 480 L0 300 Q40 260 70 300 L70 220 Q70 150 130 150 Q190 150 190 220 L190 300 Q220 260 260 300 L260 480Z" />
            <path d="M230 480 L230 250 Q270 190 310 250 L310 480Z" />
            <path d="M420 480 L420 260 Q460 150 520 150 Q580 150 620 260 L620 480Z" />
            <path d="M600 480 L600 300 Q630 260 660 300 L660 480Z" />
            <path d="M780 480 L780 220 Q820 110 900 110 Q980 110 1020 220 L1020 480Z" />
            <path d="M1000 480 L1000 300 Q1030 260 1060 300 L1060 480Z" />
            <path d="M1180 480 L1180 260 Q1220 170 1280 170 Q1340 170 1370 260 L1370 480 L1400 480 L1400 480Z" />
          </g>
          <g fill="#ffffff" opacity="0.16">
            <circle cx="900" cy="95" r="9" />
            <circle cx="520" cy="140" r="7" />
            <circle cx="130" cy="140" r="7" />
          </g>
        </svg>
        <svg
          className="absolute left-0 right-0 bottom-0 opacity-90 pointer-events-none w-full"
          viewBox="0 0 1400 90"
          preserveAspectRatio="none"
        >
          <g fill="none" stroke="#e0b657" strokeWidth="1.4" opacity=".55">
            {Array.from({ length: 20 }).map((_, i) => {
              const x = i * 70;
              return (
                <path key={i} d={`M${x} 90 V40 Q${x + 35} 8 ${x + 70} 40 V90`} />
              );
            })}
          </g>
        </svg>

        <div className="relative z-[2] px-7 pt-24 pb-[150px] max-w-[1000px] mx-auto text-center">
          <div className="font-arabic text-[32px] text-gold-400 mb-4">
            تقريب السنة بين يدي الأمة
          </div>
          <h1 className="font-display text-[56px] leading-[1.05] font-bold mb-2.5 tracking-wide">
            KWEGEREZA ISLAM
            <br />
            <span className="text-gold-400">UMURYANGO</span>
          </h1>
          <p className="max-w-[640px] mx-auto my-6 text-[16.5px] leading-relaxed text-[#e9f2ec] font-medium">
            Ikaze ku rubuga rwacu rwigisha ubumenyi bw'idini ya Islamu bushingiye kuri Qur'an na
            Sunnah, mu buryo bworoshye, bwiza kandi bunoze.
          </p>
          <div className="flex gap-4 justify-center flex-wrap mb-11">
            <Link to="/register" className="btn btn-gold">
              <GraduationCap size={17} />
              Tangira Kwiga
            </Link>
            <Link to="/register" className="btn btn-outline">
              <Users size={17} />
              Twiyungeho (Join us)
            </Link>
          </div>

          <div className="relative z-[3] bg-surface text-ink rounded-[20px] shadow-[0_20px_45px_-20px_rgba(8,37,28,0.35)] px-8 py-5 inline-flex flex-col gap-4 mx-auto">
            <div className="flex items-center gap-2 font-extrabold text-sm text-green-950">
              <span className="w-[9px] h-[9px] rounded-full bg-[#26c281] shadow-[0_0_0_4px_rgba(38,194,129,0.2)]" />
              Site Overview
            </div>
            <div className="flex gap-9">
              <div className="text-center">
                <b className="block text-[26px] text-green-950 font-display">1</b>
                <span className="text-[10.5px] tracking-wide text-ink-soft font-bold uppercase">Online</span>
              </div>
              <div className="text-center">
                <b className="block text-[26px] text-green-950 font-display">3</b>
                <span className="text-[10.5px] tracking-wide text-ink-soft font-bold uppercase">Uyu munsi</span>
              </div>
              <div className="text-center">
                <b className="block text-[26px] text-green-950 font-display">76</b>
                <span className="text-[10.5px] tracking-wide text-ink-soft font-bold uppercase">Byose</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div
        className="relative z-[2] h-16 bg-cream -mt-16"
        style={{ borderRadius: "60% 60% 0 0 / 100% 100% 0 0" }}
      />
    </div>
  );
}
