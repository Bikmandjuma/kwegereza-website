import { useEffect, useState } from 'react'
import * as api from '../../features/admin-tools/adminToolsApi'
import { Card, EmptyState, Badge, TableSkeleton, ResultCount } from '../../components/ui'
import { useToast } from '../../contexts/ToastContext'

export default function TeacherVerificationList() {
  const { push } = useToast()
  const [teachers, setTeachers] = useState([])
  const [search, setSearch] = useState('')
  const [loading, setLoading] = useState(true)

  function load() {
    api.listTeacherVerification().then((res) => setTeachers(res.data)).finally(() => setLoading(false))
  }
  useEffect(() => { load() }, [])

  async function handleVerify(id) {
    await api.verifyTeacher(id)
    push('Umwarimu yemejwe.')
    load()
  }
  async function handleUnverify(id) {
    await api.unverifyTeacher(id)
    push('Icyemezo cyakuweho.')
    load()
  }

  return (
    <div className="flex flex-col gap-6">
      <div>
        <h1 className="font-display text-2xl text-ink dark:text-sand-50">Kwemeza Abarimu (Teacher Verification)</h1>
        <p className="text-sm text-ink/60 dark:text-sand-100/60">Emeza ko abarimu ari abo bavuga ko bari</p>
        <div className="mt-2"><ResultCount count={loading ? null : teachers.length} label="abarimu" /></div>
      </div>

      <Card className="p-4">
        <input
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          placeholder="Shakisha umwarimu..."
          className="w-full max-w-xs rounded-xl border border-sand-200 bg-white px-3 py-2.5 text-sm text-ink outline-none transition-colors placeholder:text-ink/35 focus:border-teal-600 focus-visible:ring-2 focus-visible:ring-teal-600/20 dark:border-white/10 dark:bg-teal-900/40 dark:text-sand-50 dark:placeholder:text-sand-100/30 dark:focus:border-gold-400"
        />
      </Card>

      <Card className="overflow-hidden">
        {loading && <TableSkeleton cols={3} />}
        {!loading && teachers.length === 0 && <EmptyState title="Nta mwarimu uhari" />}
        {!loading && teachers.length > 0 && (() => {
          const filtered = search.trim()
            ? teachers.filter((t) => `${t.firstname} ${t.lastname}`.toLowerCase().includes(search.trim().toLowerCase()))
            : teachers
          if (filtered.length === 0) {
            return <EmptyState title="Nta mwarimu uhuye n'ubushakashatsi" />
          }
          return (
          <div className="overflow-x-auto"><table className="w-full min-w-[640px] text-left text-sm">
            <thead className="bg-sand-100 text-xs uppercase tracking-wide text-ink/50 dark:bg-white/5 dark:text-sand-100/50">
              <tr><th className="px-4 py-3">Izina</th><th className="px-4 py-3">Inshingano</th><th className="px-4 py-3">Imiterere</th><th className="px-4 py-3 text-right">Ibikorwa</th></tr>
            </thead>
            <tbody className="divide-y divide-sand-200 dark:divide-white/10">
              {filtered.map((t) => (
                <tr key={t.id}>
                  <td className="px-4 py-3 text-ink dark:text-sand-50">{t.firstname} {t.lastname}</td>
                  <td className="px-4 py-3 text-ink/70 dark:text-sand-100/70">{t.title}</td>
                  <td className="px-4 py-3">
                    <Badge tone={t.is_verified ? 'success' : 'neutral'}>{t.is_verified ? 'Yemejwe' : 'Ntiyemejwe'}</Badge>
                  </td>
                  <td className="px-4 py-3 text-right">
                    {t.is_verified ? (
                      <button onClick={() => handleUnverify(t.id)} className="text-sm text-rose-600 hover:underline dark:text-rose-400">Kuraho icyemezo</button>
                    ) : (
                      <button onClick={() => handleVerify(t.id)} className="text-sm text-teal-700 hover:underline dark:text-teal-300">Emeza</button>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table></div>
          )
        })()}
      </Card>
    </div>
  )
}
