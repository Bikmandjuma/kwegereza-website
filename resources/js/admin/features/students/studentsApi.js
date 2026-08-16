import client from '../../api/client'

export async function listStudents({ page = 1, search = '' } = {}) {
  const { data } = await client.get('/students', { params: { page, search } })
  return data
}

export async function getStudent(id) {
  const { data } = await client.get(`/students/${id}`)
  return data.data
}

export async function blockStudent(id) {
  const { data } = await client.patch(`/students/${id}/block`)
  return data.data
}

export async function unblockStudent(id) {
  const { data } = await client.patch(`/students/${id}/unblock`)
  return data.data
}
