import { useEffect, useRef, useState } from "react";
import { Link } from "react-router-dom";
import { Search, X, User, BookOpen, Mic, PenLine } from "lucide-react";
import { searchAll } from "../api/search.js";

const SECTIONS = [
  { key: "teachers", label: "Abarimu", icon: User },
  { key: "books", label: "Ibitabo", icon: BookOpen },
  { key: "darsat", label: "Dars", icon: Mic },
  { key: "ifaida", label: "Inyandiko", icon: PenLine },
];

export default function SearchModal({ onClose }) {
  const [q, setQ] = useState("");
  const [results, setResults] = useState(null);
  const [loading, setLoading] = useState(false);
  const inputRef = useRef(null);

  useEffect(() => {
    inputRef.current?.focus();
    function onKey(e) {
      if (e.key === "Escape") onClose();
    }
    window.addEventListener("keydown", onKey);
    return () => window.removeEventListener("keydown", onKey);
  }, [onClose]);

  useEffect(() => {
    if (q.trim().length < 2) {
      setResults(null);
      return undefined;
    }
    setLoading(true);
    const t = setTimeout(() => {
      searchAll(q.trim())
        .then((res) => setResults(res.data))
        .catch(() => setResults(null))
        .finally(() => setLoading(false));
    }, 250); // debounced — matches the "auto-searching" spec without hammering the API on every keystroke
    return () => clearTimeout(t);
  }, [q]);

  const totalResults = results ? Object.values(results).reduce((sum, arr) => sum + arr.length, 0) : 0;

  return (
    <div className="fixed inset-0 bg-black/50 z-[200] flex items-start justify-center pt-[12vh] px-4" onClick={onClose}>
      <div className="bg-surface rounded-2xl w-full max-w-xl max-h-[70vh] overflow-hidden shadow-2xl" onClick={(e) => e.stopPropagation()}>
        <div className="flex items-center gap-3 px-5 py-4 border-b border-line">
          <Search size={18} className="text-ink-soft flex-none" />
          <input
            ref={inputRef}
            value={q}
            onChange={(e) => setQ(e.target.value)}
            placeholder="Shakisha umwarimu, igitabo, Dars, inyandiko..."
            className="flex-1 outline-none bg-transparent text-[15px]"
          />
          <button onClick={onClose} className="w-8 h-8 rounded-full hover:bg-cream-2 flex items-center justify-center flex-none">
            <X size={16} />
          </button>
        </div>

        <div className="overflow-y-auto max-h-[calc(70vh-64px)] p-3">
          {q.trim().length < 2 ? (
            <div className="text-center text-ink-soft text-sm py-10">Andika byibuze inyuguti 2 kugira ngo dushakishe.</div>
          ) : loading ? (
            <div className="text-center text-ink-soft text-sm py-10">Turashakisha...</div>
          ) : totalResults === 0 ? (
            <div className="text-center text-ink-soft text-sm py-10">Nta bisubizo byabonetse kuri "{q}".</div>
          ) : (
            SECTIONS.map(({ key, label, icon: Icon }) => {
              const items = results?.[key] ?? [];
              if (items.length === 0) return null;
              return (
                <div key={key} className="mb-2">
                  <div className="text-[10.5px] font-extrabold text-ink-soft uppercase tracking-wide px-2 py-1.5 flex items-center gap-1.5">
                    <Icon size={11} /> {label}
                  </div>
                  {items.map((item) => (
                    <Link
                      key={item.id}
                      to={item.url}
                      onClick={onClose}
                      className="flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-cream-2"
                    >
                      <span className="text-sm font-semibold text-green-950 truncate">{item.title}</span>
                      {item.subtitle && <span className="text-xs text-ink-soft flex-none ml-3">{item.subtitle}</span>}
                    </Link>
                  ))}
                </div>
              );
            })
          )}
        </div>
      </div>
    </div>
  );
}
