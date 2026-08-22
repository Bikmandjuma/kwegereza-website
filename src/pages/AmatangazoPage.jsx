import { useEffect, useState } from "react";
import SectionHead from "../components/SectionHead.jsx";
import { getPublishedAnnouncements } from "../api/announcements.js";

export default function AmatangazoPage() {
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    getPublishedAnnouncements({ perPage: 20 })
      .then((res) => setItems(res.data))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  return (
    <div className="py-[86px]">
      <div className="max-w-[1240px] mx-auto px-7">
        <SectionHead
          eyebrow="AMATANGAZO"
          title="Amatangazo agezweho"
          desc="Kurikirana amakuru mashya y'amasomo, ibirori n'ibindi biba kuri Kwegereza."
        />

        {loading ? (
          <div className="text-center text-ink-soft text-sm py-16">Turimo gupakira...</div>
        ) : error ? (
          <div className="text-center text-red-600 text-sm py-16">{error}</div>
        ) : items.length === 0 ? (
          <div className="text-center text-ink-soft text-sm py-16">Nta tangazo ritangajwe kugeza ubu.</div>
        ) : (
          <div className="flex flex-col gap-4 max-w-[760px] mx-auto">
            {items.map((a) => {
              const date = new Date(a.publishedAt ?? a.createdAt);
              const day = date.toLocaleDateString("en-GB", { day: "2-digit" });
              const month = date.toLocaleDateString("en-GB", { month: "short" }).toUpperCase();
              return (
                <div key={a.id} className="flex gap-5 items-start bg-surface border border-line rounded-2xl px-6 py-5">
                  <div className="flex-none w-16 text-center bg-cream-2 rounded-xl py-2.5">
                    <b className="block font-display text-2xl text-green-950">{day}</b>
                    <span className="text-[10.5px] tracking-wide text-ink-soft font-bold uppercase">{month}</span>
                  </div>
                  <div>
                    <h3 className="text-[18px] text-green-950 mb-1.5 font-display font-bold">{a.title}</h3>
                    {a.coverImage && (
                      <img src={a.coverImage} alt="" className="rounded-xl mb-2.5 max-h-52 object-cover" />
                    )}
                    <p className="text-[13.8px] text-ink-soft leading-relaxed whitespace-pre-line">{a.body}</p>
                  </div>
                </div>
              );
            })}
          </div>
        )}
      </div>
    </div>
  );
}
