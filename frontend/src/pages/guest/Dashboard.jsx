import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import api from '../../lib/api'
import Layout from '../../components/Layout'
import { money, fmtDate, ucfirst } from './fmt'

const currentStatusBg = (status) =>
  status === 'pending' ? '#A0717F' : status === 'confirmed' || status === 'checked_in' ? '#1A1515' : '#4E3B46'

const pastStatusBg = (status) => (status === 'cancelled' ? '#4E3B46' : '#1A1515')

function BookingCard({ booking, past }) {
  const firstRoom = booking.booking_items?.[0]?.room
  return (
    <div className="border border-[#4E3B46] bg-[#2A2729] rounded-lg p-4 hover:shadow-md transition">
      <div className="flex justify-between items-start mb-2">
        <div>
          <h4 className="font-semibold text-[#EAD3CD]">{booking.hotel?.name ?? 'Unknown Hotel'}</h4>
          {firstRoom && <p className="text-sm mt-1 text-[#CFCBCA]">Room {firstRoom.room_number}</p>}
        </div>
        <span
          className="inline-block px-3 py-1 rounded-full text-xs font-semibold text-[#EAD3CD]"
          style={{ backgroundColor: past ? pastStatusBg(booking.status) : currentStatusBg(booking.status) }}
        >
          {ucfirst(booking.status)}
        </span>
      </div>
      <div className="text-sm mb-2 text-[#CFCBCA]">
        <p>Check-in: <strong className="text-[#EAD3CD]">{fmtDate(booking.check_in_date)}</strong></p>
        <p>Check-out: <strong className="text-[#EAD3CD]">{fmtDate(booking.check_out_date)}</strong></p>
      </div>
      {past ? (
        <div className="flex justify-between items-center">
          <p className="text-sm font-semibold text-[#A0717F]">${money(booking.total_amount)}</p>
          {(booking.status === 'completed' || booking.status === 'checked_out') && (
            <Link to={`/guest/bookings/${booking.id}/review`} className="text-sm text-[#A0717F] hover:underline">Leave Review →</Link>
          )}
        </div>
      ) : (
        <p className="text-sm font-semibold text-[#A0717F]">${money(booking.total_amount)}</p>
      )}
    </div>
  )
}

export default function GuestDashboard() {
  const [data, setData] = useState(null)

  useEffect(() => {
    api.get('/guest/dashboard').then(res => setData(res.data)).catch(() => setData({ error: true }))
  }, [])

  if (!data) return <Layout><p className="text-[#CFCBCA]">Loading…</p></Layout>

  const { currentBookingsCount = 0, pastBookingsCount = 0, reviewsCount = 0, currentBookings = [], pastBookings = [] } = data

  return (
    <Layout>
      <div className="mb-12">
        <p className="text-xs font-medium uppercase mb-2" style={{ color: '#A0717F', letterSpacing: '0.4em' }}>
          Experience
        </p>
        <h1 className="text-3xl lg:text-5xl font-bold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>
          Guest Atelier
        </h1>
      </div>

      {/* Guest Stats */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div className="bg-[#383537] rounded-lg shadow p-6 border border-[#4E3B46] border-t-4 border-t-[#A0717F]">
          <h3 className="text-sm font-semibold text-[#EAD3CD]">Current Bookings</h3>
          <p className="text-3xl font-bold mt-2 text-[#EAD3CD]">{currentBookingsCount}</p>
        </div>
        <div className="bg-[#383537] rounded-lg shadow p-6 border border-[#4E3B46] border-t-4 border-t-[#4E3B46]">
          <h3 className="text-sm font-semibold text-[#EAD3CD]">Past Stays</h3>
          <p className="text-3xl font-bold mt-2 text-[#EAD3CD]">{pastBookingsCount}</p>
        </div>
        <div className="bg-[#383537] rounded-lg shadow p-6 border border-[#4E3B46] border-t-4 border-t-[#4E3B46]">
          <h3 className="text-sm font-semibold text-[#EAD3CD]">Reviews</h3>
          <p className="text-3xl font-bold mt-2 text-[#EAD3CD]">{reviewsCount}</p>
        </div>
      </div>

      {/* Guest Functions */}
      <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-8 mb-8">
        <h3 className="text-xl font-semibold mb-6 text-[#EAD3CD]">Quick Actions</h3>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          <Link to="/guest/hotels" className="p-4 border border-[#4E3B46] bg-[#383537] hover:bg-[#2A2729] rounded-lg transition hover:shadow-md">
            <h4 className="font-semibold text-[#A0717F]">Search Hotels</h4>
            <p className="text-sm mt-1 text-[#CFCBCA]">Browse and book hotels</p>
          </Link>
          <Link to="/guest/bookings" className="p-4 border border-[#4E3B46] bg-[#383537] hover:bg-[#2A2729] rounded-lg transition hover:shadow-md">
            <h4 className="font-semibold text-[#EAD3CD]">My Bookings</h4>
            <p className="text-sm mt-1 text-[#CFCBCA]">View current and past bookings</p>
          </Link>
          <Link to="/guest/reviews" className="p-4 border border-[#4E3B46] bg-[#383537] hover:bg-[#2A2729] rounded-lg transition hover:shadow-md">
            <h4 className="font-semibold text-[#A0717F]">My Reviews</h4>
            <p className="text-sm mt-1 text-[#CFCBCA]">Leave and view your reviews</p>
          </Link>
        </div>
      </div>

      {/* Current Bookings */}
      <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-8 mb-8">
        <h3 className="text-xl font-semibold mb-6 text-[#EAD3CD]">Current Bookings</h3>
        {currentBookings.length > 0 ? (
          <div className="space-y-4">
            {currentBookings.map(b => <BookingCard key={b.id} booking={b} />)}
          </div>
        ) : (
          <p className="text-[#CFCBCA]">
            You have no current bookings. <Link to="/guest/hotels" className="text-[#A0717F] hover:underline">Search hotels</Link> to make a booking.
          </p>
        )}
      </div>

      {/* Past Stays */}
      <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-8">
        <h3 className="text-xl font-semibold mb-6 text-[#EAD3CD]">Past Stays</h3>
        {pastBookings.length > 0 ? (
          <div className="space-y-4">
            {pastBookings.map(b => <BookingCard key={b.id} booking={b} past />)}
          </div>
        ) : (
          <p className="text-[#CFCBCA]">No past stays yet. Your booking history will appear here.</p>
        )}
      </div>
    </Layout>
  )
}
