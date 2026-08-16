import client from '../../api/client'

export async function listNotifications({ page = 1 } = {}) {
  const { data } = await client.get('/notifications', { params: { page } })
  return data
}

export async function unreadCount() {
  const { data } = await client.get('/notifications/unread-count')
  return data.data.count
}

export async function markRead(id) {
  const { data } = await client.post(`/notifications/${id}/read`)
  return data
}

export async function markAllRead() {
  const { data } = await client.post('/notifications/read-all')
  return data
}
