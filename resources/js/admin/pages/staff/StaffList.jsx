import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import * as staffApi from '../../features/staff/staffApi'
import { Button, Card, EmptyState, TableSkeleton, Badge, ResultCount } from '../../components/ui'
import Can from '../../permissions/Can'
import { useAuth } from '../../contexts/AuthContext'
import { useToast } from '../../contexts/ToastContext'

export default function StaffList() {
  const { user } = useAuth()
  const { push } = useToast()
  const [state, setState] = useState({ loading: true, error: null, items: [], meta: null })
  const [search, setSearch] = useState('')
  const [page, setPage] = useState(1)

  function load() {
    setState((s) => ({ ...s, loading: true, error: null }))
    staffApi.listStaff({ page, search })
      .then((res) => setState({ loading: false, error: null, items: res.data, meta: res.meta }))
      .catch((err) => setState({
        loading: false,
        error: err.response?.status === 403 ? 'Ntabwo ufite uburenganzira bwo kubona abakoresha.' : 'Ntibishoboka gushaka abakoresha.',
        items: [], meta: null,
      }))
  }

  useEffect(() => { load() }, [page, search]) // eslint-disable-line react-hooks/exhaustive-deps

  async function handleDelete(member) {
    if (!confirm(`Wemeza gusiba ${member.firstname} ${member.lastname}?`)) return
    try {
      await staffApi.deleteStaff(member.id)
      push('Umukoresha yasibwe.')
      load()
    } catch (err) {
      push(err.response?.data?.message || 'Ntibishoboka gusiba uyu mukoresha.', 'error')
    }
  }

  return (
    <div className="flex flex-col gap-6">
      <div className="flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 className="font-display text-2xl text-ink dark:text-sand-50">Abakoresha</h1>
          <p className="text-sm text-ink/60 dark:text-sand-100/60">Abayobozi, abarimu, n'abandi bafite konti yo gucunga urubuga</p>
          <div className="mt-2"><ResultCount count={state.meta?.total} label="abakoresha" /></div>
        </div>
        <Can permission="users.create">
          <Link to="/staff/new" className="inline-flex items-center justify-center gap-2 rounded-lg bg-teal-700 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-teal-900">
            + Umukoresha mushya
          </Link>
        </Can>
      </div>

      <Card className="p-4">
        <input
          value={search}
          onChange={(e) => { setPage(1); setSearch(e.target.value) }}
          placeholder="Shakisha umukoresha..."
          className="w-full max-w-xs rounded-xl border border-sand-200 bg-white px-3 py-2.5 text-sm text-ink outline-none transition-colors placeholder:text-ink/35 focus:border-teal-600 focus-visible:ring-2 focus-visible:ring-teal-600/20 dark:border-white/10 dark:bg-teal-900/40 dark:text-sand-50 dark:placeholder:text-sand-100/30 dark:focus:border-gold-400"
        />
      </Card>

      <Card className="overflow-hidden">
        {state.loading && <TableSkeleton cols={5} />}
        {!state.loading && state.error && <EmptyState title="Habaye ikibazo" description={state.error} />}
        {!state.loading && !state.error && state.items.length === 0 && (
          <EmptyState title="Nta mukoresha uhari" description="Ongeraho umukoresha wa mbere." />
        )}
        {!state.loading && !state.error && state.items.length > 0 && (
          <>
            <div className="overflow-x-auto"><table className="w-full min-w-[640px] text-left text-sm">
              <thead className="bg-sand-100 text-xs uppercase tracking-wide text-ink/50 dark:bg-white/5 dark:text-sand-100/50">
                <tr>
                  <th className="px-4 py-3">Izina</th>
                  <th className="px-4 py-3">Imeli</th>
                  <th className="px-4 py-3">Uruhare</th>
                  <th className="px-4 py-3">Amazina y'inshingano</th>
                  <th className="px-4 py-3 text-right">Ibikorwa</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-sand-200 dark:divide-white/10">
                {state.items.map((s) => (
                  <tr key={s.id}>
                    <td className="px-4 py-3 font-medium text-ink dark:text-sand-50">{s.firstname} {s.lastname}</td>
                    <td className="px-4 py-3 text-ink/70 dark:text-sand-100/70">{s.email}</td>
                    <td className="px-4 py-3 text-ink/70 dark:text-sand-100/70">{s.title}</td>
                    <td className="px-4 py-3">
                      {(s.roles || []).map((r) => <Badge key={r} tone="neutral">{r}</Badge>)}
                    </td>
                    <td className="px-4 py-3 text-right">
                      <div className="flex justify-end gap-3">
                        <Can permission="users.update">
                          <Link to={`/staff/${s.id}/edit`} className="text-sm text-teal-700 hover:underline dark:text-teal-300">Hindura</Link>
                        </Can>
                        <Can permission="users.delete">
                          {s.id !== user?.id && (
                            <button onClick={() => handleDelete(s)} className="text-sm text-rose-600 hover:underline dark:text-rose-400">Siba</button>
                          )}
                        </Can>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table></div>

            {state.meta && state.meta.last_page > 1 && (
              <div className="flex items-center justify-between border-t border-sand-200 px-4 py-3 text-sm dark:border-white/10">
                <span className="text-ink/50 dark:text-sand-100/50">Impapuro {state.meta.current_page} kuri {state.meta.last_page}</span>
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
