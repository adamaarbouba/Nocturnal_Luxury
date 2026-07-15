import { useEffect, useState } from 'react'
import { Link, useNavigate, useParams } from 'react-router-dom'
import Layout from '../../../components/Layout'
import api from '../../../lib/api'
import { fmtDate, timeAgo, ucfirst } from '../format'

const priorityCls = (p) => (p === 'urgent' ? 'text-red-400 border-red-500' : p === 'normal' ? 'text-yellow-400 border-yellow-500' : 'text-green-400 border-green-500')
const statusCls = (s) => (s === 'pending' ? 'text-orange-400 border-orange-500' : s === 'in-progress' ? 'text-blue-400 border-blue-500' : 'text-green-400 border-green-500')

// Ports resources/views/owner/maintenance/show.blade.php
export default function MaintenanceShow() {
  const { id } = useParams()
  const navigate = useNavigate()
  const [request, setRequest] = useState(null)
  const [nextStatus, setNextStatus] = useState('')
  const [notes, setNotes] = useState('')
  const [errors, setErrors] = useState({})

  useEffect(() => {
    api.get(`/owner/maintenance/${id}`).then((res) => setRequest(res.data.maintenanceRequest)).catch(() => {})
  }, [id])

  const submit = async (e) => {
    e.preventDefault()
    setErrors({})
    try {
      await api.post(`/owner/maintenance/${id}/transition`, { next_status: nextStatus, completion_notes: notes })
      navigate('/owner/maintenance')
    } catch (err) {
      setErrors(err.response?.data?.errors ?? {})
    }
  }

  if (!request) return <Layout><p className="text-[#CFCBCA]">Loading…</p></Layout>

  return (
    <Layout>
      <Link to="/owner/maintenance" className="text-[#CFCBCA] hover:text-[#EAD3CD] mb-6 inline-block">← Back to Maintenance</Link>

      <div className="grid grid-cols-3 gap-8">
        <div className="col-span-2">
          <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-8">
            <div className="border-b border-[#4E3B46] pb-6 mb-6">
              <h2 className="text-3xl font-bold text-[#EAD3CD]">Room {request.room.room_number} Maintenance</h2>
              <p className="text-[#CFCBCA] mt-2">{request.hotel.name}</p>
              <p className="text-sm text-[#4E3B46] mt-1">Room Type: {request.room.room_type} | Capacity: {request.room.capacity} guests</p>
            </div>

            <div className="mb-8">
              <h3 className="text-lg font-semibold text-[#EAD3CD] mb-4">Request Details</h3>
              <div className="grid grid-cols-2 gap-6">
                <div>
                  <p className="text-sm text-[#CFCBCA]"><strong>Inspector:</strong></p>
                  <p className="text-[#EAD3CD]">{request.inspector.name}</p>
                </div>
                <div>
                  <p className="text-sm text-[#CFCBCA]"><strong>Priority:</strong></p>
                  <p className="text-[#EAD3CD]">
                    <span className={`inline-block px-3 py-1 rounded-full text-sm font-semibold border bg-[#2A2729] ${priorityCls(request.priority)}`}>
                      {ucfirst(request.priority)}
                    </span>
                  </p>
                </div>
                <div>
                  <p className="text-sm text-[#CFCBCA]"><strong>Requested:</strong></p>
                  <p className="text-[#EAD3CD]">{fmtDate(request.created_at, true)}</p>
                </div>
                <div>
                  <p className="text-sm text-[#CFCBCA]"><strong>Status:</strong></p>
                  <p className="text-[#EAD3CD]">
                    <span className={`inline-block px-3 py-1 rounded text-sm font-semibold border bg-[#2A2729] ${statusCls(request.status)}`}>
                      {ucfirst(request.status.replace('-', ' '))}
                    </span>
                  </p>
                </div>
              </div>
            </div>

            <div className="mb-8 bg-[#2A2729] border border-[#4E3B46] rounded-lg p-4">
              <h3 className="text-lg font-semibold text-[#EAD3CD] mb-2">Issue Description</h3>
              <p className="text-[#CFCBCA]">{request.issue_description || 'No description provided'}</p>
            </div>

            {request.status !== 'completed' ? (
              <div className="bg-[#2A2729] border border-[#4E3B46] rounded-lg p-6 mb-8">
                <h3 className="text-lg font-semibold text-[#EAD3CD] mb-4">Choose Next Action</h3>
                <p className="text-[#CFCBCA] text-sm mb-6">What should happen with this room after maintenance?</p>

                <form onSubmit={submit} className="space-y-4">
                  <div>
                    <label className="block text-sm font-semibold text-[#EAD3CD] mb-3">Next Status</label>
                    <div className="space-y-2">
                      <label className="flex items-center p-3 border border-[#4E3B46] rounded-lg cursor-pointer hover:border-[#A0717F] hover:bg-[#383537] transition">
                        <input type="radio" name="next_status" value="Cleaning" required className="w-4 h-4 text-[#A0717F]"
                          checked={nextStatus === 'Cleaning'} onChange={(e) => setNextStatus(e.target.value)} />
                        <span className="ml-3 text-[#EAD3CD]">
                          <strong>Send to Cleaning</strong>
                          <span className="text-xs text-[#CFCBCA] block">Room needs professional cleaning before guests</span>
                        </span>
                      </label>
                      <label className="flex items-center p-3 border border-[#4E3B46] rounded-lg cursor-pointer hover:border-[#A0717F] hover:bg-[#383537] transition">
                        <input type="radio" name="next_status" value="Available" required className="w-4 h-4 text-[#A0717F]"
                          checked={nextStatus === 'Available'} onChange={(e) => setNextStatus(e.target.value)} />
                        <span className="ml-3 text-[#EAD3CD]">
                          <strong>Send to Available</strong>
                          <span className="text-xs text-[#CFCBCA] block">Maintenance complete, room ready for guests</span>
                        </span>
                      </label>
                    </div>
                    {errors.next_status && <p className="text-red-500 text-sm mt-2">{errors.next_status[0]}</p>}
                  </div>

                  <div>
                    <label htmlFor="completion_notes" className="block text-sm font-semibold text-[#EAD3CD] mb-2">Completion Notes (Optional)</label>
                    <p className="text-xs text-[#CFCBCA] mb-2">Document what was done or next steps</p>
                    <textarea id="completion_notes" rows={3} value={notes} onChange={(e) => setNotes(e.target.value)}
                      placeholder="e.g., Maintenance completed. AC unit serviced and working. Ready for guests."
                      className="w-full px-4 py-2 bg-[#383537] border border-[#4E3B46] rounded-lg text-[#EAD3CD] focus:outline-none focus:border-[#A0717F]" />
                    {errors.completion_notes && <p className="text-red-500 text-sm mt-2">{errors.completion_notes[0]}</p>}
                  </div>

                  <div className="flex gap-4 pt-4">
                    <button type="submit" className="flex-1 bg-[#A0717F] hover:bg-[#8F6470] text-white font-semibold px-6 py-3 rounded-lg transition">
                      Update Room Status
                    </button>
                    <Link to="/owner/maintenance" className="flex-1 border border-[#4E3B46] hover:bg-[#2A2729] text-[#CFCBCA] font-semibold px-6 py-3 rounded-lg transition text-center">
                      Cancel
                    </Link>
                  </div>
                </form>
              </div>
            ) : (
              <div className="bg-[#1A1515] border border-green-500 rounded-lg p-6">
                <h3 className="text-lg font-semibold text-green-400 mb-3">Maintenance Completed</h3>
                <div className="space-y-2 text-[#CFCBCA]">
                  <p><strong className="text-[#EAD3CD]">Completed At:</strong> {fmtDate(request.completed_at, true)}</p>
                  <p><strong className="text-[#EAD3CD]">Room Status:</strong> {request.room.status}</p>
                  {request.completion_notes && <p><strong className="text-[#EAD3CD]">Notes:</strong> {request.completion_notes}</p>}
                </div>
              </div>
            )}
          </div>
        </div>

        <div className="col-span-1">
          <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-6 sticky top-4">
            <h3 className="font-semibold text-[#EAD3CD] mb-4">Room Status Summary</h3>
            <div className="space-y-3 text-sm">
              <div className="pb-3 border-b border-[#4E3B46]">
                <p className="text-[#CFCBCA] font-semibold">Current Status</p>
                <p className="text-[#EAD3CD] mt-1">
                  <span className={`inline-block px-2 py-1 rounded text-xs font-semibold ${request.room.status === 'Maintenance' ? 'bg-[#1A1515] text-orange-400 border border-orange-500' : ''}`}>
                    {request.room.status}
                  </span>
                </p>
              </div>
              <div className="pb-3 border-b border-[#4E3B46]">
                <p className="text-[#CFCBCA] font-semibold">Priority Level</p>
                <p className="text-[#EAD3CD] mt-1 capitalize">{request.priority}</p>
              </div>
              <div className="pb-3 border-b border-[#4E3B46]">
                <p className="text-[#CFCBCA] font-semibold">Request Age</p>
                <p className="text-[#EAD3CD] mt-1">{timeAgo(request.created_at)}</p>
              </div>
              <div>
                <p className="text-[#CFCBCA] font-semibold mb-2">Quick Actions</p>
                <Link to="/owner/maintenance" className="block bg-[#A0717F] hover:bg-[#8F6470] text-white px-3 py-2 rounded text-center transition">
                  View All Maintenance
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  )
}
