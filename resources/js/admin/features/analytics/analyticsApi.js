import client from '../../api/client'

export async function getOverview(days = 7) {
  const { data } = await client.get('/analytics/overview', { params: { days } })
  return data.data
}
