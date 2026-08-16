import { useEffect, useState, useCallback } from 'react'
import { Link } from 'react-router-dom'
import * as booksApi from '../../features/books/booksApi'
import { Button, Card, EmptyState, TableSkeleton, Badge, ResultCount } from '../../components/ui'
import Can from '../../permissions/Can'
import { useToast } from '../../contexts/ToastContext'

export default function BooksList() {
  const [state, setState] = useState({ loading: true, error: null, items: [], meta: null })
  const [search, setSearch] = useState('')
  const [page, setPage] = useState(1)
  const { push } = useToast()

  const load = useCallback(async () => {
    setState((s) => ({ ...s, loading: true, error: null }))
    try {
      const res = await booksApi.listBooks({ page, search })
      setState({ loading: false, error: null, items: res.data, meta: res.meta })
    } catch (err) {
      setState({
        loading: false,
        error: err.response?.status === 403 ? 'Ntabwo ufite uburenganzira bwo kubona ibitabo.' : 'Ntibishoboka gushaka ibitabo.',
        items: [],
        meta: null,
      })
    }
  }, [page, search])

  useEffect(() => { load() }, [load])

  async function handleDelete(book) {
    if (!confirm(`Wemeza gusiba "${book.title}"?`)) return
    try {
      await booksApi.deleteBook(book.id)
      push('Igitabo cyasibwe.')
      load()
    } catch {
      push('Ntibishoboka gusiba iki gitabo.', 'error')
    }
  }

  return (
    <div className="flex flex-col gap-6">
      <div className="flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 className="font-display text-2xl text-ink dark:text-sand-50">Ibitabo</h1>
          <p className="text-sm text-ink/60 dark:text-sand-100/60">Genzura ibitabo bishobora gukururwa</p>
          <div className="mt-2"><ResultCount count={state.meta?.total} label="ibitabo" /></div>
        </div>
        <Can permission="books.create">
          <Link to="/books/new" className="inline-flex items-center justify-center gap-2 rounded-lg bg-teal-700 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-teal-900">
            + Igitabo gishya
          </Link>
        </Can>
      </div>

      <Card className="p-4">
        <input
          value={search}
          onChange={(e) => { setPage(1); setSearch(e.target.value) }}
          placeholder="Shakisha igitabo..."
          className="w-full max-w-xs rounded-xl border border-sand-200 bg-white px-3 py-2.5 text-sm text-ink outline-none transition-colors placeholder:text-ink/35 focus:border-teal-600 focus-visible:ring-2 focus-visible:ring-teal-600/20 dark:border-white/10 dark:bg-teal-900/40 dark:text-sand-50 dark:placeholder:text-sand-100/30 dark:focus:border-gold-400"
        />
      </Card>

      <Card className="overflow-hidden">
        {state.loading && <TableSkeleton cols={5} />}
        {!state.loading && state.error && <EmptyState title="Habaye ikibazo" description={state.error} />}
        {!state.loading && !state.error && state.items.length === 0 && (
          <EmptyState title="Nta gitabo kirahari" description="Tangira wongeraho igitabo cya mbere." />
        )}

        {!state.loading && !state.error && state.items.length > 0 && (
          <>
            <div className="overflow-x-auto"><table className="w-full min-w-[640px] text-left text-sm">
              <thead className="bg-sand-100 text-xs uppercase tracking-wide text-ink/50 dark:bg-white/5 dark:text-sand-100/50">
                <tr>
                  <th className="px-4 py-3">Umutwe</th>
                  <th className="px-4 py-3">Umwanditsi</th>
                  <th className="px-4 py-3">Izo bakuye</th>
                  <th className="px-4 py-3">Imiterere</th>
                  <th className="px-4 py-3 text-right">Ibikorwa</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-sand-200 dark:divide-white/10">
                {state.items.map((book) => (
                  <tr key={book.id}>
                    <td className="px-4 py-3 font-medium text-ink dark:text-sand-50">{book.title}</td>
                    <td className="px-4 py-3 text-ink/70 dark:text-sand-100/70">{book.author || '—'}</td>
                    <td className="px-4 py-3 text-ink/70 dark:text-sand-100/70">{book.downloads}</td>
                    <td className="px-4 py-3">
                      <Badge tone={book.status === 'published' ? 'success' : 'neutral'}>
                        {book.status === 'published' ? 'Byasohotse' : 'Ntibirasohoka'}
                      </Badge>
                    </td>
                    <td className="px-4 py-3 text-right">
                      <div className="flex justify-end gap-2">
                        <Can permission="books.update">
                          <Link to={`/books/${book.id}/edit`} className="text-sm text-teal-700 hover:underline dark:text-teal-300">Hindura</Link>
                        </Can>
                        <Can permission="books.delete">
                          <button onClick={() => handleDelete(book)} className="text-sm text-rose-600 hover:underline dark:text-rose-400">Siba</button>
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
