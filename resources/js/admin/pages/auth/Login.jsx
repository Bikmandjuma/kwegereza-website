import { useState } from 'react'
import { useLocation, useNavigate } from 'react-router-dom'
import { useAuth } from '../../contexts/AuthContext'
import { useTheme } from '../../contexts/ThemeContext'
import { Button, Input } from '../../components/ui'
import StarMark from '../../components/StarMark'

export default function Login() {
  const { signIn } = useAuth()
  const { theme, toggleTheme } = useTheme()
  const navigate = useNavigate()
  const location = useLocation()
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [showPassword, setShowPassword] = useState(false)
  const [error, setError] = useState(null)
  const [submitting, setSubmitting] = useState(false)

  async function handleSubmit(e) {
    e.preventDefault()
    setError(null)
    setSubmitting(true)
    try {
      await signIn(email, password)
      navigate(location.state?.from?.pathname || '/dashboard', { replace: true })
    } catch (err) {
      const message =
        err.response?.data?.errors?.email?.[0] ||
        err.response?.data?.message ||
        'Habaye ikibazo. Ongera ugerageze.'
      setError(message)
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <div className="relative flex min-h-screen bg-sand-50 dark:bg-teal-950">
      <button
        onClick={toggleTheme}
        aria-label="Hindura imiterere y'umucyo"
        className="absolute right-4 top-4 z-20 flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white backdrop-blur-md transition-colors hover:bg-white/20 lg:right-6 lg:top-6"
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

      {/* Hero panel — hidden on small screens, becomes the whole story on lg+ */}
      <div className="relative hidden w-1/2 flex-col justify-between overflow-hidden bg-grad-teal p-12 text-white lg:flex">
        <div className="pattern-khatam absolute inset-0 opacity-80" />
        <StarMark className="pointer-events-none absolute -right-24 -top-24 h-96 w-96 animate-spin-slow text-white/[0.04]" strokeWidth={1} />
        <StarMark className="pointer-events-none absolute -bottom-32 -left-16 h-72 w-72 text-white/[0.05]" strokeWidth={1} />

        <div className="relative flex items-center gap-2.5">
          <StarMark className="h-8 w-8 text-gold-300" />
          <span className="font-display text-xl tracking-wide">Kwegereza</span>
        </div>

        <div className="relative max-w-md">
          <p className="font-display text-4xl leading-tight">
            Urubuga rw'uburezi bwa Islam, hamwe n'umuryango wawe.
          </p>
          <p className="mt-5 text-white/70">
            Amasomo, Dars, Ibitabo n'Ibizamini — byose ahantu hamwe, byizewe kandi bifite umutekano.
          </p>
        </div>

        <p className="relative text-xs text-white/40">© {new Date().getFullYear()} Kwegereza · Imiyoborere</p>
      </div>

      {/* Form panel */}
      <div className="flex w-full flex-1 items-center justify-center px-4 py-12 lg:w-1/2">
        <div className="w-full max-w-sm animate-fade-up">
          <div className="mb-8 flex flex-col items-center gap-2 text-center lg:hidden">
            <StarMark className="h-10 w-10 text-teal-700 dark:text-gold-300" />
            <h1 className="font-display text-2xl text-ink dark:text-sand-50">Kwegereza</h1>
          </div>

          <div className="hidden lg:block">
            <h1 className="font-display text-2xl text-ink dark:text-sand-50">Murakaza neza</h1>
            <p className="mt-1 text-sm text-ink/60 dark:text-sand-100/60">Injira kugira ngo ubone imiyoborere</p>
          </div>

          <p className="mb-6 text-center text-sm text-ink/60 dark:text-sand-100/60 lg:hidden">
            Injira kugira ngo ubone imiyoborere
          </p>

          <form onSubmit={handleSubmit} className="mt-6 flex flex-col gap-4">
            <Input
              label="Imeyili"
              type="email"
              required
              autoFocus
              placeholder="wowe@urugero.com"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
            />

            <div className="relative">
              <Input
                label="Ijambo banga"
                type={showPassword ? 'text' : 'password'}
                required
                placeholder="••••••••"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                className="pr-10"
              />
              <button
                type="button"
                onClick={() => setShowPassword((s) => !s)}
                className="absolute right-3 top-[38px] text-ink/40 hover:text-ink/70 dark:text-sand-100/40 dark:hover:text-sand-100/80"
                aria-label={showPassword ? 'Hisha ijambo banga' : 'Erekana ijambo banga'}
              >
                {showPassword ? (
                  <svg viewBox="0 0 24 24" className="h-4.5 w-4.5" fill="none" stroke="currentColor" strokeWidth="2">
                    <path d="M3 3l18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.4 5.5A9.8 9.8 0 0 1 12 5c5 0 9 4 10 7-.5 1.5-1.6 3.3-3.1 4.6M6.1 6.9C4.3 8.2 3 9.9 2 12c1 3 5 7 10 7 1 0 2-.2 2.9-.5" strokeLinecap="round" strokeLinejoin="round" />
                  </svg>
                ) : (
                  <svg viewBox="0 0 24 24" className="h-4.5 w-4.5" fill="none" stroke="currentColor" strokeWidth="2">
                    <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7Z" strokeLinecap="round" strokeLinejoin="round" />
                    <circle cx="12" cy="12" r="3" />
                  </svg>
                )}
              </button>
            </div>

            {error && (
              <div className="flex items-start gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2.5 text-sm text-rose-700 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-300">
                <svg viewBox="0 0 24 24" className="mt-0.5 h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" strokeWidth="2">
                  <circle cx="12" cy="12" r="10" />
                  <path d="M12 8v5M12 16h.01" strokeLinecap="round" />
                </svg>
                <span>{error}</span>
              </div>
            )}

            <Button type="submit" size="lg" disabled={submitting} className="mt-2 w-full">
              {submitting ? (
                <>
                  <svg className="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                  </svg>
                  Kwinjira...
                </>
              ) : (
                'Injira'
              )}
            </Button>
          </form>
        </div>
      </div>
    </div>
  )
}
