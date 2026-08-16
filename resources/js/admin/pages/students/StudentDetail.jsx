import { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import * as studentsApi from '../../features/students/studentsApi'
import { Card, Badge, Button, CardSkeleton } from '../../components/ui'
import Can from '../../permissions/Can'
import { useToast } from '../../contexts/ToastContext'

export default function StudentDetail() {
  const { id } = useParams()
  const navigate = useNavigate()
  const { push } = useToast()
  const [detail, setDetail] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    studentsApi.getStudent(id)
      .then(setDetail)
      .catch(() => push('Ntibishoboka gushaka aya makuru.', 'error'))
      .finally(() => setLoading(false))
  }, [id, push])

  async function toggleBlock() {
    if (!detail) return
    try {
      const updated = detail.student.is_blocked
        ? await studentsApi.unblockStudent(id)
        : await studentsApi.blockStudent(id)
      setDetail({ ...detail, student: updated })
      push(updated.is_blocked ? 'Umunyeshuri yahagaritswe.' : 'Umunyeshuri yongeye kubona urubuga.')
    } catch {
      push('Ntibishoboka gukora iki gikorwa.', 'error')
    }
  }

  if (loading) return <Card className="p-6"><CardSkeleton lines={4} /></Card>
  if (!detail) return null

  const { student, completed_darsat_count, recent_quiz_attempts, certificates, badges } = detail

  return (
    <div className="flex flex-col gap-6">
      <Button variant="ghost" onClick={() => navigate('/students')} className="w-fit px-0">← Subira ku rutonde</Button>

      <div className="flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 className="font-display text-2xl text-ink">{student.firstname} {student.lastname}</h1>
          <p className="text-sm text-ink/60">{student.email} · {student.phone}</p>
        </div>
        <div className="flex items-center gap-3">
          <Badge tone={student.is_blocked ? 'warning' : 'success'}>
            {student.is_blocked ? 'Yahagaritswe' : 'Arakora'}
          </Badge>
          <Can permission="students.manage">
            <Button variant={student.is_blocked ? 'primary' : 'danger'} onClick={toggleBlock}>
              {student.is_blocked ? 'Kubohora' : 'Guhagarika'}
            </Button>
          </Can>
        </div>
      </div>

      <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <Card className="p-5">
          <p className="text-xs uppercase tracking-wide text-ink/50">Darsat zarangiye</p>
          <p className="mt-1 font-display text-3xl text-ink">{completed_darsat_count}</p>
        </Card>
        <Card className="p-5">
          <p className="text-xs uppercase tracking-wide text-ink/50">Impamyabumenyi</p>
          <p className="mt-1 font-display text-3xl text-ink">{certificates.length}</p>
        </Card>
        <Card className="p-5">
          <p className="text-xs uppercase tracking-wide text-ink/50">Ibihembo</p>
          <p className="mt-1 font-display text-3xl text-ink">{badges.length}</p>
        </Card>
      </div>

      <Card className="p-5">
        <h2 className="mb-3 font-display text-lg text-ink">Ibizamini vya vuba</h2>
        {recent_quiz_attempts.length === 0 ? (
          <p className="text-sm text-ink/50">Nta kizamini kirakorwa.</p>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-left text-sm">
            <thead className="text-xs uppercase tracking-wide text-ink/50">
              <tr>
                <th className="py-2">Ikizamini</th>
                <th className="py-2">Amanota</th>
                <th className="py-2">Itariki</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-sand-200">
              {recent_quiz_attempts.map((a) => (
                <tr key={a.id}>
                  <td className="py-2">{a.quiz_title}</td>
                  <td className="py-2">{a.percentage != null ? `${a.percentage}%` : '—'}</td>
                  <td className="py-2 text-ink/60">{new Date(a.submitted_at).toLocaleDateString()}</td>
                </tr>
              ))}
            </tbody>
          </table>
            </div>
        )}
      </Card>
    </div>
  )
}
