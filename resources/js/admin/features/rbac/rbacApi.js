import client from '../../api/client'

export async function listRoles() {
  const { data } = await client.get('/roles')
  return data.data
}

export async function getRole(id) {
  const { data } = await client.get(`/roles/${id}`)
  return data.data
}

export async function createRole(payload) {
  const { data } = await client.post('/roles', payload)
  return data.data
}

export async function updateRole(id, payload) {
  const { data } = await client.put(`/roles/${id}`, payload)
  return data.data
}

export async function deleteRole(id) {
  const { data } = await client.delete(`/roles/${id}`)
  return data
}

export async function listPermissions(page = 1) {
  const { data } = await client.get('/permissions', { params: { page } })
  return data
}
