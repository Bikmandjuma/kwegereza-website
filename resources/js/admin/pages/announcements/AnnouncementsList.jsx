import { useEffect, useState, useCallback } from 'react'
import { Link } from 'react-router-dom'
import * as announcementsApi from '../../features/announcements/announcementsApi'
import { Button, Card, EmptyState, TableSkeleton, Badge, ResultCount } from '../../components/ui'
import Can from '../../permissions/Can'
import { useToast } from '../../contexts/ToastContext'

const STATUS_LABEL = { live: 'Live', upcoming: 'Asigaye', done: 'Ryarangiye' }

export default function AnnouncementsList() {
  const [state, setState] = useState({ loading: true, error: null, items: [], meta: null })
  const [search, setSearch] = useState('')
  const [page, setPage] = useState(1)
  const { push } = useToast()

  const load = useCallback(async () => {
    setState((s) => ({ ...s, loading: true, error: null }))
    try {
      const res = await announcementsApi.listAnnouncements({ page, search })
      setState({ loading: false, error: null, items: res.data, meta: res.meta })
    } catch (err) {
      setState({
        loading: false,
        error: err.response?.status === 403 ? 'Ntabwo ufite uburenganzira bwo kubona amatangazo.' : 'Ntibishoboka gushaka amatangazo.',
        items: [],
        meta: null,
      })
    }
  }, [page, search])

  useEffect(() => { load() }, [load])

  async function handleToggle(item) {
    try {
      await announcementsApi.togglePublish(item.id)
      push(item.is_published ? 'Itangazo ryahishwe.' : 'Itangazo ryerekanwa.')
      load()
    } catch {
      push('Ntibishoboka gukora iki gikorwa.', 'error')
    }
  }

  async function handleDelete(item) {
    if (!confirm(`Wemeza gusiba "${item.title}"?`)) return
    try {
      await announcementsApi.deleteAnnouncement(item.id)
      push('Itangazo ryasibwe.')
      load()
    } catch {
      push('Ntibishoboka gusiba iri tangazo.', 'error')
    }
  }

  return (
    <div className="flex flex-col gap-6">
      <div className="flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 className="font-display text-2xl text-ink dark:text-sand-50">Amatangazo</h1>
          <p className="text-sm text-ink/60 dark:text-sand-100/60">Menyesha abanyeshuri amakuru n'ibikorwa bishya</p>
          <div className="mt-2"><ResultCount count={state.meta?.total} label="amatangazo" /></div>
        </div>
        <Can permission="amatangazo.create">
          <Link to="/amatangazo/new" className="inline-flex items-center justify-center gap-2 rounded-lg bg-teal-700 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-teal-900">
            + Itangazo rishya
          </Link>
        </Can>
      </div>

      <Card className="p-4">
        <input
          value={search}
          onChange={(e) => { setPage(1); setSearch(e.target.value) }}
          placeholder="Shakisha itangazo..."
          className="w-full max-w-xs rounded-xl border border-sand-200 bg-white px-3 py-2.5 text-sm text-ink outline-none transition-colors placeholder:text-ink/35 focus:border-teal-600 focus-visible:ring-2 focus-visible:ring-teal-600/20 dark:border-white/10 dark:bg-teal-900/40 dark:text-sand-50 dark:placeholder:text-sand-100/30 dark:focus:border-gold-400"
        />
      </Card>

      <Card className="overflow-hidden">
        {state.loading && <TableSkeleton cols={4} />}
        {!state.loading && state.error && <EmptyState title="Habaye ikibazo" description={state.error} />}
        {!state.loading && !state.error && state.items.length === 0 && (
          <EmptyState title="Nta tangazo rirahari" description="Tangira wongeraho itangazo rya mbere." />
        )}

        {!state.loading && !state.error && state.items.length > 0 && (
          <>
            <div className="overflow-x-auto"><table className="w-full min-w-[640px] text-left text-sm">
              <thead className="bg-sand-100 text-xs uppercase tracking-wide text-ink/50 dark:bg-white/5 dark:text-sand-100/50">
                <tr>
                  <th className="px-4 py-3">Umutwe</th>
                  <th className="px-4 py-3">Uko rihagaze</th>
                  <th className="px-4 py-3">Kwerekana</th>
                  <th className="px-4 py-3 text-right">Ibikorwa</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-sand-200 dark:divide-white/10">
                {state.items.map((item) => (
                  <tr key={item.id}>
                    <td className="px-4 py-3 font-medium text-ink dark:text-sand-50">{item.title}</td>
                    <td className="px-4 py-3 text-ink/70 dark:text-sand-100/70">{STATUS_LABEL[item.status] ?? item.status}</td>
                    <td className="px-4 py-3">
                      <Can permission="amatangazo.update" fallback={
                        <Badge tone={item.is_published ? 'success' : 'neutral'}>{item.is_published ? 'Bigaragara' : 'Byahishwe'}</Badge>
                      }>
                        <button onClick={() => handleToggle(item)} className="inline-block">
                          <Badge tone={item.is_published ? 'success' : 'neutral'}>{item.is_published ? 'Bigaragara' : 'Byahishwe'}</Badge>
                        </button>
                      </Can>
                    </td>
                    <td className="px-4 py-3 text-right">
                      <div className="flex justify-end gap-2">
                        <Can permission="amatangazo.update">
                          <Link to={`/amatangazo/${item.id}/edit`} className="text-sm text-teal-700 hover:underline dark:text-teal-300">Hindura</Link>
                        </Can>
                        <Can permission="amatangazo.delete">
                          <button onClick={() => handleDelete(item)} className="text-sm text-rose-600 hover:underline dark:text-rose-400">Siba</button>
                        </Can>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table></div>

            {state.meta && state.meta.last_page > 1 && (
              <div className="flex items-center justify-between border-t border-sand-200 px-4 py-3 text-sm dark:border-white/10">
                <span className="text-ink/50 dark:text-sand-100/50">Impapuro {state.meta.current_page} kuri {state.meta.last_page} ({state.meta.total} byose)</span>
                <div className="flex gap-2">
                  <Button variant="secondary" disabled={state.meta.current_page <= 1} onClick={() => setPage((p) => p - 1)}>Ibanziriza</Button>
                  <Button variant="secondary" disabled={state.meta.current_page >= state.meta.last_page} onClick={() => setPage((p) => p + 1)}>Ikurikira</Button>
                </div>
              </div>
            )}
          </>
        )}
      </Card>
    </div>
  )
}
