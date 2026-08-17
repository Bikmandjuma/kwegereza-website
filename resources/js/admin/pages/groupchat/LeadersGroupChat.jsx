import { useEffect, useState, useRef } from 'react'
import * as groupChatApi from '../../features/groupchat/groupChatApi'
import { getEcho } from '../../services/echo'
import { useAuth } from '../../contexts/AuthContext'
import { Card, Skeleton } from '../../components/ui'

const QUICK_EMOJIS = ['👍', '❤️', '😂', '🙏', '🎉']

export default function LeadersGroupChat() {
  const { user, hasPermission } = useAuth()
  const canModerate = hasPermission('group_chat.moderate')
  const [messages, setMessages] = useState([])
  const [pinned, setPinned] = useState([])
  const [showPinned, setShowPinned] = useState(false)
  const [draft, setDraft] = useState('')
  const [replyTo, setReplyTo] = useState(null)
  const [openReactionFor, setOpenReactionFor] = useState(null)
  const [loading, setLoading] = useState(true)
  const [sending, setSending] = useState(false)
  const bottomRef = useRef(null)

  useEffect(() => {
    groupChatApi.getLeadersMessages()
      .then(({ messages, pinned }) => { setMessages(messages); setPinned(pinned || []) })
      .catch(() => {})
      .finally(() => setLoading(false))
  }, [])

  useEffect(() => {
    const echo = getEcho()
    if (!echo) return
    const channel = echo.private('group.leaders')

    channel.listen('.group-message.sent', (payload) => {
      setMessages((prev) => (prev.some((m) => m.id === payload.id) ? prev : [...prev, payload]))
    })

    channel.listen('.group-message.updated', (payload) => {
      if (payload.type === 'deleted') {
        setMessages((prev) => prev.filter((m) => m.id !== payload.message_id))
        setPinned((prev) => prev.filter((m) => m.id !== payload.message_id))
      } else if (payload.type === 'reacted' || payload.type === 'unreacted') {
        setMessages((prev) => prev.map((m) => (m.id === payload.message_id ? { ...m, reactions: payload.reactions } : m)))
      } else if (payload.type === 'pinned' || payload.type === 'unpinned') {
        // Simplest correct thing: re-fetch the small pinned list rather
        // than try to reconstruct the full message object from a partial
        // broadcast payload.
        groupChatApi.getLeadersMessages().then(({ pinned }) => setPinned(pinned || [])).catch(() => {})
      }
    })

    return () => echo.leave('group.leaders')
  }, [])

  useEffect(() => {
    bottomRef.current?.scrollIntoView({ behavior: 'smooth' })
  }, [messages])

  async function handleSend(e) {
    e.preventDefault()
    if (!draft.trim()) return
    setSending(true)
    try {
      const sent = await groupChatApi.sendLeadersMessage(draft.trim(), replyTo?.id ?? null)
      setMessages((prev) => [...prev, sent])
      setDraft('')
      setReplyTo(null)
    } catch (err) {
      alert(err.response?.data?.message || 'Ntibyakunze kohereza.')
    } finally {
      setSending(false)
    }
  }

  async function handleReact(message, emoji) {
    setOpenReactionFor(null)
    try {
      const { reactions } = await groupChatApi.react(message.id, emoji)
      setMessages((prev) => prev.map((m) => (m.id === message.id ? { ...m, reactions } : m)))
    } catch {
      // realtime broadcast will still reconcile other clients; a failed
      // local optimistic update here just means this tab stays as-is
    }
  }

  async function handleDelete(message) {
    if (!confirm('Wemeza gusiba ubu butumwa?')) return
    try {
      await groupChatApi.deleteMessage(message.id)
      setMessages((prev) => prev.filter((m) => m.id !== message.id))
    } catch (err) {
      alert(err.response?.data?.message || 'Ntibyashobotse gusiba.')
    }
  }

  async function handleReport(message) {
    const reason = prompt('Impamvu yo gutanga raporo (si ngombwa):') ?? ''
    try {
      await groupChatApi.reportMessage(message.id, reason)
      alert('Raporo yatanzwe.')
    } catch {
      alert('Ntibyashobotse gutanga raporo.')
    }
  }

  async function handleTogglePin(message) {
    try {
      if (message.is_pinned) {
        await groupChatApi.unpinMessage(message.id)
        setPinned((prev) => prev.filter((m) => m.id !== message.id))
      } else {
        await groupChatApi.pinMessage(message.id)
        setPinned((prev) => [...prev, { ...message, is_pinned: true }])
      }
      setMessages((prev) => prev.map((m) => (m.id === message.id ? { ...m, is_pinned: !m.is_pinned } : m)))
    } catch {
      alert('Ntibyashobotse.')
    }
  }

  return (
    <div className="flex h-[calc(100vh-8rem)] flex-col gap-4">
      <div>
        <h1 className="font-display text-2xl text-ink dark:text-sand-50">Itsinda ry'Abayobozi</h1>
        <p className="text-sm text-ink/60 dark:text-sand-100/60">Ubutumwa hagati y'abayobozi bose</p>
      </div>

      {pinned.length > 0 && (
        <Card className="p-3">
          <button
            onClick={() => setShowPinned((s) => !s)}
            className="flex w-full items-center justify-between text-sm font-medium text-ink dark:text-sand-50"
          >
            <span>📌 Ubutumwa bwomekwe ({pinned.length})</span>
            <span className="text-xs text-ink/40 dark:text-sand-100/40">{showPinned ? 'Hisha' : 'Erekana'}</span>
          </button>
          {showPinned && (
            <div className="mt-2 space-y-1.5 border-t border-sand-200 pt-2 dark:border-white/10">
              {pinned.map((m) => (
                <p key={m.id} className="truncate text-xs text-ink/70 dark:text-sand-100/70">
                  <span className="font-medium">{m.sender_name}:</span> {m.message}
                </p>
              ))}
            </div>
          )}
        </Card>
      )}

      <Card className="flex flex-1 flex-col overflow-hidden">
        <div className="flex-1 space-y-2 overflow-y-auto p-4">
          {loading && (
            <div className="space-y-3">
              {Array.from({ length: 3 }).map((_, i) => <Skeleton key={i} className="h-10 w-2/3" />)}
            </div>
          )}
          {!loading && messages.length === 0 && <p className="text-sm text-ink/50 dark:text-sand-100/50">Nta butumwa burahari.</p>}
          {messages.map((m) => {
            const isMine = m.sender_id === user?.id
            const reactionEntries = Object.entries(m.reactions || {})
            return (
              <div key={m.id} className={`group flex ${isMine ? 'justify-end' : 'justify-start'}`}>
                <div className="max-w-[85%] sm:max-w-xs">
                  {m.parent && (
                    <div className={`mb-1 rounded-lg border-l-2 border-gold-400 bg-sand-50 px-2 py-1 text-[11px] text-ink/50 dark:bg-white/5 dark:text-sand-100/50 ${isMine ? 'text-right' : ''}`}>
                      <span className="font-medium">{m.parent.sender_name}:</span> {m.parent.message.slice(0, 60)}
                    </div>
                  )}
                  <div className={`rounded-2xl px-3 py-2 text-sm ${isMine ? 'bg-teal-700 text-white' : 'bg-sand-100 text-ink dark:bg-white/10 dark:text-sand-50'}`}>
                    {!isMine && <p className="mb-0.5 text-xs font-medium opacity-70">{m.sender_name}</p>}
                    {m.is_pinned && <p className="mb-0.5 text-[10px] opacity-70">📌 Byomekwe</p>}
                    <p>{m.message}</p>
                    <p className={`mt-1 text-[10px] ${isMine ? 'text-white/60' : 'text-ink/40 dark:text-sand-100/40'}`}>
                      {new Date(m.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                    </p>
                  </div>

                  {reactionEntries.length > 0 && (
                    <div className="mt-1 flex flex-wrap gap-1">
                      {reactionEntries.map(([emoji, count]) => (
                        <button
                          key={emoji}
                          onClick={() => handleReact(m, emoji)}
                          className="rounded-full border border-sand-200 bg-white px-1.5 py-0.5 text-xs dark:border-white/10 dark:bg-teal-900/40"
                        >
                          {emoji} {count}
                        </button>
                      ))}
                    </div>
                  )}

                  {/* Actions row — only visible on hover, keeps the thread visually calm */}
                  <div className="relative mt-1 flex items-center gap-2 text-[11px] text-ink/40 opacity-0 transition-opacity group-hover:opacity-100 dark:text-sand-100/40">
                    <button onClick={() => setOpenReactionFor(openReactionFor === m.id ? null : m.id)} className="hover:text-ink dark:hover:text-sand-50">😊 Reaction</button>
                    <button onClick={() => setReplyTo(m)} className="hover:text-ink dark:hover:text-sand-50">↩ Subiza</button>
                    {canModerate && (
                      <button onClick={() => handleTogglePin(m)} className="hover:text-ink dark:hover:text-sand-50">
                        {m.is_pinned ? '📌 Kuraho' : '📌 Omeka'}
                      </button>
                    )}
                    {(isMine || canModerate) && (
                      <button onClick={() => handleDelete(m)} className="text-rose-500 hover:text-rose-700">🗑 Siba</button>
                    )}
                    <button onClick={() => handleReport(m)} className="hover:text-ink dark:hover:text-sand-50">⚑ Raporo</button>

                    {openReactionFor === m.id && (
                      <div className="absolute bottom-full left-0 z-10 mb-1 flex gap-1 rounded-full border border-sand-200 bg-white p-1 shadow-lift dark:border-white/10 dark:bg-teal-900">
                        {QUICK_EMOJIS.map((emoji) => (
                          <button key={emoji} onClick={() => handleReact(m, emoji)} className="px-1 text-base hover:scale-125 transition-transform">
                            {emoji}
                          </button>
                        ))}
                      </div>
                    )}
                  </div>
                </div>
              </div>
            )
          })}
          <div ref={bottomRef} />
        </div>

        {replyTo && (
          <div className="flex items-center justify-between border-t border-sand-200 bg-sand-50 px-3 py-2 text-xs text-ink/60 dark:border-white/10 dark:bg-white/5 dark:text-sand-100/60">
            <span>Usubiza {replyTo.sender_name}: {replyTo.message.slice(0, 40)}</span>
            <button onClick={() => setReplyTo(null)} className="font-medium text-rose-600 hover:underline">Hagarika</button>
          </div>
        )}

        <form onSubmit={handleSend} className="flex gap-2 border-t border-sand-200 p-3 dark:border-white/10">
          <input
            value={draft}
            onChange={(e) => setDraft(e.target.value)}
            placeholder="Andika ubutumwa..."
            className="flex-1 rounded-xl border border-sand-200 bg-white px-3 py-2.5 text-sm text-ink outline-none transition-colors placeholder:text-ink/35 focus:border-teal-600 focus-visible:ring-2 focus-visible:ring-teal-600/20 dark:border-white/10 dark:bg-teal-900/40 dark:text-sand-50 dark:placeholder:text-sand-100/30 dark:focus:border-gold-400"
          />
          <button
            type="submit"
            disabled={sending || !draft.trim()}
            className="rounded-lg bg-teal-700 px-4 py-2 text-sm font-medium text-white hover:bg-teal-900 disabled:opacity-50"
          >
            Ohereza
          </button>
        </form>
      </Card>
    </div>
  )
}
