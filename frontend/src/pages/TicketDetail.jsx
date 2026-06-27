import { useEffect, useState } from 'react'
import { useParams, useNavigate, Link } from 'react-router-dom'
import { apiFetch, removeToken } from '../api.js'

const STATUS_COLORS = {
  open: 'bg-gray-100 text-gray-800',
  pending: 'bg-yellow-100 text-yellow-800',
  on_hold: 'bg-orange-100 text-orange-800',
  resolved: 'bg-green-100 text-green-800',
  closed: 'bg-blue-100 text-blue-800',
}

const PRIORITY_COLORS = {
  low: 'bg-gray-100 text-gray-800',
  medium: 'bg-blue-100 text-blue-800',
  high: 'bg-orange-100 text-orange-800',
  urgent: 'bg-red-100 text-red-800',
}

function TicketDetail() {
  const { id } = useParams()
  const navigate = useNavigate()
  const [ticket, setTicket] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')

  const [commentBody, setCommentBody] = useState('')
  const [isInternal, setIsInternal] = useState(false)
  const [saving, setSaving] = useState(false)

  useEffect(() => {
    setLoading(true)
    apiFetch(`/api/tickets/${id}`)
      .then((data) => setTicket(data.data))
      .catch((err) => {
        setError(err.message)
        if (err.message.includes('401') || err.message.includes('403')) {
          removeToken()
          navigate('/login')
        }
      })
      .finally(() => setLoading(false))
  }, [id, navigate])

  async function submitComment(e) {
    e.preventDefault()
    if (!commentBody.trim()) return

    setSaving(true)
    try {
      await apiFetch(`/api/tickets/${id}/comments`, {
        method: 'POST',
        body: JSON.stringify({ body: commentBody, is_internal: isInternal }),
      })

      const refreshed = await apiFetch(`/api/tickets/${id}`)
      setTicket(refreshed.data)
      setCommentBody('')
      setIsInternal(false)
    } catch (err) {
      setError(err.message)
    } finally {
      setSaving(false)
    }
  }

  if (loading) return <p className="p-6 text-gray-600">Loading ticket...</p>
  if (error) return <p className="p-6 text-red-600">{error}</p>
  if (!ticket) return <p className="p-6 text-gray-600">Ticket not found.</p>

  return (
    <div className="min-h-screen bg-gray-50 p-6">
      <div className="mx-auto max-w-4xl">
        <Link to="/tickets" className="text-sm text-indigo-600 hover:underline">← Back to tickets</Link>

        <div className="mt-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
          <div className="flex items-start justify-between">
            <h1 className="text-2xl font-semibold text-gray-900">{ticket.subject}</h1>
            <div className="flex gap-2">
              <span className={`inline-flex rounded-full px-3 py-1 text-xs font-medium ${STATUS_COLORS[ticket.status] || 'bg-gray-100 text-gray-800'}`}>
                {ticket.status.replace('_', ' ')}
              </span>
              <span className={`inline-flex rounded-full px-3 py-1 text-xs font-medium ${PRIORITY_COLORS[ticket.priority] || 'bg-gray-100 text-gray-800'}`}>
                {ticket.priority}
              </span>
            </div>
          </div>

          <p className="mt-4 text-gray-700 whitespace-pre-wrap">{ticket.description}</p>

          <div className="mt-6 grid grid-cols-1 gap-4 text-sm text-gray-600 sm:grid-cols-2">
            <div>
              <strong>Requester:</strong> {ticket.requester?.name || '—'}
            </div>
            <div>
              <strong>Assignee:</strong> {ticket.assignee?.name || '—'}
            </div>
            <div>
              <strong>Created:</strong> {new Date(ticket.created_at).toLocaleString()}
            </div>
            <div>
              <strong>Ticket ID:</strong> #{ticket.id}
            </div>
          </div>
        </div>

        <div className="mt-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Comments</h2>

          {ticket.comments?.length === 0 ? (
            <p className="text-gray-600">No comments yet.</p>
          ) : (
            <ul className="space-y-4">
              {ticket.comments?.map((comment) => (
                <li
                  key={comment.id}
                  className={`rounded-lg border p-4 ${comment.is_internal ? 'border-orange-200 bg-orange-50' : 'border-gray-200 bg-white'}`}
                >
                  <div className="flex items-center justify-between">
                    <span className="text-sm font-medium text-gray-900">{comment.author?.name || 'Unknown'}</span>
                    {comment.is_internal && (
                      <span className="inline-flex rounded-full bg-orange-200 px-2 py-1 text-xs font-medium text-orange-800">
                        Internal note
                      </span>
                    )}
                  </div>
                  <p className="mt-2 text-gray-700 whitespace-pre-wrap">{comment.body}</p>
                  <span className="mt-2 block text-xs text-gray-500">{new Date(comment.created_at).toLocaleString()}</span>
                </li>
              ))}
            </ul>
          )}

          <form onSubmit={submitComment} className="mt-6 space-y-4">
            <div>
              <label htmlFor="comment" className="block text-sm font-medium text-gray-700">
                Add a response
              </label>
              <textarea
                id="comment"
                rows={4}
                value={commentBody}
                onChange={(e) => setCommentBody(e.target.value)}
                required
                className="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
              />
            </div>

            <div className="flex items-center gap-2">
              <input
                id="is_internal"
                type="checkbox"
                checked={isInternal}
                onChange={(e) => setIsInternal(e.target.checked)}
                className="rounded border-gray-300"
              />
              <label htmlFor="is_internal" className="text-sm text-gray-700">Internal note</label>
            </div>

            <button
              type="submit"
              disabled={saving}
              className="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
            >
              {saving ? 'Posting...' : 'Post comment'}
            </button>
          </form>
        </div>
      </div>
    </div>
  )
}

export default TicketDetail
