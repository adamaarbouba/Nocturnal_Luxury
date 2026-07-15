import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import Layout from '../../components/Layout'
import Breadcrumbs from '../../components/Breadcrumbs'
import Icon from '../../components/Icon'
import api from '../../lib/api'
import { money, fmtDate, Pagination } from './util'

export default function CheckInIndex() {
  const [data, setData] = useState(null)
  const [page, setPage] = useState(1)

  useEffect(() => { api.get(`/receptionist/check-in?page=${page}`).then((res) => setData(res.data)) }, [page])

  if (!data) return <Layout><div /></Layout>
  const { pendingCheckIns, hotel } = data
  const list = pendingCheckIns.data ?? []

  return (
    <Layout>
      <div className="fixed top-0 left-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none"
        style={{ background: 'rgba(160, 113, 127, 0.05)', zIndex: 0 }}></div>

      <div className="container mx-auto px-4 py-8 relative z-10">
        <Breadcrumbs links={[
          { label: 'Receptionist Dashboard', url: '/receptionist/dashboard' },
          { label: 'Check-ins', url: '#' },
        ]} />

        <div className="mb-10">
          <p className="text-xs font-medium uppercase mb-2" style={{ color: '#A0717F', letterSpacing: '0.4em' }}>{hotel.name}</p>
          <h1 className="text-3xl lg:text-5xl font-bold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Pending Arrivals</h1>
        </div>

        {list.length > 0 ? (
          <>
            <div className="block xl:hidden space-y-5">
              {list.map((booking) => (
                <div key={booking.id} className="bg-[#383537] rounded-2xl shadow-xl p-6 border-t-[3px]" style={{ borderColor: 'rgba(160, 113, 127, 0.6)' }}>
                  <div className="flex justify-between items-start mb-5 border-b border-[rgba(234,211,205,0.05)] pb-5">
                    <div>
                      <h3 className="text-xl font-bold font-serif text-[#EAD3CD] mb-1">{booking.user.name}</h3>
                      <p className="text-sm text-[#CFCBCA]">{booking.user.email}</p>
                    </div>
                  </div>
                  <div className="grid grid-cols-2 gap-4 mb-5">
                    <div>
                      <p className="text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-1">Arrival Date</p>
                      <p className="text-sm font-semibold text-[#EAD3CD]">{fmtDate(booking.check_in_date)}</p>
                    </div>
                    <div>
                      <p className="text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-1">Assigned Suites</p>
                      <div className="flex flex-wrap gap-1">
                        {booking.booking_items.map((item) => (
                          <span key={item.id} className="inline-block px-2 py-0.5 rounded text-[10px] bg-[#2A2729] border border-[rgba(160,113,127,0.3)] text-[#EAD3CD]">
                            {item.room.room_number} <span style={{ color: 'rgba(207, 203, 202, 0.5)' }}>({item.room.room_type})</span>
                          </span>
                        ))}
                      </div>
                    </div>
                  </div>
                  <div className="flex justify-between items-center pt-5 border-t border-[rgba(234,211,205,0.05)]">
                    <div>
                      <p className="text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-1">Ledger Balance</p>
                      <p className="text-xl font-bold text-[#A0717F]" style={{ fontFamily: "'Georgia', serif" }}>${money(booking.total_amount)}</p>
                    </div>
                    <Link to={`/receptionist/check-in/${booking.id}`} className="px-6 py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all duration-300 shadow-md"
                      style={{ backgroundColor: '#A0717F', color: '#FFFFFF' }}>
                      Process Arrival
                    </Link>
                  </div>
                </div>
              ))}
            </div>

            <div className="hidden xl:block rounded-2xl shadow-2xl overflow-hidden bg-[#383537] border-t border-[rgba(234,211,205,0.1)]">
              <div className="w-full overflow-x-auto custom-scrollbar">
                <table className="w-full text-left whitespace-nowrap">
                  <thead style={{ backgroundColor: '#2E2530', borderBottom: '1px solid rgba(160, 113, 127, 0.2)' }}>
                    <tr>
                      {['Guest Profile', 'Arrival Date', 'Allocated Suites', 'Folio Balance', ''].map((h, i) => (
                        <th key={i} className={`px-6 py-5 text-[11px] font-bold uppercase tracking-widest ${i === 4 ? 'text-right' : ''}`} style={{ color: '#A0717F' }}>{h || 'Action'}</th>
                      ))}
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-[rgba(234,211,205,0.05)]">
                    {list.map((booking) => (
                      <tr key={booking.id} className="transition-colors duration-300">
                        <td className="px-6 py-4">
                          <p className="font-bold text-[#EAD3CD] font-serif tracking-wide">{booking.user.name}</p>
                          <p className="text-sm text-[#CFCBCA]">{booking.user.email}</p>
                        </td>
                        <td className="px-6 py-4 text-sm font-semibold text-[#CFCBCA]">{fmtDate(booking.check_in_date)}</td>
                        <td className="px-6 py-4">
                          <div className="flex flex-wrap gap-2">
                            {booking.booking_items.map((item) => (
                              <span key={item.id} className="inline-block px-3 py-1 rounded text-xs bg-[#2A2729] border border-[rgba(160,113,127,0.3)] text-[#EAD3CD]">
                                {item.room.room_number} <span style={{ color: 'rgba(207, 203, 202, 0.5)' }}>({item.room.room_type})</span>
                              </span>
                            ))}
                          </div>
                        </td>
                        <td className="px-6 py-4">
                          <span className="text-lg font-bold" style={{ color: '#A0717F', fontFamily: "'Georgia', serif" }}>${money(booking.total_amount)}</span>
                        </td>
                        <td className="px-6 py-4 text-right">
                          <Link to={`/receptionist/check-in/${booking.id}`} className="inline-block px-6 py-2.5 rounded-md text-[10px] font-bold uppercase tracking-widest transition-all duration-300 shadow-md"
                            style={{ backgroundColor: '#A0717F', color: '#FFFFFF' }}>
                            Commence
                          </Link>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>

            <Pagination meta={pendingCheckIns} onPage={setPage} />
          </>
        ) : (
          <div className="rounded-2xl shadow-xl p-12 text-center" style={{ backgroundColor: '#383537', borderTop: '1px solid rgba(234, 211, 205, 0.1)' }}>
            <Icon name="check" className="w-12 h-12 mx-auto mb-4" style={{ color: 'rgba(160, 113, 127, 0.4)' }} />
            <p className="text-sm font-medium uppercase tracking-widest" style={{ color: 'rgba(207, 203, 202, 0.5)' }}>No pending arrivals at this time.</p>
          </div>
        )}

        <div className="mt-10">
          <Link to="/receptionist/dashboard" className="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-[10px] font-bold uppercase tracking-widest transition-all duration-300"
            style={{ color: '#CFCBCA', border: '1px solid rgba(207, 203, 202, 0.2)' }}>
            <Icon name="arrow-left" className="w-4 h-4" /> Back to Dashboard
          </Link>
        </div>
      </div>
    </Layout>
  )
}
