import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { Mic, Video } from "lucide-react";
import SectionHead from "../components/SectionHead.jsx";
import { getPublicTeachers } from "../api/teachers.js";

export default function AbarimuPage() {
  const [teachers, setTeachers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    getPublicTeachers()
      .then((res) => setTeachers(res.data))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  return (
    <div className="py-[86px]">
      <div className="max-w-[1240px] mx-auto px-7">
        <SectionHead
          eyebrow="TEACHERS"
          title="Abarimu bacu"
          desc="Abarimu bemewe kandi bize neza, bafite uburambe mu kwigisha ubumenyi bw'idini ya Islamu."
        />

        {loading ? (
          <div className="text-center text-ink-soft text-sm py-16">Turimo gupakira...</div>
        ) : error ? (
          <div className="text-center text-red-600 text-sm py-16">{error}</div>
        ) : teachers.length === 0 ? (
          <div className="text-center text-ink-soft text-sm py-16">Nta mwarimu wanditswe kugeza ubu.</div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {teachers.map((t) => (
              <Link key={t.id} to={`/abarimu/${t.id}`} className="card text-center block hover:no-underline">
                <div className="w-[82px] h-[82px] rounded-full mx-auto mb-4 flex items-center justify-center font-display font-bold text-2xl text-white border-[3px] border-gold-400 bg-gradient-to-br from-green-700 to-green-950 overflow-hidden">
                  {t.photo ? (
                    <img src={t.photo} alt={t.name} className="w-full h-full object-cover" />
                  ) : (
                    t.name
                      .split(" ")
                      .slice(0, 2)
                      .map((w) => w[0])
                      .join("")
                  )}
                </div>
                <h3 className="font-display text-[19px] text-green-950 mb-1">{t.name}</h3>
                {t.kunia && <div className="text-xs text-ink-soft mb-1">{t.kunia}</div>}
                <div className="text-gold-600 font-bold text-xs uppercase tracking-wide mb-3">{t.role}</div>
                <div className="flex items-center justify-center gap-4 text-xs font-bold text-ink-soft">
                  <span className="flex items-center gap-1"><Mic size={13} /> {t.audioCount}</span>
                  <span className="flex items-center gap-1"><Video size={13} /> {t.videoCount}</span>
                </div>
              </Link>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
