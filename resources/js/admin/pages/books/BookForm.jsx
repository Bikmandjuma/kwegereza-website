import { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import * as booksApi from '../../features/books/booksApi'
import { Button, Input, Card, CardSkeleton } from '../../components/ui'
import { useToast } from '../../contexts/ToastContext'

export default function BookForm() {
  const { id } = useParams()
  const isEditing = Boolean(id)
  const navigate = useNavigate()
  const { push } = useToast()

  const [form, setForm] = useState({ title: '', author: '', category: '', description: '', status: 'draft', is_downloadable: true })
  const [bookFile, setBookFile] = useState(null)
  const [cover, setCover] = useState(null)
  const [errors, setErrors] = useState({})
  const [loading, setLoading] = useState(isEditing)
  const [submitting, setSubmitting] = useState(false)

  useEffect(() => {
    if (!isEditing) return
    booksApi.getBook(id)
      .then((book) => setForm({
        title: book.title, author: book.author || '', category: book.category || '',
        description: book.description || '', status: book.status, is_downloadable: book.is_downloadable,
      }))
      .catch(() => push('Ntibishoboka gushaka iki gitabo.', 'error'))
      .finally(() => setLoading(false))
  }, [id, isEditing, push])

  async function handleSubmit(e) {
    e.preventDefault()
    setErrors({})
    setSubmitting(true)
    try {
      const payload = { ...form, book: bookFile || undefined, cover_image: cover || undefined }
      if (isEditing) {
        await booksApi.updateBook(id, payload)
        push('Igitabo cyahinduwe.')
      } else {
        await booksApi.createBook(payload)
        push('Igitabo cyashyizweho.')
      }
      navigate('/books')
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
      <h1 className="mb-6 font-display text-2xl text-ink">{isEditing ? 'Hindura igitabo' : 'Igitabo gishya'}</h1>

      <Card className="p-6">
        <form onSubmit={handleSubmit} className="flex flex-col gap-4">
          <Input label="Umutwe" required value={form.title} error={errors.title?.[0]} onChange={(e) => setForm({ ...form, title: e.target.value })} />
          <Input label="Umwanditsi" value={form.author} onChange={(e) => setForm({ ...form, author: e.target.value })} />
          <Input label="Icyiciro" value={form.category} onChange={(e) => setForm({ ...form, category: e.target.value })} />

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
              Dosiye ya PDF {isEditing && <span className="text-ink/40">— reka nta kimenyetso niba udashaka guhindura</span>}
            </span>
            <input type="file" accept="application/pdf" required={!isEditing} onChange={(e) => setBookFile(e.target.files?.[0] ?? null)} className="block w-full text-sm text-ink/70" />
            {errors.book && <span className="mt-1 block text-xs text-rose-600">{errors.book[0]}</span>}
          </label>

          <label className="block">
            <span className="mb-1 block text-sm font-medium text-ink">Ifoto y'ipfundikizo (si ngombwa)</span>
            <input type="file" accept="image/*" onChange={(e) => setCover(e.target.files?.[0] ?? null)} className="block w-full text-sm text-ink/70" />
          </label>

          <label className="flex items-center gap-2 text-sm text-ink">
            <input
              type="checkbox"
              checked={form.is_downloadable}
              onChange={(e) => setForm({ ...form, is_downloadable: e.target.checked })}
              className="h-4 w-4 rounded border-sand-200"
            />
            Emerera gukururwa
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
            <Button type="button" variant="secondary" onClick={() => navigate('/books')}>Reka</Button>
          </div>
        </form>
      </Card>
    </div>
  )
}
