import client from '../../api/client'

export async function listBooks({ page = 1, search = '', status = '' } = {}) {
  const { data } = await client.get('/books', { params: { page, search, status } })
  return data
}

export async function getBook(id) {
  const { data } = await client.get(`/books/${id}`)
  return data.data
}

export async function createBook(payload) {
  const form = toFormData(payload)
  const { data } = await client.post('/books', form, { headers: { 'Content-Type': 'multipart/form-data' } })
  return data.data
}

export async function updateBook(id, payload) {
  const form = toFormData(payload)
  form.append('_method', 'PUT')
  const { data } = await client.post(`/books/${id}`, form, { headers: { 'Content-Type': 'multipart/form-data' } })
  return data.data
}

export async function deleteBook(id) {
  const { data } = await client.delete(`/books/${id}`)
  return data
}

function toFormData(payload) {
  const form = new FormData()
  Object.entries(payload).forEach(([key, value]) => {
    if (value !== undefined && value !== null) form.append(key, typeof value === 'boolean' ? (value ? '1' : '0') : value)
  })
  return form
}
