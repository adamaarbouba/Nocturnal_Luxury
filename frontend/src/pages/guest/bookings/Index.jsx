import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import api from '../../../lib/api'
import Layout from '../../../components/Layout'
import Breadcrumbs from '../../../components/Breadcrumbs'
import Icon from '../../../components/Icon'
import { money, fmtDate, nightsBetween } from './../fmt'

const statusBadge = (status) => {
  const map = {
    pending: 'bg-yellow-500/10 text-yellow-400 border-yellow-500/30',
    confirmed: 'bg-green-500/10 text-green-400 border-green-500/30',
    checked_in: 'bg-blue-500/10 text-blue-400 border-blue-500/30',
  }
  return map[status] ?? ''
}

const paymentBadge = (status) => {
  const map = {
    pending: 'bg-orange-500/10 text-orange-400 border-orange-500/30',
    paid: 'bg-green-500/10 text-green-400 border-green-500/30',
    partial: 'bg-yellow-500/10 text-yellow-400 border-yellow-500/30',
    refunded: 'bg-[#2A2729] text-[#CFCBCA] border-[#4E3B46]',
  }
  return map[status] ?? ''
}

export default function GuestBookingsIndex() {
  const [data, setData] = useState(null)

  useEffect(() => {
    api.get('/guest/bookings').then(res => setData(res.data)).catch(() => setData({ currentBookings: [], pastBookings: [] }))
  }, [])

  if (!data) return <Layout><p className="text-[#CFCBCA]">Loading…</p></Layout>

  const { currentBookings = [], pastBookings = [] } = data

  return (
    <Layout>
      <div className="fixed top-0 left-0 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none" style={{ background: 'rgba(160, 113, 127, 0.05)', zIndex: 0 }}></div>
      <div className="fixed bottom-0 right-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none" style={{ background: 'rgba(234, 211, 205, 0.03)', zIndex: 0 }}></div>

      <div className="max-w-6xl mx-auto px-6 py-12 relative z-10">
        <div className="mb-12">
          <Breadcrumbs links={[
            { label: 'Guest Dashboard', url: '/guest/dashboard' },
            { label: 'My Itinerary', url: '#' },
          ]} />
        </div>

        <div className="mb-12 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-b border-[rgba(234,211,205,0.05)] pb-6">
          <div>
            <p className="text-xs font-bold uppercase tracking-[0.3em] text-[#A0717F] mb-3">Reservations</p>
            <h1 className="text-4xl lg:text-5xl font-bold font-serif text-[#EAD3CD] leading-tight">My Itinerary</h1>
            <p className="text-xs uppercase mt-4" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>
              Your upcoming escapes and past journeys
            </p>
          </div>
          <Link to="/guest/hotels"
            className="inline-flex items-center gap-2 px-8 py-4 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all duration-300 shadow-xl shrink-0 bg-[#A0717F] text-white hover:bg-[#b58290] hover:-translate-y-0.5">
            <Icon name="building" size="sm" className="w-4 h-4" />
            Reserve New Stay
          </Link>
        </div>

        {/* Current Bookings Section */}
        <div className="mb-16">
          <h2 className="text-2xl font-bold font-serif text-[#EAD3CD] mb-8 flex items-center gap-4">
            <span className="w-8 h-[1px] bg-[#A0717F]"></span>
            Current & Upcoming
            <span className="text-xs font-bold font-sans uppercase tracking-[0.2em] text-[#A0717F] opacity-60 bg-[#A0717F]/10 px-3 py-1 rounded-full">{currentBookings.length}</span>
          </h2>

          {currentBookings.length > 0 ? (
            <div className="grid grid-cols-1 gap-8">
              {currentBookings.map(booking => {
                const room = booking.booking_items?.[0]?.room
                const nights = nightsBetween(booking.check_in_date, booking.check_out_date)
                const remaining = booking.remaining_balance ?? 0
                return (
                  <div key={booking.id} className="group bg-[#2A2729] border border-[#4E3B46] rounded-3xl shadow-xl hover:shadow-2xl hover:border-[#A0717F]/50 transition-all duration-500 overflow-hidden relative">
                    <div className="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-[#A0717F]/10 to-transparent rounded-bl-full pointer-events-none transition-all duration-500 group-hover:from-[#A0717F]/20"></div>

                    <div className="p-8 lg:p-10">
                      <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
                        <div className="lg:col-span-1 space-y-4 border-b lg:border-b-0 lg:border-r border-[#4E3B46]/50 pb-6 lg:pb-0 lg:pr-6">
                          <div>
                            <p className="text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-1">Establishment</p>
                            <h3 className="text-2xl font-bold font-serif text-[#EAD3CD]">{booking.hotel?.name}</h3>
                            {booking.hotel?.city && <p className="text-[10px] text-[#CFCBCA] uppercase tracking-widest opacity-70 mt-1">{booking.hotel.city}</p>}
                          </div>
                          {room && (
                            <div className="pt-4">
                              <p className="text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-1">Accommodation</p>
                              <p className="text-sm font-semibold text-[#EAD3CD]">Room {room.room_number}</p>
                              <p className="text-[10px] text-[#CFCBCA] uppercase tracking-widest opacity-70 mt-1">{room.room_type}</p>
                            </div>
                          )}
                        </div>

                        <div className="lg:col-span-1 border-b lg:border-b-0 lg:border-r border-[#4E3B46]/50 pb-6 lg:pb-0 lg:px-6 flex flex-col justify-center">
                          <p className="text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-4">Journey</p>
                          <div className="space-y-4">
                            <div>
                              <p className="text-[10px] text-[#CFCBCA] uppercase tracking-widest opacity-60">Arrival</p>
                              <p className="text-sm font-semibold text-[#EAD3CD]">{fmtDate(booking.check_in_date)}</p>
                            </div>
                            <div>
                              <p className="text-[10px] text-[#CFCBCA] uppercase tracking-widest opacity-60">Departure</p>
                              <p className="text-sm font-semibold text-[#EAD3CD]">{fmtDate(booking.check_out_date)}</p>
                            </div>
                            <div className="inline-block px-3 py-1 bg-[#383537] rounded text-[10px] uppercase tracking-widest text-[#A0717F] font-bold">
                              {nights} Nights Duration
                            </div>
                          </div>
                        </div>

                        <div className="lg:col-span-1 space-y-6 pb-6 lg:pb-0 lg:px-6 flex flex-col justify-center border-b lg:border-b-0 lg:border-r border-[#4E3B46]/50">
                          <div>
                            <p className="text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-1">Investment</p>
                            <p className="text-3xl font-bold font-serif text-[#EAD3CD]">${money(booking.total_amount)}</p>
                          </div>

                          <div className="space-y-3">
                            <div>
                              <p className="text-[9px] text-[#CFCBCA] uppercase tracking-widest opacity-60 mb-1">Reservation Status</p>
                              <span className={`inline-block px-3 py-1 rounded-md text-[9px] font-bold uppercase tracking-widest border ${statusBadge(booking.status)}`}>
                                {booking.status.replace(/_/g, ' ')}
                              </span>
                            </div>
                            <div>
                              <p className="text-[9px] text-[#CFCBCA] uppercase tracking-widest opacity-60 mb-1">Ledger Status</p>
                              <span className={`inline-block px-3 py-1 rounded-md text-[9px] font-bold uppercase tracking-widest border ${paymentBadge(booking.payment_status)}`}>
                                {booking.payment_status}
                              </span>
                            </div>
                          </div>
                        </div>

                        <div className="lg:col-span-1 flex flex-col justify-center gap-4 lg:pl-6">
                          <Link to={`/guest/bookings/${booking.id}/confirmation`}
                            className="w-full text-center px-6 py-4 rounded-xl border border-[#4E3B46] text-[#CFCBCA] hover:text-[#EAD3CD] hover:bg-[#383537] transition-all duration-300 text-[10px] font-bold uppercase tracking-widest">
                            View Dossier
                          </Link>

                          {remaining > 0 ? (
                            <Link to={`/guest/bookings/${booking.id}/payment`}
                              className="w-full text-center bg-[#A0717F] hover:bg-[#8F6470] text-white text-[10px] font-bold uppercase tracking-widest px-6 py-4 rounded-xl transition-all duration-300 shadow-xl transform hover:-translate-y-1">
                              Settle Balance
                            </Link>
                          ) : (
                            <Link to={`/guest/bookings/${booking.id}/payment`}
                              className="w-full text-center px-6 py-4 rounded-xl border border-[#4E3B46] text-[#A0717F] hover:bg-[#A0717F] hover:text-white transition-all duration-300 text-[10px] font-bold uppercase tracking-widest">
                              Manage Ledger
                            </Link>
                          )}
                        </div>
                      </div>

                      {booking.special_requests && (
                        <div className="mt-8 pt-6 border-t border-[#4E3B46]/30">
                          <p className="text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F] mb-2">Special Concierge Requests</p>
                          <p className="text-[#CFCBCA] text-sm italic border-l-2 border-[#A0717F]/30 pl-4">"{booking.special_requests}"</p>
                        </div>
                      )}
                    </div>
                  </div>
                )
              })}
            </div>
          ) : (
            <div className="rounded-3xl p-16 text-center border border-[rgba(234,211,205,0.1)] relative overflow-hidden" style={{ backgroundColor: '#2A2729' }}>
              <div className="relative z-10">
                <div className="w-20 h-20 mx-auto rounded-full bg-[#383537] border border-[#4E3B46] flex items-center justify-center mb-6 shadow-xl">
                  <Icon name="calendar" className="w-8 h-8 text-[#A0717F]" />
                </div>
                <h3 className="text-2xl font-bold font-serif text-[#EAD3CD] mb-4">No Current Itineraries</h3>
                <p className="text-xs uppercase tracking-widest mb-10 leading-loose mx-auto" style={{ color: 'rgba(207, 203, 202, 0.5)', maxWidth: '28rem' }}>
                  Your upcoming calendar is clear. Embark on a new journey of luxury.
                </p>
                <Link to="/guest/hotels"
                  className="inline-flex text-[#FFFFFF] font-bold px-10 py-5 rounded-xl transition-all duration-500 text-xs uppercase tracking-[0.3em] shadow-2xl transform hover:-translate-y-1 bg-[#A0717F]">
                  Explore Destinations
                </Link>
              </div>
            </div>
          )}
        </div>

        {/* Past Bookings Section */}
        <div>
          <h2 className="text-2xl font-bold font-serif text-[#EAD3CD] mb-8 flex items-center gap-4">
            <span className="w-8 h-[1px] bg-[#A0717F]"></span>
            Historical Stays
            <span className="text-xs font-bold font-sans uppercase tracking-[0.2em] text-[#A0717F] opacity-60 bg-[#A0717F]/10 px-3 py-1 rounded-full">{pastBookings.length}</span>
          </h2>

          {pastBookings.length > 0 ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {pastBookings.map(booking => {
                const nights = nightsBetween(booking.check_in_date, booking.check_out_date)
                const isDone = booking.status === 'completed' || booking.status === 'checked_out'
                return (
                  <div key={booking.id} className="group bg-[#2A2729] border border-[#4E3B46] rounded-3xl p-8 hover:border-[#A0717F]/50 transition-all duration-500 flex flex-col relative overflow-hidden">
                    <div className="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-[#A0717F]/5 to-transparent rounded-bl-full pointer-events-none transition-all duration-500 group-hover:from-[#A0717F]/10"></div>

                    <div className="flex-1 space-y-6 relative z-10">
                      <div>
                        <h3 className="text-xl font-bold font-serif text-[#EAD3CD] mb-1">{booking.hotel?.name}</h3>
                        {booking.hotel?.city && <p className="text-[10px] text-[#CFCBCA] uppercase tracking-widest opacity-70">{booking.hotel.city}</p>}
                      </div>

                      <div className="space-y-2">
                        <div className="flex justify-between items-center text-sm">
                          <span className="text-[10px] uppercase tracking-widest text-[#CFCBCA] opacity-60">Duration</span>
                          <span className="font-medium text-[#EAD3CD]">{fmtDate(booking.check_in_date, { month: 'short', year: 'numeric' })} ({nights} Nights)</span>
                        </div>
                        <div className="flex justify-between items-center text-sm">
                          <span className="text-[10px] uppercase tracking-widest text-[#CFCBCA] opacity-60">Status</span>
                          <span className={`text-[10px] font-bold uppercase tracking-widest ${isDone ? 'text-[#A0717F]' : booking.status === 'cancelled' ? 'text-red-400' : ''}`}>
                            {booking.status.replace(/_/g, ' ')}
                          </span>
                        </div>
                      </div>
                    </div>

                    <div className="mt-8 pt-6 border-t border-[#4E3B46]/50 flex flex-col gap-3 relative z-10">
                      <Link to={`/guest/bookings/${booking.id}/confirmation`}
                        className="w-full text-center px-4 py-3 rounded-xl border border-[#4E3B46] text-[#EAD3CD] hover:bg-[#383537] transition-all duration-300 text-[9px] font-bold uppercase tracking-[0.2em]">
                        Review Receipt
                      </Link>
                      {isDone && (
                        <Link to={`/guest/bookings/${booking.id}/review`}
                          className="w-full text-center bg-[#4E3B46] hover:bg-[#68525F] text-[#EAD3CD] px-4 py-3 rounded-xl transition-all duration-300 text-[9px] font-bold uppercase tracking-[0.2em] shadow-lg flex items-center justify-center gap-2">
                          <Icon name="star" size="xs" className="text-[#A0717F]" />
                          Share Reflections
                        </Link>
                      )}
                    </div>
                  </div>
                )
              })}
            </div>
          ) : (
            <div className="text-center py-12 px-6 border border-[#4E3B46]/30 rounded-3xl">
              <p className="text-[#CFCBCA] text-[10px] uppercase tracking-widest opacity-60">Your historical ledger is currently unwritten.</p>
            </div>
          )}
        </div>
      </div>
    </Layout>
  )
}
