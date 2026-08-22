import { useEffect, useState, useCallback } from "react";
import { useNavigate } from "react-router-dom";
import { Eye, EyeOff, PenLine, Plus, Trash2 } from "lucide-react";
import { useAuth } from "../../context/AuthContext.jsx";
import AdminLayout from "../../layouts/AdminLayout.jsx";
import {
  createIfaida,
  deleteIfaida,
  listMyIfaida,
  publishIfaida,
  unpublishIfaida,
} from "../../api/ifaida.js";

export default function IfaidaListPage() {
  const { user } = useAuth();
  const navigate = useNavigate();
  const isAdmin = user?.role === "ADMIN";
  const perms = user?.permissions ?? [];
  const canCreate = isAdmin || perms.includes("ifaida.create");
  const canDelete = isAdmin || perms.includes("ifaida.delete");
  const canPublish = isAdmin || perms.includes("ifaida.publish");

  const [posts, setPosts] = useState([]);
  const [filter, setFilter] = useState("");
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [busyId, setBusyId] = useState(null);

  const load = useCallback(async (status = "") => {
    setLoading(true);
    setError("");
    try {
      const res = await listMyIfaida(status);
      setPosts(res.data);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    load(filter);
  }, [filter, load]);

  async function handleCreate() {
    try {
      const res = await createIfaida("Umutwe mushya w'Ifaida");
      navigate(`/leader/ifaida/${res.data.id}`);
    } catch (err) {
      setError(err.message);
    }
  }

  async function handleTogglePublish(post) {
    setBusyId(post.id);
    try {
      const res =
        post.status === "PUBLISHED" ? await unpublishIfaida(post.id) : await publishIfaida(post.id);
      setPosts((prev) => prev.map((p) => (p.id === post.id ? res.data : p)));
    } catch (err) {
      setError(err.message);
    } finally {
      setBusyId(null);
    }
  }

  async function handleDelete(post) {
    setBusyId(post.id);
    try {
      await deleteIfaida(post.id);
      setPosts((prev) => prev.filter((p) => p.id !== post.id));
    } catch (err) {
      setError(err.message);
    } finally {
      setBusyId(null);
    }
  }

  return (
    <AdminLayout breadcrumb="Ahabanza / Ifaida" title="Ifaida">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div className="flex gap-1 bg-cream-2 rounded-full p-1">
          {[
            { key: "", label: "Byose" },
            { key: "DRAFT", label: "Imishinga" },
            { key: "PUBLISHED", label: "Byatangajwe" },
          ].map((f) => (
            <button
              key={f.key}
              onClick={() => setFilter(f.key)}
              className={`px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors ${
                filter === f.key ? "bg-green-950 text-white" : "text-ink-soft"
              }`}
            >
              {f.label}
            </button>
          ))}
        </div>
        {canCreate && (
          <button onClick={handleCreate} className="btn btn-gold !py-2.5">
            <Plus size={16} /> Andika Ifaida Nshya
          </button>
        )}
      </div>

      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-5">{error}</div>
      )}

      <div className="card !p-0 overflow-hidden">
        {loading ? (
          <div className="p-10 text-center text-ink-soft text-sm">Turimo gupakira...</div>
        ) : posts.length === 0 ? (
          <div className="p-10 text-center text-ink-soft text-sm">
            {canCreate ? "Nta Ifaida wanditse. Kanda hejuru utangire iy'ambere." : "Nta Ifaida ihari."}
          </div>
        ) : (
          <table className="w-full text-sm">
            <thead className="bg-cream-2 text-ink-soft text-[11px] uppercase font-bold tracking-wide">
              <tr>
                <th className="text-left px-5 py-3">Umutwe</th>
                <th className="text-left px-5 py-3 hidden sm:table-cell">Icyiciro</th>
                <th className="text-left px-5 py-3">Uko bimeze</th>
                <th className="text-left px-5 py-3 hidden md:table-cell">Igihe cyo gusoma</th>
                <th className="text-right px-5 py-3">Ibikorwa</th>
              </tr>
            </thead>
            <tbody>
              {posts.map((p) => (
                <tr key={p.id} className="border-t border-line hover:bg-cream-2/40">
                  <td className="px-5 py-4 font-semibold text-green-950">{p.title}</td>
                  <td className="px-5 py-4 text-ink-soft hidden sm:table-cell">{p.category || "—"}</td>
                  <td className="px-5 py-4">
                    <span
                      className={`text-xs font-bold px-2.5 py-1 rounded-full ${
                        p.status === "PUBLISHED" ? "bg-emerald-50 text-emerald-700" : "bg-amber-50 text-amber-700"
                      }`}
                    >
                      {p.status === "PUBLISHED" ? "Byatangajwe" : "Umushinga"}
                    </span>
                  </td>
                  <td className="px-5 py-4 text-ink-soft hidden md:table-cell">{p.readingMinutes} min</td>
                  <td className="px-5 py-4">
                    <div className="flex justify-end gap-2">
                      <button
                        onClick={() => navigate(`/leader/ifaida/${p.id}`)}
                        className="flex items-center gap-1 text-xs font-bold bg-surface border border-line px-3 py-2 rounded-lg"
                      >
                        <PenLine size={13} /> Hindura
                      </button>
                      {canPublish && (
                        <button
                          disabled={busyId === p.id}
                          onClick={() => handleTogglePublish(p)}
                          className="flex items-center gap-1 text-xs font-bold bg-green-950 text-white px-3 py-2 rounded-lg disabled:opacity-50"
                        >
                          {p.status === "PUBLISHED" ? <EyeOff size={13} /> : <Eye size={13} />}
                          {p.status === "PUBLISHED" ? "Hagarika" : "Tangaza"}
                        </button>
                      )}
                      {canDelete && (
                        <button
                          disabled={busyId === p.id}
                          onClick={() => handleDelete(p)}
                          className="flex items-center gap-1 text-xs font-bold bg-surface border border-red-200 text-red-600 px-3 py-2 rounded-lg disabled:opacity-50"
                        >
                          <Trash2 size={13} />
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
    </AdminLayout>
  );
}
