import { Navigate, useLocation } from 'react-router-dom'
import { useAuth } from '../contexts/AuthContext'

/** Blocks the route entirely if not logged in — redirects to /login. */
export function RequireAuth({ children }) {
  const { user, loading } = useAuth()
  const location = useLocation()

  if (loading) return <FullPageSpinner />
  if (!user) return <Navigate to="/login" state={{ from: location }} replace />
  return children
}

/**
 * Blocks the route if the user lacks a permission — this is a UX nicety
 * (avoids landing on a page that will just show empty/403 states from the
 * API), NOT the security boundary. Laravel still rejects the underlying
 * API calls regardless of whether this ever renders.
 *
 * Redirects straight to /dashboard rather than a distinct "forbidden"
 * page — since the sidebar already hides any link a role isn't permitted
 * to see, the only way to land here at all is a stale bookmark or typed
 * URL, and showing a dedicated 403/not-found-style page in that case
 * reads as a broken link. Quietly landing back on the dashboard, the
 * same place an unpermitted item would never have appeared in the first
 * place, is the less jarring outcome for a Leader (or any role) hitting
 * something outside their access.
 */
export function RequirePermission({ permission, children }) {
  const { hasPermission } = useAuth()
  if (!hasPermission(permission)) return <Navigate to="/dashboard" replace />
  return children
}

function FullPageSpinner() {
  return (
    <div className="flex h-screen w-full items-center justify-center bg-sand-50">
      <div className="h-8 w-8 animate-spin rounded-full border-2 border-teal-700 border-t-transparent" />
    </div>
  )
}
