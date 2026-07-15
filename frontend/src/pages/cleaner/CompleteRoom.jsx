import { useEffect, useState } from 'react'
import { Link, useNavigate, useParams } from 'react-router-dom'
import Layout from '../../components/Layout'
import Alert from '../../components/Alert'
import api from '../../lib/api'

const fmt = d => new Date(d).toLocaleString('en-US', { month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit', hour12: false })

// Ports resources/views/cleaner/complete-room.blade.php
export default function CleanerCompleteRoom() {
  const { id } = useParams()
  const navigate = useNavigate()
  const [room, setRoom] = useState(null)
  const [history, setHistory] = useState([])
  const [notes, setNotes] = useState('')
  const [error, setError] = useState('')
  const [fieldError, setFieldError] = useState('')
  const [loading, setLoading] = useState(true)
  const [submitting, setSubmitting] = useState(false)

  useEffect(() => {
    api.get(`/cleaner/rooms/${id}/complete`)
      .then(res => { setRoom(res.data.room); setHistory(res.data.cleaningHistory) })
      .catch(err => setError(err.response?.data?.error || 'Unable to load this room.'))
      .finally(() => setLoading(false))
  }, [id])

  const submit = async action => {
    setSubmitting(true)
    setError('')
    setFieldError('')
    try {
      await api.post(`/cleaner/rooms/${id}/complete`, { action, notes })
      navigate('/cleaner')
    } catch (err) {
      if (err.response?.status === 422) setFieldError(err.response.data.errors?.notes?.[0] || '')
      else setError(err.response?.data?.error || 'Something went wrong.')
      setSubmitting(false)
    }
  }

  if (loading) return <Layout><p className="text-[#CFCBCA] text-center py-12">Loading…</p></Layout>
  if (error && !room) {
    return (
      <Layout>
        <div className="max-w-2xl mx-auto py-12">
          <Alert variant="error" dismissible={false}>{error}</Alert>
          <Link to="/cleaner" className="text-[#A0717F] mt-4 inline-block">Back to Dashboard</Link>
        </div>
      </Layout>
    )
  }

  return (
    <Layout>
      <div className="max-w-7xl mx-auto px-4 py-12">
        <Link to="/cleaner" className="text-[#CFCBCA] hover:text-[#EAD3CD] mb-6 inline-block">
          ← Back to Dashboard
        </Link>

        {error && <div className="mb-6"><Alert variant="error">{error}</Alert></div>}

        <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-8">
          <h2 className="text-2xl font-bold text-[#EAD3CD] mb-2">Room {room.room_number} - Cleaning Completion</h2>
          <p className="text-[#CFCBCA] mb-8">Room Type: {room.room_type} | Capacity: {room.capacity} guests</p>

          {history.length > 0 && (
            <div className="mb-8 bg-[#2A2729] border border-[#4E3B46] rounded-lg p-4">
              <h3 className="font-semibold text-[#EAD3CD] mb-4">Previous History</h3>
              <div className="space-y-3 max-h-60 overflow-y-auto">
                {history.map(log => (
                  <div key={log.id}
                    className={`${log.type === 'inspection' ? 'bg-[#1A1515] border border-red-500' : 'border border-[#4E3B46]'} rounded p-3 text-sm`}>
                    <div className="flex justify-between items-start mb-1">
                      <div>
                        <span className="font-semibold text-[#EAD3CD]">{log.user.name}</span>
                        {log.type === 'inspection' && (
                          <span className="ml-2 text-xs border border-red-500 text-red-400 px-2 py-1 rounded bg-[#2A2729]">Inspector Feedback</span>
                        )}
                      </div>
                      <span className="text-xs text-[#CFCBCA]">{fmt(log.created_at)}</span>
                    </div>
                    {log.action && (
                      <p className="text-xs text-[#CFCBCA] mb-1">
                        <strong>Action:</strong>{' '}
                        {log.action === 'finished' ? (
                          <span className="text-green-400">✓ Finished Cleaning</span>
                        ) : log.action === 're-clean' ? (
                          <span className="text-yellow-400">⟳ Marked for Re-cleaning</span>
                        ) : null}
                      </p>
                    )}
                    {log.notes ? (
                      <p className="text-[#EAD3CD] mt-1"><strong>Notes:</strong> {log.notes}</p>
                    ) : (
                      <p className="text-[#CFCBCA] italic">No notes provided</p>
                    )}
                  </div>
                ))}
              </div>
            </div>
          )}

          <form onSubmit={e => e.preventDefault()} className="space-y-6">
            <div>
              <label htmlFor="notes" className="block text-sm font-semibold text-[#EAD3CD] mb-2">
                Cleaning Notes
              </label>
              <p className="text-xs text-[#CFCBCA] mb-3">
                Add any notes about the cleaning. These will be visible to the Inspector and Receptionist.
              </p>
              <textarea id="notes" name="notes" rows={5} value={notes} onChange={e => setNotes(e.target.value)}
                placeholder="e.g., Minor stain on carpet, requested maintenance attention. Or: All good, room is clean and ready."
                className="w-full px-4 py-2 bg-[#383537] text-[#EAD3CD] border border-[#4E3B46] rounded-lg focus:outline-none focus:border-[#A0717F]" />
              {fieldError && <p className="text-red-500 text-sm mt-2">{fieldError}</p>}
            </div>

            <div className="bg-[#2A2729] border-l-4 border-[#A0717F] p-4 rounded">
              <p className="text-sm text-[#EAD3CD]">
                <strong>Current Status:</strong> Cleaning
              </p>
            </div>

            <div className="flex gap-4">
              <button type="button" disabled={submitting} onClick={() => submit('finished')}
                className="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition disabled:opacity-60">
                ✓ Finished Cleaning
              </button>

              <button type="button" disabled={submitting} onClick={() => submit('re-clean')}
                className="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold px-6 py-3 rounded-lg transition disabled:opacity-60">
                ⟳ For Cleaning Again
              </button>

              <Link to="/cleaner"
                className="flex-1 bg-[#4E3B46] hover:bg-[#68525F] text-[#EAD3CD] font-semibold px-6 py-3 rounded-lg transition text-center border border-[#4E3B46]">
                Cancel
              </Link>
            </div>

            <div className="grid grid-cols-2 gap-4 text-xs text-[#CFCBCA]">
              <div className="bg-[#1A1515] border border-green-500 p-3 rounded">
                <p className="font-semibold text-green-400">Finished Cleaning</p>
                <p>Room moves to Inspection for Inspector review</p>
              </div>
              <div className="bg-[#1A1515] border border-yellow-500 p-3 rounded">
                <p className="font-semibold text-yellow-400">For Cleaning Again</p>
                <p>Room stays in Cleaning. Your notes are left as a remark.</p>
              </div>
            </div>
          </form>
        </div>
      </div>
    </Layout>
  )
}
