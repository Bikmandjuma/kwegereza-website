import client from '../../api/client'

export async function getLeadersMessages() {
  const { data } = await client.get('/group-chat/leaders')
  return { messages: data.data, pinned: data.pinned }
}

export async function sendLeadersMessage(message, parentId = null) {
  const { data } = await client.post('/group-chat/leaders', { message, parent_id: parentId })
  return data.data
}

export async function react(messageId, emoji) {
  const { data } = await client.post(`/group-chat/messages/${messageId}/react`, { emoji })
  return data.data
}

export async function deleteMessage(messageId) {
  await client.delete(`/group-chat/messages/${messageId}`)
}

export async function reportMessage(messageId, reason) {
  await client.post(`/group-chat/messages/${messageId}/report`, { reason })
}

export async function pinMessage(messageId) {
  await client.post(`/group-chat/messages/${messageId}/pin`)
}

export async function unpinMessage(messageId) {
  await client.post(`/group-chat/messages/${messageId}/unpin`)
}
