import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { BookOpen, PenLine } from "lucide-react";
import SectionHead from "../components/SectionHead.jsx";
import { listPublishedIfaida } from "../api/ifaida.js";

export default function InyandikoPage() {
  const [posts, setPosts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    listPublishedIfaida()
      .then((res) => setPosts(res.data))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  return (
    <div className="py-[86px]">
      <div className="max-w-[1240px] mx-auto px-7">
        <SectionHead
          eyebrow="INYANDIKO"
          title="Inyandiko n'Ifaida"
          desc="Soma inyandiko zanditswe n'abarimu bacu ku ngingo zitandukanye z'idini ya Islamu."
        />

        {loading ? (
          <div className="text-center text-ink-soft text-sm py-16">Turimo gupakira...</div>
        ) : error ? (
          <div className="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">{error}</div>
        ) : posts.length === 0 ? (
          <div className="card text-center py-16">
            <BookOpen size={36} className="mx-auto text-ink-soft/40 mb-4" />
            <p className="text-ink-soft text-sm">
              Nta nyandiko yatangajwe kuri ubu. Ongera ugaruke vuba — abarimu bacu barimo kwandika.
            </p>
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {posts.map((p) => (
              <Link key={p.id} to={`/inyandiko/${p.id}`} className="card block">
                {p.category && (
                  <div className="inline-block bg-cream-2 text-green-800 text-[11px] font-extrabold tracking-wide uppercase px-3 py-1.5 rounded-full mb-3.5">
                    {p.category}
                  </div>
                )}
                <div
                  className="h-[150px] rounded-[14px] mb-4 relative overflow-hidden flex items-end justify-end p-3"
                  style={
                    p.coverImage
                      ? { backgroundImage: `url(${p.coverImage})`, backgroundSize: "cover", backgroundPosition: "center" }
                      : { background: "linear-gradient(135deg, #0b3d2e, #cf9d3f)" }
                  }
                >
                  {!p.coverImage && <PenLine size={46} className="text-white/35" />}
                </div>
                <h3 className="font-display text-[19px] text-green-950 mb-2 leading-snug">{p.title}</h3>
                {p.description && (
                  <p className="text-[13.8px] text-ink-soft leading-relaxed mb-4">{p.description}</p>
                )}
                <div className="flex justify-between items-center text-xs text-ink-soft border-t border-line pt-3.5">
                  <span>
                    Yanditswe na <b className="text-green-900">{p.authorName}</b>
                  </span>
                  <span>{p.readingMinutes} min</span>
                </div>
              </Link>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
