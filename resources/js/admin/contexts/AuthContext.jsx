import { createContext, useContext, useEffect, useState, useCallback } from 'react'
import * as authService from '../services/authService'

const AuthContext = createContext(null)

export function AuthProvider({ children }) {
  const [user, setUser] = useState(() => {
    const raw = localStorage.getItem('kwegereza_user')
    return raw ? JSON.parse(raw) : null
  })
  const [loading, setLoading] = useState(true)

  const persistUser = (u) => {
    setUser(u)
    if (u) localStorage.setItem('kwegereza_user', JSON.stringify(u))
    else localStorage.removeItem('kwegereza_user')
  }

  // On mount, if we have a token, confirm it's still valid and refresh the
  // permission list (roles can change between sessions).
  useEffect(() => {
    const token = localStorage.getItem('kwegereza_token')
    if (!token) {
      setLoading(false)
      return
    }
    authService
      .fetchMe()
      .then(persistUser)
      .catch(() => persistUser(null))
      .finally(() => setLoading(false))
  }, [])

  const signIn = useCallback(async (email, password) => {
    const { token, user: freshUser } = await authService.login(email, password)
    localStorage.setItem('kwegereza_token', token)
    persistUser(freshUser)
    return freshUser
  }, [])

  const signOut = useCallback(async () => {
    try {
      await authService.logout()
    } finally {
      localStorage.removeItem('kwegereza_token')
      persistUser(null)
    }
  }, [])

  const hasPermission = useCallback(
    (slug) => Boolean(user?.permissions?.includes(slug)),
    [user]
  )

  const value = { user, loading, signIn, signOut, hasPermission }

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}

export function useAuth() {
  const ctx = useContext(AuthContext)
  if (!ctx) throw new Error('useAuth must be used within an AuthProvider')
  return ctx
}
