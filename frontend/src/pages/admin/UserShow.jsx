import { useEffect, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import Layout from '../../components/Layout'
import Breadcrumbs from '../../components/Breadcrumbs'
import Icon from '../../components/Icon'
import Alert from '../../components/Alert'
import api from '../../lib/api'

const fmtDate = (s) => new Date(s).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })
const ucfirst = (s) => (s ? s.charAt(0).toUpperCase() + s.slice(1) : '')

export default function UserShow() {
  const { id } = useParams()
  const [data, setData] = useState(null)
  const [flash, setFlash] = useState(null)
  const [banModalOpen, setBanModalOpen] = useState(false)

  const load = () => api.get(`/admin/users/${id}`).then((res) => setData(res.data)).catch(() => setData({ error: true }))
  useEffect(() => { load() }, [id]) // eslint-disable-line react-hooks/exhaustive-deps

  useEffect(() => {
    const onKey = (e) => e.key === 'Escape' && setBanModalOpen(false)
    document.addEventListener('keydown', onKey)
    return () => document.removeEventListener('keydown', onKey)
  }, [])

  if (!data) return <Layout><div className="py-24 text-center text-[#CFCBCA]">Loading…</div></Layout>
  if (data.error) return <Layout><div className="py-24 text-center text-[#CFCBCA]">Failed to load user.</div></Layout>

  const { user, hotelInfo } = data
  const slug = user.role?.slug

  const act = async (action) => {
    try {
      const res = await api.post(`/admin/users/${id}/${action}`)
      setFlash({ variant: 'success', message: res.data.message })
      setBanModalOpen(false)
      load()
    } catch (err) {
      setFlash({ variant: 'error', message: err.response?.data?.message ?? 'Action failed.' })
      setBanModalOpen(false)
    }
  }

  return (
    <Layout>
      {/* Ambient Gradients */}
      <div className="fixed top-0 left-0 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none" style={{ background: 'rgba(160, 113, 127, 0.05)', zIndex: 0 }}></div>
      <div className="fixed bottom-0 right-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none" style={{ background: 'rgba(234, 211, 205, 0.03)', zIndex: 0 }}></div>

      <div className="max-w-6xl mx-auto px-6 py-12 relative z-10">
        <div className="mb-12">
          <Breadcrumbs links={[
            { label: 'Admin Dashboard', url: '/admin' },
            { label: 'User Directory', url: '/admin/users' },
            { label: 'User Dossier', url: '#' },
          ]} />
        </div>

        {flash && (
          <Alert variant={flash.variant} className="mb-8">{flash.message}</Alert>
        )}

        {/* Header */}
        <div className="mb-12 flex flex-col md:flex-row justify-between items-start md:items-end gap-6 border-b border-[rgba(234,211,205,0.05)] pb-6">
          <div>
            <p className="text-[10px] font-bold uppercase tracking-[0.3em] text-[#A0717F] mb-3">Identity Dossier</p>
            <div className="flex items-center gap-4">
              <h1 className="text-4xl lg:text-5xl font-bold font-serif text-[#EAD3CD] leading-tight">{user.name}</h1>
              {user.banned_at ? (
                <span className="px-3 py-1 bg-red-900/30 text-red-400 border border-red-900/50 rounded-full text-[9px] font-bold uppercase tracking-[0.2em]">Sanctioned</span>
              ) : (
                <span className="px-3 py-1 bg-green-900/20 text-green-400 border border-green-900/30 rounded-full text-[9px] font-bold uppercase tracking-[0.2em]">Active</span>
              )}
            </div>
            <p className="text-[10px] uppercase mt-4 text-[#CFCBCA]/60 tracking-[0.15em]">
              System Member Since {fmtDate(user.created_at)}
            </p>
          </div>

          <div className="flex gap-4">
            {user.banned_at ? (
              <button
                type="button"
                onClick={() => act('unban')}
                className="inline-flex items-center gap-2 px-8 py-4 rounded-xl text-[10px] font-bold uppercase tracking-[0.2em] transition-all duration-300 shadow-xl bg-[#4E3B46] text-[#EAD3CD] hover:bg-[#68525F] hover:-translate-y-0.5"
              >
                <Icon name="shield-check" size="sm" className="w-4 h-4 text-[#A0717F]" />
                Restore Access
              </button>
            ) : (
              <button
                type="button"
                onClick={() => setBanModalOpen(true)}
                className="inline-flex items-center gap-2 px-8 py-4 rounded-xl text-[10px] font-bold uppercase tracking-[0.2em] transition-all duration-300 shadow-xl bg-[#A0717F] text-white hover:bg-[#8F6470] hover:-translate-y-0.5"
              >
                <Icon name="shield-exclamation" size="sm" className="w-4 h-4" />
                Sanction User
              </button>
            )}
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
          {/* Left Column: Core Identity */}
          <div className="lg:col-span-8 space-y-8">
            {/* Metrics */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="bg-[#2A2729] border border-[#4E3B46] rounded-3xl p-8 relative overflow-hidden group hover:border-[#A0717F]/50 transition-colors duration-500">
                <div className="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-[#A0717F]/10 to-transparent rounded-bl-full pointer-events-none"></div>
                <p className="text-[9px] font-bold uppercase tracking-[0.3em] text-[#CFCBCA]/60 mb-2">Classification</p>
                <p className="text-3xl font-bold font-serif text-[#EAD3CD] group-hover:text-[#A0717F] transition-colors duration-500">{ucfirst(slug)}</p>
                <Icon name="star" className="absolute bottom-6 right-6 w-8 h-8 text-[#A0717F]/20 group-hover:scale-110 transition-transform duration-500" />
              </div>

              <div className="bg-[#2A2729] border border-[#4E3B46] rounded-3xl p-8 relative overflow-hidden group hover:border-[#A0717F]/50 transition-colors duration-500">
                <div className="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-[#A0717F]/10 to-transparent rounded-bl-full pointer-events-none"></div>
                <p className="text-[9px] font-bold uppercase tracking-[0.3em] text-[#CFCBCA]/60 mb-2">Record Identifier</p>
                <p className="text-3xl font-bold font-serif text-[#EAD3CD]">#{String(user.id).padStart(5, '0')}</p>
                <Icon name="identification" className="absolute bottom-6 right-6 w-8 h-8 text-[#A0717F]/20 group-hover:scale-110 transition-transform duration-500" />
              </div>
            </div>

            {/* Personal Information */}
            <div className="bg-[#2A2729] border border-[#4E3B46] rounded-3xl p-8 lg:p-12 relative overflow-hidden shadow-xl">
              <div className="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-[#A0717F]/5 to-transparent rounded-bl-full pointer-events-none"></div>

              <h2 className="text-xl font-bold font-serif text-[#EAD3CD] mb-8 flex items-center gap-4">
                <span className="w-8 h-[1px] bg-[#A0717F]"></span>
                Contact Registry
              </h2>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8 relative z-10">
                <div className="space-y-1">
                  <p className="text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F]">Electronic Mail</p>
                  <p className="text-sm font-medium text-[#EAD3CD] break-words">{user.email}</p>
                </div>

                <div className="space-y-1">
                  <p className="text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F]">Telephone Number</p>
                  <p className="text-sm font-medium text-[#EAD3CD]">{user.phone ?? 'Unregistered'}</p>
                </div>

                <div className="space-y-1 md:col-span-2">
                  <p className="text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F]">Physical Address</p>
                  <p className="text-sm font-medium text-[#EAD3CD]">{user.address ?? 'Unregistered'}</p>
                </div>
              </div>
            </div>
          </div>

          {/* Right Column: Associated Data */}
          <div className="lg:col-span-4 space-y-8">
            {slug === 'owner' && (
              <div className="bg-[#383537] border border-[#4E3B46] rounded-3xl p-8 shadow-xl">
                <h2 className="text-lg font-bold font-serif text-[#EAD3CD] mb-6 flex items-center gap-3">
                  <Icon name="building-office" className="w-5 h-5 text-[#A0717F]" />
                  Portfolio
                </h2>

                {hotelInfo && hotelInfo.length > 0 ? (
                  <div className="space-y-4">
                    {hotelInfo.map((hotel) => (
                      <div key={hotel.id} className="group bg-[#2A2729] border border-[#4E3B46] rounded-xl p-5 hover:border-[#A0717F]/50 transition-all duration-300 relative overflow-hidden">
                        <div className="absolute left-0 top-0 bottom-0 w-1 bg-[#A0717F] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div className="flex justify-between items-start mb-3">
                          <div>
                            <p className="font-bold text-[#EAD3CD] text-sm group-hover:text-[#A0717F] transition-colors">{hotel.name}</p>
                            <p className="text-[9px] text-[#CFCBCA] uppercase tracking-widest opacity-60 mt-1">{hotel.rooms?.length ?? 0} Suites</p>
                          </div>
                          <span className={`inline-block px-2 py-0.5 rounded text-[8px] font-bold uppercase tracking-widest border ${hotel.verified ? 'bg-green-500/10 text-green-400 border-green-500/30' : 'bg-yellow-500/10 text-yellow-400 border-yellow-500/30'}`}>
                            {hotel.verified ? 'Verified' : 'Pending'}
                          </span>
                        </div>
                        <Link to={`/admin/hotels/${hotel.id}`} className="inline-flex text-[9px] font-bold uppercase tracking-[0.2em] text-[#CFCBCA] hover:text-[#EAD3CD] transition-colors">
                          View Property →
                        </Link>
                      </div>
                    ))}
                  </div>
                ) : (
                  <div className="text-center py-8 px-4 border border-[#4E3B46]/30 rounded-2xl bg-[#2A2729]/50">
                    <p className="text-[10px] uppercase tracking-[0.1em] text-[#CFCBCA]/60">No properties registered to this portfolio.</p>
                  </div>
                )}
              </div>
            )}

            {(slug === 'receptionist' || slug === 'cleaner' || slug === 'inspector') && (
              <div className="bg-[#383537] border border-[#4E3B46] rounded-3xl p-8 shadow-xl">
                <h2 className="text-lg font-bold font-serif text-[#EAD3CD] mb-6 flex items-center gap-3">
                  <Icon name="briefcase" className="w-5 h-5 text-[#A0717F]" />
                  Assignment
                </h2>

                {hotelInfo ? (
                  /* ponytail: receptionist gets a single hotel object; cleaner/inspector get an array — render each like the blade does */
                  (Array.isArray(hotelInfo) ? hotelInfo : [hotelInfo]).map((hotel) => (
                    <div key={hotel.id} className="group bg-[#2A2729] border border-[#4E3B46] rounded-xl p-6 hover:border-[#A0717F]/50 transition-all duration-300 relative overflow-hidden mb-4 last:mb-0">
                      <div className="absolute left-0 top-0 bottom-0 w-1 bg-[#A0717F] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                      <p className="text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-1">Stationed At</p>
                      <p className="font-bold text-[#EAD3CD] text-lg font-serif mb-1 group-hover:text-[#A0717F] transition-colors">{hotel.name}</p>
                      <p className="text-[10px] text-[#CFCBCA] uppercase tracking-widest opacity-60 mb-4">{hotel.location ?? hotel.city}</p>

                      <Link to={`/admin/hotels/${hotel.id}`} className="inline-flex text-[9px] font-bold uppercase tracking-[0.2em] text-[#EAD3CD] bg-[#4E3B46] hover:bg-[#68525F] px-4 py-2 rounded-lg transition-colors w-full justify-center">
                        View Property
                      </Link>
                    </div>
                  ))
                ) : (
                  <div className="text-center py-8 px-4 border border-[#4E3B46]/30 rounded-2xl bg-[#2A2729]/50">
                    <p className="text-[10px] uppercase tracking-[0.1em] text-[#CFCBCA]/60">This staff member is currently unassigned.</p>
                  </div>
                )}
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Ban Confirmation Modal */}
      {banModalOpen && (
        <div
          className="fixed inset-0 bg-[#1A1515]/90 backdrop-blur-sm flex items-center justify-center z-50 transition-opacity"
          onClick={() => setBanModalOpen(false)}
        >
          <div
            className="rounded-3xl shadow-2xl p-10 max-w-md w-full mx-4 bg-[#2A2729] border border-[#4E3B46] transform transition-transform scale-100 relative overflow-hidden"
            onClick={(e) => e.stopPropagation()}
          >
            <div className="absolute top-0 right-0 p-8 opacity-5">
              <Icon name="shield-exclamation" className="w-24 h-24 text-[#A0717F]" />
            </div>

            <h2 className="text-2xl font-bold font-serif text-[#EAD3CD] mb-4 relative z-10">Sanction Member</h2>
            <p className="text-sm text-[#CFCBCA] mb-8 leading-relaxed relative z-10">
              Are you certain you wish to revoke access for <strong className="text-[#EAD3CD]">{user.name}</strong>? This action will immediately suspend their privileges within the Nocturnal Luxury network.
            </p>
            <div className="flex gap-4 relative z-10">
              <button
                type="button"
                onClick={() => setBanModalOpen(false)}
                className="flex-1 px-6 py-4 border border-[#4E3B46] text-[#CFCBCA] rounded-xl hover:bg-[#383537] hover:text-[#EAD3CD] transition-all duration-300 text-[10px] font-bold uppercase tracking-widest"
              >
                Cancel
              </button>
              <button
                type="button"
                onClick={() => act('ban')}
                className="flex-1 px-6 py-4 text-white rounded-xl transition-all duration-300 text-[10px] font-bold uppercase tracking-widest shadow-lg bg-[#A0717F] hover:bg-[#8F6470]"
              >
                Confirm Ban
              </button>
            </div>
          </div>
        </div>
      )}
    </Layout>
  )
}
