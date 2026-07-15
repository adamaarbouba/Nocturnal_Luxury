import { useEffect, useState } from 'react'
import { Link, useLocation, useNavigate, useParams } from 'react-router-dom'
import Layout from '../../components/Layout'
import Alert from '../../components/Alert'
import Breadcrumbs from '../../components/Breadcrumbs'
import Icon from '../../components/Icon'
import api from '../../lib/api'
import { money, fmtDate, pad5, statusBadgeStyle } from './util'

export default function BookingsShow() {
  const { id } = useParams()
  const [data, setData] = useState(null)
  const [flash, setFlash] = useState(null)
  const location = useLocation()
  const navigate = useNavigate()

  const load = () => api.get(`/receptionist/bookings/${id}`).then((res) => setData(res.data))

  useEffect(() => {
    load()
    if (location.state?.success) setFlash({ variant: 'success', message: location.state.success })
  }, [id])

  if (!data) return <Layout><div /></Layout>
  const { booking, hotel } = data

  const confirmBooking = async () => {
    try {
      const res = await api.post(`/receptionist/bookings/${id}/confirm`)
      setFlash({ variant: 'success', message: res.data.message })
      load()
    } catch (err) {
      setFlash({ variant: 'error', message: err.response?.data?.message ?? 'Error confirming booking.' })
    }
  }

  const cancelBooking = async () => {
    if (!window.confirm('Are you sure you want to cancel this booking?')) return
    try {
      await api.post(`/receptionist/bookings/${id}/cancel`)
      navigate('/receptionist/bookings', { state: { success: 'Booking cancelled successfully. Rooms released.' } })
    } catch (err) {
      setFlash({ variant: 'error', message: err.response?.data?.message ?? 'Error cancelling booking.' })
    }
  }

  return (
    <Layout>
      <div className="fixed top-0 right-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none"
        style={{ background: 'rgba(160, 113, 127, 0.05)', zIndex: 0 }}></div>
      <div className="fixed bottom-0 left-0 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none"
        style={{ background: 'rgba(234, 211, 205, 0.03)', zIndex: 0 }}></div>

      <div className="container mx-auto px-4 py-8 max-w-4xl relative z-10">
        <Breadcrumbs links={[
          { label: 'Receptionist Dashboard', url: '/receptionist/dashboard' },
          { label: 'Bookings', url: '/receptionist/bookings' },
          { label: `Folio #${pad5(booking.id)}`, url: '#' },
        ]} />

        <div className="mb-10">
          <p className="text-xs font-medium uppercase mb-2" style={{ color: '#A0717F', letterSpacing: '0.4em' }}>{hotel.name}</p>
          <h1 className="text-3xl lg:text-5xl font-bold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Folio #{pad5(booking.id)}</h1>
        </div>

        {flash && <div className="mb-6"><Alert variant={flash.variant}>{flash.message}</Alert></div>}

        <div className="space-y-8">
          <div className="rounded-2xl shadow-2xl p-8" style={{ backgroundColor: '#383537', borderTop: '1px solid rgba(234, 211, 205, 0.1)' }}>
            <div className="flex flex-col md:flex-row justify-between md:items-center mb-10 gap-6">
              <div>
                <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Reservation Status</p>
                <p className="text-2xl font-bold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>
                  {booking.status.replace('_', ' ').replace(/^./, (c) => c.toUpperCase())}
                </p>
              </div>
              <div>
                <span className="px-5 py-2 rounded-full text-xs font-semibold uppercase tracking-wider border" style={statusBadgeStyle(booking.status)}>
                  {booking.status.replace('_', ' ')}
                </span>
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-8 py-8" style={{ borderTop: '1px solid rgba(234, 211, 205, 0.1)', borderBottom: '1px solid rgba(234, 211, 205, 0.1)' }}>
              <div>
                <p className="text-xs font-medium uppercase mb-2" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Arrival</p>
                <p className="text-xl font-bold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>{fmtDate(booking.check_in_date)}</p>
              </div>
              <div>
                <p className="text-xs font-medium uppercase mb-2" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Departure</p>
                <p className="text-xl font-bold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>{fmtDate(booking.check_out_date)}</p>
              </div>
              <div>
                <p className="text-xs font-medium uppercase mb-2" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Total Balance</p>
                <p className="text-2xl font-bold" style={{ color: '#A0717F' }}>${money(booking.total_amount)}</p>
                <p className="text-xs mt-1" style={{ color: '#CFCBCA', fontStyle: 'italic' }}>{booking.payment_status.replace(/^./, (c) => c.toUpperCase())}</p>
              </div>
            </div>

            {booking.status === 'pending' && (
              <div className="mt-8 flex flex-wrap gap-4">
                <button type="button" onClick={confirmBooking} className="inline-block px-8 py-3 rounded-md text-sm font-semibold uppercase transition-all duration-300"
                  style={{ backgroundColor: '#3b82f6', color: '#FFFFFF', letterSpacing: '0.12em', boxShadow: '0 4px 15px rgba(59, 130, 246, 0.3)' }}>
                  Confirm Booking
                </button>
                <Link to={`/receptionist/check-in/${booking.id}`} className="inline-block px-8 py-3 rounded-md text-sm font-semibold uppercase transition-all duration-300"
                  style={{ backgroundColor: '#A0717F', color: '#FFFFFF', letterSpacing: '0.12em', boxShadow: '0 4px 15px rgba(160, 113, 127, 0.3)' }}>
                  Check-In Guest
                </Link>
                <button type="button" onClick={cancelBooking} className="inline-block px-8 py-3 rounded-md text-sm font-semibold uppercase transition-all duration-300"
                  style={{ backgroundColor: 'transparent', border: '1px solid rgba(239, 68, 68, 0.5)', color: '#F87171', letterSpacing: '0.12em' }}>
                  Cancel Booking
                </button>
              </div>
            )}
            {booking.status === 'confirmed' && (
              <div className="mt-8 flex flex-wrap gap-4">
                <Link to={`/receptionist/check-in/${booking.id}`} className="inline-block px-8 py-3 rounded-md text-sm font-semibold uppercase transition-all duration-300"
                  style={{ backgroundColor: '#A0717F', color: '#FFFFFF', letterSpacing: '0.12em', boxShadow: '0 4px 15px rgba(160, 113, 127, 0.3)' }}>
                  Check-In Guest
                </Link>
              </div>
            )}
            {booking.status === 'checked_in' && (
              <div className="mt-8">
                <Link to={`/receptionist/check-out/${booking.id}`} className="inline-block px-8 py-3 rounded-md text-sm font-semibold uppercase transition-all duration-300"
                  style={{ backgroundColor: '#A0717F', color: '#FFFFFF', letterSpacing: '0.12em', boxShadow: '0 4px 15px rgba(160, 113, 127, 0.3)' }}>
                  Process Check-Out
                </Link>
              </div>
            )}
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div className="rounded-2xl shadow-xl p-8" style={{ backgroundColor: '#383537', borderTop: '1px solid rgba(234, 211, 205, 0.1)' }}>
              <h3 className="text-xl font-bold mb-6" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Guest Profile</h3>
              <div className="space-y-6">
                <div>
                  <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Name</p>
                  <p className="text-lg" style={{ color: '#EAD3CD' }}>{booking.user.name}</p>
                </div>
                <div>
                  <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Email</p>
                  <p className="text-base" style={{ color: '#CFCBCA' }}>{booking.user.email}</p>
                </div>
                <div>
                  <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Phone</p>
                  <p className="text-base" style={{ color: '#CFCBCA' }}>{booking.user.phone ?? 'Not provided'}</p>
                </div>
                <div>
                  <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Address</p>
                  <p className="text-base" style={{ color: '#CFCBCA' }}>{booking.user.address ?? 'Not provided'}</p>
                </div>
              </div>
            </div>

            <div className="space-y-8">
              {booking.special_requests && (
                <div className="rounded-2xl shadow-xl p-8" style={{ backgroundColor: '#383537', borderTop: '1px solid rgba(234, 211, 205, 0.1)' }}>
                  <h3 className="text-xl font-bold mb-4" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Special Requests</h3>
                  <p className="text-sm leading-relaxed" style={{ color: '#CFCBCA', fontStyle: 'italic' }}>"{booking.special_requests}"</p>
                </div>
              )}
              {booking.notes && (
                <div className="rounded-2xl shadow-xl p-8" style={{ backgroundColor: '#383537', borderTop: '1px solid rgba(234, 211, 205, 0.1)' }}>
                  <h3 className="text-xl font-bold mb-4" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Internal Notes</h3>
                  <p className="text-sm leading-relaxed" style={{ color: '#CFCBCA' }}>{booking.notes}</p>
                </div>
              )}
            </div>
          </div>

          <div className="rounded-2xl shadow-xl p-8" style={{ backgroundColor: '#383537', borderTop: '1px solid rgba(234, 211, 205, 0.1)' }}>
            <h3 className="text-xl font-bold mb-6" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Suite Accommodations</h3>
            <div className="space-y-4">
              {booking.booking_items.map((item) => (
                <div key={item.id} className="rounded-xl p-6" style={{ backgroundColor: '#4E3B46', border: '1px solid rgba(160, 113, 127, 0.1)' }}>
                  <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                      <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Suite No.</p>
                      <p className="text-lg font-bold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>{item.room.room_number}</p>
                    </div>
                    <div>
                      <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Category</p>
                      <p className="text-base" style={{ color: '#CFCBCA' }}>{item.room.room_type}</p>
                    </div>
                    <div>
                      <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Capacity</p>
                      <p className="text-base" style={{ color: '#CFCBCA' }}>{item.room.capacity} {item.room.capacity === 1 ? 'Guest' : 'Guests'}</p>
                    </div>
                    <div>
                      <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Rate</p>
                      <p className="text-lg font-bold" style={{ color: '#A0717F' }}>
                        ${money(item.price_per_night)} <span className="text-xs font-normal" style={{ color: 'rgba(207, 203, 202, 0.5)' }}>/ night</span>
                      </p>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>

        <div className="mt-10">
          <Link to="/receptionist/bookings" className="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-semibold uppercase transition-all duration-300"
            style={{ color: '#CFCBCA', border: '1px solid rgba(207, 203, 202, 0.2)', letterSpacing: '0.1em' }}>
            <Icon name="arrow-left" className="w-4 h-4" /> Back to Directory
          </Link>
        </div>
      </div>
    </Layout>
  )
}
