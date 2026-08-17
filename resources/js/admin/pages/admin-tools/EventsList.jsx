import { useEffect, useState } from 'react'
import * as api from '../../features/admin-tools/adminToolsApi'
import { Button, Input, Card, EmptyState, TableSkeleton, Badge, ResultCount, Pagination } from '../../components/ui'
import Can from '../../permissions/Can'
import { useToast } from '../../contexts/ToastContext'

export default function EventsList() {
  const { push } = useToast()
  const [state, setState] = useState({ loading: true, items: [], meta: null })
  const [search, setSearch] = useState('')
  const [page, setPage] = useState(1)
  const [showForm, setShowForm] = useState(false)
  const [editingId, setEditingId] = useState(null)
  const [form, setForm] = useState({ title: '', location: '', starts_at: '', capacity: '', status: 'draft' })

  function load() {
    setState((s) => ({ ...s, loading: true }))
    api.listEvents(page, search).then((res) => setState({ loading: false, items: res.data, meta: res.meta }))
  }
  useEffect(() => { load() }, [page, search])

  function startEdit(ev) {
    setEditingId(ev.id)
    setShowForm(true)
    setForm({
      title: ev.title, location: ev.location || '',
      starts_at: ev.starts_at ? ev.starts_at.slice(0, 16) : '',
      capacity: ev.capacity || '', status: ev.status,
    })
  }

  async function handleSave(e) {
    e.preventDefault()
    try {
      const payload = { ...form, capacity: form.capacity || null }
      if (editingId) {
        await api.updateEvent(editingId, payload)
        push('Igikorwa cyahinduwe.')
      } else {
        await api.createEvent(payload)
        push('Igikorwa cyashyizweho.')
      }
      setShowForm(false)
      setEditingId(null)
      setForm({ title: '', location: '', starts_at: '', capacity: '', status: 'draft' })
      load()
    } catch (err) {
      push(err.response?.data?.message || 'Habaye ikibazo.', 'error')
    }
  }

  async function handleDelete(id) {
    if (!confirm('Wemeza gusiba iki gikorwa?')) return
    await api.deleteEvent(id)
    push('Igikorwa cyasibwe.')
    load()
  }

  return (
    <div className="flex flex-col gap-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="font-display text-2xl text-ink dark:text-sand-50">Ibikorwa (Events)</h1>
          <p className="text-sm text-ink/60 dark:text-sand-100/60">Tegura ibikorwa n'ubwiyandikishe</p>
          <div className="mt-2"><ResultCount count={state.meta?.total} label="ibikorwa" /></div>
        </div>
        <Can permission="events.create">
          <Button onClick={() => { setShowForm(!showForm); setEditingId(null); setForm({ title: '', location: '', starts_at: '', capacity: '', status: 'draft' }) }}>+ Igikorwa gishya</Button>
        </Can>
      </div>

      {showForm && (
        <Card className="p-6">
          <form onSubmit={handleSave} className="flex flex-col gap-4">
            <Input label="Umutwe" required value={form.title} onChange={(e) => setForm({ ...form, title: e.target.value })} />
            <Input label="Aho biba" value={form.location} onChange={(e) => setForm({ ...form, location: e.target.value })} />
            <Input label="Itariki/Igihe" type="datetime-local" required value={form.starts_at} onChange={(e) => setForm({ ...form, starts_at: e.target.value })} />
            <Input label="Umubare w'abemerewe (si ngombwa)" type="number" value={form.capacity} onChange={(e) => setForm({ ...form, capacity: e.target.value })} />
            <label className="block">
              <span className="mb-1 block text-sm font-medium text-ink dark:text-sand-100">Imiterere</span>
              <select value={form.status} onChange={(e) => setForm({ ...form, status: e.target.value })} className="w-full rounded-xl border border-sand-200 bg-white px-3 py-2.5 text-sm text-ink outline-none transition-colors focus:border-teal-600 focus-visible:ring-2 focus-visible:ring-teal-600/20 dark:border-white/10 dark:bg-teal-900/40 dark:text-sand-50 dark:focus:border-gold-400">
                <option value="draft">Ntibirasohoka</option>
                <option value="published">Byasohotse</option>
                <option value="cancelled">Byahagaritswe</option>
              </select>
            </label>
            <Button type="submit" className="w-fit">Bika</Button>
          </form>
        </Card>
      )}

      <Card className="p-4">
        <input
          value={search}
          onChange={(e) => { setPage(1); setSearch(e.target.value) }}
          placeholder="Shakisha igikorwa..."
          className="w-full max-w-xs rounded-xl border border-sand-200 bg-white px-3 py-2.5 text-sm text-ink outline-none transition-colors placeholder:text-ink/35 focus:border-teal-600 focus-visible:ring-2 focus-visible:ring-teal-600/20 dark:border-white/10 dark:bg-teal-900/40 dark:text-sand-50 dark:placeholder:text-sand-100/30 dark:focus:border-gold-400"
        />
      </Card>

      <Card className="overflow-hidden">
        {state.loading && <TableSkeleton cols={5} />}
        {!state.loading && state.items.length === 0 && <EmptyState title="Nta gikorwa kirahari" />}
        {!state.loading && state.items.length > 0 && (
          <div className="overflow-x-auto"><table className="w-full min-w-[640px] text-left text-sm">
            <thead className="bg-sand-100 text-xs uppercase tracking-wide text-ink/50 dark:bg-white/5 dark:text-sand-100/50">
              <tr><th className="px-4 py-3">Umutwe</th><th className="px-4 py-3">Itariki</th><th className="px-4 py-3">Abiyandikishije</th><th className="px-4 py-3">Imiterere</th><th className="px-4 py-3 text-right">Ibikorwa</th></tr>
            </thead>
            <tbody className="divide-y divide-sand-200 dark:divide-white/10">
              {state.items.map((ev) => (
                <tr key={ev.id}>
                  <td className="px-4 py-3 text-ink dark:text-sand-50">{ev.title}</td>
                  <td className="px-4 py-3 text-ink/70 dark:text-sand-100/70">{new Date(ev.starts_at).toLocaleString()}</td>
                  <td className="px-4 py-3 text-ink/70 dark:text-sand-100/70">{ev.registrations_count}{ev.capacity ? ` / ${ev.capacity}` : ''}</td>
                  <td className="px-4 py-3"><Badge tone={ev.status === 'published' ? 'success' : ev.status === 'cancelled' ? 'danger' : 'neutral'}>{ev.status}</Badge></td>
                  <td className="px-4 py-3 text-right">
                    <div className="flex justify-end gap-3">
                      <Can permission="events.update">
                        <button onClick={() => startEdit(ev)} className="text-sm text-teal-700 hover:underline dark:text-teal-300">Hindura</button>
                      </Can>
                      <Can permission="events.delete">
                        <button onClick={() => handleDelete(ev.id)} className="text-sm text-rose-600 hover:underline dark:text-rose-400">Siba</button>
                      </Can>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table></div>
        )}
        <Pagination meta={state.meta} page={page} onChange={setPage} />
      </Card>
    </div>
  )
}
