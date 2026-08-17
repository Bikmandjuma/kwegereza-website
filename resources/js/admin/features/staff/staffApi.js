import client from '../../api/client'

export async function listStaff({ page = 1, title = '', search = '' } = {}) {
  const { data } = await client.get('/staff', { params: { page, title, search } })
  return data
}

export async function getStaff(id) {
  const { data } = await client.get(`/staff/${id}`)
  return data.data
}

export async function createStaff(formData) {
  const { data } = await client.post('/staff', formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  })
  return data.data
}

export async function updateStaff(id, formData) {
  const { data } = await client.post(`/staff/${id}`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  })
  return data.data
}

export async function deleteStaff(id) {
  const { data } = await client.delete(`/staff/${id}`)
  return data
}
