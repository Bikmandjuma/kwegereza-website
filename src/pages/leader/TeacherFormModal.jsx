import { useState } from "react";
import { X, Upload } from "lucide-react";
import { createTeacher, updateTeacher } from "../../api/teachers.js";

export default function TeacherFormModal({ teacher, onClose, onSaved }) {
  const isEdit = Boolean(teacher);
  const [name, setName] = useState(teacher?.name ?? "");
  const [kunia, setKunia] = useState(teacher?.kunia ?? "");
  const [role, setRole] = useState(teacher?.role ?? "");
  const [bio, setBio] = useState(teacher?.bio ?? "");
  const [photo, setPhoto] = useState(null);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");

  async function handleSubmit(e) {
    e.preventDefault();
    if (!name.trim()) return setError("Uzuza amazina.");
    setBusy(true);
    setError("");
    try {
      const res = isEdit
        ? await updateTeacher(teacher.id, { name, kunia, role, bio }, { photo })
        : await createTeacher({ name, kunia, role, bio }, { photo });
      onSaved(res.data);
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy(false);
    }
  }

  return (
    <div className="fixed inset-0 bg-black/40 z-[110] flex items-center justify-center p-4">
      <div className="bg-surface rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div className="flex items-center justify-between px-5 py-4 border-b border-line sticky top-0 bg-surface">
          <h3 className="font-display text-lg font-bold text-green-950">{isEdit ? "Hindura umwarimu" : "Ongeramo umwarimu"}</h3>
          <button onClick={onClose} className="w-8 h-8 rounded-full hover:bg-cream-2 flex items-center justify-center">
            <X size={16} />
          </button>
        </div>

        <form onSubmit={handleSubmit} className="p-5 space-y-4">
          {error && <div className="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-2.5">{error}</div>}

          <div>
            <label className="text-xs font-bold text-ink-soft mb-1.5 block">Amazina *</label>
            <input value={name} onChange={(e) => setName(e.target.value)} className="w-full border border-line rounded-xl px-3.5 py-2.5 text-sm bg-surface" />
          </div>
          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="text-xs font-bold text-ink-soft mb-1.5 block">Kunia</label>
              <input value={kunia} onChange={(e) => setKunia(e.target.value)} className="w-full border border-line rounded-xl px-3.5 py-2.5 text-sm bg-surface" />
            </div>
            <div>
              <label className="text-xs font-bold text-ink-soft mb-1.5 block">Icyo yigisha</label>
              <input value={role} onChange={(e) => setRole(e.target.value)} placeholder="Aqida & Fiqh" className="w-full border border-line rounded-xl px-3.5 py-2.5 text-sm bg-surface" />
            </div>
          </div>
          <div>
            <label className="text-xs font-bold text-ink-soft mb-1.5 block">Bio</label>
            <textarea value={bio} onChange={(e) => setBio(e.target.value)} rows={3} className="w-full border border-line rounded-xl px-3.5 py-2.5 text-sm bg-surface resize-none" />
          </div>
          <div>
            <label className="text-xs font-bold text-ink-soft mb-1.5 block">Ifoto</label>
            <label className="flex items-center gap-2 border border-dashed border-line rounded-xl px-3.5 py-3 text-xs text-ink-soft cursor-pointer hover:bg-cream-2">
              <Upload size={14} />
              {photo ? photo.name : "Hitamo ifoto"}
              <input type="file" accept="image/jpeg,image/png,image/webp" className="hidden" onChange={(e) => setPhoto(e.target.files[0] ?? null)} />
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
