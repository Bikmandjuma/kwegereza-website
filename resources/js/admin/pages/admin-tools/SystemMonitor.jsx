import { useEffect, useState } from 'react'
import * as api from '../../features/admin-tools/adminToolsApi'
import { Card, Badge, CardSkeleton } from '../../components/ui'

function StatusDot({ status }) {
  return <span className={`inline-block h-2 w-2 rounded-full ${status === 'ok' ? 'bg-teal-600' : 'bg-rose-600'}`} />
}

export default function SystemMonitor() {
  const [data, setData] = useState(null)

  useEffect(() => {
    api.getSystemHealth().then((res) => setData(res.data))
  }, [])

  if (!data) return <Card className="p-6"><CardSkeleton lines={5} /></Card>

  return (
    <div className="flex flex-col gap-6">
      <div>
        <h1 className="font-display text-2xl text-ink">System Monitor</h1>
        <p className="text-sm text-ink/60">Uko urubuga rumeze nonaha</p>
      </div>

      <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
        {Object.entries(data.checks).map(([key, check]) => (
          <Card key={key} className="p-4">
            <p className="flex items-center gap-2 text-xs uppercase tracking-wide text-ink/50">
              <StatusDot status={check.status} /> {key}
            </p>
            <p className="mt-1 text-sm text-ink">{check.message}</p>
          </Card>
        ))}
      </div>

      <Card className="p-5">
        <h2 className="mb-3 font-display text-lg text-ink">Konfigurasiyo</h2>
        <div className="grid grid-cols-2 gap-3 text-sm sm:grid-cols-3">
          {Object.entries(data.config).map(([key, value]) => (
            <div key={key}>
              <p className="text-xs uppercase text-ink/40">{key}</p>
              <p className="text-ink">{String(value)}</p>
            </div>
          ))}
        </div>
      </Card>

      {data.disk && (
        <Card className="p-5">
          <h2 className="mb-3 font-display text-lg text-ink">Disk Usage</h2>
          <div className="h-2 w-full overflow-hidden rounded-full bg-sand-100">
            <div className="h-full bg-teal-700" style={{ width: `${data.disk.used_pct}%` }} />
          </div>
          <p className="mt-2 text-xs text-ink/50">{data.disk.used_gb}GB / {data.disk.total_gb}GB ({data.disk.used_pct}%)</p>
        </Card>
      )}

      <Card className="p-5">
        <h2 className="mb-3 font-display text-lg text-ink">Ibicu (Counts)</h2>
        <div className="grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">
          {Object.entries(data.counts).map(([key, value]) => (
            <div key={key}>
              <p className="text-xs uppercase text-ink/40">{key}</p>
              <p className="font-display text-xl text-ink">{value}</p>
            </div>
          ))}
        </div>
      </Card>

      {data.recent_errors.length > 0 && (
        <Card className="p-5">
          <h2 className="mb-3 font-display text-lg text-ink">Amakosa Aheruka (Recent Errors)</h2>
          <ul className="space-y-1 text-xs">
            {data.recent_errors.map((e, i) => (
              <li key={i} className="flex items-start gap-2">
                <Badge tone="danger">error</Badge>
                <span className="font-mono text-ink/60">{e.line}</span>
              </li>
            ))}
          </ul>
        </Card>
      )}
    </div>
  )
}
