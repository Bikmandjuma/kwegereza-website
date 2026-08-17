import { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import * as announcementsApi from '../../features/announcements/announcementsApi'
import { Button, Input, Card, CardSkeleton } from '../../components/ui'
import { useToast } from '../../contexts/ToastContext'

export default function AnnouncementForm() {
  const { id } = useParams()
  const isEditing = Boolean(id)
  const navigate = useNavigate()
  const { push } = useToast()

  const [form, setForm] = useState({ title: '', description: '', presenter: '', status: 'upcoming', is_published: true })
  const [image, setImage] = useState(null)
  const [errors, setErrors] = useState({})
  const [loading, setLoading] = useState(isEditing)
  const [submitting, setSubmitting] = useState(false)

  useEffect(() => {
    if (!isEditing) return
    announcementsApi.getAnnouncement(id)
      .then((a) => setForm({ title: a.title, description: a.description || '', presenter: a.presenter || '', status: a.status, is_published: a.is_published }))
      .catch(() => push('Ntibishoboka gushaka iri tangazo.', 'error'))
      .finally(() => setLoading(false))
  }, [id, isEditing, push])

  async function handleSubmit(e) {
    e.preventDefault()
    setErrors({})
    setSubmitting(true)
    try {
      // On edit, deliberately do NOT send is_published at all — publish
      // state is only ever changed via the dedicated toggle action on the
      // list page, matching the real edit form and the fix for the
      // silent-republish bug this phase found and fixed on the backend.
      const { is_published, ...editable } = form
      const payload = isEditing
        ? { ...editable, image: image || undefined }
        : { ...form, image: image || undefined }

      if (isEditing) {
        await announcementsApi.updateAnnouncement(id, payload)
        push('Itangazo ryahinduwe.')
      } else {
        await announcementsApi.createAnnouncement(payload)
        push('Itangazo ryashyizweho.')
      }
      navigate('/amatangazo')
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
      <h1 className="mb-6 font-display text-2xl text-ink">{isEditing ? 'Hindura itangazo' : 'Itangazo rishya'}</h1>

      <Card className="p-6">
        <form onSubmit={handleSubmit} className="flex flex-col gap-4">
          <Input label="Umutwe" required value={form.title} error={errors.title?.[0]} onChange={(e) => setForm({ ...form, title: e.target.value })} />
          <Input label="Uwigisha (si ngombwa)" value={form.presenter} onChange={(e) => setForm({ ...form, presenter: e.target.value })} />

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
            <span className="mb-1 block text-sm font-medium text-ink">Uko rihagaze</span>
            <select
              value={form.status}
              onChange={(e) => setForm({ ...form, status: e.target.value })}
              className="w-full rounded-lg border border-sand-200 px-3 py-2 text-sm outline-none focus:border-teal-600"
            >
              <option value="upcoming">Asigaye (Upcoming)</option>
              <option value="live">Live</option>
              <option value="done">Ryarangiye (Done)</option>
            </select>
          </label>

          <label className="block">
            <span className="mb-1 block text-sm font-medium text-ink">Ifoto (si ngombwa)</span>
            <input type="file" accept="image/*" onChange={(e) => setImage(e.target.files?.[0] ?? null)} className="block w-full text-sm text-ink/70" />
          </label>

          {!isEditing && (
            <label className="flex items-center gap-2 text-sm text-ink">
              <input
                type="checkbox"
                checked={form.is_published}
                onChange={(e) => setForm({ ...form, is_published: e.target.checked })}
                className="h-4 w-4 rounded border-sand-200"
              />
              Erekana ku rubuga ubu (publish immediately)
            </label>
          )}
          {isEditing && (
            <p className="text-xs text-ink/50">
              Kwerekana cyangwa guhisha bikorwa ku rutonde rw'amatangazo, ntibihinduka hano.
            </p>
          )}

          <div className="mt-2 flex gap-3">
            <Button type="submit" disabled={submitting}>{submitting ? 'Kubika...' : 'Bika'}</Button>
            <Button type="button" variant="secondary" onClick={() => navigate('/amatangazo')}>Reka</Button>
          </div>
        </form>
      </Card>
    </div>
  )
}
