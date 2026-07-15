import { useEffect, useState } from 'react'
import Layout from '../../components/Layout'
import Pagination from './Pagination'
import api from '../../lib/api'

const fmtDate = (s) => new Date(s).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })
const card = 'bg-[#383537] rounded-2xl p-8 border border-transparent shadow-sm hover:shadow-lg hover:border-[#4E3B46] transition'
const textarea = 'w-full px-3 py-2 rounded border border-[#4E3B46] bg-[#2A2729] text-[#EAD3CD] placeholder-[#8B8782] focus:outline-none focus:border-[#A0717F]'

export default function HotelRequestsIndex() {
  const [data, setData] = useState(null)
  const [tab, setTab] = useState('pending')
  const [pages, setPages] = useState({ pending_page: 1, approved_page: 1, rejected_page: 1 })
  const [flash, setFlash] = useState(null)
  const [modal, setModal] = useState(null) // { type: 'approve'|'reject', id, name }
  const [notes, setNotes] = useState('')
  const [rejectError, setRejectError] = useState('')

  const load = (p = pages) =>
    api.get('/admin/hotel-requests', { params: p }).then((res) => setData(res.data)).catch(() => setData({ error: true }))

  useEffect(() => { load() }, []) // eslint-disable-line react-hooks/exhaustive-deps

  const onPage = (key, p) => {
    const next = { ...pages, [key]: p }
    setPages(next)
    load(next)
  }

  const openModal = (type, request) => {
    setModal({ type, id: request.id, name: request.name })
    setNotes('')
    setRejectError('')
  }

  const submitApprove = async () => {
    try {
      const res = await api.post(`/admin/hotel-requests/${modal.id}/approve`, { admin_notes: notes || null })
      setFlash({ type: 'success', message: res.data.message })
    } catch (err) {
      setFlash({ type: 'error', message: err.response?.data?.message ?? 'Approval failed.' })
    }
    setModal(null)
    load()
  }

  const submitReject = async () => {
    const trimmed = notes.trim()
    if (!trimmed) return setRejectError('Rejection reason is required.')
    if (trimmed.length < 10) return setRejectError('Rejection reason must be at least 10 characters.')
    try {
      const res = await api.post(`/admin/hotel-requests/${modal.id}/reject`, { admin_notes: trimmed })
      setFlash({ type: 'success', message: res.data.message })
    } catch (err) {
      setFlash({ type: 'error', message: err.response?.data?.message ?? err.response?.data?.errors?.admin_notes?.[0] ?? 'Rejection failed.' })
    }
    setModal(null)
    load()
  }

  useEffect(() => {
    const onKey = (e) => {
      if (e.key === 'Escape') setModal(null)
      if (e.key === 'Enter' && modal) modal.type === 'approve' ? submitApprove() : submitReject()
    }
    document.addEventListener('keydown', onKey)
    return () => document.removeEventListener('keydown', onKey)
  })

  if (!data) return <Layout><div className="py-24 text-center text-[#CFCBCA]">Loading…</div></Layout>
  if (data.error) return <Layout><div className="py-24 text-center text-[#CFCBCA]">Failed to load hotel requests.</div></Layout>

  const { pendingRequests, approvedRequests, rejectedRequests } = data
  const tabs = [
    ['pending', 'Pending', pendingRequests.total],
    ['approved', 'Approved', approvedRequests.total],
    ['rejected', 'Rejected', rejectedRequests.total],
  ]

  return (
    <Layout>
      <div style={{ backgroundColor: 'transparent', minHeight: '100vh' }}>
        {/* Page Header */}
        <div className="max-w-7xl mx-auto px-4 pt-8 pb-8">
          <h1 className="text-3xl font-semibold text-[#EAD3CD]">Hotel Requests</h1>
          <p className="text-sm mt-2 text-[#CFCBCA]">Review new hotel submissions</p>
        </div>

        <div className="max-w-7xl mx-auto px-4 pb-12">
          {flash?.type === 'success' && (
            <div className="mb-6 px-4 py-3 border rounded-lg text-sm" style={{ backgroundColor: 'rgba(160, 113, 127, 0.1)', borderColor: '#4E3B46', color: '#A0717F' }}>
              {flash.message}
            </div>
          )}
          {flash?.type === 'error' && (
            <div className="mb-6 px-4 py-3 border rounded-lg text-sm" style={{ backgroundColor: 'rgba(255, 142, 139, 0.1)', borderColor: '#4E3B46', color: '#ff8e8b' }}>
              {flash.message}
            </div>
          )}

          {/* Tabs Navigation */}
          <div className="mb-6 flex gap-4 border-b border-[#4E3B46]">
            {tabs.map(([key, label, count]) => (
              <button
                key={key}
                onClick={() => setTab(key)}
                className={`px-4 py-2 font-medium border-b-2 -mb-1 text-sm ${tab === key ? 'text-[#EAD3CD] border-[#A0717F]' : 'text-[#CFCBCA] hover:text-[#EAD3CD] border-transparent'}`}
              >
                {label} <span className="text-xs">({count})</span>
              </button>
            ))}
          </div>

          {/* Pending Tab */}
          {tab === 'pending' && (
            pendingRequests.data.length > 0 ? (
              <>
                <div className="space-y-4">
                  {pendingRequests.data.map((request) => (
                    <div key={request.id} className={card}>
                      <div className="grid md:grid-cols-3 gap-6">
                        <div className="md:col-span-2">
                          <h3 className="font-semibold text-[#EAD3CD] text-lg">{request.name}</h3>
                          <p className="text-xs text-[#CFCBCA] mt-1">From <span className="font-medium text-[#EAD3CD]">{request.owner?.name}</span></p>

                          <div className="grid grid-cols-2 gap-4 mt-4">
                            <div className="border-t border-[#4E3B46] pt-3">
                              <p className="text-xs text-[#CFCBCA] font-medium">Location</p>
                              <p className="text-sm text-[#EAD3CD] mt-1">{request.address}</p>
                            </div>
                            <div className="border-t border-[#4E3B46] pt-3">
                              <p className="text-xs text-[#CFCBCA] font-medium">Contact</p>
                              <p className="text-sm text-[#EAD3CD] mt-1">{request.email}</p>
                            </div>
                          </div>

                          {request.description && (
                            <div className="border-t border-[#4E3B46] pt-3 mt-4">
                              <p className="text-xs text-[#CFCBCA] font-medium">Description</p>
                              <p className="text-sm text-[#EAD3CD] mt-1">{request.description}</p>
                            </div>
                          )}
                        </div>

                        <div className="flex flex-col gap-2">
                          <button
                            onClick={() => openModal('approve', request)}
                            className="px-4 py-2 rounded-lg font-medium text-white text-sm transition-colors bg-[#A0717F] hover:bg-[#8F6470]"
                          >
                            Approve
                          </button>
                          <button
                            onClick={() => openModal('reject', request)}
                            className="px-4 py-2 rounded-lg font-medium text-[#CFCBCA] border border-[#4E3B46] text-sm hover:bg-[#2A2729] transition"
                          >
                            Reject
                          </button>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
                <div className="mt-6"><Pagination paginator={pendingRequests} onPage={(p) => onPage('pending_page', p)} /></div>
              </>
            ) : (
              <div className="bg-[#383537] rounded-2xl p-12 text-center border border-transparent shadow-sm">
                <p className="text-[#EAD3CD] font-semibold">No pending requests</p>
                <p className="text-xs text-[#CFCBCA] mt-1">All caught up!</p>
              </div>
            )
          )}

          {/* Approved Tab */}
          {tab === 'approved' && (
            approvedRequests.data.length > 0 ? (
              <>
                <div className="space-y-4">
                  {approvedRequests.data.map((request) => (
                    <div key={request.id} className={card}>
                      <div className="flex justify-between items-start">
                        <div className="flex-1">
                          <h3 className="font-semibold text-[#EAD3CD]">{request.name}</h3>
                          <p className="text-xs mt-1" style={{ color: '#A0717F' }}>
                            Approved by <span className="text-[#EAD3CD]">{request.reviewer?.name}</span> on {fmtDate(request.reviewed_at)}
                          </p>
                          {request.admin_notes && (
                            <div className="border-t mt-3 pt-3" style={{ borderColor: '#4E3B46' }}>
                              <p className="text-xs text-[#CFCBCA] font-medium">Notes</p>
                              <p className="text-sm text-[#EAD3CD] mt-1">{request.admin_notes}</p>
                            </div>
                          )}
                        </div>
                        <span className="text-xs px-2.5 py-1 rounded font-medium" style={{ backgroundColor: '#2A2729', color: '#A0717F' }}>Approved</span>
                      </div>
                    </div>
                  ))}
                </div>
                <div className="mt-6"><Pagination paginator={approvedRequests} onPage={(p) => onPage('approved_page', p)} /></div>
              </>
            ) : (
              <div className="bg-[#383537] rounded-2xl p-12 text-center border border-transparent shadow-sm">
                <p className="text-[#CFCBCA] font-semibold">No approved requests</p>
              </div>
            )
          )}

          {/* Rejected Tab */}
          {tab === 'rejected' && (
            rejectedRequests.data.length > 0 ? (
              <>
                <div className="space-y-4">
                  {rejectedRequests.data.map((request) => (
                    <div key={request.id} className={card}>
                      <div className="flex justify-between items-start">
                        <div className="flex-1">
                          <h3 className="font-semibold text-[#EAD3CD]">{request.name}</h3>
                          <p className="text-xs mt-1" style={{ color: '#ff8e8b' }}>
                            Rejected by <span className="text-[#EAD3CD]">{request.reviewer?.name}</span> on {fmtDate(request.reviewed_at)}
                          </p>
                          {request.admin_notes && (
                            <div className="border-t mt-3 pt-3" style={{ borderColor: '#4E3B46' }}>
                              <p className="text-xs text-[#CFCBCA] font-medium">Reason</p>
                              <p className="text-sm text-[#EAD3CD] mt-1">{request.admin_notes}</p>
                            </div>
                          )}
                        </div>
                        <span className="text-xs px-2.5 py-1 rounded font-medium" style={{ backgroundColor: 'rgba(255, 142, 139, 0.1)', color: '#ff8e8b' }}>Rejected</span>
                      </div>
                    </div>
                  ))}
                </div>
                <div className="mt-6"><Pagination paginator={rejectedRequests} onPage={(p) => onPage('rejected_page', p)} /></div>
              </>
            ) : (
              <div className="bg-[#383537] rounded-2xl p-12 text-center border border-transparent shadow-sm">
                <p className="text-[#CFCBCA] font-semibold">No rejected requests</p>
              </div>
            )
          )}
        </div>
      </div>

      {/* Approve / Reject Modal */}
      {modal && (
        <div
          className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
          onClick={(e) => e.target === e.currentTarget && setModal(null)}
        >
          <div className="bg-[#383537] rounded-lg p-6 max-w-md w-full mx-4 border border-[#4E3B46]">
            <h3 className="text-lg font-semibold text-[#EAD3CD] mb-4">
              {modal.type === 'approve' ? `Approve "${modal.name}"?` : `Reject "${modal.name}"?`}
            </h3>
            <div className="mb-6">
              <label className="block text-sm text-[#CFCBCA] font-medium mb-2">
                {modal.type === 'approve' ? 'Notes (Optional)' : 'Reason (Required - min 10 characters)'}
              </label>
              <textarea
                className={textarea}
                rows="3"
                placeholder={modal.type === 'approve' ? 'Add any approval notes...' : 'Enter rejection reason...'}
                value={notes}
                onChange={(e) => { setNotes(e.target.value); setRejectError('') }}
                autoFocus
              ></textarea>
              {modal.type === 'reject' && rejectError && <div className="text-sm text-red-400 mt-2">{rejectError}</div>}
            </div>
            <div className="flex gap-3">
              <button
                type="button" onClick={() => setModal(null)}
                className="flex-1 px-4 py-2 rounded-lg border border-[#4E3B46] text-[#CFCBCA] hover:bg-[#2A2729] transition"
              >
                Cancel
              </button>
              {modal.type === 'approve' ? (
                <button type="button" onClick={submitApprove} className="flex-1 px-4 py-2 rounded-lg text-white transition-colors bg-[#A0717F] hover:bg-[#8F6470]">
                  Approve
                </button>
              ) : (
                <button
                  type="button" onClick={submitReject}
                  className="flex-1 px-4 py-2 rounded-lg text-white transition-colors bg-[#ff8e8b] hover:bg-[#ff7a76] border border-[#ff8e8b]"
                >
                  Reject
                </button>
              )}
            </div>
          </div>
        </div>
      )}
    </Layout>
  )
}
