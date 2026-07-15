import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import Layout from '../../components/Layout'
import Breadcrumbs from '../../components/Breadcrumbs'
import Icon from '../../components/Icon'
import api from '../../lib/api'

const fmtDate = d => new Date(d).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })

// ponytail: coarse diffForHumans clone; enough for display parity
export function diffForHumans(d) {
  const s = Math.floor((Date.now() - new Date(d)) / 1000)
  if (s < 60) return 'just now'
  const units = [[31536000, 'year'], [2592000, 'month'], [86400, 'day'], [3600, 'hour'], [60, 'minute']]
  for (const [sec, name] of units) {
    if (s >= sec) {
      const n = Math.floor(s / sec)
      return `${n} ${name}${n > 1 ? 's' : ''} ago`
    }
  }
}

const statusBadge = status =>
  status === 'pending'
    ? { background: 'rgba(234, 179, 8, 0.1)', borderColor: 'rgba(234, 179, 8, 0.3)', color: '#FACC15' }
    : status === 'approved'
      ? { background: 'rgba(34, 197, 94, 0.1)', borderColor: 'rgba(34, 197, 94, 0.3)', color: '#4ADE80' }
      : { background: 'rgba(239, 68, 68, 0.1)', borderColor: 'rgba(239, 68, 68, 0.3)', color: '#F87171' }

const cardBorderColor = status =>
  status === 'pending' ? 'rgba(234, 179, 8, 0.6)' : status === 'approved' ? 'rgba(34, 197, 94, 0.6)' : 'rgba(239, 68, 68, 0.6)'

// Ports resources/views/staff/my-applications.blade.php
export default function MyApplications() {
  const [applications, setApplications] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    api.get('/staff/my-applications')
      .then(res => setApplications(res.data.applications))
      .finally(() => setLoading(false))
  }, [])

  if (loading) return <Layout><p className="text-[#CFCBCA] text-center py-12">Loading…</p></Layout>

  return (
    <Layout>
      <div className="fixed top-0 left-0 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none"
        style={{ background: 'rgba(160, 113, 127, 0.05)', zIndex: 0 }}></div>

      <div className="container mx-auto px-4 py-8 relative z-10">
        <Breadcrumbs links={[
          { label: 'Staff Dashboard', url: '/staff/dashboard' },
          { label: 'My Applications', url: '#' },
        ]} />

        <div className="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-b border-[rgba(234,211,205,0.05)] pb-6">
          <div>
            <h2 className="text-3xl lg:text-5xl font-bold font-serif text-[#EAD3CD]">My Applications</h2>
            <p className="text-xs font-medium uppercase mt-3" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>
              Track the lifecycle of your hotel work applications
            </p>
          </div>
          <Link to="/staff/hotels"
            className="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all duration-300 shadow-md shrink-0"
            style={{ backgroundColor: '#A0717F', color: '#FFFFFF' }}
            onMouseOver={e => { e.currentTarget.style.backgroundColor = '#b58290'; e.currentTarget.style.transform = 'translateY(-2px)' }}
            onMouseOut={e => { e.currentTarget.style.backgroundColor = '#A0717F'; e.currentTarget.style.transform = 'translateY(0)' }}>
            <Icon name="building" className="w-4 h-4" />
            Browse Hotels
          </Link>
        </div>

        {applications.length > 0 ? (
          <>
            {/* Mobile/Tablet Card Layout */}
            <div className="block xl:hidden space-y-5">
              {applications.map(application => (
                <div key={application.id} className="bg-[#383537] rounded-2xl shadow-xl p-6 border-t-[3px]"
                  style={{ borderColor: cardBorderColor(application.status) }}>
                  <div className="flex justify-between items-start mb-5 border-b border-[rgba(234,211,205,0.05)] pb-5">
                    <div>
                      <h3 className="text-xl font-bold font-serif text-[#EAD3CD] mb-1">{application.hotel?.name}</h3>
                      <p className="text-sm text-[#CFCBCA] flex items-center gap-1.5 opacity-80">
                        <Icon name="location" className="w-3.5 h-3.5 text-[#A0717F]" />
                        {application.hotel?.city}, {application.hotel?.country}
                      </p>
                    </div>
                    <div className="shrink-0 ml-4">
                      <span className="inline-block px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-widest border"
                        style={statusBadge(application.status)}>
                        {application.status === 'pending' ? 'Pending' : application.status === 'approved' ? 'Approved' : 'Rejected'}
                      </span>
                    </div>
                  </div>

                  <div className="grid grid-cols-2 gap-4 mb-5">
                    <div>
                      <p className="text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-1">Target Role</p>
                      <span className="inline-block px-2 py-0.5 rounded text-[10px] uppercase tracking-wider text-[#EAD3CD]"
                        style={{ backgroundColor: 'rgba(26, 21, 21, 0.5)', border: '1px solid rgba(234, 211, 205, 0.1)' }}>
                        {application.role}
                      </span>
                    </div>
                    <div>
                      <p className="text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-1">Proposed Package</p>
                      <p className="text-sm font-semibold font-serif tracking-widest text-[#EAD3CD]">
                        {application.hourly_rate ? `$${Number(application.hourly_rate).toFixed(2)}/hr` : 'TBD'}
                      </p>
                    </div>
                  </div>

                  <div className="flex justify-between items-center pt-5 border-t border-[rgba(234,211,205,0.05)]">
                    <div>
                      <p className="text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-0.5">Dispatched</p>
                      <p className="text-xs text-[#CFCBCA]">{fmtDate(application.created_at)}</p>
                    </div>
                    <div className="text-right">
                      <p className="text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-0.5">Reviewed</p>
                      <p className="text-xs text-[#CFCBCA]">
                        {application.reviewed_at ? fmtDate(application.reviewed_at) : 'Pending Review'}
                      </p>
                    </div>
                  </div>
                </div>
              ))}
            </div>

            {/* Desktop Table */}
            <div className="hidden xl:block rounded-2xl shadow-2xl overflow-hidden bg-[#383537] border-t border-[rgba(234,211,205,0.1)]">
              <div className="w-full overflow-x-auto custom-scrollbar">
                <table className="w-full text-left whitespace-nowrap">
                  <thead style={{ backgroundColor: '#2E2530', borderBottom: '1px solid rgba(160, 113, 127, 0.2)' }}>
                    <tr>
                      <th className="px-6 py-5 text-[11px] font-bold uppercase tracking-widest" style={{ color: '#A0717F' }}>Establishment</th>
                      <th className="px-6 py-5 text-[11px] font-bold uppercase tracking-widest" style={{ color: '#A0717F' }}>Classification</th>
                      <th className="px-6 py-5 text-[11px] font-bold uppercase tracking-widest" style={{ color: '#A0717F' }}>Current Status</th>
                      <th className="px-6 py-5 text-[11px] font-bold uppercase tracking-widest" style={{ color: '#A0717F' }}>Remuneration</th>
                      <th className="px-6 py-5 text-[11px] font-bold uppercase tracking-widest" style={{ color: '#A0717F' }}>Dispatched On</th>
                      <th className="px-6 py-5 text-[11px] font-bold uppercase tracking-widest text-right" style={{ color: '#A0717F' }}>Adjudicated</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-[rgba(234,211,205,0.05)]">
                    {applications.map(application => (
                      <tr key={application.id} className="transition-colors duration-300"
                        onMouseOver={e => { e.currentTarget.style.backgroundColor = 'rgba(160, 113, 127, 0.05)' }}
                        onMouseOut={e => { e.currentTarget.style.backgroundColor = 'transparent' }}>
                        <td className="px-6 py-4">
                          <p className="font-bold text-[#EAD3CD] font-serif tracking-wide text-lg">{application.hotel?.name}</p>
                          <p className="text-sm text-[#CFCBCA]">{application.hotel?.city}, {application.hotel?.country}</p>
                        </td>
                        <td className="px-6 py-4">
                          <span className="inline-block px-3 py-1 rounded text-[10px] uppercase font-bold tracking-widest text-[#EAD3CD]"
                            style={{ backgroundColor: 'rgba(26, 21, 21, 0.5)', border: '1px solid rgba(234, 211, 205, 0.1)' }}>
                            {application.role}
                          </span>
                        </td>
                        <td className="px-6 py-4">
                          <span className="inline-block px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-widest border"
                            style={statusBadge(application.status)}>
                            {application.status === 'pending' ? 'Pending Review' : application.status === 'approved' ? 'Approved' : 'Rejected'}
                          </span>
                        </td>
                        <td className="px-6 py-4">
                          <span className="text-lg font-bold font-serif" style={{ color: '#A0717F' }}>
                            {application.hourly_rate ? `$${Number(application.hourly_rate).toFixed(2)}/hr` : 'TBD'}
                          </span>
                        </td>
                        <td className="px-6 py-4 text-sm font-semibold text-[#CFCBCA]">
                          {fmtDate(application.created_at)}
                          <div className="text-[10px] opacity-60 font-normal uppercase mt-0.5 tracking-wider">{diffForHumans(application.created_at)}</div>
                        </td>
                        <td className="px-6 py-4 text-right text-sm font-semibold text-[#CFCBCA]">
                          {application.reviewed_at ? (
                            <>
                              {fmtDate(application.reviewed_at)}
                              <div className="text-[10px] opacity-60 font-normal uppercase mt-0.5 tracking-wider">{diffForHumans(application.reviewed_at)}</div>
                            </>
                          ) : (
                            <span className="opacity-50 italic">Pending</span>
                          )}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </>
        ) : (
          <div className="rounded-2xl shadow-xl p-16 text-center border-t border-[rgba(234,211,205,0.1)] mt-12" style={{ backgroundColor: '#383537' }}>
            <Icon name="file" className="w-16 h-16 mx-auto mb-6" style={{ color: 'rgba(160, 113, 127, 0.3)' }} />
            <h3 className="text-xl font-bold font-serif text-[#EAD3CD] mb-2">No Applications Record</h3>
            <p className="text-sm uppercase tracking-widest mb-8 leading-loose" style={{ color: 'rgba(207, 203, 202, 0.5)' }}>
              Your dossier is currently barren.<br />Explore available properties below to establish your career.
            </p>
            <Link to="/staff/hotels"
              className="inline-flex text-[#FFFFFF] font-bold px-8 py-4 rounded-xl transition-all duration-300 text-xs uppercase tracking-widest shadow-xl"
              style={{ backgroundColor: '#A0717F' }}
              onMouseOver={e => { e.currentTarget.style.backgroundColor = '#b58290'; e.currentTarget.style.transform = 'translateY(-2px)' }}
              onMouseOut={e => { e.currentTarget.style.backgroundColor = '#A0717F'; e.currentTarget.style.transform = 'translateY(0)' }}>
              Access Network Openings
            </Link>
          </div>
        )}
      </div>
    </Layout>
  )
}
