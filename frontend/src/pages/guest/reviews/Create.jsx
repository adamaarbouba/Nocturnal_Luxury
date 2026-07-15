import { useEffect, useState } from 'react'
import { Link, useNavigate, useParams } from 'react-router-dom'
import api from '../../../lib/api'
import Layout from '../../../components/Layout'
import Breadcrumbs from '../../../components/Breadcrumbs'

const statusLabels = { 1: 'Substandard', 2: 'Balanced', 3: 'Distinguished', 4: 'Curated', 5: 'Exquisite' }

export default function GuestReviewsCreate() {
  const { id } = useParams()
  const navigate = useNavigate()
  const [booking, setBooking] = useState(null)
  const [rating, setRating] = useState(0)
  const [hoverRating, setHoverRating] = useState(0)
  const [comment, setComment] = useState('')
  const [errors, setErrors] = useState({})
  const [submitting, setSubmitting] = useState(false)

  useEffect(() => {
    api.get(`/guest/bookings/${id}/review`).then(res => {
      if (res.data.existingReviewId) {
        navigate(`/guest/reviews/${res.data.existingReviewId}`)
        return
      }
      setBooking(res.data.booking)
    }).catch(() => setBooking(false))
  }, [id])

  if (booking === null) return <Layout><p className="text-[#CFCBCA]">Loading…</p></Layout>
  if (booking === false) return <Layout><p className="text-[#CFCBCA]">Booking not found.</p></Layout>

  const submit = async (e) => {
    e.preventDefault()
    setSubmitting(true)
    setErrors({})
    try {
      const res = await api.post(`/guest/bookings/${id}/review`, { rating, comment })
      navigate(`/guest/reviews/${res.data.review.id}`)
    } catch (err) {
      setErrors(err.response?.data?.errors ?? {})
    } finally {
      setSubmitting(false)
    }
  }

  const displayed = hoverRating || rating

  return (
    <Layout>
      <div className="max-w-6xl mx-auto px-6 py-12">
        <div className="mb-12">
          <Breadcrumbs links={[
            { label: 'My Bookings', url: '/guest/bookings' },
            { label: 'Curate Your Experience', url: '#' },
          ]} />
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
          <div className="lg:col-span-4 space-y-8 lg:sticky lg:top-8">
            <header className="space-y-4">
              <p className="text-xs font-bold uppercase tracking-[0.3em] text-[#A0717F]">Perspective</p>
              <h1 className="text-4xl lg:text-5xl font-bold font-serif text-[#EAD3CD] leading-tight">Curate Your <br />Stay Experience</h1>
              <p className="text-[#CFCBCA] leading-relaxed max-w-xs text-sm">
                Refine the standards of luxury at <span className="text-[#EAD3CD] font-semibold underline decoration-[#A0717F]/30">{booking.hotel?.name}</span> through your unique lens.
              </p>
            </header>

            <div className="p-8 rounded-2xl bg-[#383537] border border-[#4E3B46] shadow-xl space-y-6">
              <div>
                <p className="text-[10px] font-bold uppercase tracking-widest text-[#A0717F] mb-1">Reservation Details</p>
                <h3 className="text-xl font-bold font-serif text-[#EAD3CD]">{booking.hotel?.name}</h3>
              </div>

              <div className="grid grid-cols-2 gap-4 pt-6 border-t border-[#4E3B46]">
                <div className="space-y-1">
                  <p className="text-[9px] uppercase tracking-widest text-[#CFCBCA]">Booking ID</p>
                  <p className="text-xs text-[#EAD3CD]">#{booking.id}</p>
                </div>
                <div className="space-y-1">
                  <p className="text-[9px] uppercase tracking-widest text-[#CFCBCA]">Period</p>
                  <p className="text-xs text-[#EAD3CD]">
                    {new Date(booking.check_in_date).toLocaleDateString('en-US', { month: 'short', day: '2-digit' })} — {new Date(booking.check_out_date).toLocaleDateString('en-US', { day: '2-digit', year: 'numeric' })}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div className="lg:col-span-8 space-y-12">
            <form onSubmit={submit} className="space-y-16">
              <section className="space-y-10">
                <div className="flex items-center gap-4">
                  <span className="w-8 h-[1px] bg-[#A0717F]"></span>
                  <h2 className="text-xl font-bold font-serif text-[#EAD3CD]">Overall Impression</h2>
                </div>

                <div className="p-10 rounded-2xl bg-[#2A2729] border border-[#4E3B46] shadow-2xl transition-all duration-500 hover:border-[#A0717F]/30">
                  <div className="flex flex-col items-center gap-8">
                    <div className="flex gap-4">
                      {[1, 2, 3, 4, 5].map(i => (
                        <button key={i} type="button"
                          onMouseEnter={() => setHoverRating(i)}
                          onMouseLeave={() => setHoverRating(0)}
                          onClick={() => setRating(i)}
                          className="group transform transition-all duration-300 hover:scale-110">
                          <svg
                            className="w-12 h-12 stroke-[1.5] transition-all duration-500 pointer-events-none"
                            viewBox="0 0 24 24"
                            fill={i <= displayed ? '#A0717F' : 'transparent'}
                            stroke={i <= displayed ? '#A0717F' : '#4E3B46'}
                            style={i <= displayed ? { filter: 'drop-shadow(0 0 10px rgba(160, 113, 127, 0.4))' } : undefined}
                          >
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                          </svg>
                        </button>
                      ))}
                    </div>
                    <div className="text-2xl font-serif italic text-[#EAD3CD] transition-all duration-500" style={{ opacity: displayed ? 1 : 0.4 }}>
                      {statusLabels[displayed] ?? 'Select Rating'}
                    </div>
                  </div>
                </div>
                {errors.rating && <p className="text-xs text-red-400 italic mt-2">{errors.rating[0]}</p>}
              </section>

              <section className="space-y-10">
                <div className="flex items-center gap-4">
                  <span className="w-8 h-[1px] bg-[#A0717F]"></span>
                  <h2 className="text-xl font-bold font-serif text-[#EAD3CD]">Detailed Feedback</h2>
                </div>

                <div className="space-y-4">
                  <textarea rows={8} maxLength={1000} value={comment} onChange={e => setComment(e.target.value)}
                    placeholder="Reflect on the nuances of your journey..."
                    className="w-full bg-[#383537] border border-[#4E3B46] text-[#EAD3CD] rounded-2xl p-8 focus:outline-none focus:border-[#A0717F] transition-all duration-500 placeholder-[#CFCBCA]/20 text-lg leading-relaxed shadow-inner" />
                  <div className="flex justify-between items-center text-[10px] font-bold uppercase tracking-[0.3em] text-[#CFCBCA]/40">
                    <span>{comment.length} / 1000 characters</span>
                    {errors.comment && <span className="text-red-400">{errors.comment[0]}</span>}
                  </div>
                </div>
              </section>

              <div className="pt-8 flex flex-col sm:flex-row items-center gap-6">
                <button type="submit" disabled={submitting}
                  className="w-full sm:w-auto bg-[#A0717F] hover:bg-[#8F6470] text-white font-bold uppercase tracking-[0.4em] text-xs px-16 py-5 rounded-xl shadow-xl transition-all duration-500 transform hover:-translate-y-1 active:scale-95 disabled:opacity-50">
                  {submitting ? 'Submitting…' : 'Submit Review'}
                </button>
                <Link to="/guest/bookings"
                  className="text-[10px] font-bold uppercase tracking-[0.4em] text-[#CFCBCA] hover:text-[#EAD3CD] transition-all duration-500">
                  Cancel
                </Link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </Layout>
  )
}
