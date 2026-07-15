import { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import api from '../../lib/api'
import Navbar from '../../components/Navbar'
import Footer from '../../components/Footer'

const Stars = ({ rating }) => (
  <>
    {[1, 2, 3, 4, 5].map(i => (
      <span key={i} style={i <= rating ? {} : { opacity: 0.3 }}>★</span>
    ))}
  </>
)

const limit = (str, len) => (str && str.length > len ? str.slice(0, len).trimEnd() + '...' : str)
const plural = (word, n) => (n === 1 ? word : word + 's')

// Ports welcome.blade.php (layouts/guest: full-width, no container)
export default function Welcome() {
  const navigate = useNavigate()
  const [hotels, setHotels] = useState([])
  const [reviews, setReviews] = useState([])
  const [search, setSearch] = useState({ city: '', min_price: '', max_price: '' })

  useEffect(() => {
    api.get('/public/home')
      .then(res => {
        setHotels(res.data.hotels)
        setReviews(res.data.reviews)
      })
      .catch(() => {})
  }, [])

  const submitSearch = (e) => {
    e.preventDefault()
    const params = new URLSearchParams()
    for (const [key, value] of Object.entries(search)) {
      if (value && String(value).trim() !== '') params.append(key, value)
    }
    navigate('/guest/hotels' + (params.toString() ? '?' + params.toString() : ''))
  }

  return (
    <div className="bg-[#4E3B46] font-sans text-[#CFCBCA] antialiased">
      <Navbar />
      <main>
        {/* Hero */}
        <section className="relative overflow-hidden" style={{ backgroundColor: '#4E3B46' }}>
          <div className="absolute top-0 right-0 w-96 h-96 rounded-full blur-3xl pointer-events-none" style={{ background: 'rgba(160, 113, 127, 0.06)' }}></div>
          <div className="absolute bottom-0 left-0 w-80 h-80 rounded-full blur-3xl pointer-events-none" style={{ background: 'rgba(234, 211, 205, 0.04)' }}></div>
          <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full blur-3xl pointer-events-none" style={{ background: 'rgba(160, 113, 127, 0.03)' }}></div>

          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16 relative z-10">
            {/* Search card */}
            <div className="rounded-xl shadow-2xl p-4 lg:p-5 mt-8 lg:mt-12" style={{ backgroundColor: '#383537', boxShadow: '0 25px 50px -12px rgba(0, 0, 0, 0.4)' }}>
              <form onSubmit={submitSearch}>
                <div className="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                  <div className="space-y-2">
                    <label className="text-xs font-medium uppercase" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>Destination</label>
                    <div className="flex items-center gap-2 rounded-lg px-4 py-3" style={{ backgroundColor: '#4E3B46', border: '1px solid rgba(160, 113, 127, 0.2)' }}>
                      <svg className="w-4 h-4 shrink-0" style={{ color: '#A0717F' }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      <input type="text" placeholder="Explore All Ateliers" value={search.city}
                        onChange={e => setSearch({ ...search, city: e.target.value })}
                        className="bg-transparent text-sm w-full outline-none" style={{ color: '#EAD3CD' }} />
                    </div>
                  </div>

                  {[['Min Price', 'min_price', '$0'], ['Max Price', 'max_price', '$999']].map(([label, name, placeholder]) => (
                    <div className="space-y-2" key={name}>
                      <label className="text-xs font-medium uppercase" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>{label}</label>
                      <div className="flex items-center gap-2 rounded-lg px-4 py-3" style={{ backgroundColor: '#4E3B46', border: '1px solid rgba(160, 113, 127, 0.2)' }}>
                        <svg className="w-4 h-4 shrink-0" style={{ color: '#A0717F' }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <input type="number" placeholder={placeholder} min="0" value={search[name]}
                          onChange={e => setSearch({ ...search, [name]: e.target.value })}
                          className="bg-transparent text-sm w-full outline-none" style={{ color: '#EAD3CD' }} />
                      </div>
                    </div>
                  ))}

                  <button type="submit"
                    className="font-semibold uppercase rounded-lg px-6 py-3.5 text-sm text-white flex items-center justify-center gap-2 transition-all duration-300 bg-[#A0717F] hover:bg-[#b58290] hover:shadow-[0_8px_25px_rgba(160,113,127,0.3)]"
                    style={{ letterSpacing: '0.1em' }}>
                    Search Atelier
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                  </button>
                </div>
              </form>
            </div>

            {/* Hero copy */}
            <div className="text-center py-24 lg:py-36 max-w-4xl mx-auto">
              <p className="text-xs font-medium uppercase mb-6" style={{ color: '#A0717F', letterSpacing: '0.4em' }}>
                An Atelier of Nocturnal Luxury
              </p>
              <h1 className="text-4xl sm:text-5xl lg:text-7xl font-bold leading-tight mb-8" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>
                Experience Elevated<br />
                Hospitality At<br />
                <em style={{ color: '#A0717F' }}>NIVADA</em>
              </h1>
              <p className="text-base lg:text-lg leading-relaxed max-w-2xl mx-auto mb-10" style={{ color: '#CFCBCA' }}>
                Indulge in sophisticated spaces, world-class service, and unforgettable stays crafted for modern luxury
                travelers. Where the architecture of silence meets the poetry of comfort.
              </p>
              <Link to="/guest/hotels"
                className="inline-block text-white font-semibold uppercase rounded-full px-10 py-4 text-sm transition-all duration-300 bg-[#A0717F] hover:bg-[#b58290] hover:-translate-y-0.5 hover:shadow-[0_15px_35px_rgba(160,113,127,0.25)]"
                style={{ letterSpacing: '0.12em' }}>
                Reserve Your Escape
              </Link>
            </div>
          </div>
        </section>

        {/* Featured hotels */}
        {hotels.length > 0 && (
          <section style={{ backgroundColor: '#383537', borderTop: '1px solid rgba(234, 211, 205, 0.1)' }}>
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16 py-20 lg:py-28">
              <div className="flex flex-col md:flex-row md:items-end justify-between mb-14 gap-6">
                <div>
                  <p className="text-xs font-medium uppercase mb-3" style={{ color: '#A0717F', letterSpacing: '0.4em' }}>
                    Curated Destinations</p>
                  <h2 className="text-3xl lg:text-5xl font-bold leading-tight" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>
                    Portfolios of Rare<br />Splendor
                  </h2>
                </div>
                <Link to="/guest/hotels"
                  className="text-sm font-medium uppercase flex items-center gap-2 shrink-0 transition-colors duration-300 group text-[rgba(207,203,202,0.7)] hover:text-[#EAD3CD]"
                  style={{ letterSpacing: '0.12em' }}>
                  View Entire Collection
                  <svg className="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                  </svg>
                </Link>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {hotels.map(hotel => (
                  <div key={hotel.id}
                    className="rounded-2xl overflow-hidden shadow-lg transition-all duration-500 cursor-pointer group hover:-translate-y-1.5 hover:shadow-[0_25px_50px_rgba(160,113,127,0.15)]"
                    style={{ backgroundColor: '#4E3B46' }}>
                    <div className="relative overflow-hidden" style={{ height: '240px', backgroundColor: '#2E2530' }}>
                      <div className="absolute inset-0" style={{ background: 'linear-gradient(to top, #4E3B46, transparent 60%)', opacity: 0.7 }}></div>
                      <div className="absolute inset-0" style={{ background: 'linear-gradient(135deg, rgba(160,113,127,0.08), transparent, rgba(234,211,205,0.05))' }}></div>
                      <div className="absolute inset-0 flex items-center justify-center">
                        <svg className="w-16 h-16" style={{ color: 'rgba(160, 113, 127, 0.2)' }} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                      </div>
                      <div className="absolute top-4 left-4 rounded-full px-3 py-1.5" style={{ background: 'rgba(56, 53, 55, 0.9)', backdropFilter: 'blur(8px)' }}>
                        <span className="text-xs font-medium" style={{ color: '#EAD3CD' }}>
                          {hotel.city}{hotel.country ? ', ' + hotel.country : ''}
                        </span>
                      </div>
                      {hotel.occupation_rate > 0 && (
                        <div className="absolute top-4 right-4 rounded-full px-3 py-1.5" style={{ background: 'rgba(160, 113, 127, 0.9)', backdropFilter: 'blur(8px)' }}>
                          <span className="text-xs font-semibold text-white">{Math.round(hotel.occupation_rate)}% Booked</span>
                        </div>
                      )}
                    </div>

                    <div className="p-6 space-y-3">
                      <h3 className="text-xl font-semibold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>
                        {hotel.name}
                      </h3>
                      {hotel.description && (
                        <p className="text-sm leading-relaxed" style={{ color: 'rgba(207, 203, 202, 0.7)' }}>
                          {limit(hotel.description, 100)}
                        </p>
                      )}
                      <div className="flex items-center gap-3">
                        {hotel.review_count > 0 ? (
                          <>
                            <div className="flex items-center gap-1 text-sm" style={{ color: '#A0717F' }}>
                              <Stars rating={Math.round(hotel.avg_rating)} />
                            </div>
                            <span className="text-xs" style={{ color: 'rgba(207, 203, 202, 0.5)' }}>
                              {Number(hotel.avg_rating).toFixed(1)} ({hotel.review_count} {plural('review', hotel.review_count)})
                            </span>
                          </>
                        ) : (
                          <span className="text-xs" style={{ color: 'rgba(207, 203, 202, 0.5)' }}>No reviews yet</span>
                        )}
                      </div>

                      <div className="flex items-center justify-between pt-3" style={{ borderTop: '1px solid rgba(234, 211, 205, 0.1)' }}>
                        <div>
                          <span className="text-xs" style={{ color: 'rgba(207, 203, 202, 0.5)' }}>
                            {hotel.room_count} {plural('room', hotel.room_count)}
                          </span>
                          {hotel.min_price != null && (
                            <p className="font-bold text-xl" style={{ color: '#A0717F' }}>
                              ${Math.round(hotel.min_price).toLocaleString()}
                              {hotel.max_price != null && hotel.max_price !== hotel.min_price && (
                                <span className="text-sm font-normal" style={{ color: 'rgba(207, 203, 202, 0.5)' }}>
                                  {' '}– ${Math.round(hotel.max_price).toLocaleString()}
                                </span>
                              )}
                              <span className="text-sm font-normal" style={{ color: 'rgba(207, 203, 202, 0.5)' }}> / night</span>
                            </p>
                          )}
                        </div>
                        <Link to={`/guest/hotels/${hotel.id}`}
                          className="text-xs font-semibold uppercase rounded-lg px-4 py-2 transition-all duration-300 text-[#A0717F] border border-[#A0717F] hover:bg-[#A0717F] hover:text-white"
                          style={{ letterSpacing: '0.08em' }}>
                          View Hotel
                        </Link>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </section>
        )}

        {/* Testimonials */}
        <section style={{ backgroundColor: '#4E3B46', borderTop: '1px solid rgba(234, 211, 205, 0.1)' }}>
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-16 py-20 lg:py-28">
            <div className="mb-14">
              <p className="text-xs font-medium uppercase mb-3" style={{ color: '#A0717F', letterSpacing: '0.4em' }}>The Guest Experience</p>
              <h2 className="text-3xl lg:text-5xl font-bold" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>
                Voices of the<br />Nocturnal Atelier
              </h2>
            </div>

            {reviews.length > 0 ? (
              <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {reviews.map((review, index) =>
                  index === 0 ? (
                    <div key={index} className="lg:row-span-2 rounded-xl overflow-hidden transition-all duration-500 hover:shadow-[0_20px_40px_rgba(160,113,127,0.1)]" style={{ backgroundColor: '#383537' }}>
                      <div className="relative" style={{ height: '320px', backgroundColor: '#2E2530' }}>
                        <div className="absolute inset-0" style={{ background: 'linear-gradient(to top, #383537, rgba(56,53,55,0.2) 50%, transparent)' }}></div>
                        <div className="absolute bottom-6 left-6 right-6">
                          <div className="rounded-xl p-6" style={{ background: 'rgba(78, 59, 70, 0.9)', backdropFilter: 'blur(10px)' }}>
                            <span className="text-3xl leading-none" style={{ color: '#A0717F', fontFamily: "'Georgia', serif" }}>❝</span>
                            <p className="text-sm italic leading-relaxed mt-2" style={{ color: '#CFCBCA' }}>
                              {review.comment ? limit(review.comment, 120) : 'The finest experience I have ever had. Pure poetry.'}
                            </p>
                          </div>
                        </div>
                      </div>
                      <div className="p-6">
                        <div className="flex items-center gap-1 text-sm mb-2" style={{ color: '#A0717F' }}>
                          <Stars rating={review.rating} />
                        </div>
                        <p className="font-semibold" style={{ color: '#EAD3CD' }}>{review.user_name}</p>
                        <p className="text-xs uppercase mt-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>
                          {review.hotel_name}
                        </p>
                      </div>
                    </div>
                  ) : (
                    <div key={index} className="rounded-xl p-6 flex flex-col justify-between transition-all duration-500 hover:shadow-[0_20px_40px_rgba(160,113,127,0.1)]" style={{ backgroundColor: '#383537' }}>
                      <div>
                        <p className="text-sm italic leading-relaxed" style={{ color: '#CFCBCA' }}>
                          "{review.comment ? limit(review.comment, 150) : 'Every detail feels intentional and deeply personal.'}"
                        </p>
                      </div>
                      <div className="mt-6">
                        <div className="flex items-center gap-1 text-sm mb-2" style={{ color: '#A0717F' }}>
                          <Stars rating={review.rating} />
                        </div>
                        <p className="font-semibold" style={{ color: '#EAD3CD' }}>{review.user_name}</p>
                        <p className="text-xs uppercase mt-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>
                          {review.hotel_name}
                        </p>
                      </div>
                    </div>
                  )
                )}
                {Array.from({ length: Math.max(0, 3 - reviews.length) }).map((_, i) => (
                  <div key={`fill-${i}`} className="rounded-xl p-6 flex flex-col justify-between" style={{ backgroundColor: '#383537' }}>
                    <p className="text-sm italic leading-relaxed" style={{ color: 'rgba(207, 203, 202, 0.4)' }}>
                      "Be the first to share your experience at one of our curated destinations."
                    </p>
                    <div className="mt-6">
                      <div className="flex items-center gap-1 text-sm mb-2" style={{ color: 'rgba(160, 113, 127, 0.3)' }}>
                        <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                      </div>
                      <p className="font-semibold" style={{ color: 'rgba(234, 211, 205, 0.4)' }}>Your Name Here</p>
                    </div>
                  </div>
                ))}
              </div>
            ) : (
              <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div className="lg:row-span-2 rounded-xl overflow-hidden" style={{ backgroundColor: '#383537' }}>
                  <div className="relative" style={{ height: '320px', backgroundColor: '#2E2530' }}>
                    <div className="absolute inset-0" style={{ background: 'linear-gradient(to top, #383537, rgba(56,53,55,0.2) 50%, transparent)' }}></div>
                    <div className="absolute bottom-6 left-6 right-6">
                      <div className="rounded-xl p-6" style={{ background: 'rgba(78, 59, 70, 0.9)', backdropFilter: 'blur(10px)' }}>
                        <span className="text-3xl leading-none" style={{ color: '#A0717F', fontFamily: "'Georgia', serif" }}>❝</span>
                        <p className="text-sm italic leading-relaxed mt-2" style={{ color: '#CFCBCA' }}>
                          The finest sleep I have ever had outside of my own estate. Pure poetry.
                        </p>
                      </div>
                    </div>
                  </div>
                  <div className="p-6">
                    <div className="flex items-center gap-1 text-sm mb-2" style={{ color: '#A0717F' }}>
                      <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                    </div>
                    <p className="font-semibold" style={{ color: '#EAD3CD' }}>Julian V.</p>
                    <p className="text-xs uppercase mt-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>London</p>
                  </div>
                </div>

                {[
                  ['"Every detail, from the thread count to the weight of the silver, feels intentional and deeply personal."', 'Elena R.', 'Milan'],
                  ['"Redefined my standards for luxury. It is simply incomparable."', 'Arthur D.', 'New York'],
                ].map(([quote, name, city]) => (
                  <div key={name} className="rounded-xl p-6 flex flex-col justify-between" style={{ backgroundColor: '#383537' }}>
                    <p className="text-sm italic leading-relaxed" style={{ color: '#CFCBCA' }}>{quote}</p>
                    <div className="mt-6">
                      <div className="flex items-center gap-1 text-sm mb-2" style={{ color: '#A0717F' }}>
                        <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                      </div>
                      <p className="font-semibold" style={{ color: '#EAD3CD' }}>{name}</p>
                      <p className="text-xs uppercase mt-1" style={{ color: 'rgba(207, 203, 202, 0.5)', letterSpacing: '0.15em' }}>{city}</p>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>
        </section>

        {/* Philosophy */}
        <section style={{ backgroundColor: '#383537', borderTop: '1px solid rgba(234, 211, 205, 0.1)' }}>
          <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-16 py-24 lg:py-32">
            <div className="text-center max-w-3xl mx-auto">
              <h2 className="text-3xl lg:text-5xl font-bold leading-tight italic" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif" }}>
                Crafted for those<br />who seek <span style={{ color: '#A0717F' }}>The Unspoken.</span>
              </h2>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-12 mt-16">
              <div className="space-y-3">
                <h3 className="text-lg font-semibold uppercase" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif", letterSpacing: '0.12em' }}>
                  Tactile Sensation
                </h3>
                <p className="text-sm leading-relaxed" style={{ color: 'rgba(207, 203, 202, 0.7)' }}>
                  We prioritize materials that beg to be touched — Egyptian cotton, brushed brass, and cold Italian
                  marble beneath bare feet.
                </p>
              </div>
              <div className="space-y-3">
                <h3 className="text-lg font-semibold uppercase" style={{ color: '#EAD3CD', fontFamily: "'Georgia', serif", letterSpacing: '0.12em' }}>
                  Aural Comfort
                </h3>
                <p className="text-sm leading-relaxed" style={{ color: 'rgba(207, 203, 202, 0.7)' }}>
                  Every NIVADA property is acoustically engineered to ensure absolute silence, protecting your most
                  precious commodity — rest.
                </p>
              </div>
            </div>
          </div>
        </section>
      </main>
      <Footer />
    </div>
  )
}
