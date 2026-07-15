import { useEffect, useState } from 'react'
import { Link, useNavigate, useParams } from 'react-router-dom'
import Layout from '../../components/Layout'
import Breadcrumbs from '../../components/Breadcrumbs'
import Icon from '../../components/Icon'
import Alert from '../../components/Alert'
import api from '../../lib/api'

const fmtShort = d => new Date(d).toLocaleDateString('en-US', { month: 'short', day: '2-digit' })
const fmtDateTime = d => new Date(d).toLocaleString('en-US', { month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit', hour12: false })

// Ports resources/views/inspector/inspect-room.blade.php
export default function InspectorInspectRoom() {
  const { id } = useParams()
  const navigate = useNavigate()
  const [room, setRoom] = useState(null)
  const [cleaningHistory, setCleaningHistory] = useState([])
  const [inspectionHistory, setInspectionHistory] = useState([])
  const [action, setAction] = useState('')
  const [severity, setSeverity] = useState('minor')
  const [priority, setPriority] = useState('normal')
  const [notes, setNotes] = useState('')
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(true)
  const [submitting, setSubmitting] = useState(false)

  useEffect(() => {
    api.get(`/inspector/rooms/${id}/inspect`)
      .then(res => {
        setRoom(res.data.room)
        setCleaningHistory(res.data.cleaningHistory)
        setInspectionHistory(res.data.inspectionHistory)
      })
      .catch(err => setError(err.response?.data?.error || 'Unable to load this room.'))
      .finally(() => setLoading(false))
  }, [id])

  const submit = async e => {
    e.preventDefault()
    if (!action) return
    setSubmitting(true)
    setError('')
    try {
      await api.post(`/inspector/rooms/${id}/inspect`, {
        action,
        notes,
        severity: action === 'rejected' ? severity : undefined,
        priority: action === 'maintenance' ? priority : undefined,
      })
      navigate('/inspector')
    } catch (err) {
      setError(err.response?.data?.error || 'Something went wrong.')
      setSubmitting(false)
    }
  }

  if (loading) return <Layout><p className="text-[#CFCBCA] text-center py-12">Loading…</p></Layout>
  if (error && !room) {
    return (
      <Layout>
        <div className="max-w-2xl mx-auto py-12">
          <Alert variant="error" dismissible={false}>{error}</Alert>
          <Link to="/inspector" className="text-[#A0717F] mt-4 inline-block">Back to Dashboard</Link>
        </div>
      </Layout>
    )
  }

  return (
    <Layout>
      <div className="fixed top-0 right-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none"
        style={{ background: 'rgba(160, 113, 127, 0.05)', zIndex: 0 }}></div>
      <div className="fixed bottom-0 left-0 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none"
        style={{ background: 'rgba(234, 211, 205, 0.03)', zIndex: 0 }}></div>

      <div className="container mx-auto px-4 py-8 max-w-5xl relative z-10">
        <Breadcrumbs links={[
          { label: 'Inspector Dashboard', url: '/inspector' },
          { label: `Inspection: Room ${room.room_number}`, url: '#' },
        ]} />

        <div className="mb-10">
          <p className="text-xs font-medium uppercase mb-2" style={{ color: '#A0717F', letterSpacing: '0.4em' }}>
            {room.hotel.name}
          </p>
          <h1 className="text-3xl lg:text-5xl font-bold" style={{ color: '#EAD3CD', fontFamily: 'Georgia, serif' }}>
            Quality Inspection
          </h1>
        </div>

        {error && <div className="mb-8"><Alert variant="error">{error}</Alert></div>}

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div className="lg:col-span-2 space-y-8">
            <div className="bg-[#383537] border border-[#4E3B46] rounded-2xl shadow-xl overflow-hidden">
              <div className="px-8 py-6 border-b border-[#4E3B46] bg-[#2A2729]">
                <h3 className="text-xl font-bold text-[#EAD3CD] font-serif">Room {room.room_number} Dossier</h3>
              </div>
              <div className="p-8 grid grid-cols-2 gap-6">
                <div>
                  <p className="text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-1">Classification</p>
                  <p className="text-lg font-semibold text-[#EAD3CD]">{room.room_type}</p>
                </div>
                <div>
                  <p className="text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-1">Capacity</p>
                  <p className="text-lg font-semibold text-[#EAD3CD]">{room.capacity} Guests</p>
                </div>
              </div>
            </div>

            <div className="bg-[#383537] border border-[#4E3B46] rounded-2xl shadow-xl overflow-hidden">
              <div className="px-8 py-6 border-b border-[#4E3B46] bg-[#2A2729]">
                <h3 className="text-xl font-bold text-[#EAD3CD] font-serif">Final Adjudication</h3>
              </div>
              <div className="p-8">
                <form onSubmit={submit} className="space-y-8">
                  <div>
                    <label className="block text-sm font-semibold text-[#EAD3CD] mb-4">Inspection Result</label>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                      {[
                        { value: 'approved', icon: 'checkmark', label: 'Approve', iconCls: 'text-green-500', activeCls: 'border-green-500 bg-green-500/10', activeTextCls: 'text-green-400' },
                        { value: 'rejected', icon: 'close', label: 'Reject', iconCls: 'text-red-500', activeCls: 'border-red-500 bg-red-500/10', activeTextCls: 'text-red-400' },
                        { value: 'maintenance', icon: 'settings', label: 'Maintenance', iconCls: 'text-orange-500', activeCls: 'border-orange-500 bg-orange-500/10', activeTextCls: 'text-orange-400' },
                      ].map(opt => (
                        <label key={opt.value} className="relative cursor-pointer group">
                          <input type="radio" name="action" value={opt.value} checked={action === opt.value}
                            onChange={() => setAction(opt.value)} className="peer sr-only" required />
                          <div className={`p-4 rounded-xl border transition-all text-center ${
                            action === opt.value ? opt.activeCls : 'border-[#4E3B46] bg-[#2A2729]'
                          }`}>
                            <Icon name={opt.icon} className={`w-6 h-6 mx-auto mb-2 ${opt.iconCls}`} />
                            <span className={`block text-xs font-bold uppercase tracking-widest ${
                              action === opt.value ? opt.activeTextCls : 'text-[#CFCBCA]'
                            }`}>{opt.label}</span>
                          </div>
                        </label>
                      ))}
                    </div>
                  </div>

                  {action === 'rejected' && (
                    <div>
                      <label htmlFor="severity" className="block text-sm font-semibold text-[#EAD3CD] mb-3">Issue Severity</label>
                      <select id="severity" value={severity} onChange={e => setSeverity(e.target.value)}
                        className="w-full px-4 py-3 bg-[#2A2729] border border-[#4E3B46] text-[#EAD3CD] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#A0717F] transition-all">
                        <option value="minor">Minor - Surface issues</option>
                        <option value="moderate">Moderate - Noticeable flaws</option>
                        <option value="severe">Severe - Deep cleaning required</option>
                      </select>
                    </div>
                  )}

                  {action === 'maintenance' && (
                    <div>
                      <label htmlFor="priority" className="block text-sm font-semibold text-[#EAD3CD] mb-3">Maintenance Priority</label>
                      <select id="priority" value={priority} onChange={e => setPriority(e.target.value)}
                        className="w-full px-4 py-3 bg-[#2A2729] border border-[#4E3B46] text-[#EAD3CD] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#A0717F] transition-all">
                        <option value="low">Low - Routine</option>
                        <option value="normal">Normal - Standard fix</option>
                        <option value="urgent">Urgent - Immediate attention</option>
                      </select>
                    </div>
                  )}

                  <div>
                    <label htmlFor="notes" className="block text-sm font-semibold text-[#EAD3CD] mb-3">Professional Remarks</label>
                    <textarea id="notes" name="notes" rows={4} value={notes} onChange={e => setNotes(e.target.value)}
                      placeholder="Enter detailed observations or specific requirements..."
                      className="w-full px-4 py-3 bg-[#2A2729] text-[#EAD3CD] border border-[#4E3B46] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#A0717F] transition-all" />
                  </div>

                  <div className="flex gap-4 pt-4">
                    <button type="submit" disabled={submitting || !action}
                      className="flex-1 bg-[#A0717F] hover:bg-[#b58290] text-white font-bold py-4 rounded-xl transition-all shadow-xl uppercase text-xs tracking-widest disabled:opacity-60">
                      {submitting ? 'Submitting…' : 'Submit Adjudication'}
                    </button>
                    <Link to="/inspector" className="px-8 py-4 bg-[#4E3B46] hover:bg-[#68525F] text-[#EAD3CD] font-bold rounded-xl transition-all uppercase text-xs tracking-widest flex items-center">
                      Cancel
                    </Link>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div className="space-y-8">
            <div className="bg-[#383537] border border-[#4E3B46] rounded-2xl shadow-xl overflow-hidden">
              <div className="px-6 py-4 border-b border-[#4E3B46] bg-[#2A2729]">
                <h4 className="text-sm font-bold text-[#EAD3CD] uppercase tracking-widest">Cleaning Audit</h4>
              </div>
              <div className="p-6">
                {cleaningHistory.length > 0 ? (
                  <div className="space-y-6 relative before:content-[''] before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-[1px] before:bg-[#4E3B46]">
                    {cleaningHistory.map(log => (
                      <div key={log.id} className="relative pl-8">
                        <div className="absolute left-0 top-1 w-[22px] h-[22px] rounded-full border-2 border-[#4E3B46] bg-[#383537] flex items-center justify-center z-10">
                          <div className="w-2 h-2 rounded-full bg-[#A0717F]"></div>
                        </div>
                        <div>
                          <p className="text-xs font-bold text-[#EAD3CD]">{log.user.name}</p>
                          <p className="text-[10px] text-[#CFCBCA] opacity-50 mb-1">{fmtDateTime(log.created_at)}</p>
                          <p className="text-xs text-[#CFCBCA] leading-relaxed italic">"{log.notes || 'No notes provided'}"</p>
                        </div>
                      </div>
                    ))}
                  </div>
                ) : (
                  <p className="text-xs text-[#CFCBCA] opacity-50 text-center py-4 italic">No cleaning records found.</p>
                )}
              </div>
            </div>

            <div className="bg-[#383537] border border-[#4E3B46] rounded-2xl shadow-xl overflow-hidden">
              <div className="px-6 py-4 border-b border-[#4E3B46] bg-[#2A2729]">
                <h4 className="text-sm font-bold text-[#EAD3CD] uppercase tracking-widest">Inspection Logs</h4>
              </div>
              <div className="p-6">
                {inspectionHistory.length > 0 ? (
                  <div className="space-y-4">
                    {inspectionHistory.map(inspection => (
                      <div key={inspection.id} className="p-3 rounded-lg border border-[#4E3B46] bg-[#2A2729]/50">
                        <div className="flex justify-between items-start mb-2">
                          <span className={`text-[10px] font-bold uppercase py-0.5 px-2 rounded-md border ${
                            inspection.status === 'approved'
                              ? 'bg-green-500/10 text-green-400 border-green-500/30'
                              : 'bg-red-500/10 text-red-400 border-red-500/30'
                          }`}>
                            {inspection.status}
                          </span>
                          <span className="text-[10px] text-[#CFCBCA] opacity-50">{fmtShort(inspection.created_at)}</span>
                        </div>
                        <p className="text-[11px] text-[#CFCBCA] line-clamp-2">{inspection.issue_description}</p>
                      </div>
                    ))}
                  </div>
                ) : (
                  <p className="text-xs text-[#CFCBCA] opacity-50 text-center py-4 italic">No previous inspections.</p>
                )}
              </div>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  )
}
