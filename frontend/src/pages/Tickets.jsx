import { useEffect, useState } from 'react'
import { useNavigate, Link } from 'react-router-dom'
import { apiFetch, removeToken } from '../api.js'

const STATUSES = ['', 'open', 'pending', 'on_hold', 'resolved', 'closed']
const PRIORITIES = ['', 'low', 'medium', 'high', 'urgent']

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

function Tickets() {
  const navigate = useNavigate()
  const [tickets, setTickets] = useState([])
  const [status, setStatus] = useState('')
  const [priority, setPriority] = useState('')
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')

  useEffect(() => {
    const params = new URLSearchParams()
    if (status) params.set('status', status)
    if (priority) params.set('priority', priority)

    setLoading(true)
    apiFetch(`/api/tickets?${params.toString()}`)
      .then((data) => setTickets(data.data || []))
      .catch((err) => {
        setError(err.message)
        if (err.message.includes('401') || err.message.includes('403')) {
          removeToken()
          navigate('/login')
        }
      })
      .finally(() => setLoading(false))
  }, [status, priority, navigate])

  function logout() {
    removeToken()
    navigate('/login')
  }

  return (
    <div className="min-h-screen bg-gray-50 p-6">
      <div className="mx-auto max-w-6xl">
        <div className="flex items-center justify-between mb-6">
          <h1 className="text-2xl font-semibold text-gray-900">Tickets</h1>
          <button
            onClick={logout}
            className="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300"
          >
            Log out
          </button>
        </div>

        <div className="flex gap-4 mb-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select
              value={status}
              onChange={(e) => setStatus(e.target.value)}
              className="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
            >
              {STATUSES.map((s) => (
                <option key={s} value={s}>{s ? s.replace('_', ' ') : 'All statuses'}</option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Priority</label>
            <select
              value={priority}
              onChange={(e) => setPriority(e.target.value)}
              className="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
            >
              {PRIORITIES.map((p) => (
                <option key={p} value={p}>{p ? p : 'All priorities'}</option>
              ))}
            </select>
          </div>
        </div>

        {error && (
          <div className="mb-4 p-3 rounded bg-red-50 text-red-700 text-sm">{error}</div>
        )}

        {loading ? (
          <p className="text-gray-600">Loading tickets...</p>
        ) : tickets.length === 0 ? (
          <p className="text-gray-600">No tickets found.</p>
        ) : (
          <div className="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
            <table className="w-full text-left text-sm">
              <thead className="bg-gray-50 text-gray-700">
                <tr>
                  <th className="px-4 py-3 font-medium">Subject</th>
                  <th className="px-4 py-3 font-medium">Status</th>
                  <th className="px-4 py-3 font-medium">Priority</th>
                  <th className="px-4 py-3 font-medium">Requester</th>
                  <th className="px-4 py-3 font-medium">Assignee</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {tickets.map((ticket) => (
                  <tr key={ticket.id} className="hover:bg-gray-50">
                    <td className="px-4 py-3">
                      <Link
                        to={`/tickets/${ticket.id}`}
                        className="font-medium text-indigo-600 hover:underline"
                      >
                        {ticket.subject}
                      </Link>
                    </td>
                    <td className="px-4 py-3">
                      <span className={`inline-flex rounded-full px-2 py-1 text-xs font-medium ${STATUS_COLORS[ticket.status] || 'bg-gray-100 text-gray-800'}`}>
                        {ticket.status.replace('_', ' ')}
                      </span>
                    </td>
                    <td className="px-4 py-3">
                      <span className={`inline-flex rounded-full px-2 py-1 text-xs font-medium ${PRIORITY_COLORS[ticket.priority] || 'bg-gray-100 text-gray-800'}`}>
                        {ticket.priority}
                      </span>
                    </td>
                    <td className="px-4 py-3 text-gray-700">{ticket.requester?.name || '—'}</td>
                    <td className="px-4 py-3 text-gray-700">{ticket.assignee?.name || '—'}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  )
}

export default Tickets
