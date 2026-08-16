import client from '../../api/client'

export async function listDarsat({ page = 1, search = '', status = '' } = {}) {
  const { data } = await client.get('/darsat', { params: { page, search, status } })
  return data
}

export async function getDarsat(id) {
  const { data } = await client.get(`/darsat/${id}`)
  return data.data
}

export async function createDarsat(payload) {
  const form = toFormData(payload)
  const { data } = await client.post('/darsat', form, {
    headers: { 'Content-Type': 'multipart/form-data' },
  })
  return data.data
}

export async function updateDarsat(id, payload) {
  const form = toFormData(payload)
  form.append('_method', 'PUT')
  const { data } = await client.post(`/darsat/${id}`, form, {
    headers: { 'Content-Type': 'multipart/form-data' },
  })
  return data.data
}

export async function deleteDarsat(id) {
  const { data } = await client.delete(`/darsat/${id}`)
  return data
}

function toFormData(payload) {
  const form = new FormData()
  Object.entries(payload).forEach(([key, value]) => {
    if (value !== undefined && value !== null) form.append(key, value)
  })
  return form
}
