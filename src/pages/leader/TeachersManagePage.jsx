import { useEffect, useState, useCallback } from "react";
import { Pencil, Plus, Trash2, Mic, Video } from "lucide-react";
import AdminLayout from "../../layouts/AdminLayout.jsx";
import DataTable from "../../components/DataTable.jsx";
import { deleteTeacher, getPublicTeachers } from "../../api/teachers.js";
import TeacherFormModal from "./TeacherFormModal.jsx";

export default function TeachersManagePage() {
  const [teachers, setTeachers] = useState([]);
  const [search, setSearch] = useState("");
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [modal, setModal] = useState(null);

  const load = useCallback(async () => {
    setLoading(true);
    setError("");
    try {
      // The public "with real audio/video counts" endpoint is exactly what
      // this table wants to show too, so it's reused here rather than
      // duplicated into a second admin-only query.
      const res = await getPublicTeachers();
      setTeachers(res.data);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  async function handleDelete(t) {
    if (!confirm(`Siba ${t.name}?`)) return;
    try {
      await deleteTeacher(t.id);
      load();
    } catch (err) {
      setError(err.message);
    }
  }

  const filtered = teachers.filter((t) => t.name.toLowerCase().includes(search.toLowerCase()));

  const columns = [
    {
      key: "name",
      label: "Umwarimu",
      render: (t) => (
        <div className="flex items-center gap-3">
          <div className="w-9 h-9 rounded-full bg-gradient-to-br from-green-700 to-green-950 flex items-center justify-center flex-none text-white font-display font-bold text-xs overflow-hidden">
            {t.photo ? <img src={t.photo} alt="" className="w-full h-full object-cover" /> : t.name[0]}
          </div>
          <div>
            <div className="font-semibold text-green-950">{t.name}</div>
            <div className="text-xs text-ink-soft">{t.kunia || "—"}</div>
          </div>
        </div>
      ),
    },
    { key: "role", label: "Icyo yigisha", hideBelow: "sm" },
    {
      key: "counts",
      label: "Dars",
      hideBelow: "md",
      render: (t) => (
        <div className="flex items-center gap-3 text-xs font-bold text-ink-soft">
          <span className="flex items-center gap-1"><Mic size={12} /> {t.audioCount}</span>
          <span className="flex items-center gap-1"><Video size={12} /> {t.videoCount}</span>
        </div>
      ),
    },
  ];

  return (
    <AdminLayout breadcrumb="Ahabanza / Abarimu" title="Abarimu">
      <div className="flex justify-end mb-4">
        <button onClick={() => setModal("new")} className="btn btn-gold text-sm">
          <Plus size={16} /> Ongeramo umwarimu
        </button>
      </div>

      <DataTable
        title="Abarimu bose"
        itemLabel="abarimu"
        total={filtered.length}
        columns={columns}
        rows={filtered}
        loading={loading}
        error={error}
        emptyMessage="Nta mwarimu wabonetse."
        search={{ value: search, onChange: setSearch, placeholder: "Shakisha umwarimu..." }}
        renderRowActions={(t) => (
          <div className="flex items-center justify-end gap-1.5">
            <button onClick={() => setModal(t)} className="w-8 h-8 rounded-lg hover:bg-cream-2 flex items-center justify-center text-green-950">
              <Pencil size={14} />
            </button>
            <button onClick={() => handleDelete(t)} className="w-8 h-8 rounded-lg hover:bg-red-50 flex items-center justify-center text-red-600">
              <Trash2 size={14} />
            </button>
          </div>
        )}
      />

      {modal && (
        <TeacherFormModal
          teacher={modal === "new" ? null : modal}
          onClose={() => setModal(null)}
          onSaved={() => {
            setModal(null);
            load();
          }}
        />
      )}
    </AdminLayout>
  );
}
