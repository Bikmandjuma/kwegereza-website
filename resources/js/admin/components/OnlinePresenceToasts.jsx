import { useEffect, useState } from 'react'
import { getEcho } from '../services/echo'
import { useAuth } from '../contexts/AuthContext'

/**
 * Spec section 17, verbatim: "Student: Aisha Uwimana is online now" —
 * bottom-right, blue background, white text, ~5 seconds. Deliberately a
 * distinct blue (not the app's teal ToastContext styling) since the spec
 * calls out this exact look for this exact feature.
 *
 * Only mounted for owners who can actually see this (students.view) — see
 * AdminLayout. Duplicate prevention is structural, not client-side: the
 * backend (UpdateLastActive) only ever broadcasts on a genuine
 * OFFLINE->ONLINE transition, so this component doesn't need its own
 * de-duplication logic to avoid spamming multiple tabs of the same student.
 */
export default function OnlinePresenceToasts() {
  const { hasPermission } = useAuth()
  const [toasts, setToasts] = useState([])

  useEffect(() => {
    if (!hasPermission('students.view')) return
    const echo = getEcho()
    if (!echo) return

    const channel = echo.private('online-students')
    channel.listen('.student.online', (payload) => {
      const id = `${payload.id}-${payload.at}`
      setToasts((prev) => [...prev, { id, name: payload.name }])
      setTimeout(() => {
        setToasts((prev) => prev.filter((t) => t.id !== id))
      }, 5000)
    })

    return () => echo.leave('online-students')
  }, [hasPermission])

  if (toasts.length === 0) return null

  return (
    <div className="fixed bottom-4 right-4 z-50 flex flex-col gap-2">
      {toasts.map((t) => (
        <div key={t.id} className="animate-toast-in rounded-lg bg-blue-600 px-4 py-3 text-sm text-white shadow-lg">
          Umunyeshuri: {t.name} ari kuri interineti nonaha
        </div>
      ))}
    </div>
  )
}
