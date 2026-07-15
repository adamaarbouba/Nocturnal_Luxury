import { useEffect, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import api from '../../../lib/api'
import Layout from '../../../components/Layout'
import Breadcrumbs from '../../../components/Breadcrumbs'
import Icon from '../../../components/Icon'
import { fmtDate } from './../fmt'

export default function GuestReviewsShow() {
  const { id } = useParams()
  const [review, setReview] = useState(null)

  useEffect(() => {
    api.get(`/guest/reviews/${id}`).then(res => setReview(res.data.review)).catch(() => setReview(false))
  }, [id])

  if (review === null) return <Layout><p className="text-[#CFCBCA]">Loading…</p></Layout>
  if (review === false) return <Layout><p className="text-[#CFCBCA]">Review not found.</p></Layout>

  const booking = review.booking

  return (
    <Layout>
      <div className="max-w-5xl mx-auto px-6 py-12">
        <div className="mb-12">
          <Breadcrumbs links={[
            { label: 'My Reviews', url: '/guest/reviews' },
            { label: 'Review Details', url: '#' },
          ]} />
        </div>
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12">
          <div className="lg:col-span-4 space-y-8 order-2 lg:order-1">
            <div className="p-8 rounded-2xl bg-[#383537] border border-[#4E3B46] shadow-xl space-y-8 sticky top-8">
              <header className="space-y-2">
                <p className="text-[10px] font-bold uppercase tracking-[0.2em] text-[#A0717F]">Establishment</p>
                <h1 className="text-2xl font-bold font-serif text-[#EAD3CD] leading-tight">{review.hotel?.name}</h1>
                <p className="text-[10px] text-[#CFCBCA] uppercase tracking-widest">{review.hotel?.city}, {review.hotel?.country}</p>
              </header>

              <div className="space-y-6 pt-6 border-t border-[#4E3B46]">
                <div className="flex justify-between items-center">
                  <span className="text-[10px] uppercase tracking-widest text-[#CFCBCA]">Reservation</span>
                  <span className="text-xs font-medium text-[#EAD3CD]">#{review.booking_id}</span>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-[10px] uppercase tracking-widest text-[#CFCBCA]">Stay Period</span>
                  <span className="text-xs font-medium text-[#EAD3CD]">{fmtDate(booking?.check_in_date, { month: 'short', day: '2-digit' })} — {fmtDate(booking?.check_out_date, { day: '2-digit', year: 'numeric' })}</span>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-[10px] uppercase tracking-widest text-[#CFCBCA]">Status</span>
                  <span className={`px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest ${booking?.status === 'completed'
                    ? 'bg-green-500/10 text-green-400 border border-green-500/20'
                    : 'bg-[#2A2729] text-[#CFCBCA] border border-[#4E3B46]'}`}>
                    {booking?.status}
                  </span>
                </div>
              </div>

              <div className="p-5 rounded-xl bg-[#2A2729] border border-[#4E3B46] space-y-3">
                <p className="text-[10px] font-bold uppercase tracking-widest text-[#CFCBCA]/60">Room Segment</p>
                {review.room ? (
                  <div className="flex items-center gap-3">
                    <Icon name="building" size="sm" className="text-[#A0717F]" />
                    <p className="text-xs text-[#EAD3CD] font-medium">Room {review.room.room_number} ({review.room.room_type})</p>
                  </div>
                ) : (
                  <p className="text-xs text-[#CFCBCA] italic">Not specified</p>
                )}
              </div>
            </div>
          </div>

          <div className="lg:col-span-8 space-y-12 order-1 lg:order-2">
            <div className="p-10 lg:p-12 rounded-3xl bg-[#2A2729] border border-[#4E3B46] shadow-2xl relative overflow-hidden">
              <div className="absolute top-0 right-0 p-8 opacity-5">
                <Icon name="sparkles" size="2xl" className="text-[#A0717F]" />
              </div>

              <div className="space-y-12">
                <header className="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                  <div className="space-y-2">
                    <p className="text-xs font-bold uppercase tracking-[0.3em] text-[#A0707F]">Reflections</p>
                    <h2 className="text-4xl font-bold font-serif text-[#EAD3CD]">Overall Impression</h2>
                  </div>
                  <div className="flex flex-col items-end gap-1">
                    <div className="flex gap-1">
                      {[1, 2, 3, 4, 5].map(i => (
                        <Icon key={i} name="star" size="md" className={i <= review.rating ? 'text-[#A0717F] fill-[#A0717F]' : 'text-[#4E3B46] fill-transparent'} />
                      ))}
                    </div>
                    <p className="text-[10px] font-bold uppercase tracking-[0.3em] text-[#CFCBCA] opacity-60">{review.rating} / 5 Excellence</p>
                  </div>
                </header>

                <div className="space-y-6">
                  <p className="text-xl lg:text-2xl font-serif italic text-[#EAD3CD] leading-relaxed relative">
                    <span className="absolute -left-6 -top-4 text-4xl text-[#A0717F]/20 font-serif">&ldquo;</span>
                    {review.comment}
                    <span className="text-4xl text-[#A0717F]/20 font-serif inline-block translate-y-2">&rdquo;</span>
                  </p>
                  <p className="text-[10px] font-bold uppercase tracking-[0.4em] text-[#CFCBCA]/30">Narrative Published on {fmtDate(review.created_at)}</p>
                </div>
              </div>
            </div>

            <div className="flex items-center gap-6 pt-6">
              <Link to="/guest/reviews"
                className="px-10 py-5 rounded-2xl bg-[#A0717F] hover:bg-[#8F6470] text-white font-bold uppercase tracking-[0.3em] text-xs transition-all duration-500 shadow-xl transform hover:-translate-y-1">
                Return to Gallery
              </Link>
              <Link to="/guest/bookings"
                className="px-10 py-5 rounded-2xl border border-[#4E3B46] text-[#CFCBCA] hover:text-[#EAD3CD] hover:bg-[#383537] transition-all duration-500 text-xs font-bold uppercase tracking-widest">
                Manage Bookings
              </Link>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  )
}
