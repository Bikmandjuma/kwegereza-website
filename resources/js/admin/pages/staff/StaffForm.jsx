import { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import * as staffApi from '../../features/staff/staffApi'
import { Button, Input, Card, CardSkeleton } from '../../components/ui'
import { useToast } from '../../contexts/ToastContext'

export default function StaffForm() {
  const { id } = useParams()
  const isEditing = Boolean(id)
  const navigate = useNavigate()
  const { push } = useToast()

  const [form, setForm] = useState({
    firstname: '', lastname: '', gender: 'male', phone: '', dob: '',
    email: '', role: 'teacher', title: '', password: '',
  })
  const [imageFile, setImageFile] = useState(null)
  const [errors, setErrors] = useState({})
  const [loading, setLoading] = useState(isEditing)
  const [submitting, setSubmitting] = useState(false)

  useEffect(() => {
    if (!isEditing) return
    staffApi.getStaff(id)
      .then((s) => setForm({
        firstname: s.firstname, lastname: s.lastname, gender: s.gender, phone: s.phone,
        dob: s.dob?.slice(0, 10) || '', email: s.email, role: s.role, title: s.title, password: '',
      }))
      .catch(() => push('Ntibishoboka gushaka uyu mukoresha.', 'error'))
      .finally(() => setLoading(false))
  }, [id, isEditing, push])

  async function handleSubmit(e) {
    e.preventDefault()
    setErrors({})
    setSubmitting(true)
    try {
      const formData = new FormData()
      Object.entries(form).forEach(([key, value]) => {
        if (key === 'password' && !value) return
        formData.append(key, value)
      })
      if (imageFile) formData.append('image', imageFile)

      if (isEditing) {
        await staffApi.updateStaff(id, formData)
        push('Umukoresha yahinduwe.')
      } else {
        await staffApi.createStaff(formData)
        push('Umukoresha yashyizweho.')
      }
      navigate('/staff')
    } catch (err) {
      if (err.response?.status === 422) setErrors(err.response.data.errors || {})
      else if (err.response?.status === 403) push('Ntabwo ufite uburenganzira.', 'error')
      else push('Habaye ikibazo. Ongera ugerageze.', 'error')
    } finally {
      setSubmitting(false)
    }
  }

  if (loading) return <Card className="max-w-xl p-6"><CardSkeleton lines={5} /></Card>

  return (
    <div className="max-w-xl">
      <h1 className="mb-6 font-display text-2xl text-ink">{isEditing ? 'Hindura umukoresha' : 'Umukoresha mushya'}</h1>

      <Card className="p-6">
        <form onSubmit={handleSubmit} className="flex flex-col gap-4">
          <div className="grid grid-cols-2 gap-4">
            <Input label="Izina rya mbere" required value={form.firstname} error={errors.firstname?.[0]} onChange={(e) => setForm({ ...form, firstname: e.target.value })} />
            <Input label="Izina rya nyuma" required value={form.lastname} error={errors.lastname?.[0]} onChange={(e) => setForm({ ...form, lastname: e.target.value })} />
          </div>

          <label className="block">
            <span className="mb-1 block text-sm font-medium text-ink">Igitsina</span>
            <select value={form.gender} onChange={(e) => setForm({ ...form, gender: e.target.value })} className="w-full rounded-lg border border-sand-200 px-3 py-2 text-sm outline-none focus:border-teal-600">
              <option value="male">Gabo</option>
              <option value="female">Gore</option>
            </select>
          </label>

          <Input label="Telefone" required value={form.phone} error={errors.phone?.[0]} onChange={(e) => setForm({ ...form, phone: e.target.value })} />
          <Input label="Itariki y'amavuko" type="date" required value={form.dob} error={errors.dob?.[0]} onChange={(e) => setForm({ ...form, dob: e.target.value })} />
          <Input label="Imeli" type="email" required value={form.email} error={errors.email?.[0]} onChange={(e) => setForm({ ...form, email: e.target.value })} />
          <Input label="Uruhare (role)" required value={form.role} error={errors.role?.[0]} onChange={(e) => setForm({ ...form, role: e.target.value })} />
          <Input label="Izina ry'inshingano (title)" required value={form.title} error={errors.title?.[0]} onChange={(e) => setForm({ ...form, title: e.target.value })} />
          <Input
            label={isEditing ? 'Ijambo banga rishya (reka nta kimenyetso niba udashaka guhindura)' : 'Ijambo banga'}
            type="password" required={!isEditing} value={form.password} error={errors.password?.[0]}
            onChange={(e) => setForm({ ...form, password: e.target.value })}
          />

          <label className="block">
            <span className="mb-1 block text-sm font-medium text-ink">Ifoto</span>
            <input type="file" accept="image/*" onChange={(e) => setImageFile(e.target.files?.[0] || null)} className="text-sm" />
          </label>

          <div className="mt-2 flex gap-3">
            <Button type="submit" disabled={submitting}>{submitting ? 'Kubika...' : 'Bika'}</Button>
            <Button type="button" variant="secondary" onClick={() => navigate('/staff')}>Reka</Button>
          </div>
        </form>
      </Card>
    </div>
  )
}
