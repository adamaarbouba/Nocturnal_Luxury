import { useEffect, useState } from 'react'
import { Link, useNavigate, useParams } from 'react-router-dom'
import Layout from '../../components/Layout'
import Breadcrumbs from '../../components/Breadcrumbs'
import api from '../../lib/api'

const statusClasses = {
  pending: 'bg-yellow-900/50 text-yellow-300 border border-yellow-800',
  approved: 'bg-green-900/50 text-green-300 border border-green-800',
  rejected: 'bg-red-900/50 text-red-300 border border-red-800',
}
const ucfirst = (s) => (s ? s.charAt(0).toUpperCase() + s.slice(1) : '')

export default function HotelRequestShow() {
  const { id } = useParams()
  const navigate = useNavigate()
  const [request, setRequest] = useState(null)
  const [error, setError] = useState(null)

  const load = () =>
    api.get(`/admin/hotel-requests/${id}`).then((res) => setRequest(res.data.request)).catch(() => setRequest({ error: true }))
  useEffect(() => { load() }, [id]) // eslint-disable-line react-hooks/exhaustive-deps

  if (!request) return <Layout><div className="py-24 text-center text-[#CFCBCA]">Loading…</div></Layout>
  if (request.error) return <Layout><div className="py-24 text-center text-[#CFCBCA]">Failed to load request.</div></Layout>

  const approve = async () => {
    try {
      await api.post(`/admin/hotel-requests/${id}/approve`)
      navigate('/admin/hotel-requests')
    } catch (err) {
      setError(err.response?.data?.message ?? 'Approval failed.')
    }
  }

  const reject = async () => {
    const reason = window.prompt(`Reject ${request.name}? Enter rejection reason (required):`)
    if (reason === null) return
    if (!reason.trim()) return setError('Rejection reason is explicitly required.')
    try {
      await api.post(`/admin/hotel-requests/${id}/reject`, { admin_notes: reason.trim() })
      navigate('/admin/hotel-requests')
    } catch (err) {
      setError(err.response?.data?.message ?? err.response?.data?.errors?.admin_notes?.[0] ?? 'Rejection failed.')
    }
  }

  return (
    <Layout>
      <div className="max-w-7xl mx-auto px-4 pt-8">
        <Breadcrumbs links={[
          { label: 'Admin', url: '/admin' },
          { label: 'Hotel Requests', url: '/admin/hotel-requests' },
          { label: request.name, url: '#' },
        ]} />
      </div>

      {/* Page Header */}
      <div
        className="mb-8 border-b border-[#4E3B46] flex justify-between items-start"
        style={{ background: 'linear-gradient(180deg, rgba(42, 39, 41, 0.5) 0%, transparent 100%)' }}
      >
        <div className="py-6 px-4">
          <h1 className="text-3xl font-semibold text-[#EAD3CD]">Request: {request.name}</h1>
          <p className="text-sm mt-2 text-[#CFCBCA]">Submitted by {request.owner?.name}</p>
        </div>
        <div className="py-6 px-4">
          <Link to="/admin/hotel-requests" className="px-4 py-2 text-sm border border-[#4E3B46] text-[#CFCBCA] rounded-lg hover:bg-[#2A2729] transition">
            Back to Requests
          </Link>
        </div>
      </div>

      <div className="max-w-4xl mx-auto mb-8">
        <div className="border border-[#4E3B46] rounded-lg p-6 bg-[#383537]">
          <h3 className="text-sm font-semibold text-[#EAD3CD] uppercase tracking-wider mb-4">Request Details</h3>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="border-b border-[#4E3B46] pb-4">
              <p className="text-xs text-[#CFCBCA] font-medium">Status</p>
              <div className="mt-2 text-sm">
                <span className={`text-xs px-2.5 py-1 rounded font-medium ${statusClasses[request.status] ?? ''}`}>
                  {ucfirst(request.status)}
                </span>
              </div>
            </div>
            <div className="border-b border-[#4E3B46] pb-4">
              <p className="text-xs text-[#CFCBCA] font-medium">Owner Email</p>
              <p className="text-sm text-[#EAD3CD] mt-2">{request.owner?.email}</p>
            </div>
            <div className="border-b border-[#4E3B46] pb-4">
              <p className="text-xs text-[#CFCBCA] font-medium">Hotel Address</p>
              <p className="text-sm text-[#EAD3CD] mt-2">{request.address}</p>
            </div>
            <div className="border-b border-[#4E3B46] pb-4">
              <p className="text-xs text-[#CFCBCA] font-medium">Hotel Contact Email</p>
              <p className="text-sm text-[#EAD3CD] mt-2">{request.email}</p>
            </div>
          </div>

          {request.description && (
            <div className="border-t border-[#4E3B46] mt-6 pt-6">
              <p className="text-xs text-[#CFCBCA] font-medium">Description provided</p>
              <p className="text-sm text-[#EAD3CD] mt-2">{request.description}</p>
            </div>
          )}

          {request.admin_notes && (
            <div className="border-t border-[#4E3B46] mt-6 pt-6">
              <p className="text-xs text-[#CFCBCA] font-medium">Admin Notes</p>
              <p className="text-sm text-[#EAD3CD] mt-2">{request.admin_notes}</p>
            </div>
          )}

          {error && <p className="text-sm text-red-400 mt-4">{error}</p>}

          {request.status === 'pending' && (
            <div className="mt-8 flex gap-4">
              <button
                onClick={approve}
                className="px-6 py-2 rounded-lg font-medium text-white transition-colors bg-[#A0717F] hover:bg-[#8F6470]"
              >
                Approve
              </button>
              <button
                onClick={reject}
                className="px-6 py-2 rounded-lg font-medium border border-[#4E3B46] text-[#CFCBCA] hover:bg-[#2A2729] transition"
              >
                Reject
              </button>
            </div>
          )}
        </div>
      </div>
    </Layout>
  )
}
