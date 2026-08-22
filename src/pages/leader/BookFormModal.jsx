import { useState } from "react";
import { X, Upload, FileText, Image as ImageIcon } from "lucide-react";
import { createBook, updateBook } from "../../api/books.js";

const CATEGORIES = ["Aqida", "Fiqh", "Hadith", "Sira", "Tajwiid", "Ibindi"];

export default function BookFormModal({ book, onClose, onSaved }) {
  const isEdit = Boolean(book);
  const [title, setTitle] = useState(book?.title ?? "");
  const [description, setDescription] = useState(book?.description ?? "");
  const [author, setAuthor] = useState(book?.author ?? "");
  const [category, setCategory] = useState(book?.category ?? CATEGORIES[0]);
  const [file, setFile] = useState(null);
  const [coverImage, setCoverImage] = useState(null);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");

  async function handleSubmit(e) {
    e.preventDefault();
    if (!title.trim()) {
      setError("Uzuza umutwe w'igitabo.");
      return;
    }
    if (!isEdit && !file) {
      setError("Ushyiremo idosiye y'igitabo (PDF).");
      return;
    }
    setBusy(true);
    setError("");
    try {
      const fields = { title, description, author, category };
      const files = { file, coverImage };
      const res = isEdit ? await updateBook(book.id, fields, files) : await createBook(fields, files);
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
          <h3 className="font-display text-lg font-bold text-green-950">
            {isEdit ? "Hindura igitabo" : "Ongeramo igitabo"}
          </h3>
          <button onClick={onClose} className="w-8 h-8 rounded-full hover:bg-cream-2 flex items-center justify-center">
            <X size={16} />
          </button>
        </div>

        <form onSubmit={handleSubmit} className="p-5 space-y-4">
          {error && <div className="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-2.5">{error}</div>}

          <div>
            <label className="text-xs font-bold text-ink-soft mb-1.5 block">Umutwe w'igitabo *</label>
            <input
              value={title}
              onChange={(e) => setTitle(e.target.value)}
              className="w-full border border-line rounded-xl px-3.5 py-2.5 text-sm bg-surface"
              placeholder="Urugero: Riyadh As-Saliheen"
            />
          </div>

          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="text-xs font-bold text-ink-soft mb-1.5 block">Umwanditsi</label>
              <input
                value={author}
                onChange={(e) => setAuthor(e.target.value)}
                className="w-full border border-line rounded-xl px-3.5 py-2.5 text-sm bg-surface"
              />
            </div>
            <div>
              <label className="text-xs font-bold text-ink-soft mb-1.5 block">Icyiciro</label>
              <select
                value={category}
                onChange={(e) => setCategory(e.target.value)}
                className="w-full border border-line rounded-xl px-3.5 py-2.5 text-sm bg-surface"
              >
                {CATEGORIES.map((c) => (
                  <option key={c} value={c}>{c}</option>
                ))}
              </select>
            </div>
          </div>

          <div>
            <label className="text-xs font-bold text-ink-soft mb-1.5 block">Ibisobanuro</label>
            <textarea
              value={description}
              onChange={(e) => setDescription(e.target.value)}
              rows={3}
              className="w-full border border-line rounded-xl px-3.5 py-2.5 text-sm bg-surface resize-none"
            />
          </div>

          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="text-xs font-bold text-ink-soft mb-1.5 block flex items-center gap-1.5">
                <FileText size={13} /> Idosiye (PDF) {!isEdit && "*"}
              </label>
              <label className="flex items-center gap-2 border border-dashed border-line rounded-xl px-3.5 py-3 text-xs text-ink-soft cursor-pointer hover:bg-cream-2">
                <Upload size={14} />
                {file ? file.name : isEdit ? "Simbuza idosiye (si ngombwa)" : "Hitamo idosiye"}
                <input type="file" accept="application/pdf" className="hidden" onChange={(e) => setFile(e.target.files[0] ?? null)} />
              </label>
            </div>
            <div>
              <label className="text-xs font-bold text-ink-soft mb-1.5 block flex items-center gap-1.5">
                <ImageIcon size={13} /> Ifoto y'ipfundo
              </label>
              <label className="flex items-center gap-2 border border-dashed border-line rounded-xl px-3.5 py-3 text-xs text-ink-soft cursor-pointer hover:bg-cream-2">
                <Upload size={14} />
                {coverImage ? coverImage.name : "Hitamo ifoto"}
                <input type="file" accept="image/jpeg,image/png,image/webp" className="hidden" onChange={(e) => setCoverImage(e.target.files[0] ?? null)} />
              </label>
            </div>
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
