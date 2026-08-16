import client from '../../api/client'

export async function getProfile() {
  const { data } = await client.get('/profile')
  return data.data
}

export async function updateProfile(payload) {
  const { data } = await client.put('/profile', payload)
  return data.data
}

export async function updatePassword(payload) {
  const { data } = await client.post('/profile/password', payload)
  return data
}

export async function updateAvatar(file) {
  const formData = new FormData()
  formData.append('avatar', file)
  const { data } = await client.post('/profile/avatar', formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  })
  return data.data
}
