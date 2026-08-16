import { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import * as quizzesApi from '../../features/quizzes/quizzesApi'
import { Button, Input, Card, CardSkeleton } from '../../components/ui'
import { useToast } from '../../contexts/ToastContext'

export default function QuizForm() {
  const { id } = useParams()
  const isEditing = Boolean(id)
  const navigate = useNavigate()
  const { push } = useToast()

  const [form, setForm] = useState({
    title: '', description: '', passing_percentage: 70, status: 'draft',
    starts_at: '', duration_minutes: '',
  })
  const [errors, setErrors] = useState({})
  const [loading, setLoading] = useState(isEditing)
  const [submitting, setSubmitting] = useState(false)

  useEffect(() => {
    if (!isEditing) return
    quizzesApi.getQuiz(id)
      .then((q) => setForm({
        title: q.title, description: q.description || '', passing_percentage: q.passing_percentage,
        status: q.status, starts_at: q.starts_at ? q.starts_at.slice(0, 16) : '', duration_minutes: q.duration_minutes ?? '',
      }))
      .catch(() => push('Ntibishoboka gushaka iki kizamini.', 'error'))
      .finally(() => setLoading(false))
  }, [id, isEditing, push])

  async function handleSubmit(e) {
    e.preventDefault()
    setErrors({})
    setSubmitting(true)
    try {
      const payload = { ...form, duration_minutes: form.duration_minutes || null, starts_at: form.starts_at || null }
      if (isEditing) {
        await quizzesApi.updateQuiz(id, payload)
        push('Ikizamini cyahinduwe.')
      } else {
        await quizzesApi.createQuiz(payload)
        push('Ikizamini cyashyizweho.')
      }
      navigate('/quizzes')
    } catch (err) {
      if (err.response?.status === 422) setErrors(err.response.data.errors || {})
      else if (err.response?.status === 403) push('Ntabwo ufite uburenganzira bwo gukora iki gikorwa.', 'error')
      else push('Habaye ikibazo. Ongera ugerageze.', 'error')
    } finally {
      setSubmitting(false)
    }
  }

  if (loading) return <Card className="max-w-xl p-6"><CardSkeleton /></Card>

  return (
    <div className="max-w-xl">
      <h1 className="mb-6 font-display text-2xl text-ink">{isEditing ? 'Hindura ikizamini' : 'Ikizamini gishya'}</h1>

      <Card className="p-6">
        <form onSubmit={handleSubmit} className="flex flex-col gap-4">
          <Input label="Umutwe" required value={form.title} error={errors.title?.[0]} onChange={(e) => setForm({ ...form, title: e.target.value })} />

          <label className="block">
            <span className="mb-1 block text-sm font-medium text-ink">Ibisobanuro</span>
            <textarea
              rows={3}
              value={form.description}
              onChange={(e) => setForm({ ...form, description: e.target.value })}
              className="w-full rounded-lg border border-sand-200 px-3 py-2 text-sm outline-none focus:border-teal-600"
            />
          </label>

          <Input
            label="Amanota yo gutsinda (%)"
            type="number" min="1" max="100" required
            value={form.passing_percentage}
            error={errors.passing_percentage?.[0]}
            onChange={(e) => setForm({ ...form, passing_percentage: e.target.value })}
          />

          <Input
            label="Igihe (iminota) — reka nta kimenyetso niba ntacyo"
            type="number" min="1" max="600"
            value={form.duration_minutes}
            error={errors.duration_minutes?.[0]}
            onChange={(e) => setForm({ ...form, duration_minutes: e.target.value })}
          />

          <Input
            label="Kizatangira ryari? (si ngombwa)"
            type="datetime-local"
            value={form.starts_at}
            onChange={(e) => setForm({ ...form, starts_at: e.target.value })}
          />

          <label className="block">
            <span className="mb-1 block text-sm font-medium text-ink">Imiterere</span>
            <select
              value={form.status}
              onChange={(e) => setForm({ ...form, status: e.target.value })}
              className="w-full rounded-lg border border-sand-200 px-3 py-2 text-sm outline-none focus:border-teal-600"
            >
              <option value="draft">Ntibirasohoka (draft)</option>
              <option value="published">Byasohotse</option>
            </select>
          </label>

          <div className="mt-2 flex gap-3">
            <Button type="submit" disabled={submitting}>{submitting ? 'Kubika...' : 'Bika'}</Button>
            <Button type="button" variant="secondary" onClick={() => navigate('/quizzes')}>Reka</Button>
          </div>
        </form>
      </Card>
    </div>
  )
}
