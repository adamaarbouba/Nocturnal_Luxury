import { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import Layout from '../../components/Layout'
import Breadcrumbs from '../../components/Breadcrumbs'
import Icon from '../../components/Icon'
import Alert from '../../components/Alert'
import api from '../../lib/api'
import { useAuth } from '../../context/AuthContext'

// Ports resources/views/inspector/dashboard.blade.php
export default function InspectorDashboard() {
  const navigate = useNavigate()
  const { user } = useAuth()
  const [rooms, setRooms] = useState([])
  const [staffRoles, setStaffRoles] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    api.get('/inspector/dashboard').then(res => {
      const { staffRoles, roomsNeedingInspection } = res.data
      if (!staffRoles.length) { navigate('/staff/hotels'); return }
      setStaffRoles(staffRoles)
      setRooms(roomsNeedingInspection)
    }).finally(() => setLoading(false))
  }, [navigate])

  if (loading) return <Layout><p className="text-[#CFCBCA] text-center py-12">Loading…</p></Layout>
  if (!staffRoles.length) return null

  return (
    <Layout>
      <div className="fixed top-0 right-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none"
        style={{ background: 'rgba(160, 113, 127, 0.05)', zIndex: 0 }}></div>
      <div className="fixed bottom-0 left-0 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none"
        style={{ background: 'rgba(234, 211, 205, 0.03)', zIndex: 0 }}></div>

      <div className="relative z-10">
        <Breadcrumbs links={[{ label: 'Inspector Dashboard', url: '/inspector' }]} />

        <div className="mb-10">
          <p className="text-xs font-medium uppercase mb-2" style={{ color: '#A0717F', letterSpacing: '0.4em' }}>
            Quality Control
          </p>
          <h1 className="text-3xl lg:text-5xl font-bold" style={{ color: '#EAD3CD', fontFamily: 'Georgia, serif' }}>
            Inspection Hub
          </h1>
          <p className="mt-4 text-[#CFCBCA] opacity-70">Review and adjudicate rooms processed by the cleaning staff</p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
          <div className="group bg-[#383537] border border-[#4E3B46] rounded-2xl shadow-xl p-8 relative overflow-hidden transition-all hover:border-[#A0717F]/50">
            <div className="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
              <Icon name="clipboard-check" className="w-16 h-16 text-[#A0717F]" />
            </div>
            <div className="relative z-10">
              <div className="text-4xl font-bold font-serif text-[#A0717F] mb-1 leading-none">{rooms.length}</div>
              <p className="text-[10px] uppercase font-bold tracking-widest text-[#CFCBCA] opacity-60">Pending Inspections</p>
            </div>
          </div>

          <div className="group bg-[#383537] border border-[#4E3B46] rounded-2xl shadow-xl p-8 relative overflow-hidden transition-all hover:border-[#EAD3CD]/30">
            <div className="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
              <Icon name="checkmark" className="w-16 h-16 text-[#EAD3CD]" />
            </div>
            <div className="relative z-10">
              <div className="text-4xl font-bold font-serif text-[#EAD3CD] mb-1 leading-none">
                {rooms.length > 0 ? 'Action Required' : 'Cleared'}
              </div>
              <p className="text-[10px] uppercase font-bold tracking-widest text-[#CFCBCA] opacity-60">Current Priority</p>
            </div>
          </div>

          <div className="group bg-[#2A2729] border border-[#4E3B46] rounded-2xl shadow-xl p-8 relative overflow-hidden transition-all">
            <div className="absolute -bottom-2 -right-2 p-4 opacity-5">
              <Icon name="user" className="w-20 h-20 text-[#EAD3CD]" />
            </div>
            <div className="relative z-10">
              <div className="text-xl font-bold font-serif text-[#EAD3CD] mb-1 truncate">{user?.name}</div>
              <p className="text-[10px] uppercase font-bold tracking-widest text-[#A0717F]">Active Inspector</p>
            </div>
          </div>
        </div>

        <div className="bg-[#383537] border border-[#4E3B46] rounded-2xl shadow-2xl overflow-hidden mb-12">
          <div className="bg-[#2A2729] border-b border-[#4E3B46] px-8 py-5 flex justify-between items-center">
            <h3 className="text-sm font-bold text-[#EAD3CD] uppercase tracking-widest">Inspection Queue</h3>
            {rooms.length > 0 && (
              <span className="bg-[#A0717F]/20 text-[#A0717F] text-[10px] font-bold px-3 py-1 rounded-full border border-[#A0717F]/30 uppercase tracking-tighter">
                {rooms.length} Pending
              </span>
            )}
          </div>

          {rooms.length > 0 ? (
            <div className="overflow-x-auto">
              <table className="w-full text-left">
                <thead className="bg-[#2A2729]/50 border-b border-[#4E3B46]">
                  <tr>
                    <th className="px-8 py-4 text-[10px] font-bold uppercase tracking-widest text-[#A0717F]">Property</th>
                    <th className="px-8 py-4 text-[10px] font-bold uppercase tracking-widest text-[#A0717F]">Dossier</th>
                    <th className="px-8 py-4 text-[10px] font-bold uppercase tracking-widest text-[#A0717F]">Classification</th>
                    <th className="px-8 py-4 text-[10px] font-bold uppercase tracking-widest text-[#A0717F]">Artisan</th>
                    <th className="px-8 py-4 text-[10px] font-bold uppercase tracking-widest text-[#A0717F]">Status</th>
                    <th className="px-8 py-4 text-center text-[10px] font-bold uppercase tracking-widest text-[#A0717F]">Action</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-[#4E3B46]">
                  {rooms.map(room => {
                    const lastCleaning = room.cleaning_logs?.[0]
                    const cleanerName = lastCleaning?.user?.name
                    return (
                      <tr key={room.id} className="hover:bg-[#2A2729]/40 transition-colors group">
                        <td className="px-8 py-5">
                          <p className="text-sm font-semibold text-[#EAD3CD]">{room.hotel.name}</p>
                        </td>
                        <td className="px-8 py-5">
                          <p className="text-sm font-bold text-[#EAD3CD]">Room #{room.room_number}</p>
                        </td>
                        <td className="px-8 py-5">
                          <p className="text-xs text-[#CFCBCA] opacity-70">{room.room_type}</p>
                        </td>
                        <td className="px-8 py-5">
                          <div className="flex items-center gap-2">
                            <div className="w-6 h-6 rounded-full bg-[#4E3B46] flex items-center justify-center text-[10px] text-[#A0717F]">
                              {(cleanerName || 'U').substring(0, 1)}
                            </div>
                            <p className="text-xs text-[#EAD3CD]">{cleanerName || 'Unassigned'}</p>
                          </div>
                        </td>
                        <td className="px-8 py-5">
                          <span className="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 uppercase tracking-widest">
                            Pending Review
                          </span>
                        </td>
                        <td className="px-8 py-5 text-center">
                          <Link to={`/inspector/rooms/${room.id}/inspect`}
                            className="inline-block bg-[#4E3B46]/50 hover:bg-[#A0717F] text-[#EAD3CD] text-[10px] font-bold uppercase tracking-widest px-4 py-2 rounded-lg transition-all border border-[#4E3B46] hover:border-[#A0717F]">
                            Begin Audit
                          </Link>
                        </td>
                      </tr>
                    )
                  })}
                </tbody>
              </table>
            </div>
          ) : (
            <div className="px-8 py-20 text-center">
              <div className="w-20 h-20 bg-[#2A2729] rounded-full flex items-center justify-center mx-auto mb-6 border border-[#4E3B46]">
                <Icon name="sparkles" className="w-10 h-10 text-[#A0717F] opacity-50" />
              </div>
              <h4 className="text-xl font-serif text-[#EAD3CD] mb-2 font-bold">All Clear</h4>
              <p className="text-xs text-[#CFCBCA] opacity-50 max-w-xs mx-auto">There are currently no rooms awaiting inspection. Everything is in order throughout the properties.</p>
            </div>
          )}
        </div>
      </div>
    </Layout>
  )
}
