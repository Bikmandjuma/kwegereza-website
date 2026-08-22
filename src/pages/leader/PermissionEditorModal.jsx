import { useState } from "react";
import { X, ShieldCheck, ArrowUpCircle, ArrowDownCircle, Ban, CheckCircle2 } from "lucide-react";
import { groupedCatalog } from "../../data/permissionCatalog.js";
import {
  updateUserRole,
  updateUserPermissions,
  blockAnyUser,
  unblockAnyUser,
} from "../../api/adminUsers.js";

const STATUS_LABELS = {
  PENDING: "Bitegereje",
  ACTIVE: "Bikora",
  INACTIVE: "Ntibikora",
  SUSPENDED: "Byahagaritswe by'agateganyo",
  BLOCKED: "Byahagaritswe",
  REJECTED: "Byanzwe",
};

export default function PermissionEditorModal({ targetUser, onClose, onSaved }) {
  const [permissions, setPermissions] = useState(targetUser.permissions ?? []);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");
  const groups = groupedCatalog();

  function togglePermission(key) {
    setPermissions((prev) => (prev.includes(key) ? prev.filter((p) => p !== key) : [...prev, key]));
  }

  function toggleGroup(groupKeys, allSelected) {
    setPermissions((prev) =>
      allSelected ? prev.filter((p) => !groupKeys.includes(p)) : Array.from(new Set([...prev, ...groupKeys]))
    );
  }

  async function handleSavePermissions() {
    setBusy(true);
    setError("");
    try {
      const res = await updateUserPermissions(targetUser.id, permissions);
      onSaved(res.data.user);
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy(false);
    }
  }

  async function handlePromote() {
    setBusy(true);
    setError("");
    try {
      const res = await updateUserRole(targetUser.id, "LEADER");
      onSaved(res.data.user);
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy(false);
    }
  }

  async function handleDemote() {
    setBusy(true);
    setError("");
    try {
      const res = await updateUserRole(targetUser.id, "STUDENT");
      onSaved(res.data.user);
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy(false);
    }
  }

  async function handleBlock() {
    setBusy(true);
    setError("");
    try {
      const res = await blockAnyUser(targetUser.id);
      onSaved(res.data.user);
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy(false);
    }
  }

  async function handleUnblock() {
    setBusy(true);
    setError("");
    try {
      const res = await unblockAnyUser(targetUser.id);
      onSaved(res.data.user);
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy(false);
    }
  }

  return (
    <div className="fixed inset-0 z-[150] flex items-start sm:items-center justify-center p-4 overflow-y-auto">
      <button className="fixed inset-0 bg-black/40" onClick={onClose} aria-label="Funga" />
      <div className="relative bg-surface rounded-2xl shadow-2xl w-full max-w-[640px] my-8 overflow-hidden">
        <div className="px-6 py-4 border-b border-line flex items-center justify-between">
          <div>
            <h2 className="font-display text-lg font-bold text-green-950">{targetUser.fullName}</h2>
            <p className="text-xs text-ink-soft">{targetUser.email}</p>
          </div>
          <button onClick={onClose} className="w-8 h-8 rounded-full bg-cream-2 flex items-center justify-center">
            <X size={16} />
          </button>
        </div>

        <div className="px-6 py-4 border-b border-line flex flex-wrap items-center gap-2">
          <span className="text-xs font-bold text-ink-soft uppercase">Uruhare:</span>
          <span className="text-xs font-bold bg-green-950 text-white px-2.5 py-1 rounded-full">{targetUser.role}</span>
          <span className="text-xs font-bold text-ink-soft uppercase ml-3">Uko bimeze:</span>
          <span className="text-xs font-bold bg-cream-2 text-ink px-2.5 py-1 rounded-full">
            {STATUS_LABELS[targetUser.status] ?? targetUser.status}
          </span>

          <div className="ml-auto flex gap-2">
            {targetUser.role === "STUDENT" && (
              <button
                disabled={busy}
                onClick={handlePromote}
                className="flex items-center gap-1.5 text-xs font-bold bg-green-950 text-white px-3 py-2 rounded-lg disabled:opacity-50"
              >
                <ArrowUpCircle size={14} /> Guha uruhare rw'Umuyobozi
              </button>
            )}
            {targetUser.role === "LEADER" && (
              <button
                disabled={busy}
                onClick={handleDemote}
                className="flex items-center gap-1.5 text-xs font-bold bg-surface border border-line px-3 py-2 rounded-lg disabled:opacity-50"
              >
                <ArrowDownCircle size={14} /> Garura kuri Umunyeshuri
              </button>
            )}
            {targetUser.status === "BLOCKED" ? (
              <button
                disabled={busy}
                onClick={handleUnblock}
                className="flex items-center gap-1.5 text-xs font-bold bg-emerald-600 text-white px-3 py-2 rounded-lg disabled:opacity-50"
              >
                <CheckCircle2 size={14} /> Subiza
              </button>
            ) : (
              <button
                disabled={busy}
                onClick={handleBlock}
                className="flex items-center gap-1.5 text-xs font-bold bg-surface border border-red-200 text-red-600 px-3 py-2 rounded-lg disabled:opacity-50"
              >
                <Ban size={14} /> Hagarika
              </button>
            )}
          </div>
        </div>

        {error && (
          <div className="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
            {error}
          </div>
        )}

        {targetUser.role === "LEADER" ? (
          <div className="px-6 py-4 max-h-[420px] overflow-y-auto">
            <div className="flex items-center gap-2 mb-4 text-sm font-bold text-green-950">
              <ShieldCheck size={16} /> Uburenganzira (Permissions)
            </div>
            {Object.entries(groups).map(([category, perms]) => {
              const keys = perms.map((p) => p.key);
              const allSelected = keys.every((k) => permissions.includes(k));
              return (
                <div key={category} className="mb-4">
                  <div className="flex items-center justify-between mb-1.5">
                    <span className="text-xs font-extrabold text-ink-soft uppercase tracking-wide">{category}</span>
                    <button
                      onClick={() => toggleGroup(keys, allSelected)}
                      className="text-[11px] font-bold text-gold-600"
                    >
                      {allSelected ? "Kuraho byose" : "Hitamo byose"}
                    </button>
                  </div>
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                    {perms.map((p) => (
                      <label
                        key={p.key}
                        className="flex items-center gap-2 text-[13px] text-ink px-2.5 py-1.5 rounded-lg hover:bg-cream-2 cursor-pointer"
                      >
                        <input
                          type="checkbox"
                          checked={permissions.includes(p.key)}
                          onChange={() => togglePermission(p.key)}
                          className="accent-green-900"
                        />
                        {p.label}
                      </label>
                    ))}
                  </div>
                </div>
              );
            })}
          </div>
        ) : (
          <div className="px-6 py-8 text-center text-sm text-ink-soft">
            Uburenganzira buhabwa gusa abafite uruhare rw'Umuyobozi (LEADER). Muhe uruhare rw'Umuyobozi mbere.
          </div>
        )}

        {targetUser.role === "LEADER" && (
          <div className="px-6 py-4 border-t border-line flex justify-end gap-2">
            <button onClick={onClose} className="text-sm font-bold text-ink-soft px-4 py-2.5">
              Reka
            </button>
            <button
              disabled={busy}
              onClick={handleSavePermissions}
              className="btn btn-gold !py-2.5 disabled:opacity-50"
            >
              {busy ? "Turabika..." : "Bika Uburenganzira"}
            </button>
          </div>
        )}
      </div>
    </div>
  );
}
