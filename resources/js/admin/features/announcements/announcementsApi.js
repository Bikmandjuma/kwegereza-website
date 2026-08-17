import client from '../../api/client'

export async function listAnnouncements({ page = 1, search = '', status = '' } = {}) {
  const { data } = await client.get('/amatangazo', { params: { page, search, status } })
  return data
}

export async function getAnnouncement(id) {
  const { data } = await client.get(`/amatangazo/${id}`)
  return data.data
}

export async function createAnnouncement(payload) {
  const form = toFormData(payload)
  const { data } = await client.post('/amatangazo', form, { headers: { 'Content-Type': 'multipart/form-data' } })
  return data.data
}

export async function updateAnnouncement(id, payload) {
  const form = toFormData(payload)
  form.append('_method', 'PUT')
  const { data } = await client.post(`/amatangazo/${id}`, form, { headers: { 'Content-Type': 'multipart/form-data' } })
  return data.data
}

export async function deleteAnnouncement(id) {
  const { data } = await client.delete(`/amatangazo/${id}`)
  return data
}

export async function togglePublish(id) {
  const { data } = await client.patch(`/amatangazo/${id}/toggle`)
  return data.data
}

function toFormData(payload) {
  const form = new FormData()
  Object.entries(payload).forEach(([key, value]) => {
    if (value !== undefined && value !== null) form.append(key, typeof value === 'boolean' ? (value ? '1' : '0') : value)
  })
  return form
}
