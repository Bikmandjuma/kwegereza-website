import client from '../../api/client'

export async function listQuizzes({ page = 1, search = '', status = '' } = {}) {
  const { data } = await client.get('/quizzes', { params: { page, search, status } })
  return data
}

export async function getQuiz(id) {
  const { data } = await client.get(`/quizzes/${id}`)
  return data.data
}

export async function createQuiz(payload) {
  const { data } = await client.post('/quizzes', payload)
  return data.data
}

export async function updateQuiz(id, payload) {
  const { data } = await client.put(`/quizzes/${id}`, payload)
  return data.data
}

export async function deleteQuiz(id) {
  const { data } = await client.delete(`/quizzes/${id}`)
  return data
}

export async function createQuestion(quizId, payload) {
  const { data } = await client.post(`/quizzes/${quizId}/questions`, payload)
  return data.data
}

export async function updateQuestion(questionId, payload) {
  const { data } = await client.put(`/quizzes/questions/${questionId}`, payload)
  return data.data
}

export async function deleteQuestion(questionId) {
  const { data } = await client.delete(`/quizzes/questions/${questionId}`)
  return data
}
