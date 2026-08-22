import { useEffect, useState, useCallback } from "react";
import { Ban, CheckCircle2, UserCog } from "lucide-react";
import { bulkUpdateUserStatus, listAllUsers } from "../../api/adminUsers.js";
import AdminLayout from "../../layouts/AdminLayout.jsx";
import DataTable, { downloadCsv } from "../../components/DataTable.jsx";
import PermissionEditorModal from "./PermissionEditorModal.jsx";

const ROLE_FILTERS = [
  { key: "", label: "Bose" },
  { key: "STUDENT", label: "Abanyeshuri" },
  { key: "LEADER", label: "Abayobozi" },
  { key: "ADMIN", label: "Admin" },
];

const STATUS_BADGE = {
  ACTIVE: "bg-emerald-50 text-emerald-700",
  PENDING: "bg-amber-50 text-amber-700",
  BLOCKED: "bg-red-50 text-red-700",
  SUSPENDED: "bg-orange-50 text-orange-700",
  REJECTED: "bg-gray-100 text-gray-600",
  INACTIVE: "bg-gray-100 text-gray-600",
};

export default function UserManagementPage() {
  const [users, setUsers] = useState([]);
  const [total, setTotal] = useState(0);
  const [totalPages, setTotalPages] = useState(1);
  const [search, setSearch] = useState("");
  const [role, setRole] = useState("");
  const [page, setPage] = useState(1);
  const [perPage, setPerPage] = useState(20);
  const [sort, setSort] = useState({ key: "createdAt", order: "desc" });
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [editing, setEditing] = useState(null);
  const [selectedIds, setSelectedIds] = useState(() => new Set());
  const [bulkError, setBulkError] = useState("");

  const load = useCallback(async () => {
    setLoading(true);
    setError("");
    try {
      const res = await listAllUsers({ search, role, page, perPage, sort: sort.key, order: sort.order });
      setUsers(res.data);
      setTotal(res.meta.total);
      setTotalPages(res.meta.totalPages);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }, [search, role, page, perPage, sort]);

  useEffect(() => {
    const t = setTimeout(() => load(), 300);
    return () => clearTimeout(t);
  }, [load]);

  // Any filter/search/sort change resets to page 1 — otherwise you can land
  // on an empty page 4 of a 1-row result set.
  useEffect(() => {
    setPage(1);
  }, [search, role, perPage, sort]);

  function handleSaved(updatedUser) {
    setUsers((prev) => prev.map((u) => (u.id === updatedUser.id ? updatedUser : u)));
    setEditing(updatedUser);
  }

  function handleSortChange(key) {
    setSort((prev) => (prev.key === key ? { key, order: prev.order === "asc" ? "desc" : "asc" } : { key, order: "asc" }));
  }

  function toggleRow(id) {
    setSelectedIds((prev) => {
      const next = new Set(prev);
      if (next.has(id)) next.delete(id);
      else next.add(id);
      return next;
    });
  }
  function toggleAll(ids) {
    setSelectedIds((prev) => (ids.every((id) => prev.has(id)) ? new Set() : new Set(ids)));
  }

  async function handleBulk(action) {
    setBulkError("");
    try {
      await bulkUpdateUserStatus(Array.from(selectedIds), action);
      setSelectedIds(new Set());
      await load();
    } catch (err) {
      setBulkError(err.message);
    }
  }

  function handleExport() {
    downloadCsv(
      "abakoresha.csv",
      [
        { key: "fullName", label: "Amazina" },
        { key: "email", label: "Imeli" },
        { key: "role", label: "Uruhare" },
        { key: "status", label: "Uko bimeze" },
        { key: "createdAt", label: "Yiyandikishije", csvValue: (r) => new Date(r.createdAt).toLocaleDateString() },
      ],
      users
    );
  }

  const columns = [
    {
      key: "fullName",
      label: "Umukoresha",
      sortable: true,
      render: (u) => (
        <div className="flex items-center gap-3">
          <div className="w-9 h-9 rounded-full bg-gradient-to-br from-green-700 to-green-950 text-white flex items-center justify-center font-display font-bold text-sm flex-none">
            {u.fullName[0]}
          </div>
          <div>
            <div className="font-semibold text-green-950">{u.fullName}</div>
            <div className="text-xs text-ink-soft">{u.email}</div>
          </div>
        </div>
      ),
    },
    {
      key: "role",
      label: "Uruhare",
      sortable: true,
      hideBelow: "sm",
      render: (u) => <span className="text-xs font-bold bg-cream-2 px-2.5 py-1 rounded-full">{u.role}</span>,
    },
    {
      key: "permissions",
      label: "Uburenganzira",
      hideBelow: "md",
      render: (u) => (u.role === "LEADER" ? `${u.permissions.length} uburenganzira` : "—"),
    },
    {
      key: "status",
      label: "Uko bimeze",
      sortable: true,
      render: (u) => (
        <span className={`text-xs font-bold px-2.5 py-1 rounded-full ${STATUS_BADGE[u.status] ?? ""}`}>{u.status}</span>
      ),
    },
  ];

  return (
    <AdminLayout breadcrumb="Ahabanza / Abakoresha" title="Abakoresha">
      {bulkError && (
        <div className="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-4">{bulkError}</div>
      )}

      <DataTable
        title="Abakoresha"
        itemLabel="abakoresha"
        total={total}
        columns={columns}
        rows={users}
        rowKey={(u) => u.id}
        loading={loading}
        error={error}
        emptyMessage="Nta mukoresha uboneka."
        search={{ value: search, onChange: setSearch, placeholder: "Shakisha..." }}
        filters={
          <div className="flex gap-1 bg-cream-2 rounded-full p-1">
            {ROLE_FILTERS.map((r) => (
              <button
                key={r.key}
                onClick={() => setRole(r.key)}
                className={`px-3 py-1.5 rounded-full text-xs font-bold transition-colors ${
                  role === r.key ? "bg-green-950 text-white" : "text-ink-soft"
                }`}
              >
                {r.label}
              </button>
            ))}
          </div>
        }
        sort={sort}
        onSortChange={handleSortChange}
        page={page}
        perPage={perPage}
        totalPages={totalPages}
        onPageChange={setPage}
        onPerPageChange={setPerPage}
        selectable
        selectedIds={selectedIds}
        onToggleRow={toggleRow}
        onToggleAll={toggleAll}
        bulkActions={[
          { label: "Hagarika", icon: <Ban size={12} />, danger: true, onClick: () => handleBulk("BLOCK") },
          { label: "Emeza", icon: <CheckCircle2 size={12} />, onClick: () => handleBulk("UNBLOCK") },
        ]}
        onExport={handleExport}
        renderRowActions={(u) =>
          u.role === "ADMIN" ? (
            <span className="text-xs text-ink-soft italic">Ntibihindurwa</span>
          ) : (
            <button
              onClick={() => setEditing(u)}
              className="flex items-center gap-1.5 text-xs font-bold bg-green-950 text-white px-3 py-2 rounded-lg ml-auto"
            >
              <UserCog size={13} /> Hindura
            </button>
          )
        }
      />

      <p className="text-xs text-ink-soft mt-4">
        Iyi paji igaragara gusa ku Admin. Guha uruhare n'uburenganzira ni ibikorwa bya sisitemu, ntibitangwa
        n'uburenganzira bwihariye.
      </p>

      {editing && (
        <PermissionEditorModal targetUser={editing} onClose={() => setEditing(null)} onSaved={handleSaved} />
      )}
    </AdminLayout>
  );
}
