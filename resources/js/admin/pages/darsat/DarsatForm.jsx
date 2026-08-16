import { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import * as darsatApi from '../../features/darsat/darsatApi'
import client from '../../api/client'
import { Button, Input, Card, CardSkeleton } from '../../components/ui'
import { useToast } from '../../contexts/ToastContext'

const TYPES = ['Fiqh', 'Aqeedah', 'Tafsir', 'Hadith', 'Seerah']

export default function DarsatForm() {
  const { id } = useParams()
  const isEditing = Boolean(id)
  const navigate = useNavigate()
  const { push } = useToast()

  const [form, setForm] = useState({ title: '', teachers: '', type: TYPES[0], description: '', status: 'draft' })
  const [audio, setAudio] = useState(null)
  const [thumbnail, setThumbnail] = useState(null)
  const [teacherOptions, setTeacherOptions] = useState([])
  const [errors, setErrors] = useState({})
  const [loading, setLoading] = useState(true)
  const [submitting, setSubmitting] = useState(false)

  useEffect(() => {
    Promise.all([
      client.get('/teachers').then((r) => setTeacherOptions(r.data.data)),
      isEditing ? darsatApi.getDarsat(id) : Promise.resolve(null),
    ])
      .then(([, darsat]) => {
        if (darsat) {
          setForm({
            title: darsat.title,
            teachers: darsat.teacher?.id ?? '',
            type: darsat.type,
            description: darsat.description || '',
            status: darsat.status,
          })
        }
      })
      .catch(() => push('Ntibishoboka gushaka amakuru.', 'error'))
      .finally(() => setLoading(false))
  }, [id, isEditing, push])

  async function handleSubmit(e) {
    e.preventDefault()
    setErrors({})
    setSubmitting(true)
    try {
      const payload = { ...form, audio: audio || undefined, thumbnail: thumbnail || undefined }
      if (isEditing) {
        await darsatApi.updateDarsat(id, payload)
        push('Darsat yahinduwe.')
      } else {
        await darsatApi.createDarsat(payload)
        push('Darsat yashyizweho.')
      }
      navigate('/darsat')
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

  if (loading) return <Card className="max-w-xl p-6"><CardSkeleton /></Card>

  return (
    <div className="max-w-xl">
      <h1 className="mb-6 font-display text-2xl text-ink">{isEditing ? 'Hindura Darsat' : 'Darsat nshya'}</h1>

      <Card className="p-6">
        <form onSubmit={handleSubmit} className="flex flex-col gap-4">
          <Input
            label="Umutwe"
            required
            value={form.title}
            error={errors.title?.[0]}
            onChange={(e) => setForm({ ...form, title: e.target.value })}
          />

          <label className="block">
            <span className="mb-1 block text-sm font-medium text-ink">Umwarimu</span>
            <select
              required
              value={form.teachers}
              onChange={(e) => setForm({ ...form, teachers: e.target.value })}
              className="w-full rounded-lg border border-sand-200 px-3 py-2 text-sm outline-none focus:border-teal-600"
            >
              <option value="">-- Hitamo umwarimu --</option>
              {teacherOptions.map((t) => (
                <option key={t.id} value={t.id}>{t.name}</option>
              ))}
            </select>
            {errors.teachers && <span className="mt-1 block text-xs text-rose-600">{errors.teachers[0]}</span>}
          </label>

          <label className="block">
            <span className="mb-1 block text-sm font-medium text-ink">Ubwoko</span>
            <select
              value={form.type}
              onChange={(e) => setForm({ ...form, type: e.target.value })}
              className="w-full rounded-lg border border-sand-200 px-3 py-2 text-sm outline-none focus:border-teal-600"
            >
              {TYPES.map((t) => <option key={t} value={t}>{t}</option>)}
            </select>
          </label>

          <label className="block">
            <span className="mb-1 block text-sm font-medium text-ink">Ibisobanuro</span>
            <textarea
              rows={3}
              value={form.description}
              onChange={(e) => setForm({ ...form, description: e.target.value })}
              className="w-full rounded-lg border border-sand-200 px-3 py-2 text-sm outline-none focus:border-teal-600"
            />
          </label>

          <label className="block">
            <span className="mb-1 block text-sm font-medium text-ink">
              Ijwi (audio) {isEditing && <span className="text-ink/40">— reka nta kimenyetso niba udashaka guhindura</span>}
            </span>
            <input
              type="file"
              accept="audio/mp3,audio/wav,audio/ogg,.m4a"
              required={!isEditing}
              onChange={(e) => setAudio(e.target.files?.[0] ?? null)}
              className="block w-full text-sm text-ink/70"
            />
            {errors.audio && <span className="mt-1 block text-xs text-rose-600">{errors.audio[0]}</span>}
          </label>

          <label className="block">
            <span className="mb-1 block text-sm font-medium text-ink">Ifoto (si ngombwa)</span>
            <input
              type="file"
              accept="image/*"
              onChange={(e) => setThumbnail(e.target.files?.[0] ?? null)}
              className="block w-full text-sm text-ink/70"
            />
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
            <Button type="submit" disabled={submitting}>{submitting ? 'Kubika...' : 'Bika'}</Button>
            <Button type="button" variant="secondary" onClick={() => navigate('/darsat')}>Reka</Button>
          </div>
        </form>
      </Card>
    </div>
  )
}
