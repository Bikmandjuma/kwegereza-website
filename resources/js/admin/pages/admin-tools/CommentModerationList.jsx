import { useEffect, useState } from 'react'
import * as api from '../../features/admin-tools/adminToolsApi'
import { Card, EmptyState, Badge } from '../../components/ui'
import { useToast } from '../../contexts/ToastContext'

export default function CommentModerationList() {
  const { push } = useToast()
  const [data, setData] = useState(null)
  const [loading, setLoading] = useState(true)

  function load() {
    api.listCommentModeration().then((res) => setData(res.data)).finally(() => setLoading(false))
  }
  useEffect(() => { load() }, [])

  async function handleHide(id) {
    await api.hideComment(id)
    push('Igitekerezo cyahishwe.')
    load()
  }
  async function handleApprove(id) {
    await api.approveComment(id)
    push('Igitekerezo cyemejwe.')
    load()
  }
  async function handleDelete(id) {
    if (!confirm('Wemeza gusiba burundu iki gitekerezo?')) return
    await api.deleteComment(id)
    push('Igitekerezo cyasibwe burundu.')
    load()
  }

  if (loading) return <div className="animate-pulse text-sm text-ink/50">Turapakira...</div>

  return (
    <div className="flex flex-col gap-6">
      <div>
        <h1 className="font-display text-2xl text-ink">Ibitekerezo Byatanzwe (Moderation)</h1>
        <p className="text-sm text-ink/60">Genzura ibitekerezo byatanzweho raporo</p>
      </div>

      <Card className="p-5">
        <h2 className="mb-3 font-display text-lg text-ink">Byatanzweho raporo</h2>
        {(!data?.reported || data.reported.length === 0) ? (
          <EmptyState title="Nta raporo irahari" />
        ) : (
          <div className="space-y-3">
            {data.reported.map((r) => (
              <div key={r.comment_id} className="rounded-lg border border-sand-200 p-3">
                <div className="flex items-start justify-between">
                  <div>
                    <p className="text-sm text-ink">{r.comment.content}</p>
                    <p className="mt-1 text-xs text-ink/50">{r.comment.user} · <Badge tone="warning">{r.reports_count} raporo</Badge></p>
                  </div>
                  <div className="flex gap-2 text-sm">
                    <button onClick={() => handleApprove(r.comment.id)} className="text-teal-700 hover:underline">Emeza</button>
                    <button onClick={() => handleHide(r.comment.id)} className="text-gold-600 hover:underline">Hisha</button>
                    <button onClick={() => handleDelete(r.comment.id)} className="text-rose-600 hover:underline">Siba</button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
      </Card>

      <Card className="overflow-hidden">
        <div className="border-b border-sand-200 px-4 py-3"><h2 className="font-display text-lg text-ink">Byanditswe vuba</h2></div>
        <ul className="divide-y divide-sand-200">
          {data?.recent?.map((c) => (
            <li key={c.id} className="flex items-center justify-between px-4 py-3 text-sm">
              <div>
                <p className="text-ink">{c.content}</p>
                <p className="text-xs text-ink/50">{c.user}</p>
              </div>
              <Badge tone={c.status === 'hidden' ? 'danger' : c.status === 'approved' ? 'success' : 'neutral'}>{c.status}</Badge>
            </li>
          ))}
        </ul>
      </Card>
    </div>
  )
}
