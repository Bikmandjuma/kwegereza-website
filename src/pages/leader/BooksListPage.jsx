import { useEffect, useState, useCallback } from "react";
import { BookOpen, Download, Pencil, Plus, Trash2 } from "lucide-react";
import AdminLayout from "../../layouts/AdminLayout.jsx";
import DataTable, { downloadCsv } from "../../components/DataTable.jsx";
import { deleteBook, listAdminBooks, updateBook } from "../../api/books.js";
import BookFormModal from "./BookFormModal.jsx";

const STATUS_BADGE = {
  DRAFT: "bg-amber-50 text-amber-700",
  PUBLISHED: "bg-emerald-50 text-emerald-700",
};
const STATUS_LABEL = { DRAFT: "Umushinga", PUBLISHED: "Byatangajwe" };

export default function BooksListPage() {
  const [books, setBooks] = useState([]);
  const [total, setTotal] = useState(0);
  const [totalPages, setTotalPages] = useState(1);
  const [search, setSearch] = useState("");
  const [status, setStatus] = useState("");
  const [page, setPage] = useState(1);
  const [perPage, setPerPage] = useState(20);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [modal, setModal] = useState(null); // null | "new" | book

  const load = useCallback(async () => {
    setLoading(true);
    setError("");
    try {
      const res = await listAdminBooks({ search, status, page, perPage });
      setBooks(res.data);
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

  async function togglePublish(book) {
    try {
      const res = await updateBook(book.id, { status: book.status === "PUBLISHED" ? "DRAFT" : "PUBLISHED" });
      setBooks((prev) => prev.map((b) => (b.id === book.id ? res.data : b)));
    } catch (err) {
      setError(err.message);
    }
  }

  async function handleDelete(book) {
    if (!confirm(`Siba "${book.title}"? Ntibishobora gusubizwa inyuma.`)) return;
    try {
      await deleteBook(book.id);
      load();
    } catch (err) {
      setError(err.message);
    }
  }

  const columns = [
    {
      key: "title",
      label: "Igitabo",
      render: (b) => (
        <div className="flex items-center gap-3">
          <div className="w-9 h-9 rounded-lg bg-green-950 flex items-center justify-center flex-none text-white">
            <BookOpen size={15} />
          </div>
          <div>
            <div className="font-semibold text-green-950">{b.title}</div>
            <div className="text-xs text-ink-soft">{b.author || "—"}</div>
          </div>
        </div>
      ),
    },
    { key: "category", label: "Icyiciro", hideBelow: "sm" },
    {
      key: "downloads",
      label: "Downloads",
      hideBelow: "md",
      render: (b) => (
        <span className="flex items-center gap-1 text-xs font-bold text-ink-soft">
          <Download size={12} /> {b.downloads}
        </span>
      ),
    },
    {
      key: "status",
      label: "Uko bimeze",
      render: (b) => (
        <button
          onClick={() => togglePublish(b)}
          className={`text-[11px] font-bold px-2.5 py-1 rounded-full ${STATUS_BADGE[b.status]}`}
        >
          {STATUS_LABEL[b.status]}
        </button>
      ),
    },
  ];

  return (
    <AdminLayout breadcrumb="Ahabanza / Ibitabo" title="Ibitabo">
      <div className="flex justify-end mb-4">
        <button onClick={() => setModal("new")} className="btn btn-gold text-sm">
          <Plus size={16} /> Ongeramo igitabo
        </button>
      </div>

      <DataTable
        title="Ibitabo byose"
        itemLabel="ibitabo"
        total={total}
        columns={columns}
        rows={books}
        loading={loading}
        error={error}
        emptyMessage="Nta gitabo cyabonetse."
        search={{ value: search, onChange: setSearch, placeholder: "Shakisha igitabo..." }}
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
        onExport={() =>
          downloadCsv("ibitabo.csv", [
            { key: "title", label: "Umutwe" },
            { key: "author", label: "Umwanditsi" },
            { key: "category", label: "Icyiciro" },
            { key: "downloads", label: "Downloads" },
            { key: "status", label: "Uko bimeze" },
          ], books)
        }
        renderRowActions={(b) => (
          <div className="flex items-center justify-end gap-1.5">
            <button onClick={() => setModal(b)} className="w-8 h-8 rounded-lg hover:bg-cream-2 flex items-center justify-center text-green-950">
              <Pencil size={14} />
            </button>
            <button onClick={() => handleDelete(b)} className="w-8 h-8 rounded-lg hover:bg-red-50 flex items-center justify-center text-red-600">
              <Trash2 size={14} />
            </button>
          </div>
        )}
      />

      {modal && (
        <BookFormModal
          book={modal === "new" ? null : modal}
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
