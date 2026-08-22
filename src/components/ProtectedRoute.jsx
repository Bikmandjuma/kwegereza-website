import { Navigate } from "react-router-dom";
import { useAuth } from "../context/AuthContext.jsx";

/**
 * Frontend RBAC is a UX convenience only — it hides links/pages that a role
 * can't use. The backend (middleware/auth.ts requirePermission) is always the
 * final authority; this component never substitutes for that check.
 */
export default function ProtectedRoute({ roles, permission, anyOfPermissions, children }) {
  const { user, loading } = useAuth();

  if (loading) {
    return (
      <div className="min-h-[60vh] flex items-center justify-center text-ink-soft">
        Turimo kubona...
      </div>
    );
  }

  if (!user) return <Navigate to="/login" replace />;

  if (roles && !roles.includes(user.role)) {
    return <Navigate to="/" replace />;
  }

  const perms = user.permissions ?? [];
  if (permission && user.role !== "ADMIN" && !perms.includes(permission)) {
    return <Navigate to="/" replace />;
  }
  if (anyOfPermissions && user.role !== "ADMIN" && !anyOfPermissions.some((p) => perms.includes(p))) {
    return <Navigate to="/" replace />;
  }

  return children;
}
