import { useEffect, useState } from 'react'
import * as api from '../../features/admin-tools/adminToolsApi'
import { Button, Card, EmptyState, TableSkeleton, ResultCount } from '../../components/ui'
import Can from '../../permissions/Can'
import { useToast } from '../../contexts/ToastContext'

function formatBytes(bytes) {
  if (!bytes) return '0 B'
  const units = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(1024))
  return `${(bytes / Math.pow(1024, i)).toFixed(1)} ${units[i]}`
}

export default function BackupsList() {
  const { push } = useToast()
  const [backups, setBackups] = useState([])
  const [loading, setLoading] = useState(true)
  const [creating, setCreating] = useState(false)

  function load() {
    api.listBackups().then((res) => setBackups(res.data)).finally(() => setLoading(false))
  }
  useEffect(() => { load() }, [])

  async function handleCreate() {
    setCreating(true)
    try {
      const res = await api.createBackup()
      push(res.message)
      load()
    } catch (err) {
      push(err.response?.data?.message || 'Backup ntiyakunze.', 'error')
    } finally {
      setCreating(false)
    }
  }

  async function handleDelete(filename) {
    if (!confirm(`Wemeza gusiba ${filename}?`)) return
    await api.deleteBackup(filename)
    push('Backup yasibwe.')
    load()
  }

  return (
    <div className="flex flex-col gap-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="font-display text-2xl text-ink dark:text-sand-50">Backups</h1>
          <p className="text-sm text-ink/60 dark:text-sand-100/60">Kora kandi ugenzure backup za database</p>
          <div className="mt-2"><ResultCount count={loading ? null : backups.length} label="backups" /></div>
        </div>
        <Can permission="backups.create">
          <Button onClick={handleCreate} disabled={creating}>{creating ? 'Turakora...' : '+ Kora backup nshya'}</Button>
        </Can>
      </div>

      <Card className="overflow-hidden">
        {loading && <TableSkeleton cols={3} />}
        {!loading && backups.length === 0 && <EmptyState title="Nta backup irahari" />}
        {!loading && backups.length > 0 && (
          <div className="overflow-x-auto"><table className="w-full min-w-[640px] text-left text-sm">
            <thead className="bg-sand-100 text-xs uppercase tracking-wide text-ink/50 dark:bg-white/5 dark:text-sand-100/50">
              <tr><th className="px-4 py-3">Izina</th><th className="px-4 py-3">Ingano</th><th className="px-4 py-3">Itariki</th><th className="px-4 py-3 text-right">Ibikorwa</th></tr>
            </thead>
            <tbody className="divide-y divide-sand-200 dark:divide-white/10">
              {backups.map((b) => (
                <tr key={b.filename}>
                  <td className="px-4 py-3 font-mono text-xs text-ink dark:text-sand-50">{b.filename}</td>
                  <td className="px-4 py-3 text-ink/70 dark:text-sand-100/70">{formatBytes(b.size)}</td>
                  <td className="px-4 py-3 text-ink/50 dark:text-sand-100/50">{new Date(b.created_at).toLocaleString()}</td>
                  <td className="px-4 py-3 text-right">
                    <Can permission="backups.delete">
                      <button onClick={() => handleDelete(b.filename)} className="text-sm text-rose-600 hover:underline dark:text-rose-400">Siba</button>
                    </Can>
                  </td>
                </tr>
              ))}
            </tbody>
          </table></div>
        )}
      </Card>
    </div>
  )
}
