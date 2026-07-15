import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import Layout from '../../../components/Layout'
import Breadcrumbs from '../../../components/Breadcrumbs'
import Icon from '../../../components/Icon'
import Alert from '../../../components/Alert'
import api from '../../../lib/api'

// Ports resources/views/staff/hotels/index.blade.php
export default function StaffHotelsIndex() {
  const [data, setData] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    api.get('/staff/hotels').then(res => setData(res.data)).finally(() => setLoading(false))
  }, [])

  if (loading || !data) return <Layout><p className="text-[#CFCBCA] text-center py-12">Loading…</p></Layout>

  const { hotels, myApplications, workingAtHotels, role, isReceptionistBlocked } = data

  return (
    <Layout>
      <div className="fixed top-0 left-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none"
        style={{ background: 'rgba(160, 113, 127, 0.05)', zIndex: 0 }}></div>

      <div className="container mx-auto px-4 py-8 relative z-10">
        <Breadcrumbs links={[
          { label: 'Staff Dashboard', url: '/staff/dashboard' },
          { label: 'Browse Jobs', url: '#' },
        ]} />

        <div className="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-b border-[rgba(234,211,205,0.05)] pb-6">
          <div>
            <h2 className="text-3xl lg:text-5xl font-bold font-serif text-[#EAD3CD]">Available Positions</h2>
            <p className="text-xs font-medium uppercase mt-3" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>
              Explore hotels hiring <strong className="text-[#A0717F]">{role}s</strong> across our network
            </p>
          </div>
          <Link to="/staff/my-applications"
            className="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all duration-300 shadow-md shrink-0"
            style={{ backgroundColor: '#383537', color: '#EAD3CD', border: '1px solid rgba(234, 211, 205, 0.1)' }}
            onMouseOver={e => { e.currentTarget.style.backgroundColor = 'rgba(160, 113, 127, 0.1)'; e.currentTarget.style.borderColor = 'rgba(160, 113, 127, 0.3)' }}
            onMouseOut={e => { e.currentTarget.style.backgroundColor = '#383537'; e.currentTarget.style.borderColor = 'rgba(234, 211, 205, 0.1)' }}>
            <Icon name="file" className="w-4 h-4 text-[#A0717F]" />
            Track Applications
          </Link>
        </div>

        {isReceptionistBlocked && (
          <div className="mb-8 p-5 rounded-xl border-l-[4px]" style={{ backgroundColor: 'rgba(234, 179, 8, 0.1)', borderColor: 'rgba(234, 179, 8, 0.5)' }}>
            <div className="flex items-start gap-3">
              <Icon name="alert" className="w-5 h-5 text-yellow-500 mt-0.5 shrink-0" />
              <div>
                <h4 className="text-sm font-bold uppercase tracking-widest text-[#EAD3CD] mb-1">Restricted Action</h4>
                <p className="text-sm text-[#CFCBCA]">You are presently assigned to a hotel. Receptionist roles enforce a strict single-establishment workflow.</p>
              </div>
            </div>
          </div>
        )}

        {hotels.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {hotels.map(hotel => {
              const applicationStatus = myApplications[hotel.id] ?? null
              const isWorking = workingAtHotels.includes(hotel.id)
              return (
                <div key={hotel.id}
                  className="rounded-2xl shadow-xl overflow-hidden bg-[#383537] border-t-[3px] transition-all duration-500 relative flex flex-col group"
                  style={{ borderColor: 'rgba(160, 113, 127, 0.4)' }}
                  onMouseOver={e => { e.currentTarget.style.transform = 'translateY(-6px)'; e.currentTarget.style.boxShadow = '0 25px 50px rgba(160,113,127,0.15)' }}
                  onMouseOut={e => { e.currentTarget.style.transform = 'translateY(0)'; e.currentTarget.style.boxShadow = '0 10px 15px rgba(0,0,0,0.1)' }}>
                  <div className="p-6 md:p-8 flex-grow">
                    <h3 className="text-2xl font-bold text-[#EAD3CD] font-serif mb-2 line-clamp-1 group-hover:text-[#A0717F] transition-colors">{hotel.name}</h3>
                    <p className="text-xs uppercase tracking-widest text-[#CFCBCA] mb-6 flex items-center gap-1.5 opacity-70">
                      <Icon name="location" className="w-3.5 h-3.5" />
                      {hotel.city}, {hotel.country}
                    </p>

                    <div className="space-y-4 mb-6">
                      <div className="flex items-center justify-between pb-3 border-b border-[rgba(234,211,205,0.05)]">
                        <span className="text-[10px] uppercase font-bold tracking-widest text-[#A0717F]">Room Count</span>
                        <span className="font-semibold text-[#EAD3CD]">{hotel.rooms_count} Suites</span>
                      </div>
                      <div className="flex items-center justify-between pb-3 border-b border-[rgba(234,211,205,0.05)]">
                        <span className="text-[10px] uppercase font-bold tracking-widest text-[#A0717F]">Remuneration</span>
                        <span className="font-bold font-serif tracking-widest text-[#EAD3CD]">
                          {hotel.default_hourly_wage ? `$${Number(hotel.default_hourly_wage).toFixed(2)}/hr` : 'Discussed upon hire'}
                        </span>
                      </div>
                      {hotel.rating && (
                        <div className="flex items-center justify-between">
                          <span className="text-[10px] uppercase font-bold tracking-widest text-[#A0717F]">Reputation</span>
                          <span className="font-semibold text-[#EAD3CD] flex items-center gap-1">
                            <Icon name="star" className="w-3.5 h-3.5 text-yellow-500" />
                            {hotel.rating} / 5.0
                          </span>
                        </div>
                      )}
                    </div>

                    {hotel.description && (
                      <p className="text-xs leading-relaxed text-[#CFCBCA] opacity-80 line-clamp-3 mb-6">{hotel.description}</p>
                    )}
                  </div>

                  <div className="p-6 md:p-8 pt-0 mt-auto">
                    {isWorking ? (
                      <div className="w-full text-center px-4 py-3 rounded-lg text-[10px] font-bold uppercase tracking-widest border"
                        style={{ background: 'rgba(34, 197, 94, 0.1)', borderColor: 'rgba(34, 197, 94, 0.3)', color: '#4ADE80' }}>
                        Currently Employed
                      </div>
                    ) : applicationStatus === 'pending' ? (
                      <div className="w-full text-center px-4 py-3 rounded-lg text-[10px] font-bold uppercase tracking-widest border"
                        style={{ background: 'rgba(234, 179, 8, 0.1)', borderColor: 'rgba(234, 179, 8, 0.3)', color: '#FACC15' }}>
                        Application Pending
                      </div>
                    ) : applicationStatus === 'rejected' ? (
                      <div className="w-full text-center px-4 py-3 rounded-lg text-[10px] font-bold uppercase tracking-widest border"
                        style={{ background: 'rgba(239, 68, 68, 0.1)', borderColor: 'rgba(239, 68, 68, 0.3)', color: '#F87171' }}>
                        Application Rejected
                      </div>
                    ) : isReceptionistBlocked ? (
                      <div className="w-full text-center px-4 py-3 rounded-lg text-[10px] font-bold uppercase tracking-widest border cursor-not-allowed"
                        style={{ background: 'rgba(207, 203, 202, 0.05)', borderColor: 'rgba(207, 203, 202, 0.2)', color: 'rgba(207, 203, 202, 0.5)' }}>
                        Blocked
                      </div>
                    ) : (
                      <Link to={`/staff/hotels/${hotel.id}/apply`}
                        className="block w-full text-center px-4 py-3 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all duration-300 shadow-md"
                        style={{ backgroundColor: '#A0717F', color: '#FFFFFF' }}
                        onMouseOver={e => { e.currentTarget.style.backgroundColor = '#b58290'; e.currentTarget.style.transform = 'scale(1.02)' }}
                        onMouseOut={e => { e.currentTarget.style.backgroundColor = '#A0717F'; e.currentTarget.style.transform = 'scale(1)' }}>
                        Submit Application
                      </Link>
                    )}
                  </div>
                </div>
              )
            })}
          </div>
        ) : (
          <div className="rounded-2xl shadow-xl p-16 text-center border-t border-[rgba(234,211,205,0.1)]" style={{ backgroundColor: '#383537' }}>
            <Icon name="building" className="w-16 h-16 mx-auto mb-6" style={{ color: 'rgba(160, 113, 127, 0.3)' }} />
            <h3 className="text-xl font-bold font-serif text-[#EAD3CD] mb-2">No Openings</h3>
            <p className="text-sm uppercase tracking-widest" style={{ color: 'rgba(207, 203, 202, 0.5)' }}>
              There are currently no hotels accepting applications on our network.
            </p>
          </div>
        )}
      </div>
    </Layout>
  )
}
