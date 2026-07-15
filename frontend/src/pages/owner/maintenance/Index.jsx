import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import Layout from '../../../components/Layout'
import api from '../../../lib/api'
import { fmtDate, ucfirst } from '../format'

const priorityCls = (p) => (p === 'urgent' ? 'text-red-400 border-red-500' : p === 'normal' ? 'text-yellow-400 border-yellow-500' : 'text-green-400 border-green-500')
const statusCls = (s) => (s === 'pending' ? 'text-orange-400 border-orange-500' : s === 'in-progress' ? 'text-blue-400 border-blue-500' : 'text-green-400 border-green-500')

// Ports resources/views/owner/maintenance/index.blade.php
export default function MaintenanceIndex() {
  const [data, setData] = useState(null)

  useEffect(() => {
    api.get('/owner/maintenance').then((res) => setData(res.data)).catch(() => {})
  }, [])

  if (!data) return <Layout><p className="text-[#CFCBCA]">Loading…</p></Layout>

  const { maintenanceRequests } = data
  const countByStatus = (s) => maintenanceRequests.filter((r) => r.status === s).length

  return (
    <Layout>
      <Link to="/owner/dashboard" className="text-[#CFCBCA] hover:text-[#EAD3CD] mb-6 inline-block">← Back to Dashboard</Link>

      <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg overflow-hidden">
        <div className="bg-[#2A2729] border-b border-[#4E3B46] px-8 py-6 text-white">
          <h2 className="text-3xl font-bold text-[#EAD3CD]">Maintenance Management</h2>
          <p className="mt-2 text-[#CFCBCA]">Review and manage maintenance requests from inspectors</p>
        </div>

        <div className="p-8">
          {maintenanceRequests.length === 0 ? (
            <div className="bg-[#2A2729] border border-[#4E3B46] rounded-lg p-6 text-center">
              <p className="text-[#EAD3CD] text-lg">No maintenance requests at this time</p>
              <p className="text-[#CFCBCA] text-sm mt-1">All your rooms are in good condition</p>
            </div>
          ) : (
            <div className="space-y-4">
              {maintenanceRequests.map((request) => (
                <div key={request.id} className="bg-[#2A2729] border border-[#4E3B46] rounded-lg p-4 hover:bg-[#383537] transition">
                  <div className="flex justify-between items-start mb-3">
                    <div>
                      <h3 className="text-lg font-bold text-[#EAD3CD]">
                        Room {request.room.room_number}
                        <span className="text-sm font-normal text-[#CFCBCA]"> - {request.hotel.name}</span>
                      </h3>
                      <p className="text-sm text-[#CFCBCA] mt-1"><strong className="text-[#EAD3CD]">Inspector:</strong> {request.inspector.name}</p>
                      <p className="text-xs text-[#4E3B46] mt-1">Requested: {fmtDate(request.created_at, true)}</p>
                    </div>
                    <div className="text-right">
                      <span className={`inline-block px-3 py-1 rounded-full text-sm font-semibold border bg-[#1A1515] ${priorityCls(request.priority)}`}>
                        {ucfirst(request.priority)} Priority
                      </span>
                      <span className={`block mt-2 px-3 py-1 rounded text-xs font-semibold border bg-[#1A1515] ${statusCls(request.status)}`}>
                        {ucfirst(request.status.replace('-', ' '))}
                      </span>
                    </div>
                  </div>

                  <div className="bg-[#1A1515] rounded p-3 my-3">
                    <p className="text-sm text-[#CFCBCA]"><strong className="text-[#EAD3CD]">Issue Description:</strong></p>
                    <p className="text-sm text-[#CFCBCA] mt-1">{request.issue_description || 'No description provided'}</p>
                  </div>

                  <div className="grid grid-cols-2 gap-2 text-xs text-[#CFCBCA] mb-3">
                    <div><span className="font-semibold text-[#EAD3CD]">Room Type:</span> {request.room.room_type}</div>
                    <div><span className="font-semibold text-[#EAD3CD]">Capacity:</span> {request.room.capacity} guests</div>
                  </div>

                  {request.status === 'completed' && request.completion_notes && (
                    <div className="bg-[#1A1515] border border-green-500 rounded p-3 mb-3">
                      <p className="text-xs text-green-400"><strong>Completion Notes:</strong></p>
                      <p className="text-sm text-green-300 mt-1">{request.completion_notes}</p>
                    </div>
                  )}

                  {request.status !== 'completed' ? (
                    <div className="flex gap-2">
                      <Link to={`/owner/maintenance/${request.id}`} className="flex-1 bg-[#A0717F] hover:bg-[#8F6470] text-white px-4 py-2 rounded font-semibold transition text-center">
                        Manage Room
                      </Link>
                    </div>
                  ) : (
                    <div className="bg-[#1A1515] border border-green-500 rounded p-3 text-center">
                      <p className="text-sm text-green-400 font-semibold">Completed on {fmtDate(request.completed_at, true)}</p>
                    </div>
                  )}
                </div>
              ))}
            </div>
          )}

          <div className="grid grid-cols-3 gap-4 mt-8 pt-8 border-t border-[#4E3B46]">
            <div className="bg-[#2A2729] border border-[#4E3B46] rounded-lg p-4 text-center">
              <div className="text-3xl font-bold text-[#EAD3CD]">{countByStatus('pending')}</div>
              <p className="text-sm text-[#CFCBCA] mt-1">Pending</p>
            </div>
            <div className="bg-[#2A2729] border border-[#4E3B46] rounded-lg p-4 text-center">
              <div className="text-3xl font-bold text-[#EAD3CD]">{countByStatus('in-progress')}</div>
              <p className="text-sm text-[#CFCBCA] mt-1">In Progress</p>
            </div>
            <div className="bg-[#2A2729] border border-[#4E3B46] rounded-lg p-4 text-center">
              <div className="text-3xl font-bold text-[#EAD3CD]">{countByStatus('completed')}</div>
              <p className="text-sm text-[#CFCBCA] mt-1">Completed</p>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  )
}
