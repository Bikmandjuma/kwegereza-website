import { useEffect, useState, useCallback } from "react";
import { Megaphone, Pencil, Plus, Trash2 } from "lucide-react";
import AdminLayout from "../../layouts/AdminLayout.jsx";
import DataTable, { downloadCsv } from "../../components/DataTable.jsx";
import { deleteAnnouncement, listAdminAnnouncements, publishAnnouncement, unpublishAnnouncement } from "../../api/announcements.js";
import AnnouncementFormModal from "./AnnouncementFormModal.jsx";

const STATUS_BADGE = { DRAFT: "bg-amber-50 text-amber-700", PUBLISHED: "bg-emerald-50 text-emerald-700" };
const STATUS_LABEL = { DRAFT: "Umushinga", PUBLISHED: "Byatangajwe" };

export default function AnnouncementsListPage() {
  const [rows, setRows] = useState([]);
  const [total, setTotal] = useState(0);
  const [totalPages, setTotalPages] = useState(1);
  const [search, setSearch] = useState("");
  const [status, setStatus] = useState("");
  const [page, setPage] = useState(1);
  const [perPage, setPerPage] = useState(20);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [modal, setModal] = useState(null);

  const load = useCallback(async () => {
    setLoading(true);
    setError("");
    try {
      const res = await listAdminAnnouncements({ search, status, page, perPage });
      setRows(res.data);
      setTotal(res.meta.total);
      setTotalPages(res.meta.totalPages);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }, [search, status, page, perPage]);

  useEffect(() => {
    const t = setTimeout(() => load(), 250);
    return () => clearTimeout(t);
  }, [load]);
  useEffect(() => setPage(1), [search, status]);

  async function togglePublish(a) {
    try {
      const res = a.status === "PUBLISHED" ? await unpublishAnnouncement(a.id) : await publishAnnouncement(a.id);
      setRows((prev) => prev.map((r) => (r.id === a.id ? res.data : r)));
    } catch (err) {
      setError(err.message);
    }
  }

  async function handleDelete(a) {
    if (!confirm(`Siba "${a.title}"?`)) return;
    try {
      await deleteAnnouncement(a.id);
      load();
    } catch (err) {
      setError(err.message);
    }
  }

  const columns = [
    {
      key: "title",
      label: "Itangazo",
      render: (a) => (
        <div className="flex items-center gap-3">
          <div className="w-9 h-9 rounded-lg bg-gold-500 flex items-center justify-center flex-none text-[#1a1206]">
            <Megaphone size={15} />
          </div>
          <div>
            <div className="font-semibold text-green-950">{a.title}</div>
            <div className="text-xs text-ink-soft">{a.authorName}</div>
          </div>
        </div>
      ),
    },
    {
      key: "status",
      label: "Uko bimeze",
      render: (a) => (
        <button onClick={() => togglePublish(a)} className={`text-[11px] font-bold px-2.5 py-1 rounded-full ${STATUS_BADGE[a.status]}`}>
          {STATUS_LABEL[a.status]}
        </button>
      ),
    },
  ];

  return (
    <AdminLayout breadcrumb="Ahabanza / Amatangazo" title="Amatangazo">
      <div className="flex justify-end mb-4">
        <button onClick={() => setModal("new")} className="btn btn-gold text-sm">
          <Plus size={16} /> Andika itangazo
        </button>
      </div>

      <DataTable
        title="Amatangazo yose"
        itemLabel="amatangazo"
        total={total}
        columns={columns}
        rows={rows}
        loading={loading}
        error={error}
        emptyMessage="Nta tangazo ryabonetse."
        search={{ value: search, onChange: setSearch, placeholder: "Shakisha itangazo..." }}
        filters={
          <select value={status} onChange={(e) => setStatus(e.target.value)} className="border border-line rounded-full px-4 py-2.5 text-sm bg-surface">
            <option value="">Byose</option>
            <option value="DRAFT">Umushinga</option>
            <option value="PUBLISHED">Byatangajwe</option>
          </select>
        }
        page={page}
        perPage={perPage}
        totalPages={totalPages}
        onPageChange={setPage}
        onPerPageChange={setPerPage}
        onExport={() => downloadCsv("amatangazo.csv", [
          { key: "title", label: "Umutwe" },
          { key: "authorName", label: "Uwanditse" },
          { key: "status", label: "Uko bimeze" },
        ], rows)}
        renderRowActions={(a) => (
          <div className="flex items-center justify-end gap-1.5">
            <button onClick={() => setModal(a)} className="w-8 h-8 rounded-lg hover:bg-cream-2 flex items-center justify-center text-green-950">
              <Pencil size={14} />
            </button>
            <button onClick={() => handleDelete(a)} className="w-8 h-8 rounded-lg hover:bg-red-50 flex items-center justify-center text-red-600">
              <Trash2 size={14} />
            </button>
          </div>
        )}
      />

      {modal && (
        <AnnouncementFormModal
          announcement={modal === "new" ? null : modal}
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
