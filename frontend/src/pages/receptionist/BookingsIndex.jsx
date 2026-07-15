import { useEffect, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import Layout from '../../components/Layout'
import Breadcrumbs from '../../components/Breadcrumbs'
import Icon from '../../components/Icon'
import api from '../../lib/api'
import { money, fmtDate, statusBadgeStyle, paymentBadgeStyle, Pagination } from './util'

const cardHover = {
  onMouseOver: (e) => { e.currentTarget.style.transform = 'translateY(-6px)'; e.currentTarget.style.boxShadow = '0 25px 50px rgba(160,113,127,0.15)' },
  onMouseOut: (e) => { e.currentTarget.style.transform = 'translateY(0)'; e.currentTarget.style.boxShadow = '0 10px 15px rgba(0,0,0,0.1)' },
}

const filters = { pending: 'Pending', checked_in: 'Checked In', checked_out: 'Checked Out', cancelled: 'Cancelled' }

export default function BookingsIndex() {
  const { status } = useParams()
  const [data, setData] = useState(null)
  const [page, setPage] = useState(1)

  useEffect(() => {
    const url = status ? `/receptionist/bookings/status/${status}` : '/receptionist/bookings'
    api.get(`${url}?page=${page}`).then((res) => setData(res.data))
  }, [status, page])

  useEffect(() => { setPage(1) }, [status])

  if (!data) return <Layout><div /></Layout>

  const { bookings, hotel, pendingBookings, checkedInBookings, checkedOutBookings } = data
  const list = bookings.data ?? []

  return (
    <Layout>
      <div className="fixed top-0 right-0 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none"
        style={{ background: 'rgba(160, 113, 127, 0.04)', zIndex: 0 }}></div>

      <div className="container mx-auto px-4 py-8 relative z-10">
        <Breadcrumbs links={[
          { label: 'Receptionist Dashboard', url: '/receptionist/dashboard' },
          { label: 'Bookings', url: '#' },
        ]} />

        <div className="mb-10">
          <p className="text-xs font-medium uppercase mb-2" style={{ color: '#A0717F', letterSpacing: '0.4em' }}>{hotel.name}</p>
          <h1 className="text-3xl lg:text-5xl font-bold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Booking Management</h1>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
          <Link to="/receptionist/bookings" className="rounded-2xl overflow-hidden shadow-lg transition-all duration-500 cursor-pointer"
            style={{ backgroundColor: '#383537', borderTop: !status ? '2px solid #A0717F' : '1px solid rgba(234, 211, 205, 0.1)' }} {...cardHover}>
            <div className="p-6">
              <h3 className="text-xs font-medium uppercase" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>Pending Bookings</h3>
              <p className="text-4xl font-bold mt-2" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>{pendingBookings}</p>
            </div>
          </Link>
          <Link to="/receptionist/bookings/status/checked_in" className="rounded-2xl overflow-hidden shadow-lg transition-all duration-500 cursor-pointer"
            style={{ backgroundColor: '#383537', borderTop: status === 'checked_in' ? '2px solid #A0717F' : '1px solid rgba(234, 211, 205, 0.1)' }} {...cardHover}>
            <div className="p-6">
              <h3 className="text-xs font-medium uppercase" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>Checked In</h3>
              <p className="text-4xl font-bold mt-2" style={{ color: 'rgba(34, 197, 94, 0.8)', fontFamily: "'Georgia', serif" }}>{checkedInBookings}</p>
            </div>
          </Link>
          <Link to="/receptionist/bookings/status/checked_out" className="rounded-2xl overflow-hidden shadow-lg transition-all duration-500 cursor-pointer"
            style={{ backgroundColor: '#383537', borderTop: status === 'checked_out' ? '2px solid #A0717F' : '1px solid rgba(234, 211, 205, 0.1)' }} {...cardHover}>
            <div className="p-6">
              <h3 className="text-xs font-medium uppercase" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>Checked Out</h3>
              <p className="text-4xl font-bold mt-2" style={{ color: 'rgba(168, 85, 247, 0.8)', fontFamily: "'Georgia', serif" }}>{checkedOutBookings ?? 0}</p>
            </div>
          </Link>
        </div>

        <div className="mb-8 flex flex-wrap gap-3">
          <Link to="/receptionist/bookings" className="px-5 py-2.5 rounded-full text-xs font-semibold uppercase transition-all duration-300"
            style={{ letterSpacing: '0.1em', ...(!status ? { backgroundColor: '#A0717F', color: '#FFFFFF' } : { backgroundColor: '#383537', color: '#CFCBCA', border: '1px solid rgba(234, 211, 205, 0.1)' }) }}>
            All Bookings
          </Link>
          {Object.entries(filters).map(([val, label]) => (
            <Link key={val} to={`/receptionist/bookings/status/${val}`} className="px-5 py-2.5 rounded-full text-xs font-semibold uppercase transition-all duration-300"
              style={{ letterSpacing: '0.1em', ...(status === val ? { backgroundColor: '#A0717F', color: '#FFFFFF' } : { backgroundColor: '#383537', color: '#CFCBCA', border: '1px solid rgba(234, 211, 205, 0.1)' }) }}>
              {label}
            </Link>
          ))}
        </div>

        {list.length > 0 ? (
          <>
            {/* Mobile/Tablet Card Layout */}
            <div className="block xl:hidden space-y-5">
              {list.map((booking) => (
                <div key={booking.id} className="bg-[#383537] rounded-2xl shadow-xl p-6 border-t-[3px]"
                  style={{ borderColor: statusBadgeStyle(booking.status).borderColor }}>
                  <div className="flex justify-between items-start mb-5 border-b border-[rgba(234,211,205,0.05)] pb-5">
                    <div>
                      <h3 className="text-xl font-bold font-serif text-[#EAD3CD] mb-1">{booking.user.name}</h3>
                      <p className="text-sm text-[#CFCBCA]">{booking.user.email}</p>
                    </div>
                    <div className="flex flex-col items-end gap-2 shrink-0 ml-4">
                      <span className="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-widest border" style={statusBadgeStyle(booking.status)}>
                        {booking.status.replace('_', ' ')}
                      </span>
                      <span className="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-widest border" style={paymentBadgeStyle(booking.payment_status)}>
                        {booking.payment_status}
                      </span>
                    </div>
                  </div>

                  <div className="grid grid-cols-2 gap-4 mb-5">
                    <div>
                      <p className="text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-1">Stay Duration</p>
                      <p className="text-sm font-semibold text-[#EAD3CD] flex items-center gap-2">
                        {fmtDate(booking.check_in_date)} <span className="text-[#A0717F]">→</span> {fmtDate(booking.check_out_date)}
                      </p>
                    </div>
                    <div>
                      <p className="text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-1">Units Reserved</p>
                      <p className="text-sm font-semibold text-[#EAD3CD]">
                        {booking.booking_items.length} {booking.booking_items.length === 1 ? 'room' : 'rooms'}
                      </p>
                    </div>
                  </div>

                  <div className="flex justify-between items-center pt-5 border-t border-[rgba(234,211,205,0.05)]">
                    <div>
                      <p className="text-[10px] uppercase font-bold tracking-widest text-[#A0717F] mb-1">Total Due</p>
                      <p className="text-xl font-bold text-[#A0717F]" style={{ fontFamily: "'Georgia', serif" }}>${money(booking.total_amount)}</p>
                    </div>
                    <Link to={`/receptionist/bookings/${booking.id}`}
                      className="px-6 py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest transition-all duration-300"
                      style={{ backgroundColor: 'transparent', border: '1px solid #A0717F', color: '#A0717F' }}>
                      View Folio
                    </Link>
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
                      {['Guest Profile', 'Check-In', 'Check-Out', 'Rooms', 'Amount Due', 'Status', 'Ledger', 'Action'].map((h) => (
                        <th key={h} className="px-6 py-5 text-[11px] font-bold uppercase tracking-widest" style={{ color: '#A0717F' }}>{h}</th>
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
                        <td className="px-6 py-4 text-sm font-semibold text-[#CFCBCA]">{fmtDate(booking.check_out_date)}</td>
                        <td className="px-6 py-4 text-sm text-[#CFCBCA]">
                          {booking.booking_items.length} {booking.booking_items.length === 1 ? 'room' : 'rooms'}
                        </td>
                        <td className="px-6 py-4">
                          <span className="text-lg font-bold" style={{ color: '#A0717F', fontFamily: "'Georgia', serif" }}>${money(booking.total_amount)}</span>
                        </td>
                        <td className="px-6 py-4">
                          <span className="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-widest border" style={statusBadgeStyle(booking.status)}>
                            {booking.status.replace('_', ' ')}
                          </span>
                        </td>
                        <td className="px-6 py-4">
                          <span className="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-widest border" style={paymentBadgeStyle(booking.payment_status)}>
                            {booking.payment_status}
                          </span>
                        </td>
                        <td className="px-6 py-4">
                          <Link to={`/receptionist/bookings/${booking.id}`}
                            className="inline-block px-5 py-2 rounded-md text-[10px] font-bold uppercase transition-all duration-300 tracking-widest"
                            style={{ backgroundColor: 'transparent', border: '1px solid #A0717F', color: '#A0717F' }}>
                            Open
                          </Link>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>

            <Pagination meta={bookings} onPage={setPage} />
          </>
        ) : (
          <div className="rounded-2xl shadow-xl p-12 text-center" style={{ backgroundColor: '#383537', borderTop: '1px solid rgba(234, 211, 205, 0.1)' }}>
            <Icon name="calendar" className="w-12 h-12 mx-auto mb-4" style={{ color: 'rgba(160, 113, 127, 0.4)' }} />
            <p className="text-sm font-medium uppercase tracking-widest" style={{ color: 'rgba(207, 203, 202, 0.5)' }}>
              No bookings found{status ? ` with status: ${status.replace('_', ' ')}` : ''}.
            </p>
          </div>
        )}

        <div className="mt-10">
          <Link to="/receptionist/dashboard"
            className="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-[10px] font-bold uppercase tracking-widest transition-all duration-300"
            style={{ color: '#CFCBCA', border: '1px solid rgba(207, 203, 202, 0.2)' }}>
            <Icon name="arrow-left" className="w-4 h-4" /> Back to Dashboard
          </Link>
        </div>
      </div>
    </Layout>
  )
}
