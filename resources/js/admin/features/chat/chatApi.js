import client from '../../api/client'

export async function listConversations() {
  const { data } = await client.get('/chat/conversations')
  return data.data
}

export async function getMessages(guestId) {
  const { data } = await client.get(`/chat/messages/${guestId}`)
  return data.data
}

export async function sendMessage(guestId, message) {
  const { data } = await client.post(`/chat/messages/${guestId}`, { message })
  return data.data
}

export async function markRead(guestId) {
  const { data } = await client.post(`/chat/messages/${guestId}/read`)
  return data
}
