import { useEffect, useState, useCallback, useRef } from 'react'
import * as chatApi from '../../features/chat/chatApi'
import { getEcho } from '../../services/echo'
import { Card, EmptyState, Badge, Skeleton } from '../../components/ui'
import { useAuth } from '../../contexts/AuthContext'

function timeAgo(dateStr) {
  if (!dateStr) return ''
  const diff = (Date.now() - new Date(dateStr).getTime()) / 1000
  if (diff < 60) return 'nonaha'
  if (diff < 3600) return `${Math.floor(diff / 60)} min`
  if (diff < 86400) return `${Math.floor(diff / 3600)} h`
  return `${Math.floor(diff / 86400)} d`
}

export default function ChatInbox() {
  const { user } = useAuth()
  const [conversations, setConversations] = useState([])
  const [loadingList, setLoadingList] = useState(true)
  const [activeId, setActiveId] = useState(null)
  const [activeName, setActiveName] = useState('')
  const [messages, setMessages] = useState([])
  const [draft, setDraft] = useState('')
  const [sending, setSending] = useState(false)
  // Only meaningful below the lg breakpoint — on desktop both panels
  // always show side by side regardless of this. On mobile, WhatsApp/
  // Telegram-style: the list and the open conversation are two separate
  // full-width screens, never squeezed into the same narrow viewport
  // together (spec §51: "do not squeeze two desktop panels into a 360px
  // screen").
  const [mobileView, setMobileView] = useState('list')
  const bottomRef = useRef(null)

  const loadConversations = useCallback(() => {
    chatApi.listConversations().then(setConversations).catch(() => {}).finally(() => setLoadingList(false))
  }, [])

  useEffect(() => {
    loadConversations()
    const interval = setInterval(loadConversations, 15000)
    return () => clearInterval(interval)
  }, [loadConversations])

  async function openConversation(conversation) {
    setActiveId(conversation.guest_id)
    setActiveName(conversation.sender_name)
    setMobileView('conversation')
    const msgs = await chatApi.getMessages(conversation.guest_id)
    setMessages(msgs)
    await chatApi.markRead(conversation.guest_id)
    loadConversations()
  }

  function backToList() {
    setMobileView('list')
  }

  useEffect(() => {
    if (!activeId) return
    const echo = getEcho()
    if (!echo) return

    const channel = echo.channel(`chat.${activeId}`)
    channel.listen('.message.sent', (payload) => {
      setMessages((prev) => (prev.some((m) => m.id === payload.id) ? prev : [...prev, payload]))
      if (payload.sender_type === 'guest') chatApi.markRead(activeId)
      loadConversations()
    })

    return () => echo.leave(`chat.${activeId}`)
  }, [activeId, loadConversations])

  useEffect(() => {
    bottomRef.current?.scrollIntoView({ behavior: 'smooth' })
  }, [messages])

  async function handleSend(e) {
    e.preventDefault()
    if (!draft.trim() || !activeId) return
    setSending(true)
    try {
      const sent = await chatApi.sendMessage(activeId, draft.trim())
      setMessages((prev) => [...prev, sent])
      setDraft('')
      loadConversations()
    } finally {
      setSending(false)
    }
  }

  return (
    <div className="flex h-[calc(100vh-8rem)] gap-4">
      {/* Conversation list — full width on mobile when it's the active
          screen, fixed-width sidebar on desktop always. */}
      <Card
        className={`flex-col overflow-hidden lg:flex lg:w-72 lg:flex-shrink-0 ${
          mobileView === 'list' ? 'flex w-full' : 'hidden'
        }`}
      >
        <div className="border-b border-sand-200 px-4 py-3 dark:border-white/10">
          <h2 className="font-display text-lg text-ink dark:text-sand-50">Ubutumwa</h2>
        </div>
        <div className="flex-1 overflow-y-auto">
          {loadingList && (
            <div className="space-y-3 p-4">
              {Array.from({ length: 4 }).map((_, i) => <Skeleton key={i} className="h-12 w-full" />)}
            </div>
          )}
          {!loadingList && conversations.length === 0 && (
            <p className="p-4 text-sm text-ink/50 dark:text-sand-100/50">Nta butumwa burahari.</p>
          )}
          {conversations.map((c) => (
            <button
              key={c.guest_id}
              onClick={() => openConversation(c)}
              className={`block w-full border-b border-sand-100 px-4 py-3 text-left transition-colors hover:bg-sand-50 dark:border-white/5 dark:hover:bg-white/5 ${
                activeId === c.guest_id ? 'bg-sand-100 dark:bg-white/10' : ''
              }`}
            >
              <div className="flex items-center justify-between gap-2">
                <span className="flex items-center gap-1.5 truncate font-medium text-ink dark:text-sand-50">
                  <span className={`h-2 w-2 flex-shrink-0 rounded-full ${c.online ? 'bg-teal-600' : 'bg-sand-200 dark:bg-white/15'}`} />
                  {c.sender_name}
                </span>
                {c.unread_count > 0 && <Badge tone="warning">{c.unread_count}</Badge>}
              </div>
              <p className="mt-0.5 truncate text-xs text-ink/50 dark:text-sand-100/45">
                {c.typing ? <span className="italic text-teal-700 dark:text-teal-300">arandika...</span> : c.last_message}
              </p>
              <p className="mt-0.5 text-[11px] text-ink/40 dark:text-sand-100/40">{timeAgo(c.last_message_at)}</p>
            </button>
          ))}
        </div>
      </Card>

      {/* Conversation thread — full width on mobile when it's the active
          screen (with its own back button), flex-1 alongside the list
          on desktop always. */}
      <Card
        className={`flex-col overflow-hidden lg:flex lg:flex-1 ${
          mobileView === 'conversation' ? 'flex w-full' : 'hidden'
        }`}
      >
        {!activeId ? (
          <div className="flex flex-1 items-center justify-center">
            <EmptyState title="Hitamo ubutumwa" description="Hitamo umuntu ku ruhande kugira ngo urebe ubutumwa." />
          </div>
        ) : (
          <>
            <div className="flex items-center gap-2 border-b border-sand-200 px-4 py-3 dark:border-white/10 lg:hidden">
              <button
                onClick={backToList}
                aria-label="Garuka ku rutonde"
                className="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg text-ink/60 hover:bg-sand-100 dark:text-sand-100/60 dark:hover:bg-white/10"
              >
                <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M15 18l-6-6 6-6" strokeLinecap="round" strokeLinejoin="round" />
                </svg>
              </button>
              <span className="truncate font-medium text-ink dark:text-sand-50">{activeName}</span>
            </div>

            <div className="flex-1 space-y-2 overflow-y-auto p-4">
              {messages.map((m) => (
                <div key={m.id} className={`flex ${m.sender_type === 'admin' ? 'justify-end' : 'justify-start'}`}>
                  <div
                    className={`max-w-[85%] rounded-2xl px-3 py-2 text-sm sm:max-w-xs ${
                      m.sender_type === 'admin' ? 'bg-teal-700 text-white' : 'bg-sand-100 text-ink dark:bg-white/10 dark:text-sand-50'
                    }`}
                  >
                    <p>{m.message}</p>
                    <p className={`mt-1 flex items-center gap-1 text-[10px] ${m.sender_type === 'admin' ? 'text-white/60' : 'text-ink/40 dark:text-sand-100/40'}`}>
                      {new Date(m.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                      {m.sender_type === 'admin' && (
                        <span
                          className={m.is_read ? 'text-gold-300' : ''}
                          title={m.is_read ? `Yasomwe${m.read_at ? ' · ' + new Date(m.read_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : ''}` : 'Yoherejwe'}
                        >
                          {m.is_read ? '✓✓' : '✓'}
                        </span>
                      )}
                    </p>
                  </div>
                </div>
              ))}
              <div ref={bottomRef} />
            </div>
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
          </>
        )}
      </Card>
    </div>
  )
}
