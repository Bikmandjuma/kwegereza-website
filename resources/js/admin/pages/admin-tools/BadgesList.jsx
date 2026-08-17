import { useEffect, useState } from 'react'
import * as api from '../../features/admin-tools/adminToolsApi'
import { Button, Input, Card, EmptyState, TableSkeleton, Badge, ResultCount } from '../../components/ui'
import Can from '../../permissions/Can'
import { useToast } from '../../contexts/ToastContext'

const CRITERIA_LABEL = {
  streak_days: 'Iminsi ikurikiranye',
  darsat_completed: 'Amasomo yarangiye',
  courses_completed: 'Amasomo (courses) yarangiye',
  quizzes_passed: 'Ibizamini byatsinzwe',
}

export default function BadgesList() {
  const { push } = useToast()
  const [badges, setBadges] = useState([])
  const [loading, setLoading] = useState(true)
  const [editingId, setEditingId] = useState(null)
  const [showForm, setShowForm] = useState(false)
  const [form, setForm] = useState({ name: '', description: '', icon: '🏅', criteria_type: 'streak_days', criteria_value: 7 })

  function load() {
    setLoading(true)
    api.listBadges().then((res) => setBadges(res.data)).finally(() => setLoading(false))
  }
  useEffect(() => { load() }, [])

  function startEdit(b) {
    setEditingId(b.id)
    setShowForm(true)
    setForm({ name: b.name, description: b.description || '', icon: b.icon, criteria_type: b.criteria_type, criteria_value: b.criteria_value })
  }

  async function handleSave(e) {
    e.preventDefault()
    try {
      if (editingId) {
        await api.updateBadge(editingId, form)
        push('Ikimenyetso cyahinduwe.')
      } else {
        await api.createBadge(form)
        push('Ikimenyetso cyashyizweho.')
      }
      setShowForm(false)
      setEditingId(null)
      setForm({ name: '', description: '', icon: '🏅', criteria_type: 'streak_days', criteria_value: 7 })
      load()
    } catch (err) {
      push(err.response?.data?.message || 'Habaye ikibazo.', 'error')
    }
  }

  async function handleDelete(id) {
    if (!confirm('Wemeza gusiba iki kimenyetso?')) return
    await api.deleteBadge(id)
    push('Ikimenyetso cyasibwe.')
    load()
  }

  return (
    <div className="flex flex-col gap-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="font-display text-2xl text-ink dark:text-sand-50">Ibimenyetso (Badges)</h1>
          <p className="text-sm text-ink/60 dark:text-sand-100/60">Ibimenyetso abanyeshuri bahabwa</p>
          <div className="mt-2"><ResultCount count={loading ? null : badges.length} label="ibimenyetso" /></div>
        </div>
        <Can permission="gamification.manage">
          <Button onClick={() => { setShowForm(!showForm); setEditingId(null); setForm({ name: '', description: '', icon: '🏅', criteria_type: 'streak_days', criteria_value: 7 }) }}>+ Ikimenyetso gishya</Button>
        </Can>
      </div>

      {showForm && (
        <Card className="p-6">
          <form onSubmit={handleSave} className="flex flex-col gap-4">
            <Input label="Izina" required value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} />
            <Input label="Ibisobanuro" value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })} />
            <Input label="Ikimenyetso (emoji)" value={form.icon} onChange={(e) => setForm({ ...form, icon: e.target.value })} />
            <label className="block">
              <span className="mb-1 block text-sm font-medium text-ink dark:text-sand-100">Ubwoko bw'ibipimo</span>
              <select value={form.criteria_type} onChange={(e) => setForm({ ...form, criteria_type: e.target.value })} className="w-full rounded-xl border border-sand-200 bg-white px-3 py-2.5 text-sm text-ink outline-none transition-colors focus:border-teal-600 focus-visible:ring-2 focus-visible:ring-teal-600/20 dark:border-white/10 dark:bg-teal-900/40 dark:text-sand-50 dark:focus:border-gold-400">
                {Object.entries(CRITERIA_LABEL).map(([k, v]) => <option key={k} value={k}>{v}</option>)}
              </select>
            </label>
            <Input label="Umubare" type="number" min="1" required value={form.criteria_value} onChange={(e) => setForm({ ...form, criteria_value: e.target.value })} />
            <Button type="submit" className="w-fit">Bika</Button>
          </form>
        </Card>
      )}

      <Card className="overflow-hidden">
        {loading && <TableSkeleton cols={4} />}
        {!loading && badges.length === 0 && <EmptyState title="Nta kimenyetso kirahari" />}
        {!loading && badges.length > 0 && (
          <div className="overflow-x-auto"><table className="w-full min-w-[640px] text-left text-sm">
            <thead className="bg-sand-100 text-xs uppercase tracking-wide text-ink/50 dark:bg-white/5 dark:text-sand-100/50">
              <tr><th className="px-4 py-3">Ikimenyetso</th><th className="px-4 py-3">Ibipimo</th><th className="px-4 py-3">Abahawe</th><th className="px-4 py-3 text-right">Ibikorwa</th></tr>
            </thead>
            <tbody className="divide-y divide-sand-200 dark:divide-white/10">
              {badges.map((b) => (
                <tr key={b.id}>
                  <td className="px-4 py-3 text-ink dark:text-sand-50">{b.icon} {b.name}</td>
                  <td className="px-4 py-3"><Badge tone="neutral">{CRITERIA_LABEL[b.criteria_type]} ≥ {b.criteria_value}</Badge></td>
                  <td className="px-4 py-3 text-ink/70 dark:text-sand-100/70">{b.user_badges_count ?? 0}</td>
                  <td className="px-4 py-3 text-right">
                    <div className="flex justify-end gap-3">
                      <Can permission="gamification.manage">
                        <button onClick={() => startEdit(b)} className="text-sm text-teal-700 hover:underline dark:text-teal-300">Hindura</button>
                        <button onClick={() => handleDelete(b.id)} className="text-sm text-rose-600 hover:underline dark:text-rose-400">Siba</button>
                      </Can>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table></div>
        )}
      </Card>
    </div>
  )
}
