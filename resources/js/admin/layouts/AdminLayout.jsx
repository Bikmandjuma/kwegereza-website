import { useEffect, useMemo, useRef, useState } from 'react'
import { NavLink, Outlet, Link, useLocation, useNavigate } from 'react-router-dom'
import { useAuth } from '../contexts/AuthContext'
import { useTheme } from '../contexts/ThemeContext'
import StarMark from '../components/StarMark'
import NotificationBell from '../components/NotificationBell'
import OnlinePresenceToasts from '../components/OnlinePresenceToasts'
import { NAV_GROUPS } from '../navigation'

function NavGroup({ group, hasPermission, currentPath, onNavigate }) {
  const visibleItems = group.items.filter((item) => !item.permission || hasPermission(item.permission))
  const containsActive = visibleItems.some((item) => currentPath.startsWith(item.to))
  const [open, setOpen] = useState(containsActive)

  if (visibleItems.length === 0) return null

  return (
    <div>
      <button
        onClick={() => setOpen((o) => !o)}
        className="flex w-full items-center justify-between rounded-lg px-3 py-2 text-[11px] font-semibold uppercase tracking-wider text-white/40 transition-colors hover:text-white/70"
      >
        {group.label}
        <svg viewBox="0 0 24 24" className={`h-3.5 w-3.5 transition-transform duration-200 ${open ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" strokeWidth="2">
          <path d="M6 9l6 6 6-6" strokeLinecap="round" strokeLinejoin="round" />
        </svg>
      </button>
      <div className={`grid transition-all duration-200 ease-spring ${open ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'}`}>
        <div className="overflow-hidden">
          <div className="flex flex-col gap-0.5 pb-1 pl-2">
            {visibleItems.map((item) => (
              <NavLink
                key={item.to}
                to={item.to}
                onClick={onNavigate}
                className={({ isActive }) =>
                  `group relative flex items-center rounded-lg px-3 py-2 pl-5 text-sm transition-colors ${
                    isActive ? 'bg-white/10 font-medium text-white' : 'text-white/60 hover:bg-white/5 hover:text-white'
                  }`
                }
              >
                {({ isActive }) => (
                  <>
                    <span className={`absolute left-0 top-1/2 h-4 w-0.5 -translate-y-1/2 rounded-full bg-gold-400 transition-opacity ${isActive ? 'opacity-100' : 'opacity-0'}`} />
                    {item.label}
                  </>
                )}
              </NavLink>
            ))}
          </div>
        </div>
      </div>
    </div>
  )
}

function QuickNav({ hasPermission, onNavigate }) {
  const navigate = useNavigate()
  const [query, setQuery] = useState('')
  const allItems = useMemo(
    () => NAV_GROUPS.flatMap((g) => g.items).filter((item) => !item.permission || hasPermission(item.permission)),
    [hasPermission]
  )
  const results = query.trim()
    ? allItems.filter((item) => item.label.toLowerCase().includes(query.trim().toLowerCase()))
    : []

  function goTo(item) {
    navigate(item.to)
    setQuery('')
    onNavigate?.()
  }

  return (
    <div className="relative w-full max-w-xs">
      <svg viewBox="0 0 24 24" className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink/35 dark:text-sand-100/35" fill="none" stroke="currentColor" strokeWidth="2">
        <circle cx="11" cy="11" r="7" />
        <path d="M21 21l-4.3-4.3" strokeLinecap="round" />
      </svg>
      <input
        value={query}
        onChange={(e) => setQuery(e.target.value)}
        onKeyDown={(e) => {
          if (e.key === 'Enter' && results[0]) goTo(results[0])
          if (e.key === 'Escape') setQuery('')
        }}
        placeholder="Shakisha ahantu..."
        className="w-full rounded-xl border border-sand-200 bg-sand-50 py-2 pl-9 pr-3 text-sm text-ink outline-none transition-colors placeholder:text-ink/35 focus:border-teal-600 focus-visible:ring-2 focus-visible:ring-teal-600/20 dark:border-white/10 dark:bg-teal-900/40 dark:text-sand-50 dark:placeholder:text-sand-100/30"
      />
      {query.trim() && (
        <div className="absolute left-0 right-0 top-full z-50 mt-1.5 max-h-64 overflow-y-auto rounded-xl border border-sand-200 bg-white py-1 shadow-lift dark:border-white/10 dark:bg-teal-900">
          {results.length === 0 && <p className="px-3 py-2 text-sm text-ink/45 dark:text-sand-100/45">Nta kintu kibonetse.</p>}
          {results.map((item) => (
            <button
              key={item.to}
              onClick={() => goTo(item)}
              className="block w-full px-3 py-2 text-left text-sm text-ink hover:bg-sand-50 dark:text-sand-50 dark:hover:bg-white/5"
            >
              {item.label}
            </button>
          ))}
        </div>
      )}
    </div>
  )
}

function ThemeToggle() {
  const { theme, toggleTheme } = useTheme()
  return (
    <button
      onClick={toggleTheme}
      aria-label="Hindura imiterere y'umucyo"
      className="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl text-ink/60 transition-colors hover:bg-sand-100 hover:text-ink dark:text-sand-100/60 dark:hover:bg-white/10 dark:hover:text-sand-50"
    >
      {theme === 'dark' ? (
        <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="2">
          <circle cx="12" cy="12" r="4" />
          <path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" strokeLinecap="round" />
        </svg>
      ) : (
        <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="2">
          <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z" strokeLinecap="round" strokeLinejoin="round" />
        </svg>
      )}
    </button>
  )
}

function AvatarMenu({ user, signOut }) {
  const [open, setOpen] = useState(false)
  const ref = useRef(null)
  const initials = `${user?.firstname?.[0] || ''}${user?.lastname?.[0] || ''}`.toUpperCase()

  useEffect(() => {
    function onClick(e) {
      if (ref.current && !ref.current.contains(e.target)) setOpen(false)
    }
    document.addEventListener('mousedown', onClick)
    return () => document.removeEventListener('mousedown', onClick)
  }, [])

  return (
    <div className="relative" ref={ref}>
      <button
        onClick={() => setOpen((o) => !o)}
        className="flex items-center gap-2 rounded-xl py-1 pl-1 pr-2 transition-colors hover:bg-sand-100 dark:hover:bg-white/10"
      >
        <span className="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-teal-600 to-teal-900 text-xs font-semibold text-white shadow-soft">
          {initials || '?'}
        </span>
        <span className="hidden text-left sm:block">
          <span className="block text-sm font-medium leading-tight text-ink dark:text-sand-50">{user?.firstname}</span>
        </span>
        <svg viewBox="0 0 24 24" className={`hidden h-3.5 w-3.5 text-ink/40 transition-transform sm:block dark:text-sand-100/40 ${open ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" strokeWidth="2">
          <path d="M6 9l6 6 6-6" strokeLinecap="round" strokeLinejoin="round" />
        </svg>
      </button>

      {open && (
        <div className="absolute right-0 z-50 mt-2 w-56 animate-fade-up overflow-hidden rounded-xl2 border border-sand-200 bg-white py-1.5 shadow-lift dark:border-white/10 dark:bg-teal-900">
          <div className="border-b border-sand-100 px-3.5 py-2.5 dark:border-white/10">
            <p className="truncate text-sm font-medium text-ink dark:text-sand-50">{user?.firstname} {user?.lastname}</p>
            <p className="truncate text-xs text-ink/50 dark:text-sand-100/45">{user?.email}</p>
          </div>
          <Link
            to="/profile"
            onClick={() => setOpen(false)}
            className="flex items-center gap-2.5 px-3.5 py-2 text-sm text-ink transition-colors hover:bg-sand-50 dark:text-sand-100 dark:hover:bg-white/5"
          >
            <svg viewBox="0 0 24 24" className="h-4 w-4 text-ink/40 dark:text-sand-100/40" fill="none" stroke="currentColor" strokeWidth="2">
              <circle cx="12" cy="8" r="4" /><path d="M4 20c1.5-4 5-6 8-6s6.5 2 8 6" strokeLinecap="round" />
            </svg>
            Igenamiterere
          </Link>
          <button
            onClick={signOut}
            className="flex w-full items-center gap-2.5 px-3.5 py-2 text-left text-sm text-rose-600 transition-colors hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-500/10"
          >
            <svg viewBox="0 0 24 24" className="h-4 w-4" fill="none" stroke="currentColor" strokeWidth="2">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" strokeLinecap="round" strokeLinejoin="round" />
            </svg>
            Sohoka
          </button>
        </div>
      )}
    </div>
  )
}

export default function AdminLayout() {
  const { user, hasPermission, signOut } = useAuth()
  const location = useLocation()
  const [mobileOpen, setMobileOpen] = useState(false)

  useEffect(() => { setMobileOpen(false) }, [location.pathname])

  return (
    <div className="flex min-h-screen bg-sand-50 dark:bg-teal-950">
      {mobileOpen && (
        <div
          className="fixed inset-0 z-30 bg-ink/50 backdrop-blur-sm transition-opacity xl:hidden"
          onClick={() => setMobileOpen(false)}
        />
      )}

      <aside
        className={`fixed inset-y-0 left-0 z-40 flex w-72 transform flex-col overflow-hidden bg-grad-teal text-white transition-transform duration-300 ease-spring xl:static xl:translate-x-0 ${
          mobileOpen ? 'translate-x-0' : '-translate-x-full'
        }`}
      >
        <div className="pattern-khatam pointer-events-none absolute inset-0" />
        <StarMark className="pointer-events-none absolute -right-16 -top-16 h-64 w-64 text-white/[0.04]" strokeWidth={1} />

        <div className="relative flex items-center gap-2.5 px-6 py-6">
          <span className="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-white/10">
            <StarMark className="h-5 w-5 text-gold-300" />
          </span>
          <span className="font-display text-lg tracking-wide">Kwegereza</span>
          <button
            onClick={() => setMobileOpen(false)}
            className="ml-auto rounded-lg p-1.5 text-white/60 hover:bg-white/10 hover:text-white xl:hidden"
            aria-label="Funga urutonde"
          >
            <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="2">
              <path d="M6 6l12 12M18 6L6 18" strokeLinecap="round" />
            </svg>
          </button>
        </div>

        <nav className="relative flex flex-1 flex-col gap-1 overflow-y-auto px-3 pb-4">
          <NavLink
            to="/dashboard"
            className={({ isActive }) =>
              `flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition-colors ${
                isActive ? 'bg-white/10 font-medium text-white' : 'text-white/70 hover:bg-white/5 hover:text-white'
              }`
            }
          >
            <svg viewBox="0 0 24 24" className="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" strokeWidth="2">
              <path d="M3 11l9-7 9 7M5 10v10h14V10" strokeLinecap="round" strokeLinejoin="round" />
            </svg>
            Ahabanza
          </NavLink>

          <div className="my-2 border-t border-white/10" />

          {NAV_GROUPS.map((group) => (
            <NavGroup
              key={group.label}
              group={group}
              hasPermission={hasPermission}
              currentPath={location.pathname}
              onNavigate={() => setMobileOpen(false)}
            />
          ))}
        </nav>

        <div className="relative border-t border-white/10 p-4">
          <p className="truncate text-xs text-white/40">Kwegereza · Imiyoborere</p>
        </div>
      </aside>

      <div className="flex min-w-0 flex-1 flex-col xl:pl-0">
        <header className="sticky top-0 z-20 flex items-center gap-3 border-b border-sand-200 bg-white/80 px-4 py-3 backdrop-blur-md dark:border-white/10 dark:bg-teal-950/80">
          <button
            onClick={() => setMobileOpen(true)}
            aria-label="Fungura urutonde"
            className="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl text-ink/70 hover:bg-sand-100 dark:text-sand-100/70 dark:hover:bg-white/10 xl:hidden"
          >
            <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="2">
              <path d="M4 6h16M4 12h16M4 18h16" strokeLinecap="round" />
            </svg>
          </button>

          <div className="hidden sm:block">
            <QuickNav hasPermission={hasPermission} />
          </div>

          <div className="ml-auto flex items-center gap-1.5">
            <ThemeToggle />
            <NotificationBell variant="light" />
            <div className="mx-1 h-6 w-px bg-sand-200 dark:bg-white/10" />
            <AvatarMenu user={user} signOut={signOut} />
          </div>
        </header>

        <main className="flex-1 p-4 sm:p-6 lg:p-8">
          <div key={location.pathname} className="animate-fade-up">
            <Outlet />
          </div>
        </main>
      </div>

      <OnlinePresenceToasts />
    </div>
  )
}
