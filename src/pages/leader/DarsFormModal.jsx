import { useEffect, useState } from "react";
import { X, Upload, Mic, Video, Image as ImageIcon } from "lucide-react";
import { createDars, updateDars } from "../../api/dars.js";
import { listAdminTeachers } from "../../api/teachers.js";

export default function DarsFormModal({ dars, onClose, onSaved }) {
  const isEdit = Boolean(dars);
  const [title, setTitle] = useState(dars?.title ?? "");
  const [type, setType] = useState(dars?.type ?? "AUDIO");
  const [description, setDescription] = useState(dars?.description ?? "");
  const [youtubeUrl, setYoutubeUrl] = useState(dars?.youtubeUrl ?? "");
  const [teacherId, setTeacherId] = useState(dars?.teacherId ?? "");
  const [audio, setAudio] = useState(null);
  const [thumbnail, setThumbnail] = useState(null);
  const [teachers, setTeachers] = useState([]);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");

  useEffect(() => {
    listAdminTeachers()
      .then((res) => setTeachers(res.data))
      .catch(() => {});
  }, []);

  async function handleSubmit(e) {
    e.preventDefault();
    if (!title.trim()) return setError("Uzuza umutwe wa Dars.");
    if (type === "AUDIO" && !isEdit && !audio) return setError("Ushyiremo idosiye y'ijwi.");
    if (type === "VIDEO" && !youtubeUrl.trim()) return setError("Ushyiremo link ya YouTube.");
    setBusy(true);
    setError("");
    try {
      const fields = { title, type, description, youtubeUrl: type === "VIDEO" ? youtubeUrl : "", teacherId };
      const files = { audio, thumbnail };
      const res = isEdit ? await updateDars(dars.id, fields, files) : await createDars(fields, files);
      onSaved(res.data);
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy(false);
    }
  }

  return (
    <div className="fixed inset-0 bg-black/40 z-[110] flex items-center justify-center p-4">
      <div className="bg-surface rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div className="flex items-center justify-between px-5 py-4 border-b border-line sticky top-0 bg-surface">
          <h3 className="font-display text-lg font-bold text-green-950">{isEdit ? "Hindura Dars" : "Ongeramo Dars"}</h3>
          <button onClick={onClose} className="w-8 h-8 rounded-full hover:bg-cream-2 flex items-center justify-center">
            <X size={16} />
          </button>
        </div>

        <form onSubmit={handleSubmit} className="p-5 space-y-4">
          {error && <div className="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-2.5">{error}</div>}

          <div className="flex gap-2">
            {[
              { key: "AUDIO", label: "Ijwi (Audio)", icon: Mic },
              { key: "VIDEO", label: "Videwo (YouTube)", icon: Video },
            ].map(({ key, label, icon: Icon }) => (
              <button
                type="button"
                key={key}
                onClick={() => !isEdit && setType(key)}
                disabled={isEdit}
                className={`flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-xs font-bold border ${
                  type === key ? "bg-green-950 text-white border-green-950" : "border-line text-ink-soft"
                } ${isEdit ? "opacity-60 cursor-not-allowed" : ""}`}
              >
                <Icon size={14} /> {label}
              </button>
            ))}
          </div>

          <div>
            <label className="text-xs font-bold text-ink-soft mb-1.5 block">Umutwe *</label>
            <input value={title} onChange={(e) => setTitle(e.target.value)} className="w-full border border-line rounded-xl px-3.5 py-2.5 text-sm bg-surface" />
          </div>

          <div>
            <label className="text-xs font-bold text-ink-soft mb-1.5 block">Umwarimu (si ngombwa)</label>
            <select value={teacherId} onChange={(e) => setTeacherId(e.target.value)} className="w-full border border-line rounded-xl px-3.5 py-2.5 text-sm bg-surface">
              <option value="">— Nta mwarimu —</option>
              {teachers.map((t) => (
                <option key={t.id} value={t.id}>{t.name}</option>
              ))}
            </select>
          </div>

          <div>
            <label className="text-xs font-bold text-ink-soft mb-1.5 block">Ibisobanuro</label>
            <textarea value={description} onChange={(e) => setDescription(e.target.value)} rows={3} className="w-full border border-line rounded-xl px-3.5 py-2.5 text-sm bg-surface resize-none" />
          </div>

          {type === "VIDEO" ? (
            <div>
              <label className="text-xs font-bold text-ink-soft mb-1.5 block">Link ya YouTube *</label>
              <input
                value={youtubeUrl}
                onChange={(e) => setYoutubeUrl(e.target.value)}
                placeholder="https://youtube.com/watch?v=..."
                className="w-full border border-line rounded-xl px-3.5 py-2.5 text-sm bg-surface"
              />
            </div>
          ) : (
            <div>
              <label className="text-xs font-bold text-ink-soft mb-1.5 block">Idosiye y'ijwi {!isEdit && "*"}</label>
              <label className="flex items-center gap-2 border border-dashed border-line rounded-xl px-3.5 py-3 text-xs text-ink-soft cursor-pointer hover:bg-cream-2">
                <Upload size={14} />
                {audio ? audio.name : isEdit ? "Simbuza idosiye (si ngombwa)" : "Hitamo idosiye (mp3/m4a/wav)"}
                <input type="file" accept="audio/*" className="hidden" onChange={(e) => setAudio(e.target.files[0] ?? null)} />
              </label>
            </div>
          )}

          <div>
            <label className="text-xs font-bold text-ink-soft mb-1.5 block flex items-center gap-1.5">
              <ImageIcon size={13} /> Thumbnail (si ngombwa)
            </label>
            <label className="flex items-center gap-2 border border-dashed border-line rounded-xl px-3.5 py-3 text-xs text-ink-soft cursor-pointer hover:bg-cream-2">
              <Upload size={14} />
              {thumbnail ? thumbnail.name : "Hitamo ifoto"}
              <input type="file" accept="image/jpeg,image/png,image/webp" className="hidden" onChange={(e) => setThumbnail(e.target.files[0] ?? null)} />
            </label>
          </div>

          <div className="flex gap-3 pt-2">
            <button type="button" onClick={onClose} className="flex-1 btn btn-outline !border-line !text-green-950 justify-center">
              Hagarika
            </button>
            <button type="submit" disabled={busy} className="flex-1 btn btn-gold justify-center disabled:opacity-60">
              {busy ? "Turabika..." : "Bika"}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
