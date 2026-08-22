import { Link } from "react-router-dom";
import { Info, Users, PenLine, BookOpen } from "lucide-react";
import Hero from "../components/Hero.jsx";
import SectionHead from "../components/SectionHead.jsx";

const previews = [
  {
    to: "/about",
    icon: Info,
    title: "Aho tuvuye",
    desc: "Menya intego n'amateka ya Kwegereza Islam Umuryango.",
    cta: "Reba About Us",
  },
  {
    to: "/abarimu",
    icon: Users,
    title: "Abarimu",
    desc: "Menyana n'abarimu batanga amasomo ku rubuga rwacu.",
    cta: "Reba Abarimu",
  },
  {
    to: "/inyandiko",
    icon: PenLine,
    title: "Inyandiko",
    desc: "Soma inyandiko n'ifaida zanditswe n'abarimu bacu.",
    cta: "Soma Inyandiko",
  },
  {
    to: "/ibitabo",
    icon: BookOpen,
    title: "Ibitabo",
    desc: "Bona no gukoresha ibitabo by'ubumenyi bwa dini.",
    cta: "Reba Ibitabo",
  },
];

export default function HomePage() {
  return (
    <div>
      <Hero />

      <div className="max-w-[1240px] mx-auto px-7 pt-10">
        <SectionHead
          eyebrow="IBY'INGENZI"
          title="Ibyo Kwegereza igutangira"
          desc="Reba mu magambo make ibiri kuri uru rubuga, hanyuma ujye aho ushaka kubona byinshi."
        />

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-20">
          {previews.map(({ to, icon: Icon, title, desc, cta }) => (
            <div key={to} className="card flex flex-col">
              <div className="w-[52px] h-[52px] rounded-[14px] bg-cream-2 text-green-800 flex items-center justify-center mb-4">
                <Icon size={24} />
              </div>
              <h3 className="font-display text-[19px] text-green-950 mb-1">{title}</h3>
              <p className="text-[13.5px] text-ink-soft leading-relaxed mb-4">{desc}</p>
              <Link
                to={to}
                className="text-gold-600 font-extrabold text-[13px] flex items-center gap-1 mt-auto"
              >
                {cta} →
              </Link>
            </div>
          ))}
        </div>

        <div
          className="mb-20 rounded-[26px] p-10 sm:p-14 text-white flex flex-col sm:flex-row items-center justify-between gap-7"
          style={{
            background:
              "linear-gradient(120deg, #08251c, #0f4d3a 60%, #b9862f)",
          }}
        >
          <div className="text-center sm:text-left">
            <h2 className="font-display text-[30px] mb-2">
              Witeguye gutangira urugendo rwo kwiga?
            </h2>
            <p className="text-[#e6efe9] text-[14.5px] max-w-[460px]">
              Iyandikishe none maze utangire kwiga Aqida, Fiqh, Qur'an na Sunnah uyobowe n'abarimu
              bemewe.
            </p>
          </div>
          <a href="#" className="btn btn-gold flex-none">
            Twiyungeho Ubu
          </a>
        </div>
      </div>
    </div>
  );
}
