import { useEffect, useState } from 'react'
import { Link, useSearchParams } from 'react-router-dom'
import api from '../../../lib/api'
import Layout from '../../../components/Layout'
import Breadcrumbs from '../../../components/Breadcrumbs'
import Icon from '../../../components/Icon'
import { limit } from './../fmt'

export default function GuestReviewsIndex() {
  const [searchParams, setSearchParams] = useSearchParams()
  const [data, setData] = useState(null)

  useEffect(() => {
    setData(null)
    api.get(`/guest/reviews?${searchParams.toString()}`).then(res => setData(res.data.reviews)).catch(() => setData({ data: [] }))
  }, [searchParams])

  const goPage = (p) => {
    const params = new URLSearchParams(searchParams)
    params.set('page', p)
    setSearchParams(params)
  }

  return (
    <Layout>
      <div className="fixed top-0 left-0 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none" style={{ background: 'rgba(160, 113, 127, 0.05)', zIndex: 0 }}></div>
      <div className="fixed bottom-0 right-0 w-[500px] h-[500px] rounded-full blur-3xl pointer-events-none" style={{ background: 'rgba(234, 211, 205, 0.03)', zIndex: 0 }}></div>

      <div className="max-w-6xl mx-auto px-6 py-12 relative z-10">
        <div className="mb-12">
          <Breadcrumbs links={[
            { label: 'Guest Dashboard', url: '/guest/dashboard' },
            { label: 'Review Gallery', url: '#' },
          ]} />
        </div>

        <div className="mb-12 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-b border-[rgba(234,211,205,0.05)] pb-6">
          <div>
            <p className="text-xs font-bold uppercase tracking-[0.3em] text-[#A0717F] mb-3">Perspectives</p>
            <h1 className="text-4xl lg:text-5xl font-bold font-serif text-[#EAD3CD] leading-tight">My Review Gallery</h1>
            <p className="text-xs uppercase mt-4" style={{ color: 'rgba(207, 203, 202, 0.6)', letterSpacing: '0.15em' }}>
              Your curated reflections on past stays
            </p>
          </div>
          <Link to="/guest/bookings"
            className="inline-flex items-center gap-2 px-8 py-4 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all duration-300 shadow-xl shrink-0 bg-[#A0717F] text-white hover:bg-[#b58290] hover:-translate-y-0.5">
            <Icon name="building" size="sm" className="w-4 h-4" />
            Review More Stays
          </Link>
        </div>

        {!data ? (
          <p className="text-[#CFCBCA]">Loading…</p>
        ) : data.data.length > 0 ? (
          <>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
              {data.data.map(review => (
                <div key={review.id} className="group bg-[#2A2729] border border-[#4E3B46] rounded-3xl shadow-xl hover:shadow-2xl hover:border-[#A0717F]/50 transition-all duration-500 overflow-hidden relative flex flex-col">
                  <div className="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-[#A0717F]/10 to-transparent rounded-bl-full pointer-events-none transition-all duration-500 group-hover:from-[#A0717F]/20"></div>

                  <div className="p-8 flex-1 flex flex-col">
                    <div className="flex justify-between items-start mb-6">
                      <div className="space-y-1">
                        <p className="text-[9px] font-bold uppercase tracking-[0.2em] text-[#A0717F]">Establishment</p>
                        <h3 className="text-2xl font-bold font-serif text-[#EAD3CD]">{review.hotel?.name}</h3>
                        <p className="text-[10px] text-[#CFCBCA] uppercase tracking-widest opacity-70">
                          {review.hotel?.city}, {review.hotel?.country}
                        </p>
                      </div>
                      <div className="text-right flex flex-col items-end gap-2">
                        <div className="flex gap-1">
                          {[1, 2, 3, 4, 5].map(i => (
                            <Icon key={i} name="star" size="sm" className={`${i <= review.rating ? 'text-[#A0717F] fill-[#A0717F]' : 'text-[#4E3B46] fill-transparent'} transition-colors duration-300`} />
                          ))}
                        </div>
                        <span className="text-[9px] font-bold uppercase tracking-widest text-[#EAD3CD]">{review.rating}.0 Excellence</span>
                      </div>
                    </div>

                    <div className="mb-8 flex-1">
                      <p className="text-[#CFCBCA] text-sm leading-relaxed italic relative pl-4 border-l-2 border-[#A0717F]/30">
                        "{limit(review.comment, 150)}"
                      </p>
                    </div>

                    <div className="flex items-end justify-between pt-6 border-t border-[#4E3B46]/50">
                      <div className="space-y-2">
                        {review.room && (
                          <div className="flex items-center gap-2">
                            <Icon name="building" size="xs" className="text-[#A0717F]" />
                            <p className="text-[10px] font-medium text-[#CFCBCA] uppercase tracking-widest">
                              Room {review.room.room_number} <span className="opacity-50">({review.room.room_type})</span>
                            </p>
                          </div>
                        )}
                        <div className="flex items-center gap-2">
                          <Icon name="calendar" size="xs" className="text-[#A0717F]" />
                          <p className="text-[9px] font-bold uppercase tracking-[0.2em] text-[#CFCBCA] opacity-60">
                            Published {new Date(review.created_at).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })}
                          </p>
                        </div>
                      </div>

                      <Link to={`/guest/reviews/${review.id}`}
                        className="inline-flex items-center justify-center w-10 h-10 rounded-full bg-[#383537] border border-[#4E3B46] text-[#A0717F] group-hover:bg-[#A0717F] group-hover:text-white transition-all duration-300 transform group-hover:scale-110 shadow-lg">
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7"></path></svg>
                      </Link>
                    </div>
                  </div>
                </div>
              ))}
            </div>

            {data.last_page > 1 && (
              <div className="mt-12 flex justify-center gap-2 flex-wrap">
                {Array.from({ length: data.last_page }, (_, i) => i + 1).map(p => (
                  <button key={p} type="button" onClick={() => goPage(p)}
                    className={`min-w-10 h-10 px-3 text-xs font-bold uppercase tracking-widest border rounded-xl transition-all duration-300 ${p === data.current_page
                      ? 'bg-[#A0717F] text-white border-[#A0717F]'
                      : 'text-[#CFCBCA] bg-[#2A2729] border-[#4E3B46] hover:bg-[#A0717F] hover:text-white'}`}>
                    {p}
                  </button>
                ))}
              </div>
            )}
          </>
        ) : (
          <div className="rounded-3xl shadow-2xl p-16 text-center border border-[rgba(234,211,205,0.1)] mt-12 relative overflow-hidden" style={{ backgroundColor: '#2A2729' }}>
            <div className="relative z-10">
              <div className="w-20 h-20 mx-auto rounded-full bg-[#383537] border border-[#4E3B46] flex items-center justify-center mb-6 shadow-xl">
                <Icon name="star" className="w-8 h-8 text-[#A0717F]" />
              </div>
              <h3 className="text-2xl font-bold font-serif text-[#EAD3CD] mb-4">Your Gallery is Empty</h3>
              <p className="text-xs uppercase tracking-widest mb-10 leading-loose mx-auto" style={{ color: 'rgba(207, 203, 202, 0.5)', maxWidth: '28rem' }}>
                You have not yet contributed any reflections on your experiences.
                Your insights help refine the Nocturnal Luxury standard.
              </p>
              <Link to="/guest/bookings"
                className="inline-flex text-[#FFFFFF] font-bold px-10 py-5 rounded-xl transition-all duration-500 text-xs uppercase tracking-[0.3em] shadow-2xl transform hover:-translate-y-1 bg-[#A0717F] hover:bg-[#b58290]">
                Curate a Past Stay
              </Link>
            </div>
          </div>
        )}
      </div>
    </Layout>
  )
}
