import { useEffect, useState } from 'react'
import { Link, useNavigate, useParams } from 'react-router-dom'
import Layout from '../../components/Layout'
import Alert from '../../components/Alert'
import Icon from '../../components/Icon'
import api from '../../lib/api'
import { money, fmtDate } from './util'

export default function CheckOutShow() {
  const { id } = useParams()
  const [data, setData] = useState(null)
  const [notes, setNotes] = useState('')
  const [error, setError] = useState(null)
  const navigate = useNavigate()

  useEffect(() => {
    api.get(`/receptionist/check-out/${id}`).then((res) => {
      setData(res.data)
      setNotes(res.data.booking.notes ?? '')
    })
  }, [id])

  if (!data) return <Layout><div /></Layout>
  const { booking, hotel, remainingBalance } = data

  const submit = async (e) => {
    e.preventDefault()
    if (remainingBalance > 0) return
    setError(null)
    try {
      const res = await api.post(`/receptionist/check-out/${id}`, { notes })
      navigate('/receptionist/check-out', { state: { success: res.data.message } })
    } catch (err) {
      setError(err.response?.data?.message ?? 'Error during check-out.')
    }
  }

  return (
    <Layout>
      <div className="container mx-auto px-4 py-8 max-w-3xl relative z-10">
        <div className="mb-10 text-center">
          <p className="text-xs font-medium uppercase mb-2" style={{ color: '#A0717F', letterSpacing: '0.4em' }}>{hotel.name}</p>
          <h1 className="text-3xl lg:text-5xl font-bold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Departure Procedure</h1>
        </div>

        {error && <div className="mb-6"><Alert variant="error">{error}</Alert></div>}

        <div className="rounded-2xl shadow-2xl p-8 lg:p-12" style={{ backgroundColor: '#383537', borderTop: '1px solid rgba(234, 211, 205, 0.1)' }}>
          <div className="mb-10">
            <h3 className="text-xl font-bold mb-6 flex items-center gap-3" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>
              <Icon name="user" className="w-6 h-6" style={{ color: '#A0717F' }} /> Guest Profile
            </h3>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 rounded-xl" style={{ backgroundColor: '#2E2530', border: '1px solid rgba(160, 113, 127, 0.1)' }}>
              <div>
                <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Name</p>
                <p className="text-lg font-bold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>{booking.user.name}</p>
              </div>
              <div>
                <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Email</p>
                <p className="text-base" style={{ color: '#CFCBCA' }}>{booking.user.email}</p>
              </div>
              <div className="md:col-span-2">
                <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Phone</p>
                <p className="text-base" style={{ color: '#CFCBCA' }}>{booking.user.phone ?? 'Not provided'}</p>
              </div>
            </div>
          </div>

          <div className="mb-10">
            <h3 className="text-xl font-bold mb-6 flex items-center gap-3" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>
              <Icon name="calendar" className="w-6 h-6" style={{ color: '#A0717F' }} /> Folio Details
            </h3>
            <div className="grid grid-cols-2 gap-6 p-6 rounded-xl" style={{ backgroundColor: '#2E2530', border: '1px solid rgba(160, 113, 127, 0.1)' }}>
              <div>
                <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Arrival</p>
                <p className="text-lg font-bold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>{fmtDate(booking.check_in_date)}</p>
              </div>
              <div>
                <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Departure</p>
                <p className="text-lg font-bold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>{fmtDate(booking.check_out_date)}</p>
              </div>
              <div className="col-span-2 pt-4 border-t" style={{ borderColor: 'rgba(160, 113, 127, 0.1)' }}>
                <div className="flex justify-between items-center">
                  <div>
                    <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Total Billing</p>
                    <p className="text-2xl font-bold" style={{ color: '#EAD3CD' }}>${money(booking.total_amount)}</p>
                  </div>
                  <div className="text-right">
                    <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Outstanding Balance</p>
                    <p className="text-2xl font-bold" style={remainingBalance <= 0 ? { color: '#A0717F' } : { color: '#F87171' }}>
                      ${money(remainingBalance)}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            {remainingBalance > 0 ? (
              <div className="mt-4 rounded-xl p-5" style={{ backgroundColor: 'rgba(239, 68, 68, 0.05)', border: '1px solid rgba(239, 68, 68, 0.2)' }}>
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                  <div>
                    <p className="text-red-400 font-bold mb-1" style={{ fontFamily: "'Georgia', serif" }}>Payment Required</p>
                    <p className="text-red-300 text-sm">Clear the outstanding balance of ${money(remainingBalance)} prior to departure.</p>
                  </div>
                  <Link to={`/receptionist/bookings/${booking.id}/payment`} className="inline-block px-5 py-2.5 rounded-full text-xs font-bold uppercase transition-all duration-300 shrink-0"
                    style={{ backgroundColor: 'transparent', border: '1px solid #F87171', color: '#F87171', letterSpacing: '0.1em' }}>
                    Record Payment
                  </Link>
                </div>
              </div>
            ) : (
              <div className="mt-4 rounded-xl p-5" style={{ backgroundColor: 'rgba(34, 197, 94, 0.05)', border: '1px solid rgba(34, 197, 94, 0.2)' }}>
                <p className="text-green-400 font-bold mb-1" style={{ fontFamily: "'Georgia', serif" }}>Payment Settled</p>
                <p className="text-green-300 text-sm">Guest account is cleared. Safe to finalize checkout.</p>
              </div>
            )}
          </div>

          <div className="mb-10">
            <h3 className="text-xl font-bold mb-6 flex items-center gap-3" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>
              <Icon name="building" className="w-6 h-6" style={{ color: '#A0717F' }} /> Suites to Release
            </h3>
            <div className="space-y-4">
              {booking.booking_items.map((item) => (
                <div key={item.id} className="flex items-center justify-between p-5 rounded-xl border transition-colors duration-300"
                  style={{ backgroundColor: '#2E2530', borderColor: 'rgba(160, 113, 127, 0.1)' }}>
                  <div>
                    <p className="text-xl font-bold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Suite {item.room.room_number}</p>
                    <p className="text-sm mt-1" style={{ color: '#CFCBCA' }}>{item.room.room_type} &middot; Up to {item.room.capacity} Guests</p>
                  </div>
                  <div className="text-right">
                    <p className="text-lg font-bold" style={{ color: '#A0717F' }}>
                      ${money(item.price_per_night, 0)}<span className="text-xs font-normal" style={{ color: 'rgba(207, 203, 202, 0.5)' }}>/nt</span>
                    </p>
                    <span className="inline-block mt-2 px-3 py-1 text-xs font-medium uppercase tracking-wider rounded border"
                      style={item.room.status === 'Occupied'
                        ? { background: 'rgba(239, 68, 68, 0.05)', borderColor: 'rgba(239, 68, 68, 0.2)', color: '#F87171' }
                        : { background: 'rgba(207, 203, 202, 0.1)', borderColor: 'rgba(207, 203, 202, 0.2)', color: '#CFCBCA' }}>
                      State: {item.room.status}
                    </span>
                  </div>
                </div>
              ))}
            </div>
          </div>

          <form onSubmit={submit}>
            <div className="mb-10">
              <h3 className="text-xl font-bold mb-4 flex items-center gap-3" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>
                <Icon name="user" className="w-6 h-6" style={{ color: '#A0717F' }} /> Departure Formalities
              </h3>

              {booking.notes && (
                <div className="mb-4 rounded-xl p-5" style={{ backgroundColor: 'rgba(160, 113, 127, 0.05)', border: '1px solid rgba(160, 113, 127, 0.2)' }}>
                  <p className="text-xs font-medium uppercase mb-2" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Existing Notes</p>
                  <p className="text-sm leading-relaxed" style={{ color: '#EAD3CD' }}>{booking.notes}</p>
                </div>
              )}

              <div className="space-y-4 mt-6">
                <label className="block text-xs font-semibold uppercase" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>
                  Add Checkout Notes (Optional)
                </label>
                <textarea maxLength={500} rows={3} value={notes} onChange={(e) => setNotes(e.target.value)}
                  placeholder="Room condition, items to retrieve, feedback gathered..."
                  className="w-full px-5 py-4 rounded-xl focus:outline-none transition-all duration-300"
                  style={{ backgroundColor: '#2E2530', color: '#EAD3CD', border: '1px solid rgba(160, 113, 127, 0.2)', resize: 'none' }} />
              </div>
            </div>

            <div className="flex flex-col sm:flex-row gap-4 pt-8" style={{ borderTop: '1px solid rgba(160, 113, 127, 0.1)' }}>
              {remainingBalance > 0 ? (
                <button type="submit" disabled className="flex-1 py-4 rounded-full text-sm font-bold uppercase transition-all duration-300 text-center cursor-not-allowed opacity-50"
                  style={{ backgroundColor: '#4E3B46', color: '#CFCBCA', letterSpacing: '0.12em' }}>
                  Confirm Departure (Payment Required)
                </button>
              ) : (
                <button type="submit" className="flex-1 py-4 rounded-full text-sm font-bold uppercase transition-all duration-300 text-center"
                  style={{ backgroundColor: 'transparent', border: '1px solid #A0717F', color: '#EAD3CD', letterSpacing: '0.12em', boxShadow: '0 4px 15px rgba(160, 113, 127, 0.3)' }}>
                  Confirm Departure
                </button>
              )}
              <Link to="/receptionist/check-out" className="flex-1 py-4 rounded-full text-sm font-semibold uppercase transition-all duration-300 text-center"
                style={{ backgroundColor: 'transparent', border: '1px solid rgba(207, 203, 202, 0.2)', color: '#CFCBCA', letterSpacing: '0.12em' }}>
                Dismiss
              </Link>
            </div>
          </form>
        </div>
      </div>
    </Layout>
  )
}
