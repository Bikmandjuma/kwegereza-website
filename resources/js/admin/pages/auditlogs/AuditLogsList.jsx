import { useEffect, useState, Fragment } from 'react'
import * as auditLogApi from '../../features/auditlogs/auditLogApi'
import { Card, EmptyState, Badge, TableSkeleton, Button, ResultCount } from '../../components/ui'

const ACTION_TONE = { created: 'success', updated: 'neutral', deleted: 'danger' }

export default function AuditLogsList() {
  const [state, setState] = useState({ loading: true, error: null, items: [], meta: null, entityTypes: [] })
  const [page, setPage] = useState(1)
  const [entityType, setEntityType] = useState('')
  const [action, setAction] = useState('')
  const [expanded, setExpanded] = useState(null)

  function load() {
    setState((s) => ({ ...s, loading: true }))
    auditLogApi.listAuditLogs({ page, entity_type: entityType, action })
      .then((res) => setState({ loading: false, error: null, items: res.data, meta: res.meta, entityTypes: res.entity_types }))
      .catch((err) => setState({
        loading: false,
        error: err.response?.status === 403 ? 'Ntabwo ufite uburenganzira bwo kubona ibi bikorwa.' : 'Ntibishoboka gushaka ibikorwa.',
        items: [], meta: null, entityTypes: [],
      }))
  }

  useEffect(() => { load() }, [page, entityType, action]) // eslint-disable-line react-hooks/exhaustive-deps

  return (
    <div className="flex flex-col gap-6">
      <div>
        <h1 className="font-display text-2xl text-ink dark:text-sand-50">Ibikorwa by'Abakoresha (Audit Log)</h1>
        <p className="text-sm text-ink/60 dark:text-sand-100/60">Ibikorwa byose byakozwe n'abafite konti yo gucunga urubuga</p>
        <div className="mt-2"><ResultCount count={state.meta?.total} label="ibikorwa" /></div>
      </div>

      <div className="flex flex-wrap gap-3">
        <select value={action} onChange={(e) => { setAction(e.target.value); setPage(1) }} className="rounded-xl border border-sand-200 bg-white px-3 py-2 text-sm text-ink outline-none transition-colors focus:border-teal-600 focus-visible:ring-2 focus-visible:ring-teal-600/20 dark:border-white/10 dark:bg-teal-900/40 dark:text-sand-50 dark:focus:border-gold-400">
          <option value="">Ibikorwa byose</option>
          <option value="created">Byashyizweho</option>
          <option value="updated">Byahinduwe</option>
          <option value="deleted">Byasibwe</option>
        </select>
        <select value={entityType} onChange={(e) => { setEntityType(e.target.value); setPage(1) }} className="rounded-xl border border-sand-200 bg-white px-3 py-2 text-sm text-ink outline-none transition-colors focus:border-teal-600 focus-visible:ring-2 focus-visible:ring-teal-600/20 dark:border-white/10 dark:bg-teal-900/40 dark:text-sand-50 dark:focus:border-gold-400">
          <option value="">Ibintu byose</option>
          {state.entityTypes.map((t) => <option key={t} value={t}>{t.split('\\').pop()}</option>)}
        </select>
      </div>

      <Card className="overflow-hidden">
        {state.loading && <TableSkeleton cols={5} />}
        {!state.loading && state.error && <EmptyState title="Habaye ikibazo" description={state.error} />}
        {!state.loading && !state.error && state.items.length === 0 && (
          <EmptyState title="Nta gikorwa kirahari" description="Nta gikorwa cyanditswe kugeza ubu." />
        )}
        {!state.loading && !state.error && state.items.length > 0 && (
          <>
            <div className="overflow-x-auto"><table className="w-full min-w-[640px] text-left text-sm">
              <thead className="bg-sand-100 text-xs uppercase tracking-wide text-ink/50 dark:bg-white/5 dark:text-sand-100/50">
                <tr>
                  <th className="px-4 py-3">Igihe</th>
                  <th className="px-4 py-3">Umukoresha</th>
                  <th className="px-4 py-3">Igikorwa</th>
                  <th className="px-4 py-3">Ikintu</th>
                  <th className="px-4 py-3"></th>
                </tr>
              </thead>
              <tbody className="divide-y divide-sand-200 dark:divide-white/10">
                {state.items.map((log) => (
                  <Fragment key={log.id}>
                    <tr>
                      <td className="px-4 py-3 text-ink/60 dark:text-sand-100/60">{new Date(log.created_at).toLocaleString()}</td>
                      <td className="px-4 py-3 text-ink/70 dark:text-sand-100/70">{log.owner?.name || '—'}</td>
                      <td className="px-4 py-3"><Badge tone={ACTION_TONE[log.action]}>{log.action}</Badge></td>
                      <td className="px-4 py-3 text-ink dark:text-sand-50">{log.entity_type} {log.entity_label && `· ${log.entity_label}`}</td>
                      <td className="px-4 py-3 text-right">
                        <button onClick={() => setExpanded(expanded === log.id ? null : log.id)} className="text-sm text-teal-700 hover:underline dark:text-teal-300">
                          {expanded === log.id ? 'Hisha' : 'Reba'}
                        </button>
                      </td>
                    </tr>
                    {expanded === log.id && (
                      <tr>
                        <td colSpan={5} className="bg-sand-50 px-4 py-3 dark:bg-teal-900/30">
                          <div className="grid grid-cols-2 gap-4 text-xs">
                            <div>
                              <p className="mb-1 font-medium text-ink/50 dark:text-sand-100/50">Mbere</p>
                              <pre className="overflow-x-auto rounded bg-white p-2">{JSON.stringify(log.before, null, 2) || '—'}</pre>
                            </div>
                            <div>
                              <p className="mb-1 font-medium text-ink/50 dark:text-sand-100/50">Nyuma</p>
                              <pre className="overflow-x-auto rounded bg-white p-2">{JSON.stringify(log.after, null, 2) || '—'}</pre>
                            </div>
                          </div>
                          <p className="mt-2 text-xs text-ink/40 dark:text-sand-100/40">IP: {log.ip}</p>
                        </td>
                      </tr>
                    )}
                  </Fragment>
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
