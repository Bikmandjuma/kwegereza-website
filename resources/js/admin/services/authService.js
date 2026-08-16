import client from '../api/client'

export async function login(email, password) {
  const { data } = await client.post('/auth/login', { email, password })
  return data.data // { token, user: { id, roles, permissions, ... } }
}

export async function fetchMe() {
  const { data } = await client.get('/auth/me')
  return data.data
}

export async function logout() {
  await client.post('/auth/logout')
}
