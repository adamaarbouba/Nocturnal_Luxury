import { useEffect, useState } from 'react'
import { Link, useNavigate, useParams } from 'react-router-dom'
import api from '../../../lib/api'
import Layout from '../../../components/Layout'
import { money, nightsBetween } from './../fmt'

export default function GuestBookingsCreate() {
  const { id } = useParams()
  const navigate = useNavigate()
  const [room, setRoom] = useState(null)
  const [form, setForm] = useState({ check_in_date: '', check_out_date: '', special_requests: '' })
  const [errors, setErrors] = useState({})
  const [submitting, setSubmitting] = useState(false)

  useEffect(() => {
    api.get(`/guest/rooms/${id}/book`).then(res => setRoom(res.data.room)).catch(() => setRoom(false))
  }, [id])

  if (room === null) return <Layout><p className="text-[#CFCBCA]">Loading…</p></Layout>
  if (room === false) return <Layout><p className="text-[#CFCBCA]">Room not found.</p></Layout>

  const nights = form.check_in_date && form.check_out_date ? Math.max(nightsBetween(form.check_in_date, form.check_out_date), 0) : 0
  const total = nights * Number(room.price_per_night)

  const submit = async (e) => {
    e.preventDefault()
    setSubmitting(true)
    setErrors({})
    try {
      const res = await api.post(`/guest/rooms/${id}/book`, form)
      navigate(`/guest/bookings/${res.data.booking.id}/confirmation`)
    } catch (err) {
      setErrors(err.response?.data?.errors ?? {})
    } finally {
      setSubmitting(false)
    }
  }

  const tomorrow = new Date(Date.now() + 86400000).toISOString().slice(0, 10)
  const dayAfter = new Date(Date.now() + 2 * 86400000).toISOString().slice(0, 10)

  return (
    <Layout>
      <div className="max-w-7xl mx-auto px-4 py-12">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Booking Form */}
          <div className="lg:col-span-2">
            <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-8">
              <h2 className="text-2xl font-bold text-[#EAD3CD] mb-6">Booking Details</h2>

              <form onSubmit={submit} className="space-y-6">
                <div className="bg-[#2A2729] border border-[#4E3B46] p-4 rounded-lg mb-6">
                  <p className="text-sm text-[#CFCBCA]">Hotel</p>
                  <h3 className="text-lg font-semibold text-[#EAD3CD]">{room.hotel?.name}</h3>
                  <p className="text-sm text-[#CFCBCA] mt-1">Room {room.room_number} - {room.room_type}</p>
                </div>

                <div>
                  <label htmlFor="check_in_date" className="block text-sm font-medium text-[#EAD3CD] mb-2">
                    Check-in Date <span className="text-red-400">*</span>
                  </label>
                  <input type="date" id="check_in_date" min={tomorrow} value={form.check_in_date}
                    onChange={e => setForm({ ...form, check_in_date: e.target.value })}
                    className={`w-full px-4 py-2 border bg-[#2A2729] text-[#EAD3CD] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#A0717F] ${errors.check_in_date ? 'border-red-500' : 'border-[#4E3B46]'}`} />
                  {errors.check_in_date && <p className="text-red-400 text-sm mt-1">{errors.check_in_date[0]}</p>}
                </div>

                <div>
                  <label htmlFor="check_out_date" className="block text-sm font-medium text-[#EAD3CD] mb-2">
                    Check-out Date <span className="text-red-400">*</span>
                  </label>
                  <input type="date" id="check_out_date" min={dayAfter} value={form.check_out_date}
                    onChange={e => setForm({ ...form, check_out_date: e.target.value })}
                    className={`w-full px-4 py-2 border bg-[#2A2729] text-[#EAD3CD] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#A0717F] ${errors.check_out_date ? 'border-red-500' : 'border-[#4E3B46]'}`} />
                  {errors.check_out_date && <p className="text-red-400 text-sm mt-1">{errors.check_out_date[0]}</p>}
                </div>

                <div>
                  <label htmlFor="special_requests" className="block text-sm font-medium text-[#EAD3CD] mb-2">
                    Special Requests
                  </label>
                  <textarea id="special_requests" rows={4} maxLength={500}
                    placeholder="Any special requests or preferences? (e.g., high floor, near elevator, etc.)"
                    value={form.special_requests}
                    onChange={e => setForm({ ...form, special_requests: e.target.value })}
                    className="w-full px-4 py-2 border border-[#4E3B46] bg-[#2A2729] text-[#EAD3CD] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#A0717F]" />
                  <p className="text-xs text-[#CFCBCA] mt-1">Optional - max 500 characters</p>
                </div>

                <div className="flex gap-4">
                  <button type="submit" disabled={submitting}
                    className="flex-1 bg-[#A0717F] hover:bg-[#8F6470] text-[#EAD3CD] px-6 py-3 rounded-lg font-semibold transition disabled:opacity-50">
                    {submitting ? 'Booking…' : 'Confirm Booking'}
                  </button>
                  <Link to={`/guest/hotels/${room.hotel_id}`}
                    className="flex-1 bg-[#4E3B46] hover:bg-[#68525F] text-[#EAD3CD] border border-[#4E3B46] px-6 py-3 rounded-lg font-semibold text-center transition">
                    Cancel
                  </Link>
                </div>
              </form>
            </div>
          </div>

          {/* Booking Summary Sidebar */}
          <div className="lg:col-span-1">
            <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-8 sticky top-4">
              <h3 className="text-xl font-bold text-[#EAD3CD] mb-6">Booking Summary</h3>

              <div className="space-y-4 pb-6 border-b border-[#4E3B46]">
                <div className="flex justify-between">
                  <span className="text-[#CFCBCA]">Room Type</span>
                  <span className="font-semibold text-[#EAD3CD]">{room.room_type}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-[#CFCBCA]">Room No.</span>
                  <span className="font-semibold text-[#EAD3CD]">{room.room_number}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-[#CFCBCA]">Capacity</span>
                  <span className="font-semibold text-[#EAD3CD]">{room.capacity} Guests</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-[#CFCBCA]">Price/Night</span>
                  <span className="font-semibold text-[#A0717F]">${money(room.price_per_night)}</span>
                </div>
              </div>

              <div className="space-y-4 py-6 border-b border-[#4E3B46]">
                <div>
                  <p className="text-sm text-[#CFCBCA] mb-1">Check-in</p>
                  <p className="font-semibold text-[#EAD3CD]">{form.check_in_date ? new Date(form.check_in_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '-'}</p>
                </div>
                <div>
                  <p className="text-sm text-[#CFCBCA] mb-1">Check-out</p>
                  <p className="font-semibold text-[#EAD3CD]">{form.check_out_date ? new Date(form.check_out_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '-'}</p>
                </div>
                <div>
                  <p className="text-sm text-[#CFCBCA] mb-1">Number of Nights</p>
                  <p className="font-semibold text-[#EAD3CD]">{nights} nights</p>
                </div>
              </div>

              <div className="pt-6 text-center">
                <p className="text-sm text-[#CFCBCA] mb-1">Total Cost</p>
                <p className="text-3xl font-bold text-[#A0717F]">${money(nights > 0 ? total : room.price_per_night)}</p>
                <p className="text-xs text-[#CFCBCA] mt-2">{nights} nights × ${money(room.price_per_night)}/night</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  )
}
