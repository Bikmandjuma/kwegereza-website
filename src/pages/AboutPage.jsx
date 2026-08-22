import { BookOpen, Users, GraduationCap, ShieldCheck } from "lucide-react";
import SectionHead from "../components/SectionHead.jsx";

const values = [
  {
    icon: BookOpen,
    title: "Ubumenyi nyakuri",
    desc: "Twigisha dushingiye ku Qur'an na Sunnah, tudakurikira ibitekerezo bidafite ishingiro.",
  },
  {
    icon: Users,
    title: "Umuryango",
    desc: "Tubaka umuryango w'abanyeshuri n'abarimu bafatanya mu kwigisha no kwiga.",
  },
  {
    icon: GraduationCap,
    title: "Uburezi bugera kuri bose",
    desc: "Amasomo agenewe buri wese, uko yaba ari mu mujyi cyangwa mu cyaro.",
  },
  {
    icon: ShieldCheck,
    title: "Ubunyangamugayo",
    desc: "Dukorera mu mucyo, twubaha abanyeshuri n'abarimu bose ku rubuga rwacu.",
  },
];

export default function AboutPage() {
  return (
    <div className="py-[86px]">
      <div className="max-w-[1240px] mx-auto px-7">
        <div className="grid lg:grid-cols-2 gap-14 items-center mb-[70px]">
          <div>
            <div className="eyebrow">ABOUT US</div>
            <h2 className="font-display text-[38px] text-green-950 mb-4 font-bold">
              Kwegereza Islam Umuryango — inzira yo kwiga idini mu buryo bworoshye
            </h2>
            <p className="text-ink-soft text-[15.5px] leading-loose mb-2.5">
              Kwegereza Islam Umuryango (K.I.U) ni urubuga rugamije guhuza abanyeshuri n'abarimu,
              rukagira uruhare mu kwigisha ubumenyi bw'idini ya Islamu bushingiye kuri Qur'an na
              Sunnah, mu buryo bugezweho kandi bworoshye kugera.
            </p>
            <p className="text-ink-soft text-[15.5px] leading-loose">
              Twizera ko buri muntu, uko yaba ari he, agomba kubona uburyo bwiza bwo kwiga,
              gusobanukirwa, no gukomeza umuryango w'abemera. Ibi ni byo bishishikaje itsinda ryacu
              kubaka iyi platform.
            </p>
            <div className="flex gap-8 mt-6">
              <div>
                <b className="block text-[30px] font-display text-green-950">3</b>
                <span className="text-xs text-ink-soft font-bold">Uyu munsi</span>
              </div>
              <div>
                <b className="block text-[30px] font-display text-green-950">76</b>
                <span className="text-xs text-ink-soft font-bold">Ibyanditswe byose</span>
              </div>
              <div>
                <b className="block text-[30px] font-display text-green-950">1</b>
                <span className="text-xs text-ink-soft font-bold">Ku rubuga ubu</span>
              </div>
            </div>
          </div>
          <div
            className="rounded-[22px] h-[360px] shadow-[0_20px_45px_-20px_rgba(8,37,28,0.35)]"
            style={{
              background: "linear-gradient(150deg, #08251c, #146349 60%, #cf9d3f)",
            }}
          />
        </div>

        <SectionHead eyebrow="INDANGAGACIRO" title="Ibyo dushingiraho" />
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          {values.map(({ icon: Icon, title, desc }) => (
            <div key={title} className="card text-center">
              <div className="w-[52px] h-[52px] rounded-[14px] bg-cream-2 text-green-800 flex items-center justify-center mx-auto mb-4">
                <Icon size={24} />
              </div>
              <h3 className="text-[16px] text-green-950 mb-2 font-display font-bold">{title}</h3>
              <p className="text-[13px] text-ink-soft leading-relaxed">{desc}</p>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
