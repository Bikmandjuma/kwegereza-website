import { useEffect, useState, useCallback } from "react";
import { Mic, Video, Pencil, Plus, Trash2, PlayCircle } from "lucide-react";
import AdminLayout from "../../layouts/AdminLayout.jsx";
import DataTable, { downloadCsv } from "../../components/DataTable.jsx";
import { deleteDars, listAdminDarsat, publishDars, unpublishDars } from "../../api/dars.js";
import DarsFormModal from "./DarsFormModal.jsx";

const STATUS_BADGE = { DRAFT: "bg-amber-50 text-amber-700", PUBLISHED: "bg-emerald-50 text-emerald-700" };
const STATUS_LABEL = { DRAFT: "Umushinga", PUBLISHED: "Byatangajwe" };

export default function DarsListPage() {
  const [rows, setRows] = useState([]);
  const [total, setTotal] = useState(0);
  const [totalPages, setTotalPages] = useState(1);
  const [search, setSearch] = useState("");
  const [status, setStatus] = useState("");
  const [type, setType] = useState("");
  const [page, setPage] = useState(1);
  const [perPage, setPerPage] = useState(20);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [modal, setModal] = useState(null);

  const load = useCallback(async () => {
    setLoading(true);
    setError("");
    try {
      const res = await listAdminDarsat({ search, status, type, page, perPage });
      setRows(res.data);
      setTotal(res.meta.total);
      setTotalPages(res.meta.totalPages);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }, [search, status, type, page, perPage]);

  useEffect(() => {
    const t = setTimeout(() => load(), 250);
    return () => clearTimeout(t);
  }, [load]);
  useEffect(() => setPage(1), [search, status, type]);

  async function togglePublish(d) {
    try {
      const res = d.status === "PUBLISHED" ? await unpublishDars(d.id) : await publishDars(d.id);
      setRows((prev) => prev.map((r) => (r.id === d.id ? res.data : r)));
    } catch (err) {
      setError(err.message);
    }
  }

  async function handleDelete(d) {
    if (!confirm(`Siba "${d.title}"?`)) return;
    try {
      await deleteDars(d.id);
      load();
    } catch (err) {
      setError(err.message);
    }
  }

  const columns = [
    {
      key: "title",
      label: "Dars",
      render: (d) => (
        <div className="flex items-center gap-3">
          <div className={`w-9 h-9 rounded-lg flex items-center justify-center flex-none text-white ${d.type === "AUDIO" ? "bg-green-950" : "bg-[#cf1f3e]"}`}>
            {d.type === "AUDIO" ? <Mic size={15} /> : <Video size={15} />}
          </div>
          <div>
            <div className="font-semibold text-green-950">{d.title}</div>
            <div className="text-xs text-ink-soft">{d.teacherName || "Nta mwarimu"}</div>
          </div>
        </div>
      ),
    },
    { key: "type", label: "Ubwoko", hideBelow: "sm" },
    {
      key: "plays",
      label: "Plays",
      hideBelow: "md",
      render: (d) => (
        <span className="flex items-center gap-1 text-xs font-bold text-ink-soft">
          <PlayCircle size={12} /> {d.plays}
        </span>
      ),
    },
    {
      key: "status",
      label: "Uko bimeze",
      render: (d) => (
        <button onClick={() => togglePublish(d)} className={`text-[11px] font-bold px-2.5 py-1 rounded-full ${STATUS_BADGE[d.status]}`}>
          {STATUS_LABEL[d.status]}
        </button>
      ),
    },
  ];

  return (
    <AdminLayout breadcrumb="Ahabanza / Dars" title="Amasomo (Dars)">
      <div className="flex justify-end mb-4">
        <button onClick={() => setModal("new")} className="btn btn-gold text-sm">
          <Plus size={16} /> Ongeramo Dars
        </button>
      </div>

      <DataTable
        title="Dars zose"
        itemLabel="Dars"
        total={total}
        columns={columns}
        rows={rows}
        loading={loading}
        error={error}
        emptyMessage="Nta Dars yabonetse."
        search={{ value: search, onChange: setSearch, placeholder: "Shakisha Dars..." }}
        filters={
          <div className="flex gap-2">
            <select value={type} onChange={(e) => setType(e.target.value)} className="border border-line rounded-full px-3.5 py-2.5 text-sm bg-surface">
              <option value="">Ubwoko bwose</option>
              <option value="AUDIO">Ijwi</option>
              <option value="VIDEO">Videwo</option>
            </select>
            <select value={status} onChange={(e) => setStatus(e.target.value)} className="border border-line rounded-full px-3.5 py-2.5 text-sm bg-surface">
              <option value="">Byose</option>
              <option value="DRAFT">Umushinga</option>
              <option value="PUBLISHED">Byatangajwe</option>
            </select>
          </div>
        }
        page={page}
        perPage={perPage}
        totalPages={totalPages}
        onPageChange={setPage}
        onPerPageChange={setPerPage}
        onExport={() =>
          downloadCsv("darsat.csv", [
            { key: "title", label: "Umutwe" },
            { key: "type", label: "Ubwoko" },
            { key: "teacherName", label: "Umwarimu" },
            { key: "plays", label: "Plays" },
            { key: "status", label: "Uko bimeze" },
          ], rows)
        }
        renderRowActions={(d) => (
          <div className="flex items-center justify-end gap-1.5">
            <button onClick={() => setModal(d)} className="w-8 h-8 rounded-lg hover:bg-cream-2 flex items-center justify-center text-green-950">
              <Pencil size={14} />
            </button>
            <button onClick={() => handleDelete(d)} className="w-8 h-8 rounded-lg hover:bg-red-50 flex items-center justify-center text-red-600">
              <Trash2 size={14} />
            </button>
          </div>
        )}
      />

      {modal && (
        <DarsFormModal
          dars={modal === "new" ? null : modal}
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
