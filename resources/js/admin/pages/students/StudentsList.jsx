import { useEffect, useState, useCallback } from 'react'
import { Link } from 'react-router-dom'
import * as studentsApi from '../../features/students/studentsApi'
import { Button, Card, EmptyState, TableSkeleton, Badge, ResultCount } from '../../components/ui'
import Can from '../../permissions/Can'
import { useToast } from '../../contexts/ToastContext'

export default function StudentsList() {
  const [state, setState] = useState({ loading: true, error: null, items: [], meta: null })
  const [search, setSearch] = useState('')
  const [page, setPage] = useState(1)
  const { push } = useToast()

  const load = useCallback(async () => {
    setState((s) => ({ ...s, loading: true, error: null }))
    try {
      const res = await studentsApi.listStudents({ page, search })
      setState({ loading: false, error: null, items: res.data, meta: res.meta })
    } catch (err) {
      setState({
        loading: false,
        error: err.response?.status === 403
          ? 'Ntabwo ufite uburenganzira bwo kubona abanyeshuri.'
          : 'Ntibishoboka gushaka abanyeshuri.',
        items: [],
        meta: null,
      })
    }
  }, [page, search])

  useEffect(() => { load() }, [load])

  async function toggleBlock(student) {
    const action = student.is_blocked ? 'kubohora' : 'guhagarika'
    if (!confirm(`Wemeza ${action} ${student.firstname} ${student.lastname}?`)) return
    try {
      if (student.is_blocked) await studentsApi.unblockStudent(student.id)
      else await studentsApi.blockStudent(student.id)
      push(student.is_blocked ? 'Umunyeshuri yongeye kubona urubuga.' : 'Umunyeshuri yahagaritswe.')
      load()
    } catch {
      push('Ntibishoboka gukora iki gikorwa.', 'error')
    }
  }

  return (
    <div className="flex flex-col gap-6">
      <div>
        <h1 className="font-display text-2xl text-ink dark:text-sand-50">Abanyeshuri</h1>
        <p className="text-sm text-ink/60 dark:text-sand-100/60">Reba amakuru y'abanyeshuri, uhagarike cyangwa wongere ubuhe uburenganzira</p>
        <div className="mt-2"><ResultCount count={state.meta?.total} label="abanyeshuri" /></div>
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
        {state.loading && <TableSkeleton cols={5} />}

        {!state.loading && state.error && <EmptyState title="Habaye ikibazo" description={state.error} />}

        {!state.loading && !state.error && state.items.length === 0 && (
          <EmptyState title="Nta munyeshuri urahari" description="Abanyeshuri bazagaragara hano nyuma yo kwiyandikisha." />
        )}

        {!state.loading && !state.error && state.items.length > 0 && (
          <>
            <div className="overflow-x-auto"><table className="w-full min-w-[640px] text-left text-sm">
              <thead className="bg-sand-100 text-xs uppercase tracking-wide text-ink/50 dark:bg-white/5 dark:text-sand-100/50">
                <tr>
                  <th className="px-4 py-3">Amazina</th>
                  <th className="px-4 py-3">Imeyili</th>
                  <th className="px-4 py-3">Telefoni</th>
                  <th className="px-4 py-3">Imiterere</th>
                  <th className="px-4 py-3 text-right">Ibikorwa</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-sand-200 dark:divide-white/10">
                {state.items.map((s) => (
                  <tr key={s.id}>
                    <td className="px-4 py-3 font-medium text-ink dark:text-sand-50">
                      <Link to={`/students/${s.id}`} className="hover:underline">{s.firstname} {s.lastname}</Link>
                    </td>
                    <td className="px-4 py-3 text-ink/70 dark:text-sand-100/70">{s.email || '—'}</td>
                    <td className="px-4 py-3 text-ink/70 dark:text-sand-100/70">{s.phone}</td>
                    <td className="px-4 py-3">
                      <Badge tone={s.is_blocked ? 'warning' : 'success'}>
                        {s.is_blocked ? 'Yahagaritswe' : 'Arakora'}
                      </Badge>
                    </td>
                    <td className="px-4 py-3 text-right">
                      <Can permission="students.manage">
                        <button
                          onClick={() => toggleBlock(s)}
                          className={`text-sm hover:underline ${s.is_blocked ? 'text-teal-700' : 'text-rose-600'}`}
                        >
                          {s.is_blocked ? 'Kubohora' : 'Guhagarika'}
                        </button>
                      </Can>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table></div>

            {state.meta && state.meta.last_page > 1 && (
              <div className="flex items-center justify-between border-t border-sand-200 px-4 py-3 text-sm dark:border-white/10">
                <span className="text-ink/50 dark:text-sand-100/50">
                  Impapuro {state.meta.current_page} kuri {state.meta.last_page} ({state.meta.total} byose)
                </span>
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
