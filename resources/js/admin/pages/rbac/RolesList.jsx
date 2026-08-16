import { useEffect, useState } from 'react'
import * as rbacApi from '../../features/rbac/rbacApi'
import { Button, Input, Card, EmptyState, Badge, TableSkeleton, ResultCount } from '../../components/ui'
import Can from '../../permissions/Can'
import { useToast } from '../../contexts/ToastContext'

export default function RolesList() {
  const { push } = useToast()
  const [roles, setRoles] = useState([])
  const [permissions, setPermissions] = useState([])
  const [search, setSearch] = useState('')
  const [loading, setLoading] = useState(true)
  const [editingId, setEditingId] = useState(null)
  const [form, setForm] = useState({ name: '', description: '', permissions: [] })
  const [showCreate, setShowCreate] = useState(false)

  function load() {
    setLoading(true)
    Promise.all([rbacApi.listRoles(), rbacApi.listPermissions()])
      .then(([rolesData, permsRes]) => {
        setRoles(rolesData)
        setPermissions(permsRes.data)
      })
      .finally(() => setLoading(false))
  }

  useEffect(() => { load() }, [])

  async function handleEdit(role) {
    const full = await rbacApi.getRole(role.id)
    setEditingId(role.id)
    setForm({ name: full.name, description: full.description || '', permissions: full.permissions.map((p) => p.id) })
  }

  async function handleSave() {
    try {
      if (editingId) {
        await rbacApi.updateRole(editingId, form)
        push('Uruhare rwahinduwe.')
      } else {
        await rbacApi.createRole(form)
        push('Uruhare rushya rwashyizweho.')
      }
      setEditingId(null)
      setShowCreate(false)
      setForm({ name: '', description: '', permissions: [] })
      load()
    } catch (err) {
      push(err.response?.data?.message || 'Habaye ikibazo.', 'error')
    }
  }

  async function handleDelete(role) {
    if (!confirm(`Wemeza gusiba "${role.name}"?`)) return
    try {
      await rbacApi.deleteRole(role.id)
      push('Uruhare rwasibwe.')
      load()
    } catch (err) {
      push(err.response?.data?.message || 'Ntibishoboka gusiba uru ruhare.', 'error')
    }
  }

  function togglePermission(id) {
    setForm((f) => ({
      ...f,
      permissions: f.permissions.includes(id) ? f.permissions.filter((p) => p !== id) : [...f.permissions, id],
    }))
  }

  const isFormOpen = editingId !== null || showCreate

  return (
    <div className="flex flex-col gap-6">
      <div className="flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 className="font-display text-2xl text-ink dark:text-sand-50">Amashimikiro (Roles)</h1>
          <p className="text-sm text-ink/60 dark:text-sand-100/60">Genzura amashimikiro n'uburenganzira bwayo</p>
          <div className="mt-2"><ResultCount count={loading ? null : roles.length} label="amashimikiro" /></div>
        </div>
        <Can permission="roles.create">
          <Button onClick={() => { setShowCreate(true); setEditingId(null); setForm({ name: '', description: '', permissions: [] }) }}>
            + Ishimikiro rishya
          </Button>
        </Can>
      </div>

      {isFormOpen && (
        <Card className="p-6">
          <h2 className="mb-4 font-display text-lg text-ink dark:text-sand-50">{editingId ? 'Hindura ishimikiro' : 'Ishimikiro rishya'}</h2>
          <div className="flex flex-col gap-4">
            <Input label="Izina" required value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} />
            <Input label="Ibisobanuro" value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })} />

            <div>
              <span className="mb-2 block text-sm font-medium text-ink dark:text-sand-100">Uburenganzira</span>
              <div className="grid max-h-72 grid-cols-2 gap-x-4 gap-y-1 overflow-y-auto rounded-lg border border-sand-200 p-3 sm:grid-cols-3 dark:border-white/10">
                {permissions.map((p) => (
                  <label key={p.id} className="flex items-center gap-2 text-sm">
                    <input type="checkbox" checked={form.permissions.includes(p.id)} onChange={() => togglePermission(p.id)} />
                    {p.slug}
                  </label>
                ))}
              </div>
            </div>

            <div className="flex gap-3">
              <Button onClick={handleSave}>Bika</Button>
              <Button variant="secondary" onClick={() => { setEditingId(null); setShowCreate(false) }}>Reka</Button>
            </div>
          </div>
        </Card>
      )}

      <Card className="p-4">
        <input
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          placeholder="Shakisha ishimikiro..."
          className="w-full max-w-xs rounded-xl border border-sand-200 bg-white px-3 py-2.5 text-sm text-ink outline-none transition-colors placeholder:text-ink/35 focus:border-teal-600 focus-visible:ring-2 focus-visible:ring-teal-600/20 dark:border-white/10 dark:bg-teal-900/40 dark:text-sand-50 dark:placeholder:text-sand-100/30 dark:focus:border-gold-400"
        />
      </Card>

      <Card className="overflow-hidden">
        {loading && <TableSkeleton cols={4} />}
        {!loading && roles.length === 0 && <EmptyState title="Nta shimikiro rihari" description="Ongeraho ishimikiro rya mbere." />}
        {!loading && roles.length > 0 && (() => {
          // Small, fully-loaded list already (no server pagination on
          // this endpoint) — filtering client-side is simpler and
          // instant, no extra round-trip needed.
          const filtered = search.trim()
            ? roles.filter((r) => r.name.toLowerCase().includes(search.trim().toLowerCase()))
            : roles
          if (filtered.length === 0) {
            return <EmptyState title="Nta shimikiro rihuye n'ubushakashatsi" />
          }
          return (
          <div className="overflow-x-auto"><table className="w-full min-w-[640px] text-left text-sm">
            <thead className="bg-sand-100 text-xs uppercase tracking-wide text-ink/50 dark:bg-white/5 dark:text-sand-100/50">
              <tr>
                <th className="px-4 py-3">Izina</th>
                <th className="px-4 py-3">Uburenganzira</th>
                <th className="px-4 py-3">Abakoresha</th>
                <th className="px-4 py-3 text-right">Ibikorwa</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-sand-200 dark:divide-white/10">
              {filtered.map((r) => (
                <tr key={r.id}>
                  <td className="px-4 py-3 font-medium text-ink dark:text-sand-50">
                    {r.name} {r.is_super && <Badge tone="warning">Super Admin</Badge>}
                  </td>
                  <td className="px-4 py-3 text-ink/70 dark:text-sand-100/70">{r.permissions_count}</td>
                  <td className="px-4 py-3 text-ink/70 dark:text-sand-100/70">{r.owners_count}</td>
                  <td className="px-4 py-3 text-right">
                    {!r.is_super && (
                      <div className="flex justify-end gap-3">
                        <Can permission="roles.update">
                          <button onClick={() => handleEdit(r)} className="text-sm text-teal-700 hover:underline dark:text-teal-300">Hindura</button>
                        </Can>
                        <Can permission="roles.delete">
                          <button onClick={() => handleDelete(r)} className="text-sm text-rose-600 hover:underline dark:text-rose-400">Siba</button>
                        </Can>
                      </div>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table></div>
          )
        })()}
      </Card>
    </div>
  )
}
