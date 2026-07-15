import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import Layout from '../../../components/Layout'
import api from '../../../lib/api'
import { fmtDate } from '../format'

// Ports resources/views/owner/hotel-requests/my-requests.blade.php
export default function MyRequests() {
  const [requests, setRequests] = useState(null)

  useEffect(() => {
    api.get('/owner/hotel-requests').then((res) => setRequests(res.data)).catch(() => {})
  }, [])

  if (!requests) return <Layout><p className="text-[#CFCBCA]">Loading…</p></Layout>

  const items = requests.data ?? []

  const statusBorder = (status) => (status === 'pending' ? 'border-[#A0717F]' : 'border-[#4E3B46]')
  const statusBadge = (status) =>
    status === 'pending'
      ? 'bg-[#A0717F] text-white'
      : 'border border-[#4E3B46] text-[#CFCBCA] bg-[#2A2729]'
  const statusLabel = (status) => (status === 'pending' ? 'Pending Review' : status === 'approved' ? 'Approved' : 'Rejected')

  return (
    <Layout>
      <div className="mb-6 flex justify-between items-center">
        <h2 className="text-3xl font-bold text-[#EAD3CD]">My Hotel Requests</h2>
        <Link to="/owner/hotel-requests/create" className="bg-[#A0717F] hover:bg-[#8F6470] text-white font-semibold px-6 py-2 rounded transition">
          Request New Hotel
        </Link>
      </div>

      {items.length > 0 ? (
        <div className="space-y-4">
          {items.map((request) => (
            <div key={request.id} className={`rounded-lg shadow-md p-6 bg-[#383537] border-l-4 ${statusBorder(request.status)}`}>
              <div className="flex justify-between items-start">
                <div className="flex-1">
                  <div className="flex items-center gap-3 mb-2">
                    <h3 className="text-2xl font-bold text-[#EAD3CD]">{request.name}</h3>
                    <span className={`inline-block px-3 py-1 rounded-full font-semibold text-sm ${statusBadge(request.status)}`}>
                      {statusLabel(request.status)}
                    </span>
                  </div>

                  <p className="text-[#CFCBCA] text-sm mb-3">Submitted: <strong>{fmtDate(request.created_at, true)}</strong></p>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm my-4">
                    <div>
                      <span className="text-[#CFCBCA]">Location:</span>
                      <p className="text-[#EAD3CD]"><strong>{request.address}, {request.city}, {request.country}</strong></p>
                    </div>
                    <div>
                      <span className="text-[#CFCBCA]">Email:</span>
                      <p className="text-[#EAD3CD]"><strong>{request.email}</strong></p>
                    </div>
                    <div>
                      <span className="text-[#CFCBCA]">Phone:</span>
                      <p className="text-[#EAD3CD]"><strong>{request.phone}</strong></p>
                    </div>
                  </div>

                  {request.description && (
                    <div className="my-4 pt-4 border-t border-[#4E3B46]">
                      <span className="text-[#CFCBCA] text-sm leading-relaxed">Description:</span>
                      <p className="text-[#EAD3CD] text-sm mt-1">{request.description}</p>
                    </div>
                  )}

                  {request.status === 'approved' ? (
                    <div className="mt-4 p-4 bg-green-900/50 border border-green-800 rounded">
                      <p className="text-green-300 text-sm"><strong>Approved by {request.reviewer?.name}</strong> on {fmtDate(request.reviewed_at)}</p>
                      {request.admin_notes && <p className="text-green-300 text-sm mt-2"><strong>Admin Notes:</strong> {request.admin_notes}</p>}
                      <p className="text-green-300 text-sm mt-3 font-semibold">Your hotel has been created! You can now manage it from your dashboard.</p>
                    </div>
                  ) : request.status === 'rejected' ? (
                    <div className="mt-4 p-4 bg-red-900/50 border border-red-800 rounded">
                      <p className="text-red-300 text-sm"><strong>Rejected by {request.reviewer?.name}</strong> on {fmtDate(request.reviewed_at)}</p>
                      {request.admin_notes && <p className="text-red-300 text-sm mt-2"><strong>Reason:</strong> {request.admin_notes}</p>}
                      <p className="text-red-300 text-sm mt-3">You can submit a new request after addressing the concerns mentioned above.</p>
                    </div>
                  ) : (
                    <div className="mt-4 p-4 bg-[#2A2729] border border-[#4E3B46] rounded">
                      <p className="text-[#CFCBCA] text-sm">Your request is under review. An admin will review it shortly.</p>
                    </div>
                  )}
                </div>
              </div>
            </div>
          ))}
        </div>
      ) : (
        <div className="bg-[#383537] border border-[#4E3B46] p-8 rounded-lg text-center">
          <p className="text-[#CFCBCA] mb-4">You haven't submitted any hotel requests yet.</p>
          <Link to="/owner/hotel-requests/create" className="inline-block bg-[#A0717F] hover:bg-[#8F6470] text-white font-semibold px-6 py-3 rounded transition">
            Submit Your First Hotel Request
          </Link>
        </div>
      )}
    </Layout>
  )
}
