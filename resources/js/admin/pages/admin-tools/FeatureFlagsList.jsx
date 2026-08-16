import { useEffect, useState } from 'react'
import * as api from '../../features/admin-tools/adminToolsApi'
import { Card, EmptyState, TableSkeleton, ResultCount } from '../../components/ui'
import { useToast } from '../../contexts/ToastContext'

export default function FeatureFlagsList() {
  const { push } = useToast()
  const [flags, setFlags] = useState([])
  const [loading, setLoading] = useState(true)

  function load() {
    api.listFeatureFlags().then((res) => setFlags(res.data)).finally(() => setLoading(false))
  }
  useEffect(() => { load() }, [])

  async function handleToggle(flag) {
    try {
      const res = await api.toggleFeatureFlag(flag.id)
      push(res.message)
      setFlags((prev) => prev.map((f) => (f.id === flag.id ? { ...f, is_enabled: res.data.is_enabled } : f)))
    } catch {
      push('Habaye ikibazo.', 'error')
    }
  }

  return (
    <div className="flex flex-col gap-6">
      <div>
        <h1 className="font-display text-2xl text-ink dark:text-sand-50">Feature Flags</h1>
        <p className="text-sm text-ink/60 dark:text-sand-100/60">Emeza cyangwa uhagarike ibice bya sisitemu</p>
        <div className="mt-2"><ResultCount count={loading ? null : flags.length} label="feature flags" /></div>
      </div>

      <Card className="overflow-hidden">
        {loading && <TableSkeleton cols={2} />}
        {!loading && flags.length === 0 && <EmptyState title="Nta flag irahari" />}
        {!loading && flags.length > 0 && (
          <ul className="divide-y divide-sand-200 dark:divide-white/10">
            {flags.map((f) => (
              <li key={f.id} className="flex items-center justify-between px-4 py-3">
                <div>
                  <p className="font-medium text-ink dark:text-sand-50">{f.label}</p>
                  {f.description && <p className="text-xs text-ink/50 dark:text-sand-100/45">{f.description}</p>}
                </div>
                <button
                  onClick={() => handleToggle(f)}
                  className={`h-6 w-11 rounded-full transition-colors ${f.is_enabled ? 'bg-gradient-to-br from-teal-600 to-teal-800' : 'bg-sand-200 dark:bg-white/10'}`}
                >
                  <span className={`block h-5 w-5 rounded-full bg-white shadow transition-transform ${f.is_enabled ? 'translate-x-5' : 'translate-x-0.5'}`} />
                </button>
              </li>
            ))}
          </ul>
        )}
      </Card>
    </div>
  )
}
