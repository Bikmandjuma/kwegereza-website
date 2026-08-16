import client from '../../api/client'

export async function getOnlineStudents() {
  const { data } = await client.get('/students-online')
  return data.data
}
