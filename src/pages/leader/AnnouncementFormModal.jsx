import { useState } from "react";
import { X, Upload } from "lucide-react";
import { createAnnouncement, updateAnnouncement } from "../../api/announcements.js";

export default function AnnouncementFormModal({ announcement, onClose, onSaved }) {
  const isEdit = Boolean(announcement);
  const [title, setTitle] = useState(announcement?.title ?? "");
  const [body, setBody] = useState(announcement?.body ?? "");
  const [coverImage, setCoverImage] = useState(null);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");

  async function handleSubmit(e) {
    e.preventDefault();
    if (!title.trim()) return setError("Uzuza umutwe w'itangazo.");
    setBusy(true);
    setError("");
    try {
      const res = isEdit
        ? await updateAnnouncement(announcement.id, { title, body }, { coverImage })
        : await createAnnouncement({ title, body }, { coverImage });
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
          <h3 className="font-display text-lg font-bold text-green-950">{isEdit ? "Hindura itangazo" : "Andika itangazo"}</h3>
          <button onClick={onClose} className="w-8 h-8 rounded-full hover:bg-cream-2 flex items-center justify-center">
            <X size={16} />
          </button>
        </div>

        <form onSubmit={handleSubmit} className="p-5 space-y-4">
          {error && <div className="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-2.5">{error}</div>}

          <div>
            <label className="text-xs font-bold text-ink-soft mb-1.5 block">Umutwe *</label>
            <input value={title} onChange={(e) => setTitle(e.target.value)} className="w-full border border-line rounded-xl px-3.5 py-2.5 text-sm bg-surface" />
          </div>

          <div>
            <label className="text-xs font-bold text-ink-soft mb-1.5 block">Ubutumwa</label>
            <textarea value={body} onChange={(e) => setBody(e.target.value)} rows={5} className="w-full border border-line rounded-xl px-3.5 py-2.5 text-sm bg-surface resize-none" />
          </div>

          <div>
            <label className="text-xs font-bold text-ink-soft mb-1.5 block">Ifoto (si ngombwa)</label>
            <label className="flex items-center gap-2 border border-dashed border-line rounded-xl px-3.5 py-3 text-xs text-ink-soft cursor-pointer hover:bg-cream-2">
              <Upload size={14} />
              {coverImage ? coverImage.name : "Hitamo ifoto"}
              <input type="file" accept="image/jpeg,image/png,image/webp" className="hidden" onChange={(e) => setCoverImage(e.target.files[0] ?? null)} />
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
