import { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import * as liveClassApi from '../../features/liveclass/liveClassApi'
import { Button, Card, Input, EmptyState, Badge, TableSkeleton } from '../../components/ui'
import Can from '../../permissions/Can'
import { useAuth } from '../../contexts/AuthContext'
import { useToast } from '../../contexts/ToastContext'

const STATUS_LABEL = { scheduled: 'Rizatangira', live: 'Live nonaha', ended: 'Ryarangiye' }

export default function LiveClassesList() {
  const navigate = useNavigate()
  const { user, hasPermission } = useAuth()
  const { push } = useToast()
  const [classes, setClasses] = useState([])
  const [loading, setLoading] = useState(true)
  const [title, setTitle] = useState('')

  function load() {
    liveClassApi.listClasses().then((res) => setClasses(res.data)).catch(() => {}).finally(() => setLoading(false))
  }

  useEffect(() => { load() }, [])

  async function handleCreate(e) {
    e.preventDefault()
    if (!title.trim()) return
    await liveClassApi.createClass(title.trim())
    setTitle('')
    push('Isomo rya live ryashyizweho.')
    load()
  }

  async function handleStart(id) {
    await liveClassApi.startClass(id)
    navigate(`/live-classes/${id}`)
  }

  async function handleEnd(id) {
    await liveClassApi.endClass(id)
    push('Isomo ryarangiye.')
    load()
  }

  // The Laravel API and the student-facing Blade pages are served by
  // the same backend app — VITE_API_BASE_URL points at .../api/owner,
  // so the web root is that same origin with the API path stripped.
  // Same origin now (this React app is served BY Laravel at /admin, not
  // a separate app on a different port) — the student pages live at
  // the domain root, so window.location.origin is always correct here,
  // no more deriving it from the API base URL's string shape.
  function studentJoinUrl(id) {
    return `${window.location.origin}/student/live-classes/${id}`
  }

  async function handleCopyLink(id) {
    const url = studentJoinUrl(id)
    try {
      await navigator.clipboard.writeText(url)
      push('Link yakoporowe — ushobora kuyisangiza abanyeshuri.')
    } catch {
      // Clipboard API can fail (permissions, non-HTTPS context) — fall
      // back to showing the link directly so it's never a dead end.
      prompt('Kopera iyi link:', url)
    }
  }

  return (
    <div className="flex flex-col gap-6">
      <div>
        <h1 className="font-display text-2xl text-ink dark:text-sand-50">Amasomo ya Live</h1>
        <p className="text-sm text-ink/60 dark:text-sand-100/60">Tegura kandi ukore amasomo ya video/ijwi mu gihe nyacyo</p>
      </div>

      <Can permission="live_class.create">
        <Card className="p-4">
          <form onSubmit={handleCreate} className="flex gap-2">
            <Input placeholder="Umutwe w'isomo" value={title} onChange={(e) => setTitle(e.target.value)} className="flex-1" />
            <Button type="submit">Shyiraho</Button>
          </form>
        </Card>
      </Can>

      <Card className="overflow-hidden">
        {loading && <TableSkeleton rows={3} cols={3} />}
        {!loading && classes.length === 0 && <EmptyState title="Nta somo rya live rirahari" description="Tangira ushyireho isomo rya mbere." />}
        {!loading && classes.length > 0 && (
          <div className="overflow-x-auto"><table className="w-full min-w-[640px] text-left text-sm">
            <thead className="bg-sand-100 text-xs uppercase tracking-wide text-ink/50 dark:bg-white/5 dark:text-sand-100/50">
              <tr><th className="px-4 py-3">Umutwe</th><th className="px-4 py-3">Imiterere</th><th className="px-4 py-3 text-right">Ibikorwa</th></tr>
            </thead>
            <tbody className="divide-y divide-sand-200 dark:divide-white/10">
              {classes.map((c) => {
                // Was showing Start/Join/End to EVERY leader who could
                // see this list at all (live_class.manage is broad —
                // "can run classes in general") without checking whether
                // this leader is actually the host of THIS class. The
                // backend already enforced that correctly
                // (authorizeHost() in LiveClassController), so clicking a
                // button for someone else's class always 403'd — the bug
                // was the button being visible and clickable in the
                // first place instead of simply not being there.
                const canControl = c.host?.id === user?.id || hasPermission('live_class.moderate')
                return (
                <tr key={c.id}>
                  <td className="px-4 py-3 font-medium text-ink dark:text-sand-50">{c.title}</td>
                  <td className="px-4 py-3"><Badge tone={c.status === 'live' ? 'success' : 'neutral'}>{STATUS_LABEL[c.status]}</Badge></td>
                  <td className="px-4 py-3 text-right">
                    <div className="flex justify-end gap-3">
                      {!canControl && <span className="text-xs text-ink/40 dark:text-sand-100/40">{c.host?.name}</span>}
                      {canControl && c.status === 'scheduled' && (
                        <button onClick={() => handleStart(c.id)} className="text-sm text-teal-700 hover:underline dark:text-teal-300">Tangira</button>
                      )}
                      {canControl && c.status === 'live' && (
                        <>
                          <Link to={`/live-classes/${c.id}`} className="text-sm text-teal-700 hover:underline dark:text-teal-300">Injira</Link>
                          <button onClick={() => handleCopyLink(c.id)} className="text-sm text-gold-600 hover:underline dark:text-gold-300">Kopera link</button>
                          <button onClick={() => handleEnd(c.id)} className="text-sm text-rose-600 hover:underline dark:text-rose-400">Rangiza</button>
                        </>
                      )}
                    </div>
                  </td>
                </tr>
                )
              })}
            </tbody>
          </table></div>
        )}
      </Card>
    </div>
  )
}
