import { useEffect, useState } from "react";
import { BookOpen, Download } from "lucide-react";
import SectionHead from "../components/SectionHead.jsx";
import { getPublishedBooks, trackBookDownload } from "../api/books.js";
import { trackEvent } from "../api/activity.js";
import { useAuth } from "../context/AuthContext.jsx";

export default function IbitaboPage() {
  const { user } = useAuth();
  const [books, setBooks] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    getPublishedBooks({ perPage: 24 })
      .then((res) => setBooks(res.data))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  async function handleDownload(book) {
    try {
      const res = await trackBookDownload(book.id);
      if (user) trackEvent("BOOK_DOWNLOAD", { title: book.title }).catch(() => {});
      window.open(res.data.fileUrl, "_blank", "noopener");
    } catch {
      // Silent — the download link still opens even if the counter fails.
      window.open(book.fileUrl, "_blank", "noopener");
    }
  }

  return (
    <div className="py-[86px]">
      <div className="max-w-[1240px] mx-auto px-7">
        <SectionHead
          eyebrow="IBITABO"
          title="Isomero ry'Ibitabo"
          desc="Bona ibitabo by'ubumenyi bwa dini wabike cyangwa usome kuri interineti."
        />

        {loading ? (
          <div className="text-center text-ink-soft text-sm py-16">Turimo gupakira ibitabo...</div>
        ) : error ? (
          <div className="text-center text-red-600 text-sm py-16">{error}</div>
        ) : books.length === 0 ? (
          <div className="text-center text-ink-soft text-sm py-16">Nta gitabo cyatangajwe kugeza ubu.</div>
        ) : (
          <div className="grid grid-cols-2 lg:grid-cols-4 gap-6">
            {books.map((b) => (
              <div key={b.id} className="card text-left">
                <div
                  className="h-[190px] rounded-[14px] mb-4 relative overflow-hidden flex items-center justify-center"
                  style={
                    b.coverImage
                      ? { backgroundImage: `url(${b.coverImage})`, backgroundSize: "cover", backgroundPosition: "center" }
                      : { background: "linear-gradient(160deg, #0b3d2e, #146349)" }
                  }
                >
                  {!b.coverImage && (
                    <>
                      <div
                        className="absolute inset-0"
                        style={{ background: "linear-gradient(160deg, rgba(207,157,63,.35), transparent 60%)" }}
                      />
                      <BookOpen size={52} className="text-white/85 relative" />
                    </>
                  )}
                </div>
                <h3 className="text-[16.5px] text-green-950 mb-1 font-display font-bold line-clamp-1">{b.title}</h3>
                <div className="text-xs text-ink-soft mb-3.5">{b.author || "—"}</div>
                <button
                  onClick={() => handleDownload(b)}
                  className="flex items-center justify-center gap-1.5 w-full py-2.5 rounded-[10px] border-[1.5px] border-green-900 text-green-950 font-bold text-[13px] bg-surface hover:bg-green-950 hover:text-white transition-colors"
                >
                  <Download size={14} />
                  Kuraho / Download
                </button>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
