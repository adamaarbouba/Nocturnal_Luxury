import { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import Layout from '../../components/Layout'
import Alert from '../../components/Alert'
import api from '../../lib/api'
import { money } from './util'

const inputStyle = { backgroundColor: '#4E3B46', color: '#EAD3CD', border: '1px solid rgba(160, 113, 127, 0.2)' }
const inputClass = 'w-full px-4 py-3 rounded-lg focus:outline-none transition-all duration-300'

export default function BookingsCreate() {
  const [hotel, setHotel] = useState(null)
  const [availableRooms, setAvailableRooms] = useState([])
  const [form, setForm] = useState({
    guest_name: '', guest_email: '', guest_phone: '', guest_address: '',
    check_in_date: '', check_out_date: '', room_ids: [], special_requests: '', notes: '',
  })
  const [errors, setErrors] = useState({})
  const [error, setError] = useState(null)
  const navigate = useNavigate()

  useEffect(() => {
    api.get('/receptionist/bookings/create').then((res) => {
      setHotel(res.data.hotel)
      setAvailableRooms(res.data.availableRooms)
    })
  }, [])

  const set = (key) => (e) => setForm((f) => ({ ...f, [key]: e.target.value }))

  const toggleRoom = (id) =>
    setForm((f) => ({
      ...f,
      room_ids: f.room_ids.includes(id) ? f.room_ids.filter((r) => r !== id) : [...f.room_ids, id],
    }))

  const today = new Date().toISOString().slice(0, 10)
  const tomorrow = new Date(Date.now() + 86400000).toISOString().slice(0, 10)

  const submit = async (e) => {
    e.preventDefault()
    setErrors({})
    setError(null)
    try {
      const res = await api.post('/receptionist/bookings', form)
      navigate(`/receptionist/bookings/${res.data.booking.id}`, { state: { success: res.data.message } })
    } catch (err) {
      if (err.response?.status === 422) setErrors(err.response.data.errors ?? {})
      else setError(err.response?.data?.message ?? 'Error creating booking.')
    }
  }

  if (!hotel) return <Layout><div /></Layout>

  return (
    <Layout>
      <div className="fixed top-20 right-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none"
        style={{ background: 'rgba(160, 113, 127, 0.04)', zIndex: 0 }}></div>

      <div className="max-w-4xl mx-auto px-4 py-8 relative z-10">
        <div className="flex items-center gap-3 mb-8 text-xs font-medium uppercase" style={{ letterSpacing: '0.1em' }}>
          <Link to="/receptionist/dashboard" style={{ color: 'rgba(207, 203, 202, 0.5)' }}>Dashboard</Link>
          <span style={{ color: '#4E3B46' }}>/</span>
          <Link to="/receptionist/bookings" style={{ color: 'rgba(207, 203, 202, 0.5)' }}>Bookings</Link>
          <span style={{ color: '#4E3B46' }}>/</span>
          <span style={{ color: '#A0717F' }}>Create New</span>
        </div>

        <div className="mb-10">
          <h2 className="text-3xl lg:text-5xl font-bold mb-2" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Create Booking</h2>
          <p className="text-sm italic" style={{ color: '#CFCBCA' }}>Secure accommodations for discerning guests.</p>
        </div>

        {error && <div className="mb-6"><Alert variant="error">{error}</Alert></div>}

        <div className="rounded-2xl shadow-2xl p-8 lg:p-12" style={{ backgroundColor: '#383537', borderTop: '1px solid rgba(234, 211, 205, 0.1)' }}>
          <form onSubmit={submit}>
            {/* Guest Information */}
            <div className="mb-12">
              <h3 className="text-2xl font-bold mb-8" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Guest Details</h3>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div className="space-y-2">
                  <label className="block text-xs font-semibold uppercase" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>Full Name</label>
                  <input type="text" required value={form.guest_name} onChange={set('guest_name')} className={inputClass} style={inputStyle} placeholder="e.g. Julian Vanderbilt" />
                  {errors.guest_name && <p className="text-red-400 text-xs mt-1">{errors.guest_name[0]}</p>}
                </div>
                <div className="space-y-2">
                  <label className="block text-xs font-semibold uppercase" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>Email Address</label>
                  <input type="email" required value={form.guest_email} onChange={set('guest_email')} className={inputClass} style={inputStyle} placeholder="julian@example.com" />
                  {errors.guest_email && <p className="text-red-400 text-xs mt-1">{errors.guest_email[0]}</p>}
                </div>
                <div className="space-y-2">
                  <label className="block text-xs font-semibold uppercase" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>Private Line</label>
                  <input type="text" required value={form.guest_phone} onChange={set('guest_phone')} className={inputClass} style={inputStyle} placeholder="+1 555-0199" />
                  {errors.guest_phone && <p className="text-red-400 text-xs mt-1">{errors.guest_phone[0]}</p>}
                </div>
                <div className="space-y-2">
                  <label className="block text-xs font-semibold uppercase" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>Origin Estate (Address)</label>
                  <input type="text" required value={form.guest_address} onChange={set('guest_address')} className={inputClass} style={inputStyle} placeholder="123 Serenity Blvd, NY" />
                  {errors.guest_address && <p className="text-red-400 text-xs mt-1">{errors.guest_address[0]}</p>}
                </div>
              </div>
            </div>

            <hr className="mb-12 border-t" style={{ borderColor: 'rgba(160, 113, 127, 0.1)' }} />

            {/* Dates */}
            <div className="mb-12">
              <h3 className="text-2xl font-bold mb-8" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Duration of Stay</h3>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div className="space-y-2">
                  <label className="block text-xs font-semibold uppercase" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>Arrival</label>
                  <input type="date" required min={today} value={form.check_in_date} onChange={set('check_in_date')} className={inputClass} style={{ ...inputStyle, colorScheme: 'dark' }} />
                  {errors.check_in_date && <p className="text-red-400 text-xs mt-1">{errors.check_in_date[0]}</p>}
                </div>
                <div className="space-y-2">
                  <label className="block text-xs font-semibold uppercase" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>Departure</label>
                  <input type="date" required min={tomorrow} value={form.check_out_date} onChange={set('check_out_date')} className={inputClass} style={{ ...inputStyle, colorScheme: 'dark' }} />
                  {errors.check_out_date && <p className="text-red-400 text-xs mt-1">{errors.check_out_date[0]}</p>}
                </div>
              </div>
            </div>

            <hr className="mb-12 border-t" style={{ borderColor: 'rgba(160, 113, 127, 0.1)' }} />

            {/* Rooms */}
            <div className="mb-12">
              <h3 className="text-2xl font-bold mb-8" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Suite Selection</h3>
              {availableRooms.length === 0 ? (
                <div className="rounded-xl p-6 text-center" style={{ backgroundColor: 'rgba(239, 68, 68, 0.05)', border: '1px solid rgba(239, 68, 68, 0.2)' }}>
                  <p className="text-red-400 italic font-medium">No suites available for the selected parameters.</p>
                </div>
              ) : (
                <>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                    {availableRooms.map((room) => {
                      const checked = form.room_ids.includes(room.id)
                      return (
                        <label key={room.id} className="block relative rounded-xl p-5 cursor-pointer transition-all duration-300"
                          style={{ backgroundColor: checked ? '#2E2530' : '#4E3B46', border: `1px solid ${checked ? '#A0717F' : 'rgba(160, 113, 127, 0.15)'}` }}>
                          <div className="flex items-start gap-4">
                            <div className="flex items-center h-6">
                              <input type="checkbox" checked={checked} onChange={() => toggleRoom(room.id)} className="w-5 h-5 rounded" style={{ accentColor: '#A0717F' }} />
                            </div>
                            <div>
                              <p className="text-xl font-bold mb-1" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Suite {room.room_number}</p>
                              <p className="text-xs font-medium uppercase mb-3" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.1em' }}>{room.room_type}</p>
                              <div className="flex items-center gap-4 text-sm mt-3 pt-3" style={{ borderTop: '1px solid rgba(234, 211, 205, 0.05)' }}>
                                <span style={{ color: '#CFCBCA' }}>{room.capacity} Guests cap.</span>
                                <span className="font-bold" style={{ color: '#A0717F' }}>${money(room.price_per_night, 0)} / night</span>
                              </div>
                            </div>
                          </div>
                        </label>
                      )
                    })}
                  </div>
                  {errors.room_ids && <p className="text-red-400 text-xs mt-2">{errors.room_ids[0]}</p>}
                </>
              )}
            </div>

            <hr className="mb-12 border-t" style={{ borderColor: 'rgba(160, 113, 127, 0.1)' }} />

            {/* Requests & Notes */}
            <div className="mb-12">
              <h3 className="text-2xl font-bold mb-8" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>Final Preparations</h3>
              <div className="space-y-8">
                <div className="space-y-2">
                  <label className="block text-xs font-semibold uppercase" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>
                    Special Requests <span style={{ fontStyle: 'italic', textTransform: 'none' }}>(Optional)</span>
                  </label>
                  <textarea rows={3} value={form.special_requests} onChange={set('special_requests')} className={inputClass} style={{ ...inputStyle, resize: 'none' }}
                    placeholder="Dietary restrictions, pillow preferences, specific views..." />
                  {errors.special_requests && <p className="text-red-400 text-xs mt-1">{errors.special_requests[0]}</p>}
                </div>
                <div className="space-y-2">
                  <label className="block text-xs font-semibold uppercase" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>
                    Internal Notes <span style={{ fontStyle: 'italic', textTransform: 'none' }}>(Optional)</span>
                  </label>
                  <textarea rows={2} value={form.notes} onChange={set('notes')} className={inputClass} style={{ ...inputStyle, resize: 'none' }} placeholder="Administrative details..." />
                  {errors.notes && <p className="text-red-400 text-xs mt-1">{errors.notes[0]}</p>}
                </div>
              </div>
            </div>

            <div className="flex flex-wrap items-center gap-4 mt-12">
              <button type="submit" className="px-10 py-4 rounded-full text-sm font-bold uppercase transition-all duration-300"
                style={{ backgroundColor: '#A0717F', color: '#FFFFFF', letterSpacing: '0.12em', boxShadow: '0 4px 15px rgba(160, 113, 127, 0.3)' }}>
                Confirm Reservation
              </button>
              <Link to="/receptionist/bookings" className="px-8 py-4 rounded-full text-sm font-semibold uppercase transition-all duration-300"
                style={{ backgroundColor: 'transparent', border: '1px solid rgba(207, 203, 202, 0.2)', color: '#CFCBCA', letterSpacing: '0.12em' }}>
                Cancel
              </Link>
            </div>
          </form>
        </div>
      </div>
    </Layout>
  )
}
