import client from '../../api/client'

export async function listAuditLogs({ page = 1, entity_type = '', action = '' } = {}) {
  const { data } = await client.get('/audit-logs', { params: { page, entity_type, action } })
  return data
}
