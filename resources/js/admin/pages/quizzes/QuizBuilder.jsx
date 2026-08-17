import { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import * as quizzesApi from '../../features/quizzes/quizzesApi'
import { Button, Input, Card, Badge, CardSkeleton } from '../../components/ui'
import Can from '../../permissions/Can'
import { useToast } from '../../contexts/ToastContext'

const TYPE_LABEL = { multiple_choice: 'Guhitamo', true_false: "Ni ukuri/Si ukuri", short_answer: 'Igisubizo gito' }

export default function QuizBuilder() {
  const { id } = useParams()
  const navigate = useNavigate()
  const { push } = useToast()
  const [quiz, setQuiz] = useState(null)
  const [loading, setLoading] = useState(true)
  const [showForm, setShowForm] = useState(false)

  const [q, setQ] = useState({ question: '', type: 'multiple_choice', points: 10, short_answer: '', answers: ['', '', ''], correct: '' })

  function load() {
    quizzesApi.getQuiz(id).then(setQuiz).catch(() => push('Ntibishoboka gushaka iki kizamini.', 'error')).finally(() => setLoading(false))
  }

  useEffect(() => { load() }, [id]) // eslint-disable-line react-hooks/exhaustive-deps

  async function handleAddQuestion(e) {
    e.preventDefault()
    try {
      await quizzesApi.createQuestion(id, q)
      push('Ikibazo cyongewe.')
      setShowForm(false)
      setQ({ question: '', type: 'multiple_choice', points: 10, short_answer: '', answers: ['', '', ''], correct: '' })
      load()
    } catch (err) {
      push(err.response?.status === 403 ? 'Ntabwo ufite uburenganzira.' : 'Habaye ikibazo.', 'error')
    }
  }

  async function handleDeleteQuestion(questionId) {
    if (!confirm('Wemeza gusiba iki kibazo?')) return
    try {
      await quizzesApi.deleteQuestion(questionId)
      push('Ikibazo cyakuweho.')
      load()
    } catch {
      push('Ntibishoboka gusiba iki kibazo.', 'error')
    }
  }

  if (loading) return <Card className="p-6"><CardSkeleton lines={4} /></Card>
  if (!quiz) return null

  return (
    <div className="flex flex-col gap-6">
      <Button variant="ghost" onClick={() => navigate('/quizzes')} className="w-fit px-0">← Subira ku bizamini</Button>

      <div>
        <h1 className="font-display text-2xl text-ink">{quiz.title}</h1>
        <p className="text-sm text-ink/60">Amanota yose: {quiz.total_points}</p>
      </div>

      <div className="flex flex-col gap-3">
        {quiz.questions?.map((question, i) => (
          <Card key={question.id} className="p-4">
            <div className="flex items-start justify-between gap-4">
              <div>
                <p className="text-xs uppercase tracking-wide text-ink/40">Ikibazo {i + 1} · <Badge tone="neutral">{TYPE_LABEL[question.type]}</Badge> · {question.points} pts</p>
                <p className="mt-1 font-medium text-ink">{question.question}</p>
                {question.answers?.length > 0 && (
                  <ul className="mt-2 space-y-1 text-sm text-ink/70">
                    {question.answers.map((a) => (
                      <li key={a.id} className={a.is_correct ? 'font-medium text-teal-700' : ''}>
                        {a.is_correct ? '✓ ' : '— '}{a.answer_text}
                      </li>
                    ))}
                  </ul>
                )}
                {question.type === 'short_answer' && (
                  <p className="mt-2 text-sm text-teal-700">Igisubizo nyacyo: {question.short_answer}</p>
                )}
              </div>
              <Can permission="quizzes.delete">
                <button onClick={() => handleDeleteQuestion(question.id)} className="text-sm text-rose-600 hover:underline">Siba</button>
              </Can>
            </div>
          </Card>
        ))}
        {(!quiz.questions || quiz.questions.length === 0) && (
          <Card className="p-8 text-center text-sm text-ink/50">Nta kibazo kirahari.</Card>
        )}
      </div>

      <Can permission="quizzes.create">
        {!showForm ? (
          <Button onClick={() => setShowForm(true)} className="w-fit">+ Ongeraho ikibazo</Button>
        ) : (
          <Card className="p-6">
            <form onSubmit={handleAddQuestion} className="flex flex-col gap-4">
              <label className="block">
                <span className="mb-1 block text-sm font-medium text-ink">Ikibazo</span>
                <textarea
                  required rows={2} value={q.question}
                  onChange={(e) => setQ({ ...q, question: e.target.value })}
                  className="w-full rounded-lg border border-sand-200 px-3 py-2 text-sm outline-none focus:border-teal-600"
                />
              </label>

              <label className="block">
                <span className="mb-1 block text-sm font-medium text-ink">Ubwoko</span>
                <select
                  value={q.type}
                  onChange={(e) => setQ({ ...q, type: e.target.value })}
                  className="w-full rounded-lg border border-sand-200 px-3 py-2 text-sm outline-none focus:border-teal-600"
                >
                  <option value="multiple_choice">Guhitamo</option>
                  <option value="true_false">Ni ukuri/Si ukuri</option>
                  <option value="short_answer">Igisubizo gito</option>
                </select>
              </label>

              <Input label="Amanota" type="number" min="1" max="100" required value={q.points} onChange={(e) => setQ({ ...q, points: e.target.value })} />

              {q.type === 'multiple_choice' && (
                <div className="flex flex-col gap-2">
                  <span className="text-sm font-medium text-ink">Ibisubizo (hitamo icy'ukuri)</span>
                  {q.answers.map((ans, idx) => (
                    <div key={idx} className="flex items-center gap-2">
                      <input type="radio" name="correct" checked={String(q.correct) === String(idx)} onChange={() => setQ({ ...q, correct: idx })} />
                      <input
                        value={ans}
                        onChange={(e) => {
                          const next = [...q.answers]; next[idx] = e.target.value
                          setQ({ ...q, answers: next })
                        }}
                        className="flex-1 rounded-lg border border-sand-200 px-3 py-2 text-sm outline-none focus:border-teal-600"
                        placeholder={`Igisubizo ${idx + 1}`}
                      />
                    </div>
                  ))}
                </div>
              )}

              {q.type === 'true_false' && (
                <div className="flex gap-4 text-sm">
                  <label className="flex items-center gap-2"><input type="radio" name="tf" checked={q.correct === 'true'} onChange={() => setQ({ ...q, correct: 'true' })} /> Ni ukuri</label>
                  <label className="flex items-center gap-2"><input type="radio" name="tf" checked={q.correct === 'false'} onChange={() => setQ({ ...q, correct: 'false' })} /> Si ukuri</label>
                </div>
              )}

              {q.type === 'short_answer' && (
                <Input label="Igisubizo nyacyo" required value={q.short_answer} onChange={(e) => setQ({ ...q, short_answer: e.target.value })} />
              )}

              <div className="flex gap-3">
                <Button type="submit">Bika ikibazo</Button>
                <Button type="button" variant="secondary" onClick={() => setShowForm(false)}>Reka</Button>
              </div>
            </form>
          </Card>
        )}
      </Can>
    </div>
  )
}
