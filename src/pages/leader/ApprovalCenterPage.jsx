import { useEffect, useState, useCallback } from "react";
import { Check, Eye, Search, ShieldAlert, X } from "lucide-react";
import {
  approveStudentRequest,
  blockStudentRequest,
  fetchPendingStudents,
  getStudentDetail,
  rejectStudentRequest,
} from "../../api/students.js";
import { useAuth } from "../../context/AuthContext.jsx";
import AdminLayout from "../../layouts/AdminLayout.jsx";

function DetailModal({ student, onClose }) {
  if (!student) return null;
  return (
    <div className="fixed inset-0 z-[150] flex items-center justify-center p-4">
      <button className="fixed inset-0 bg-black/40" onClick={onClose} aria-label="Funga" />
      <div className="relative bg-surface rounded-2xl shadow-2xl w-full max-w-[440px] p-6">
        <div className="flex items-center gap-3 mb-4">
          <div className="w-12 h-12 rounded-full bg-gradient-to-br from-green-700 to-green-950 text-white flex items-center justify-center font-display font-bold text-lg">
            {student.fullName[0]}
          </div>
          <div>
            <h2 className="font-display text-lg font-bold text-green-950">{student.fullName}</h2>
            <p className="text-xs text-ink-soft">{student.email}</p>
          </div>
        </div>
        <div className="space-y-2 text-sm">
          <div className="flex justify-between border-b border-line pb-2">
            <span className="text-ink-soft">Telefoni</span>
            <span className="font-semibold text-green-950">{student.phone ?? "—"}</span>
          </div>
          <div className="flex justify-between border-b border-line pb-2">
            <span className="text-ink-soft">Yiyandikishije</span>
            <span className="font-semibold text-green-950">
              {new Date(student.createdAt).toLocaleString("rw-RW")}
            </span>
          </div>
          <div className="flex justify-between border-b border-line pb-2">
            <span className="text-ink-soft">Uko bimeze</span>
            <span className="font-semibold text-green-950">{student.status}</span>
          </div>
          {student.approvedByName && (
            <div className="flex justify-between border-b border-line pb-2">
              <span className="text-ink-soft">Yemejwe na</span>
              <span className="font-semibold text-green-950">{student.approvedByName}</span>
            </div>
          )}
        </div>
        <button onClick={onClose} className="btn btn-outline !border-green-900 !text-green-950 w-full justify-center mt-5">
          Funga
        </button>
      </div>
    </div>
  );
}

export default function ApprovalCenterPage() {
  const { user } = useAuth();
  const canBlock = user?.role === "ADMIN" || (user?.permissions ?? []).includes("student.block");

  const [students, setStudents] = useState([]);
  const [total, setTotal] = useState(0);
  const [search, setSearch] = useState("");
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [actingOn, setActingOn] = useState(null);
  const [viewing, setViewing] = useState(null);

  const load = useCallback(async (q = "") => {
    setLoading(true);
    setError("");
    try {
      const res = await fetchPendingStudents(q);
      setStudents(res.data);
      setTotal(res.meta.total);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  useEffect(() => {
    const t = setTimeout(() => load(search), 350); // debounced search
    return () => clearTimeout(t);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [search]);

  async function handleView(id) {
    try {
      const res = await getStudentDetail(id);
      setViewing(res.data);
    } catch (err) {
      setError(err.message);
    }
  }

  async function handleApprove(id) {
    setActingOn(id);
    try {
      await approveStudentRequest(id);
      setStudents((s) => s.filter((st) => st.id !== id));
      setTotal((t) => t - 1);
    } catch (err) {
      setError(err.message);
    } finally {
      setActingOn(null);
    }
  }

  async function handleReject(id) {
    setActingOn(id);
    try {
      await rejectStudentRequest(id);
      setStudents((s) => s.filter((st) => st.id !== id));
      setTotal((t) => t - 1);
    } catch (err) {
      setError(err.message);
    } finally {
      setActingOn(null);
    }
  }

  async function handleBlock(id) {
    setActingOn(id);
    try {
      await blockStudentRequest(id);
      setStudents((s) => s.filter((st) => st.id !== id));
      setTotal((t) => t - 1);
    } catch (err) {
      setError(err.message);
    } finally {
      setActingOn(null);
    }
  }

  return (
    <AdminLayout breadcrumb="Ahabanza / Abanyeshuri" title="Abanyeshuri Bategereje Kwemezwa">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <p className="text-ink-soft text-sm font-semibold">{total} banyeshuri bategereje</p>
        <div className="flex items-center gap-2 bg-surface border border-line rounded-full px-4 py-2.5 w-full sm:w-[280px]">
          <Search size={16} className="text-ink-soft flex-none" />
          <input
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            placeholder="Shakisha..."
            className="w-full text-sm outline-none bg-transparent"
          />
        </div>
      </div>

      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-5">
          {error}
        </div>
      )}

      <div className="card !p-0 overflow-hidden">
        {loading ? (
          <div className="p-10 text-center text-ink-soft text-sm">Turimo gupakira...</div>
        ) : students.length === 0 ? (
          <div className="p-10 text-center text-ink-soft text-sm">
            Nta munyeshuri utegereje kwemezwa kuri ubu.
          </div>
        ) : (
          <table className="w-full text-sm">
            <thead className="bg-cream-2 text-ink-soft text-[11px] uppercase font-bold tracking-wide">
              <tr>
                <th className="text-left px-5 py-3">Umunyeshuri</th>
                <th className="text-left px-5 py-3 hidden sm:table-cell">Email</th>
                <th className="text-left px-5 py-3 hidden md:table-cell">Yiyandikishije</th>
                <th className="text-right px-5 py-3">Ibikorwa</th>
              </tr>
            </thead>
            <tbody>
              {students.map((s) => (
                <tr key={s.id} className="border-t border-line hover:bg-cream-2/40">
                  <td className="px-5 py-4">
                    <div className="flex items-center gap-3">
                      <div className="w-9 h-9 rounded-full bg-gradient-to-br from-green-700 to-green-950 text-white flex items-center justify-center font-display font-bold text-sm flex-none">
                        {s.fullName[0]}
                      </div>
                      <span className="font-semibold text-green-950">{s.fullName}</span>
                    </div>
                  </td>
                  <td className="px-5 py-4 text-ink-soft hidden sm:table-cell">{s.email}</td>
                  <td className="px-5 py-4 text-ink-soft hidden md:table-cell">
                    {new Date(s.createdAt).toLocaleDateString("rw-RW")}
                  </td>
                  <td className="px-5 py-4">
                    <div className="flex justify-end gap-2">
                      <button
                        onClick={() => handleView(s.id)}
                        className="flex items-center gap-1 text-xs font-bold bg-surface border border-line text-ink px-3 py-2 rounded-lg"
                      >
                        <Eye size={14} /> REBA
                      </button>
                      <button
                        disabled={actingOn === s.id}
                        onClick={() => handleApprove(s.id)}
                        className="flex items-center gap-1 text-xs font-bold bg-green-950 text-white px-3 py-2 rounded-lg disabled:opacity-50"
                      >
                        <Check size={14} /> EMEZA
                      </button>
                      <button
                        disabled={actingOn === s.id}
                        onClick={() => handleReject(s.id)}
                        className="flex items-center gap-1 text-xs font-bold bg-surface border border-line text-ink px-3 py-2 rounded-lg disabled:opacity-50"
                      >
                        <X size={14} /> REANGA
                      </button>
                      {canBlock && (
                        <button
                          disabled={actingOn === s.id}
                          onClick={() => handleBlock(s.id)}
                          className="flex items-center gap-1 text-xs font-bold bg-surface border border-red-200 text-red-600 px-3 py-2 rounded-lg disabled:opacity-50"
                        >
                          <ShieldAlert size={14} /> HAGARIKA
                        </button>
                      )}
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>
      <p className="text-xs text-ink-soft mt-4">
        REBA/EMEZA/REANGA bisaba <code>student.approve</code>. HAGARIKA isaba <code>student.block</code> ku giti
        cyayo — leader utabifite ntabwo abibona.
      </p>

      <DetailModal student={viewing} onClose={() => setViewing(null)} />
    </AdminLayout>
  );
}
