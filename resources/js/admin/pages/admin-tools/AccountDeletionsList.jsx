import { useEffect, useState } from 'react'
import * as api from '../../features/admin-tools/adminToolsApi'
import { Card, EmptyState, Badge, TableSkeleton, ResultCount, Pagination } from '../../components/ui'
import { useToast } from '../../contexts/ToastContext'

export default function AccountDeletionsList() {
  const { push } = useToast()
  const [state, setState] = useState({ loading: true, items: [], meta: null })
  const [search, setSearch] = useState('')
  const [page, setPage] = useState(1)

  function load() {
    setState((s) => ({ ...s, loading: true }))
    api.listAccountDeletions(page, search).then((res) => setState({ loading: false, items: res.data, meta: res.meta }))
  }
  useEffect(() => { load() }, [page, search])

  async function handleApprove(id) {
    if (!confirm('Wemeza guhagarika iyi konti?')) return
    await api.approveAccountDeletion(id)
    push('Konti yahagaritswe.')
    load()
  }
  async function handleReject(id) {
    await api.rejectAccountDeletion(id)
    push('Icyifuzo cyanzwe.')
    load()
  }

  return (
    <div className="flex flex-col gap-6">
      <div>
        <h1 className="font-display text-2xl text-ink dark:text-sand-50">Ibyifuzo byo Gusiba Konti</h1>
        <p className="text-sm text-ink/60 dark:text-sand-100/60">Genzura ibyifuzo by'abanyeshuri byo gusiba konti zabo</p>
        <div className="mt-2"><ResultCount count={state.meta?.total} label="ibyifuzo" /></div>
      </div>

      <Card className="p-4">
        <input
          value={search}
          onChange={(e) => { setPage(1); setSearch(e.target.value) }}
          placeholder="Shakisha umunyeshuri..."
          className="w-full max-w-xs rounded-xl border border-sand-200 bg-white px-3 py-2.5 text-sm text-ink outline-none transition-colors placeholder:text-ink/35 focus:border-teal-600 focus-visible:ring-2 focus-visible:ring-teal-600/20 dark:border-white/10 dark:bg-teal-900/40 dark:text-sand-50 dark:placeholder:text-sand-100/30 dark:focus:border-gold-400"
        />
      </Card>

      <Card className="overflow-hidden">
        {state.loading && <TableSkeleton cols={4} />}
        {!state.loading && state.items.length === 0 && <EmptyState title="Nta cyifuzo kirahari" />}
        {!state.loading && state.items.length > 0 && (
          <div className="overflow-x-auto"><table className="w-full min-w-[640px] text-left text-sm">
            <thead className="bg-sand-100 text-xs uppercase tracking-wide text-ink/50 dark:bg-white/5 dark:text-sand-100/50">
              <tr><th className="px-4 py-3">Umunyeshuri</th><th className="px-4 py-3">Itariki</th><th className="px-4 py-3">Imiterere</th><th className="px-4 py-3 text-right">Ibikorwa</th></tr>
            </thead>
            <tbody className="divide-y divide-sand-200 dark:divide-white/10">
              {state.items.map((r) => (
                <tr key={r.id}>
                  <td className="px-4 py-3 text-ink dark:text-sand-50">{r.user?.name}</td>
                  <td className="px-4 py-3 text-ink/50 dark:text-sand-100/50">{new Date(r.created_at).toLocaleDateString()}</td>
                  <td className="px-4 py-3">
                    <Badge tone={r.status === 'approved' ? 'danger' : r.status === 'rejected' ? 'neutral' : 'warning'}>{r.status}</Badge>
                  </td>
                  <td className="px-4 py-3 text-right">
                    {r.status === 'pending' && (
                      <div className="flex justify-end gap-3">
                        <button onClick={() => handleApprove(r.id)} className="text-sm text-rose-600 hover:underline dark:text-rose-400">Emeza guhagarika</button>
                        <button onClick={() => handleReject(r.id)} className="text-sm text-teal-700 hover:underline dark:text-teal-300">Anga</button>
                      </div>
                    )}
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
