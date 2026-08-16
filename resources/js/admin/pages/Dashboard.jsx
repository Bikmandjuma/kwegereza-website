import { useEffect, useState, useCallback } from 'react'
import { Link } from 'react-router-dom'
import { useAuth } from '../contexts/AuthContext'
import { getEcho } from '../services/echo'
import * as presenceApi from '../features/presence/presenceApi'
import * as analyticsApi from '../features/analytics/analyticsApi'
import { Card, Skeleton } from '../components/ui'
import Can from '../permissions/Can'
import StarMark from '../components/StarMark'
import { NAV_GROUPS } from '../navigation'

/**
 * Rebuilt as a real "cool dashboard" (per request, modeled loosely on a
 * shared reference screenshot) — stat cards, a bar-chart trend, a donut,
 * an active-students list, and a highlighted stat panel — but every
 * number here comes from an existing, already-tested API endpoint
 * (AnalyticsService), not invented placeholder data. No chart library
 * is installed in this project (checked package.json before starting),
 * so the bar chart and donut are hand-built with plain SVG rather than
 * pulling in a new dependency that would need another npm install.
 */
export default function Dashboard() {
  const { user, hasPermission } = useAuth()

  return (
    <div className="flex flex-col gap-5 sm:gap-6">
      <WelcomeBanner name={user?.firstname} />

      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <Can permission="students.view">
          <OnlineStudentsCard />
        </Can>
        <Can permission="analytics.view">
          <ExamStatCard />
        </Can>
        <Can permission="analytics.view">
          <AttemptsStatCard />
        </Can>
      </div>

      <Can permission="analytics.view">
        <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
          <Card hover className="p-5 lg:col-span-2">
            <div className="mb-4 flex items-center justify-between">
              <h2 className="font-display text-lg text-ink dark:text-sand-50">Abanyeshuri bari kuri interineti</h2>
            </div>
            <DailyActiveBarChart />
          </Card>

          <Card hover className="p-5">
            <h2 className="mb-4 font-display text-lg text-ink dark:text-sand-50">Ibizamini</h2>
            <ExamDonut />
          </Card>
        </div>

        <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
          <Card hover className="p-5 lg:col-span-2">
            <h2 className="mb-4 font-display text-lg text-ink dark:text-sand-50">Abanyeshuri bakora cyane</h2>
            <MostActiveStudents />
          </Card>

          <MostPlayedDarsatHighlight />
        </div>
      </Can>

      <Card className="p-5">
        <h2 className="mb-3 font-display text-lg text-ink dark:text-sand-50">Ibikorwa byihuse</h2>
        <QuickLinks hasPermission={hasPermission} />
      </Card>
    </div>
  )
}

function WelcomeBanner({ name }) {
  const hour = new Date().getHours()
  const greeting = hour < 12 ? 'Mwaramutse' : hour < 18 ? 'Mwiriwe' : 'Mwiriwe neza'

  return (
    <div className="relative overflow-hidden rounded-xl2 bg-grad-teal p-6 text-white shadow-lift sm:p-8">
      <div className="pattern-khatam pointer-events-none absolute inset-0" />
      <StarMark className="pointer-events-none absolute -right-10 -top-10 h-40 w-40 animate-spin-slow text-white/[0.05]" strokeWidth={1} />
      <StarMark className="pointer-events-none absolute -bottom-16 left-1/3 h-32 w-32 text-white/[0.04]" strokeWidth={1} />
      <p className="relative text-xs font-medium uppercase tracking-wider text-gold-300">{greeting}</p>
      <h1 className="relative mt-1 font-display text-2xl sm:text-3xl">Muraho, {name}</h1>
      <p className="relative mt-2 max-w-md text-sm text-white/70">Reba incamake y'urubuga rwa Kwegereza.</p>
    </div>
  )
}

function StatCard({ label, value, tone = 'teal', icon }) {
  const toneClasses = {
    teal: 'bg-gradient-to-br from-teal-500 to-teal-800 text-white',
    gold: 'bg-gradient-to-br from-gold-400 to-gold-700 text-white',
    rose: 'bg-gradient-to-br from-rose-500 to-rose-700 text-white',
  }
  return (
    <Card hover className="flex items-center gap-4 p-5">
      <div className={`flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl shadow-soft ${toneClasses[tone]}`}>
        {icon || <StarMark className="h-5 w-5" strokeWidth={1.5} />}
      </div>
      <div className="min-w-0">
        {value === null ? (
          <Skeleton className="h-7 w-16" />
        ) : (
          <p className="font-display text-2xl leading-tight text-ink dark:text-sand-50">{value}</p>
        )}
        <p className="truncate text-xs text-ink/50 dark:text-sand-100/50">{label}</p>
      </div>
    </Card>
  )
}

const IconUsers = (
  <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="2">
    <circle cx="9" cy="8" r="3" /><path d="M2 20c0-3.3 3.1-6 7-6s7 2.7 7 6" strokeLinecap="round" />
    <circle cx="17" cy="9" r="2.5" /><path d="M22 20c0-2.6-2-4.7-4.5-5.4" strokeLinecap="round" />
  </svg>
)
const IconTrophy = (
  <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="2">
    <path d="M8 4h8v5a4 4 0 0 1-8 0V4Z" strokeLinecap="round" strokeLinejoin="round" />
    <path d="M8 5H4v2a4 4 0 0 0 4 4M16 5h4v2a4 4 0 0 1-4 4M10 15v3M14 15v3M8 21h8" strokeLinecap="round" />
  </svg>
)
const IconClipboard = (
  <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="2">
    <rect x="6" y="4" width="12" height="17" rx="2" />
    <path d="M9 4V3a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v1M9 11h6M9 15h6" strokeLinecap="round" />
  </svg>
)

function OnlineStudentsCard() {
  const [count, setCount] = useState(null)

  const refresh = useCallback(() => {
    presenceApi.getOnlineStudents().then((d) => setCount(d.count)).catch(() => {})
  }, [])

  useEffect(() => {
    refresh()
    const interval = setInterval(refresh, 30000)
    return () => clearInterval(interval)
  }, [refresh])

  useEffect(() => {
    const echo = getEcho()
    if (!echo) return
    const channel = echo.private('online-students')
    channel.listen('.student.online', () => refresh())
    return () => echo.leave('online-students')
  }, [refresh])

  return <StatCard label="Abanyeshuri bari kuri interineti" value={count} tone="teal" icon={IconUsers} />
}

function ExamStatCard() {
  const [pct, setPct] = useState(null)
  useEffect(() => {
    analyticsApi.getOverview().then((d) => setPct(d.exam_participation.pass_rate)).catch(() => {})
  }, [])
  return <StatCard label="Igipimo cyo gutsinda ibizamini" value={pct === null ? null : `${pct}%`} tone="gold" icon={IconTrophy} />
}

function AttemptsStatCard() {
  const [total, setTotal] = useState(null)
  useEffect(() => {
    analyticsApi.getOverview().then((d) => setTotal(d.exam_participation.total_attempts)).catch(() => {})
  }, [])
  return <StatCard label="Ibizamini byakozwe (byose)" value={total} tone="rose" icon={IconClipboard} />
}

const TREND_RANGES = [
  { days: 7, label: 'Iki cyumweru' },
  { days: 14, label: 'Ibyumweru 2' },
  { days: 30, label: 'Ukwezi' },
]

function DailyActiveBarChart() {
  const [trend, setTrend] = useState(null)
  const [days, setDays] = useState(7) // defaults to "this week" per spec, not a 2-week rolling window

  useEffect(() => {
    setTrend(null)
    analyticsApi.getOverview(days).then((d) => setTrend(d.daily_active_trend)).catch(() => {})
  }, [days])

  return (
    <div>
      <div className="mb-3 flex justify-end gap-1">
        {TREND_RANGES.map((r) => (
          <button
            key={r.days}
            onClick={() => setDays(r.days)}
            className={`rounded-full px-3 py-1 text-xs font-medium transition-colors ${
              days === r.days
                ? 'bg-teal-700 text-white'
                : 'bg-sand-100 text-ink/60 hover:bg-sand-200 dark:bg-white/10 dark:text-sand-100/60 dark:hover:bg-white/20'
            }`}
          >
            {r.label}
          </button>
        ))}
      </div>
      <DailyActiveBarChartBody trend={trend} />
    </div>
  )
}

function DailyActiveBarChartBody({ trend }) {
  if (!trend) return <Skeleton className="h-40 w-full" />
  if (trend.length === 0) return <p className="text-sm text-ink/50 dark:text-sand-100/50">Nta makuru arahari.</p>

  const max = Math.max(...trend.map((d) => d.peak_online_count), 1)

  return (
    <div className="flex h-40 items-end gap-1.5 sm:gap-2">
      {trend.map((d) => (
        <div key={d.date} className="group flex flex-1 flex-col items-center gap-1.5">
          <div className="relative flex w-full flex-1 items-end">
            <div
              className="w-full rounded-t-md bg-gradient-to-t from-teal-700 to-teal-500 transition-all duration-300 group-hover:from-gold-600 group-hover:to-gold-400"
              style={{ height: `${Math.max((d.peak_online_count / max) * 100, 4)}%` }}
              title={`${d.date}: ${d.peak_online_count}`}
            />
          </div>
          <span className="text-[10px] text-ink/40 dark:text-sand-100/35">{d.date.slice(5)}</span>
        </div>
      ))}
    </div>
  )
}

function ExamDonut() {
  const [data, setData] = useState(null)
  useEffect(() => {
    analyticsApi.getOverview().then((d) => setData(d.exam_participation)).catch(() => {})
  }, [])

  if (!data) return <Skeleton className="mx-auto h-32 w-32 rounded-full" />

  const passRate = data.pass_rate || 0
  const circumference = 2 * Math.PI * 40
  const passLength = (passRate / 100) * circumference

  return (
    <div className="flex flex-col items-center">
      <div className="relative flex h-32 w-32 items-center justify-center">
        <svg viewBox="0 0 100 100" className="absolute inset-0 h-32 w-32 -rotate-90">
          <defs>
            <linearGradient id="donutGrad" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stopColor="#1F6F63" />
              <stop offset="100%" stopColor="#B08D57" />
            </linearGradient>
          </defs>
          <circle cx="50" cy="50" r="40" fill="none" className="stroke-sand-200 dark:stroke-white/10" strokeWidth="12" />
          <circle
            cx="50" cy="50" r="40" fill="none" stroke="url(#donutGrad)" strokeWidth="12"
            strokeDasharray={`${passLength} ${circumference}`} strokeLinecap="round"
            className="transition-all duration-700 ease-spring"
          />
        </svg>
        <p className="relative font-display text-2xl text-ink dark:text-sand-50">{passRate}%</p>
      </div>
      <div className="mt-4 flex gap-4 text-xs text-ink/70 dark:text-sand-100/60">
        <span className="flex items-center gap-1.5"><span className="h-2 w-2 rounded-full bg-teal-600" /> Byatsinzwe ({data.passed})</span>
        <span className="flex items-center gap-1.5"><span className="h-2 w-2 rounded-full bg-sand-200 dark:bg-white/15" /> Byatsinzwe nabi ({data.failed})</span>
      </div>
    </div>
  )
}

function MostActiveStudents() {
  const [students, setStudents] = useState(null)
  useEffect(() => {
    analyticsApi.getOverview().then((d) => setStudents(d.most_active_students)).catch(() => {})
  }, [])

  if (!students) return <Skeleton className="h-32 w-full" />
  if (students.length === 0) return <p className="text-sm text-ink/50 dark:text-sand-100/50">Nta makuru arahari.</p>

  return (
    <ul className="divide-y divide-sand-100 text-sm dark:divide-white/5">
      {students.slice(0, 5).map((s) => {
        const initials = `${s.firstname?.[0] || ''}${s.lastname?.[0] || ''}`.toUpperCase()
        return (
          <li key={s.id} className="flex items-center gap-3 py-2.5">
            <span className="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-teal-600 to-teal-900 text-[11px] font-semibold text-white">
              {initials}
            </span>
            <span className="flex-1 truncate text-ink dark:text-sand-50">{s.firstname} {s.lastname}</span>
            <span className="flex-shrink-0 text-xs text-ink/40 dark:text-sand-100/35">{new Date(s.last_active_at).toLocaleString()}</span>
          </li>
        )
      })}
    </ul>
  )
}

function MostPlayedDarsatHighlight() {
  const [top, setTop] = useState(null)
  useEffect(() => {
    analyticsApi.getOverview().then((d) => setTop(d.most_played_darsat?.[0] || null)).catch(() => {})
  }, [])

  return (
    <div className="relative overflow-hidden rounded-xl2 bg-grad-gold p-5 text-white shadow-lift">
      <div className="pattern-khatam pointer-events-none absolute inset-0" />
      <p className="relative text-xs font-medium uppercase tracking-wider text-white/80">Darsat ikunzwe cyane</p>
      {top ? (
        <>
          <p className="relative mt-2 font-display text-xl">{top.title}</p>
          <p className="relative mt-1 text-sm text-white/85">{top.total_plays} yumviwe · {top.unique_listeners} bumva</p>
        </>
      ) : (
        <p className="relative mt-2 text-sm text-white/80">Nta makuru arahari.</p>
      )}
    </div>
  )
}

function QuickLinks({ hasPermission }) {
  const links = NAV_GROUPS.flatMap((g) => g.items).filter((item) => !item.permission || hasPermission(item.permission))

  if (links.length === 0) {
    return <p className="text-sm text-ink/50 dark:text-sand-100/50">Nta bikorwa biboneka kuri konti yawe.</p>
  }

  return (
    <div className="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
      {links.slice(0, 8).map((link) => (
        <Link
          key={link.to}
          to={link.to}
          className="rounded-xl border border-sand-200 px-3 py-2.5 text-sm text-ink transition-all duration-150 hover:-translate-y-0.5 hover:border-teal-200 hover:bg-teal-50 hover:shadow-soft dark:border-white/10 dark:text-sand-50 dark:hover:border-white/20 dark:hover:bg-white/5"
        >
          {link.label}
        </Link>
      ))}
    </div>
  )
}
