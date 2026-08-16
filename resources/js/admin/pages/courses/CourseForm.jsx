import { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import * as coursesApi from '../../features/courses/coursesApi'
import { Button, Input, Card, CardSkeleton } from '../../components/ui'
import { useToast } from '../../contexts/ToastContext'

export default function CourseForm() {
  const { id } = useParams()
  const isEditing = Boolean(id)
  const navigate = useNavigate()
  const { push } = useToast()

  const [form, setForm] = useState({ title: '', description: '', status: 'draft' })
  const [thumbnail, setThumbnail] = useState(null)
  const [errors, setErrors] = useState({})
  const [loading, setLoading] = useState(isEditing)
  const [submitting, setSubmitting] = useState(false)

  useEffect(() => {
    if (!isEditing) return
    coursesApi
      .getCourse(id)
      .then((course) =>
        setForm({ title: course.title, description: course.description || '', status: course.status })
      )
      .catch(() => push('Ntibishoboka gushaka iri somo.', 'error'))
      .finally(() => setLoading(false))
  }, [id, isEditing, push])

  async function handleSubmit(e) {
    e.preventDefault()
    setErrors({})
    setSubmitting(true)
    try {
      const payload = { ...form, thumbnail: thumbnail || undefined }
      if (isEditing) {
        await coursesApi.updateCourse(id, payload)
        push('Isomo ryahinduwe.')
      } else {
        await coursesApi.createCourse(payload)
        push('Isomo ryashyizweho.')
      }
      navigate('/courses')
    } catch (err) {
      if (err.response?.status === 422) {
        setErrors(err.response.data.errors || {})
      } else if (err.response?.status === 403) {
        push('Ntabwo ufite uburenganzira bwo gukora iki gikorwa.', 'error')
      } else {
        push('Habaye ikibazo. Ongera ugerageze.', 'error')
      }
    } finally {
      setSubmitting(false)
    }
  }

  if (loading) {
    return <Card className="max-w-xl p-6"><CardSkeleton /></Card>
  }

  return (
    <div className="max-w-xl">
      <h1 className="mb-6 font-display text-2xl text-ink">
        {isEditing ? 'Hindura isomo' : 'Isomo rishya'}
      </h1>

      <Card className="p-6">
        <form onSubmit={handleSubmit} className="flex flex-col gap-4">
          <Input
            label="Umutwe w'isomo"
            required
            value={form.title}
            error={errors.title?.[0]}
            onChange={(e) => setForm({ ...form, title: e.target.value })}
          />

          <label className="block">
            <span className="mb-1 block text-sm font-medium text-ink">Ibisobanuro</span>
            <textarea
              rows={4}
              value={form.description}
              onChange={(e) => setForm({ ...form, description: e.target.value })}
              className="w-full rounded-lg border border-sand-200 px-3 py-2 text-sm outline-none focus:border-teal-600"
            />
          </label>

          <label className="block">
            <span className="mb-1 block text-sm font-medium text-ink">Ifoto</span>
            <input
              type="file"
              accept="image/*"
              onChange={(e) => setThumbnail(e.target.files?.[0] ?? null)}
              className="block w-full text-sm text-ink/70"
            />
            {errors.thumbnail && <span className="mt-1 block text-xs text-rose-600">{errors.thumbnail[0]}</span>}
          </label>

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
            <Button type="submit" disabled={submitting}>
              {submitting ? 'Kubika...' : 'Bika'}
            </Button>
            <Button type="button" variant="secondary" onClick={() => navigate('/courses')}>
              Reka
            </Button>
          </div>
        </form>
      </Card>
    </div>
  )
}
