import { useEffect, useState } from 'react'
import { Link, useNavigate, useParams } from 'react-router-dom'
import Layout from '../../components/Layout'
import Alert from '../../components/Alert'
import Icon from '../../components/Icon'
import api from '../../lib/api'
import { money, fmtDate, paymentBadgeStyle } from './util'

export default function CheckInShow() {
  const { id } = useParams()
  const [data, setData] = useState(null)
  const [notes, setNotes] = useState('')
  const [error, setError] = useState(null)
  const navigate = useNavigate()

  useEffect(() => {
    api.get(`/receptionist/check-in/${id}`).then((res) => {
      setData(res.data)
      setNotes(res.data.booking.notes ?? '')
    })
  }, [id])

  if (!data) return <Layout><div /></Layout>
  const { booking, hotel } = data

  const submit = async (e) => {
    e.preventDefault()
    setError(null)
    try {
      const res = await api.post(`/receptionist/check-in/${id}`, { notes })
      navigate('/receptionist/check-in', { state: { success: res.data.message } })
    } catch (err) {
      setError(err.response?.data?.message ?? 'Error during check-in.')
    }
  }

  return (
    <Layout>
      <div className="container mx-auto px-4 py-8 max-w-3xl relative z-10">
        <div className="mb-10 text-center">
          <p className="text-xs font-medium uppercase mb-2" style={{ color: '#A0717F', letterSpacing: '0.4em' }}>{hotel.name}</p>
          <h1 className="text-3xl lg:text-5xl font-bold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Arrival Procedure</h1>
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
                    <p className="text-xs font-medium uppercase mb-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Outstanding Balance</p>
                    <p className="text-2xl font-bold" style={{ color: '#A0717F' }}>${money(booking.total_amount)}</p>
                  </div>
                  <span className="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider border" style={paymentBadgeStyle(booking.payment_status)}>
                    {booking.payment_status}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div className="mb-10">
            <h3 className="text-xl font-bold mb-6 flex items-center gap-3" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>
              <Icon name="building" className="w-6 h-6" style={{ color: '#A0717F' }} /> Pre-Cleared Suites
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
                      style={item.room.status === 'Reserved'
                        ? { background: 'rgba(59, 130, 246, 0.1)', borderColor: 'rgba(59, 130, 246, 0.3)', color: '#60A5FA' }
                        : { background: 'rgba(207, 203, 202, 0.1)', borderColor: 'rgba(207, 203, 202, 0.2)', color: '#CFCBCA' }}>
                      Status: {item.room.status}
                    </span>
                  </div>
                </div>
              ))}
            </div>
          </div>

          <form onSubmit={submit}>
            <div className="mb-10">
              <h3 className="text-xl font-bold mb-4 flex items-center gap-3" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>
                <Icon name="check" className="w-6 h-6" style={{ color: '#A0717F' }} /> Arrival Formalities
              </h3>
              <div className="space-y-4">
                <label className="block text-xs font-semibold uppercase" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>
                  Administrative Notes (Optional)
                </label>
                <textarea maxLength={500} rows={3} value={notes} onChange={(e) => setNotes(e.target.value)}
                  placeholder="Luggage assistance required, late arrival verified, etc."
                  className="w-full px-5 py-4 rounded-xl focus:outline-none transition-all duration-300"
                  style={{ backgroundColor: '#2E2530', color: '#EAD3CD', border: '1px solid rgba(160, 113, 127, 0.2)', resize: 'none' }} />
              </div>
            </div>

            <div className="flex flex-col sm:flex-row gap-4 pt-6" style={{ borderTop: '1px solid rgba(160, 113, 127, 0.1)' }}>
              <button type="submit" className="flex-1 py-4 rounded-full text-sm font-bold uppercase transition-all duration-300 text-center"
                style={{ backgroundColor: '#A0717F', color: '#FFFFFF', letterSpacing: '0.12em', boxShadow: '0 4px 15px rgba(160, 113, 127, 0.3)' }}>
                Confirm Check-In
              </button>
              <Link to="/receptionist/check-in" className="flex-1 py-4 rounded-full text-sm font-semibold uppercase transition-all duration-300 text-center"
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
