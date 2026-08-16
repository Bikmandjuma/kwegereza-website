import { useEffect, useState, useRef } from 'react'
import * as profileApi from '../../features/profile/profileApi'
import { Button, Input, Card, CardSkeleton } from '../../components/ui'
import { useToast } from '../../contexts/ToastContext'
import { useAuth } from '../../contexts/AuthContext'

export default function ProfileSettings() {
  const { push } = useToast()
  const { refreshUser } = useAuth()
  const [profile, setProfile] = useState(null)
  const [form, setForm] = useState({ firstname: '', lastname: '', phone: '', bio: '' })
  const [errors, setErrors] = useState({})
  const [savingProfile, setSavingProfile] = useState(false)
  const fileInputRef = useRef(null)
  const [uploadingAvatar, setUploadingAvatar] = useState(false)

  const [passwordForm, setPasswordForm] = useState({ current_password: '', new_password: '', new_password_confirmation: '' })
  const [passwordErrors, setPasswordErrors] = useState({})
  const [savingPassword, setSavingPassword] = useState(false)

  useEffect(() => {
    profileApi.getProfile().then((p) => {
      setProfile(p)
      setForm({ firstname: p.firstname, lastname: p.lastname, phone: p.phone, bio: p.bio || '' })
    })
  }, [])

  async function handleProfileSubmit(e) {
    e.preventDefault()
    setErrors({})
    setSavingProfile(true)
    try {
      const updated = await profileApi.updateProfile(form)
      setProfile(updated)
      push('Umwirondoro wahinduwe.')
      refreshUser?.()
    } catch (err) {
      if (err.response?.status === 422) setErrors(err.response.data.errors || {})
      else push('Habaye ikibazo.', 'error')
    } finally {
      setSavingProfile(false)
    }
  }

  async function handleAvatarChange(e) {
    const file = e.target.files?.[0]
    if (!file) return
    setUploadingAvatar(true)
    try {
      const updated = await profileApi.updateAvatar(file)
      setProfile(updated)
      push('Ifoto yahinduwe.')
      refreshUser?.()
    } catch (err) {
      push(err.response?.data?.errors?.avatar?.[0] || 'Ntibishoboka gushyiraho ifoto.', 'error')
    } finally {
      setUploadingAvatar(false)
    }
  }

  async function handlePasswordSubmit(e) {
    e.preventDefault()
    setPasswordErrors({})
    setSavingPassword(true)
    try {
      await profileApi.updatePassword(passwordForm)
      setPasswordForm({ current_password: '', new_password: '', new_password_confirmation: '' })
      push('Ijambo banga ryahinduwe.')
    } catch (err) {
      if (err.response?.status === 422) setPasswordErrors(err.response.data.errors || {})
      else push('Habaye ikibazo.', 'error')
    } finally {
      setSavingPassword(false)
    }
  }

  if (!profile) return <Card className="max-w-2xl p-6"><CardSkeleton lines={4} /></Card>

  return (
    <div className="flex max-w-2xl flex-col gap-6">
      <div>
        <h1 className="font-display text-2xl text-ink">Igenamiterere</h1>
        <p className="text-sm text-ink/60">Genzura umwirondoro wawe n'umutekano wa konti yawe</p>
      </div>

      <Card className="p-6">
        <div className="mb-6 flex items-center gap-4">
          <div className="h-16 w-16 overflow-hidden rounded-full bg-sand-200">
            {profile.image_url ? (
              <img src={profile.image_url} alt="" className="h-full w-full object-cover" />
            ) : (
              <div className="flex h-full w-full items-center justify-center text-xl text-ink/40">
                {profile.firstname?.[0]}
              </div>
            )}
          </div>
          <div>
            <input ref={fileInputRef} type="file" accept="image/*" hidden onChange={handleAvatarChange} />
            <Button type="button" variant="secondary" disabled={uploadingAvatar} onClick={() => fileInputRef.current?.click()}>
              {uploadingAvatar ? 'Kohereza...' : 'Hindura ifoto'}
            </Button>
          </div>
        </div>

        <form onSubmit={handleProfileSubmit} className="flex flex-col gap-4">
          <div className="grid grid-cols-2 gap-4">
            <Input label="Izina rya mbere" required value={form.firstname} error={errors.firstname?.[0]} onChange={(e) => setForm({ ...form, firstname: e.target.value })} />
            <Input label="Izina rya nyuma" required value={form.lastname} error={errors.lastname?.[0]} onChange={(e) => setForm({ ...form, lastname: e.target.value })} />
          </div>
          <Input label="Telefone" required value={form.phone} error={errors.phone?.[0]} onChange={(e) => setForm({ ...form, phone: e.target.value })} />
          <label className="block">
            <span className="mb-1 block text-sm font-medium text-ink">Bio</span>
            <textarea
              rows={3} value={form.bio} onChange={(e) => setForm({ ...form, bio: e.target.value })}
              className="w-full rounded-lg border border-sand-200 px-3 py-2 text-sm outline-none focus:border-teal-600"
            />
          </label>
          <Button type="submit" disabled={savingProfile} className="w-fit">{savingProfile ? 'Kubika...' : 'Bika impinduka'}</Button>
        </form>
      </Card>

      <Card className="p-6">
        <h2 className="mb-4 font-display text-lg text-ink">Hindura ijambo banga</h2>
        <form onSubmit={handlePasswordSubmit} className="flex flex-col gap-4">
          <Input
            label="Ijambo banga risanzwe" type="password" required
            value={passwordForm.current_password} error={passwordErrors.current_password?.[0]}
            onChange={(e) => setPasswordForm({ ...passwordForm, current_password: e.target.value })}
          />
          <Input
            label="Ijambo banga rishya" type="password" required
            value={passwordForm.new_password} error={passwordErrors.new_password?.[0]}
            onChange={(e) => setPasswordForm({ ...passwordForm, new_password: e.target.value })}
          />
          <Input
            label="Emeza ijambo banga rishya" type="password" required
            value={passwordForm.new_password_confirmation}
            onChange={(e) => setPasswordForm({ ...passwordForm, new_password_confirmation: e.target.value })}
          />
          <Button type="submit" disabled={savingPassword} className="w-fit">{savingPassword ? 'Kubika...' : 'Hindura ijambo banga'}</Button>
        </form>
      </Card>
    </div>
  )
}
