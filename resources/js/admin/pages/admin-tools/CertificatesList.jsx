import { useEffect, useState } from 'react'
import * as api from '../../features/admin-tools/adminToolsApi'
import { Button, Input, Card, EmptyState, TableSkeleton, ResultCount, Pagination } from '../../components/ui'
import Can from '../../permissions/Can'
import { useToast } from '../../contexts/ToastContext'

export default function CertificatesList() {
  const { push } = useToast()
  const [state, setState] = useState({ loading: true, items: [], meta: null })
  const [search, setSearch] = useState('')
  const [page, setPage] = useState(1)
  const [showForm, setShowForm] = useState(false)
  const [form, setForm] = useState({ user_id: '', title: '', course_id: '' })

  function load() {
    setState((s) => ({ ...s, loading: true }))
    api.listCertificates(page, search).then((res) => setState({ loading: false, items: res.data, meta: res.meta }))
  }
  useEffect(() => { load() }, [page, search])

  async function handleIssue(e) {
    e.preventDefault()
    try {
      await api.issueCertificate({ user_id: Number(form.user_id), title: form.title, course_id: form.course_id ? Number(form.course_id) : null })
      push('Icyemezo cyatanzwe.')
      setShowForm(false)
      setForm({ user_id: '', title: '', course_id: '' })
      load()
    } catch (err) {
      push(err.response?.data?.message || 'Habaye ikibazo.', 'error')
    }
  }

  return (
    <div className="flex flex-col gap-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="font-display text-2xl text-ink dark:text-sand-50">Ibyemezo (Certificates)</h1>
          <p className="text-sm text-ink/60 dark:text-sand-100/60">Ibyemezo byatanzwe ku banyeshuri</p>
          <div className="mt-2"><ResultCount count={state.meta?.total} label="ibyemezo" /></div>
        </div>
        <Can permission="certificates.issue">
          <Button onClick={() => setShowForm(!showForm)}>+ Tanga icyemezo</Button>
        </Can>
      </div>

      {showForm && (
        <Card className="p-6">
          <form onSubmit={handleIssue} className="flex flex-col gap-4">
            <Input label="Student ID" type="number" required value={form.user_id} onChange={(e) => setForm({ ...form, user_id: e.target.value })} />
            <Input label="Umutwe w'icyemezo" required value={form.title} onChange={(e) => setForm({ ...form, title: e.target.value })} />
            <Input label="Course ID (si ngombwa)" type="number" value={form.course_id} onChange={(e) => setForm({ ...form, course_id: e.target.value })} />
            <Button type="submit" className="w-fit">Tanga</Button>
          </form>
        </Card>
      )}

      <Card className="p-4">
        <input
          value={search}
          onChange={(e) => { setPage(1); setSearch(e.target.value) }}
          placeholder="Shakisha icyemezo cyangwa umunyeshuri..."
          className="w-full max-w-xs rounded-xl border border-sand-200 bg-white px-3 py-2.5 text-sm text-ink outline-none transition-colors placeholder:text-ink/35 focus:border-teal-600 focus-visible:ring-2 focus-visible:ring-teal-600/20 dark:border-white/10 dark:bg-teal-900/40 dark:text-sand-50 dark:placeholder:text-sand-100/30 dark:focus:border-gold-400"
        />
      </Card>

      <Card className="overflow-hidden">
        {state.loading && <TableSkeleton cols={4} />}
        {!state.loading && state.items.length === 0 && <EmptyState title="Nta cyemezo kirahari" />}
        {!state.loading && state.items.length > 0 && (
          <div className="overflow-x-auto"><table className="w-full min-w-[640px] text-left text-sm">
            <thead className="bg-sand-100 text-xs uppercase tracking-wide text-ink/50 dark:bg-white/5 dark:text-sand-100/50">
              <tr><th className="px-4 py-3">Nomero</th><th className="px-4 py-3">Umunyeshuri</th><th className="px-4 py-3">Umutwe</th><th className="px-4 py-3">Itariki</th></tr>
            </thead>
            <tbody className="divide-y divide-sand-200 dark:divide-white/10">
              {state.items.map((c) => (
                <tr key={c.id}>
                  <td className="px-4 py-3 font-mono text-xs text-ink/70 dark:text-sand-100/70">{c.certificate_number}</td>
                  <td className="px-4 py-3 text-ink dark:text-sand-50">{c.user?.name}</td>
                  <td className="px-4 py-3 text-ink/70 dark:text-sand-100/70">{c.title}</td>
                  <td className="px-4 py-3 text-ink/50 dark:text-sand-100/50">{new Date(c.issued_at).toLocaleDateString()}</td>
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
