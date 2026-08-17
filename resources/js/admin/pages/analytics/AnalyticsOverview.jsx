import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import * as analyticsApi from '../../features/analytics/analyticsApi'
import { Card, EmptyState, Badge, CardSkeleton, Skeleton } from '../../components/ui'

const TREND_RANGES = [
  { days: 7, label: 'Iki cyumweru' },
  { days: 14, label: 'Ibyumweru 2' },
  { days: 30, label: 'Ukwezi' },
  { days: 90, label: 'Amezi 3' },
]

export default function AnalyticsOverview() {
  const [data, setData] = useState(null)
  const [error, setError] = useState(null)
  const [trend, setTrend] = useState(null)
  const [days, setDays] = useState(7)

  useEffect(() => {
    analyticsApi.getOverview()
      .then(setData)
      .catch((err) => setError(err.response?.status === 403 ? 'Ntabwo ufite uburenganzira bwo kubona ibi bipimo.' : 'Ntibishoboka gushaka ibipimo.'))
  }, [])

  useEffect(() => {
    setTrend(null)
    analyticsApi.getOverview(days).then((d) => setTrend(d.daily_active_trend)).catch(() => {})
  }, [days])

  if (error) return <EmptyState title="Habaye ikibazo" description={error} />
  if (!data) return (
    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
      <Card className="p-5"><CardSkeleton /></Card>
      <Card className="p-5"><CardSkeleton /></Card>
    </div>
  )

  const { exam_participation: exam } = data

  return (
    <div className="flex flex-col gap-6">
      <div>
        <h1 className="font-display text-2xl text-ink dark:text-sand-50">Ibipimo by'Ikoreshwa rw'Urubuga</h1>
        <p className="text-sm text-ink/60 dark:text-sand-100/60">Incamake y'uko abanyeshuri bakoresha urubuga — ibyakozwe, ibyasomwe, n'ibyakuweho</p>
      </div>

      <div className="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <StatCard label="Ibizamini byakozwe" value={exam.total_attempts} />
        <StatCard label="Ijanisha ry'itsinda" value={`${exam.pass_rate}%`} />
        <StatCard label="Amanota (impuzandengo)" value={exam.average_percentage != null ? `${exam.average_percentage}%` : '—'} />
        <StatCard label="Bigikomeje" value={exam.in_progress} />
      </div>

      {/* Online-activity trend, with a real time-range picker — this
          answers "where do I see activity over different time periods"
          directly, reusing the same endpoint/param the Dashboard chart
          already uses (Phase E), just exposed here with more range
          options for a proper analytics view rather than a quick glance. */}
      <Card className="p-5">
        <div className="mb-4 flex flex-wrap items-center justify-between gap-3">
          <h2 className="font-display text-lg text-ink dark:text-sand-50">Abanyeshuri bari kuri interineti</h2>
          <div className="flex gap-1">
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
        </div>
        {!trend ? (
          <Skeleton className="h-40 w-full" />
        ) : trend.length === 0 ? (
          <p className="text-sm text-ink/50 dark:text-sand-100/50">Nta makuru arahari kuri iki gihe.</p>
        ) : (
          <TrendChart trend={trend} />
        )}
      </Card>

      <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <Card className="p-5">
          <h2 className="mb-3 font-display text-lg text-ink dark:text-sand-50">Darsat zikunzwe cyane</h2>
          {data.most_played_darsat.length === 0 ? (
            <p className="text-sm text-ink/50 dark:text-sand-100/50">Nta makuru arahari.</p>
          ) : (
            <div className="overflow-x-auto"><table className="w-full min-w-[400px] text-left text-sm">
              <thead className="text-xs uppercase tracking-wide text-ink/50 dark:text-sand-100/50">
                <tr><th className="py-2">Umutwe</th><th className="py-2">Kwakiriwe</th><th className="py-2">Abumva</th></tr>
              </thead>
              <tbody className="divide-y divide-sand-200 dark:divide-white/10">
                {data.most_played_darsat.map((d) => (
                  <tr key={d.darsat_id}>
                    <td className="py-2">{d.title} <Badge tone="neutral">{d.type}</Badge></td>
                    <td className="py-2">{d.total_plays}</td>
                    <td className="py-2">{d.unique_listeners}</td>
                  </tr>
                ))}
              </tbody>
            </table></div>
          )}
        </Card>

        <Card className="p-5">
          <h2 className="mb-3 font-display text-lg text-ink dark:text-sand-50">Ibitabo bikururwa cyane</h2>
          {data.most_downloaded_books.length === 0 ? (
            <p className="text-sm text-ink/50 dark:text-sand-100/50">Nta makuru arahari.</p>
          ) : (
            <div className="overflow-x-auto"><table className="w-full min-w-[300px] text-left text-sm">
              <thead className="text-xs uppercase tracking-wide text-ink/50 dark:text-sand-100/50">
                <tr><th className="py-2">Umutwe</th><th className="py-2">Kwakiriwe</th></tr>
              </thead>
              <tbody className="divide-y divide-sand-200 dark:divide-white/10">
                {data.most_downloaded_books.map((b) => (
                  <tr key={b.book_id}>
                    <td className="py-2">{b.title}<br /><span className="text-xs text-ink/50 dark:text-sand-100/50">{b.author || '—'}</span></td>
                    <td className="py-2">{b.total_downloads}</td>
                  </tr>
                ))}
              </tbody>
            </table></div>
          )}
        </Card>

        <Card className="p-5">
          <h2 className="mb-3 font-display text-lg text-ink dark:text-sand-50">Ibitabo birebwa cyane</h2>
          {!data.most_viewed_books || data.most_viewed_books.length === 0 ? (
            <p className="text-sm text-ink/50 dark:text-sand-100/50">Nta makuru arahari.</p>
          ) : (
            <div className="overflow-x-auto"><table className="w-full min-w-[300px] text-left text-sm">
              <thead className="text-xs uppercase tracking-wide text-ink/50 dark:text-sand-100/50">
                <tr><th className="py-2">Umutwe</th><th className="py-2">Byarebwe</th></tr>
              </thead>
              <tbody className="divide-y divide-sand-200 dark:divide-white/10">
                {data.most_viewed_books.map((b) => (
                  <tr key={b.book_id}>
                    <td className="py-2">{b.title}<br /><span className="text-xs text-ink/50 dark:text-sand-100/50">{b.author || '—'}</span></td>
                    <td className="py-2">{b.views}</td>
                  </tr>
                ))}
              </tbody>
            </table></div>
          )}
        </Card>
      </div>

      <Card className="p-5">
        <h2 className="mb-1 font-display text-lg text-ink dark:text-sand-50">Abanyeshuri bakora cyane</h2>
        <p className="mb-3 text-xs text-ink/45 dark:text-sand-100/45">
          Bashyizwe ku rutonde hakurikijwe igihe baheruka kuba kuri interineti — ntabwo bipimo igihe cyose bamaze bafunguye urubuga (ako gapimo ntabwo karahari muri sisitemu).
        </p>
        {data.most_active_students.length === 0 ? (
          <p className="text-sm text-ink/50 dark:text-sand-100/50">Nta makuru arahari.</p>
        ) : (
          <ul className="divide-y divide-sand-200 text-sm dark:divide-white/10">
            {data.most_active_students.map((s) => (
              <li key={s.id} className="flex items-center justify-between py-2.5">
                <Link to={`/students/${s.id}`} className="text-ink hover:underline dark:text-sand-50">{s.firstname} {s.lastname}</Link>
                <div className="flex items-center gap-3">
                  <Badge tone="success">{s.completed_lessons_count} amasomo yarangiye</Badge>
                  <span className="text-ink/50 dark:text-sand-100/50">{new Date(s.last_active_at).toLocaleString()}</span>
                </div>
              </li>
            ))}
          </ul>
        )}
      </Card>
    </div>
  )
}

function StatCard({ label, value }) {
  return (
    <Card className="p-4">
      <p className="text-xs uppercase tracking-wide text-ink/50 dark:text-sand-100/50">{label}</p>
      <p className="mt-1 font-display text-2xl text-ink dark:text-sand-50">{value}</p>
    </Card>
  )
}

function TrendChart({ trend }) {
  const max = Math.max(...trend.map((d) => d.peak_online_count), 1)
  return (
    <div className="flex h-40 items-end gap-1">
      {trend.map((d) => (
        <div key={d.date} className="group flex flex-1 flex-col items-center gap-1.5">
          <div className="relative flex w-full flex-1 items-end">
            <div
              className="w-full rounded-t-md bg-gradient-to-t from-teal-700 to-teal-500 transition-all duration-300 group-hover:from-gold-600 group-hover:to-gold-400"
              style={{ height: `${Math.max((d.peak_online_count / max) * 100, 4)}%` }}
              title={`${d.date}: ${d.peak_online_count}`}
            />
          </div>
          <span className="text-[9px] text-ink/40 dark:text-sand-100/35">{d.date.slice(5)}</span>
        </div>
      ))}
    </div>
  )
}
