import { useAuth } from '../contexts/AuthContext'

/**
 * The single reusable authorization layer for the UI, per spec section 5:
 * "Do not scatter permission logic randomly throughout components."
 *
 * Usage:
 *   <Can permission="darsat.create"><button>Create Dars</button></Can>
 *   <Can permission="darsat.delete" fallback={<span>—</span>}>...</Can>
 *
 * This ONLY controls what renders. It is not a security boundary — Laravel
 * (permission.api middleware) is. A user could still hit the API directly;
 * this component exists purely so honest users never see controls they
 * can't use, matching spec section 6 ("do not trust the frontend").
 */
export default function Can({ permission, children, fallback = null }) {
  const { hasPermission } = useAuth()
  return hasPermission(permission) ? children : fallback
}
