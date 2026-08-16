import { useEffect, useRef, useState, useCallback } from 'react'
import * as notificationsApi from '../features/notifications/notificationsApi'
import { useAuth } from '../contexts/AuthContext'
import { getEcho } from '../services/echo'

export default function NotificationBell({ variant = 'dark' }) {
  const { user } = useAuth()
  const [open, setOpen] = useState(false)
  const [count, setCount] = useState(0)
  const [items, setItems] = useState([])
  const [loading, setLoading] = useState(false)
  const ref = useRef(null)

  const refreshCount = useCallback(() => {
    notificationsApi.unreadCount().then(setCount).catch(() => {})
  }, [])

  useEffect(() => {
    refreshCount()
    // Polling stays on regardless of whether realtime connects — the
    // fallback, not a leftover. Interval is generous (30s) since the
    // WebSocket subscription below is the primary path when it's available.
    const interval = setInterval(refreshCount, 30000)
    return () => clearInterval(interval)
  }, [refreshCount])

  // Realtime: instant update the moment a new notification is broadcast,
  // on this owner's own private channel (routes/channels.php). If Echo
  // isn't configured or the connection drops, this effect simply does
  // nothing further — the poll above still covers it.
  useEffect(() => {
    if (!user?.id) return
    const echo = getEcho()
    if (!echo) return

    const channel = echo.private(`owner.${user.id}.notifications`)
    channel.notification(() => {
      refreshCount()
      if (open) {
        notificationsApi.listNotifications().then((res) => setItems(res.data)).catch(() => {})
      }
    })

    return () => {
      echo.leave(`owner.${user.id}.notifications`)
    }
  }, [user?.id, open, refreshCount])

  useEffect(() => {
    function handleClickOutside(e) {
      if (ref.current && !ref.current.contains(e.target)) setOpen(false)
    }
    document.addEventListener('mousedown', handleClickOutside)
    return () => document.removeEventListener('mousedown', handleClickOutside)
  }, [])

  async function toggleOpen() {
    const next = !open
    setOpen(next)
    if (next) {
      setLoading(true)
      try {
        const res = await notificationsApi.listNotifications()
        setItems(res.data)
      } finally {
        setLoading(false)
      }
    }
  }

  async function handleItemClick(item) {
    if (!item.is_read) {
      await notificationsApi.markRead(item.id)
      setItems((prev) => prev.map((i) => (i.id === item.id ? { ...i, is_read: true } : i)))
      refreshCount()
    }
    if (item.url) window.location.href = item.url
  }

  async function handleMarkAllRead() {
    await notificationsApi.markAllRead()
    setItems((prev) => prev.map((i) => ({ ...i, is_read: true })))
    setCount(0)
  }

  return (
    <div className="relative" ref={ref}>
      <button
        onClick={toggleOpen}
        aria-label="Ibimenyetso"
        className={`relative rounded-xl p-2 transition-colors ${
          variant === 'dark'
            ? 'text-white/80 hover:bg-white/10 hover:text-white'
            : 'text-ink/60 hover:bg-sand-100 hover:text-ink dark:text-sand-100/60 dark:hover:bg-white/10 dark:hover:text-sand-50'
        }`}
      >
        <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="2">
          <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 0 0-4-5.7V5a2 2 0 1 0-4 0v.3A6 6 0 0 0 6 11v3.2a2 2 0 0 1-.6 1.4L4 17h5" strokeLinecap="round" strokeLinejoin="round" />
          <path d="M9 17a3 3 0 0 0 6 0" strokeLinecap="round" />
        </svg>
        {count > 0 && (
          <span className="absolute right-0.5 top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-semibold text-white ring-2 ring-white dark:ring-teal-900">
            {count > 9 ? '9+' : count}
          </span>
        )}
      </button>

      {open && (
        <div className="absolute right-0 z-50 mt-2 w-80 animate-fade-up overflow-hidden rounded-xl2 border border-sand-200 bg-white shadow-lift dark:border-white/10 dark:bg-teal-900">
          <div className="flex items-center justify-between border-b border-sand-200 px-4 py-3 dark:border-white/10">
            <span className="font-display text-ink dark:text-sand-50">Ibimenyetso</span>
            {count > 0 && (
              <button onClick={handleMarkAllRead} className="text-xs font-medium text-teal-700 hover:underline dark:text-gold-300">
                Byose byasomwe
              </button>
            )}
          </div>
          <div className="max-h-80 overflow-y-auto">
            {loading && (
              <div className="space-y-3 p-4">
                {[0, 1, 2].map((i) => (
                  <div key={i} className="flex gap-3">
                    <div className="shimmer h-8 w-8 flex-shrink-0 animate-shimmer rounded-full" />
                    <div className="flex-1 space-y-2">
                      <div className="shimmer h-3 w-3/4 animate-shimmer rounded" />
                      <div className="shimmer h-2.5 w-1/2 animate-shimmer rounded" />
                    </div>
                  </div>
                ))}
              </div>
            )}
            {!loading && items.length === 0 && (
              <p className="p-6 text-center text-sm text-ink/45 dark:text-sand-100/45">Nta kimenyetso kirahari.</p>
            )}
            {!loading && items.map((item) => (
              <button
                key={item.id}
                onClick={() => handleItemClick(item)}
                className={`flex w-full items-start gap-3 border-b border-sand-100 px-4 py-3 text-left text-sm transition-colors last:border-0 hover:bg-sand-50 dark:border-white/5 dark:hover:bg-white/5 ${
                  item.is_read ? 'text-ink/60 dark:text-sand-100/60' : 'font-medium text-ink dark:text-sand-50'
                }`}
              >
                {!item.is_read && <span className="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-gold-500" />}
                <span className={item.is_read ? 'pl-4' : ''}>
                  <p>{item.title}</p>
                  <p className="mt-0.5 text-xs text-ink/50 dark:text-sand-100/40">{item.message}</p>
                </span>
              </button>
            ))}
          </div>
        </div>
      )}
    </div>
  )
}
