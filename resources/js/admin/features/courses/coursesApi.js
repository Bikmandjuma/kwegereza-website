import client from '../../api/client'

export async function listCourses({ page = 1, search = '', status = '' } = {}) {
  const { data } = await client.get('/courses', { params: { page, search, status } })
  return data // { success, data: [...], meta: {...} }
}

export async function getCourse(id) {
  const { data } = await client.get(`/courses/${id}`)
  return data.data
}

export async function createCourse(payload) {
  const form = toFormData(payload)
  const { data } = await client.post('/courses', form, {
    headers: { 'Content-Type': 'multipart/form-data' },
  })
  return data.data
}

export async function updateCourse(id, payload) {
  const form = toFormData(payload)
  form.append('_method', 'PUT') // Laravel method-spoofing for multipart PUT
  const { data } = await client.post(`/courses/${id}`, form, {
    headers: { 'Content-Type': 'multipart/form-data' },
  })
  return data.data
}

export async function deleteCourse(id) {
  const { data } = await client.delete(`/courses/${id}`)
  return data
}

function toFormData(payload) {
  const form = new FormData()
  Object.entries(payload).forEach(([key, value]) => {
    if (value !== undefined && value !== null) form.append(key, value)
  })
  return form
}
