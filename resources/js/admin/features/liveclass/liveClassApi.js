import client from '../../api/client'

export async function listClasses() {
  const { data } = await client.get('/live-classes')
  return data
}

export async function getClass(id) {
  const { data } = await client.get(`/live-classes/${id}`)
  return data.data
}

export async function createClass(title, scheduledAt) {
  const { data } = await client.post('/live-classes', { title, scheduled_at: scheduledAt })
  return data.data
}

export async function startClass(id) {
  const { data } = await client.post(`/live-classes/${id}/start`)
  return data.data
}

export async function endClass(id) {
  const { data } = await client.post(`/live-classes/${id}/end`)
  return data.data
}

export async function getParticipants(id) {
  const { data } = await client.get(`/live-classes/${id}/participants`)
  return data.data
}

export async function approveHand(classId, participantId) {
  await client.post(`/live-classes/${classId}/participants/${participantId}/approve-hand`)
}

export async function rejectHand(classId, participantId) {
  await client.post(`/live-classes/${classId}/participants/${participantId}/reject-hand`)
}

export async function muteParticipant(classId, participantId) {
  await client.post(`/live-classes/${classId}/participants/${participantId}/mute`)
}

export async function muteEveryone(classId) {
  await client.post(`/live-classes/${classId}/mute-everyone`)
}

export async function removeParticipant(classId, participantId) {
  await client.post(`/live-classes/${classId}/participants/${participantId}/remove`)
}

export async function sendSignal(classId, to, signal) {
  await client.post(`/live-classes/${classId}/signal`, { to, signal })
}
