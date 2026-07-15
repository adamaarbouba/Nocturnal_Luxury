import { useEffect, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import api from '../../../lib/api'
import Layout from '../../../components/Layout'
import Icon from '../../../components/Icon'
import { money } from './../fmt'

const diffForHumans = (d) => {
  const days = Math.floor((Date.now() - new Date(d)) / 86400000)
  if (days < 1) return 'today'
  if (days < 30) return `${days} day${days > 1 ? 's' : ''} ago`
  const months = Math.floor(days / 30)
  if (months < 12) return `${months} month${months > 1 ? 's' : ''} ago`
  const years = Math.floor(months / 12)
  return `${years} year${years > 1 ? 's' : ''} ago`
}

export default function GuestHotelShow() {
  const { id } = useParams()
  const [data, setData] = useState(null)

  useEffect(() => {
    api.get(`/guest/hotels/${id}`).then(res => setData(res.data)).catch(() => setData({ error: true }))
  }, [id])

  if (!data) return <Layout><p className="text-[#CFCBCA]">Loading…</p></Layout>
  if (data.error) return <Layout><p className="text-[#CFCBCA]">Hotel not found.</p></Layout>

  const { hotel, availableRooms = [] } = data
  const reviews = hotel.reviews ?? []
  const roomTypes = new Set(availableRooms.map(r => r.room_type)).size
  const prices = availableRooms.map(r => Number(r.price_per_night))
  const minPrice = prices.length ? Math.min(...prices) : 0
  const maxPrice = prices.length ? Math.max(...prices) : 0

  return (
    <Layout>
      <div className="max-w-7xl mx-auto px-4 py-12">
        {/* Hotel Hero */}
        <div className="relative overflow-hidden rounded-2xl mb-12"
          style={{ background: 'linear-gradient(135deg, #A0717F 0%, #2A2729 100%)', height: '320px' }}>
          <div className="absolute inset-0 bg-black/30"></div>
          <div className="relative z-10 h-full flex flex-col items-start justify-end p-8">
            <h1 className="text-5xl font-black text-[#EAD3CD] mb-2">{hotel.name}</h1>
            <div className="flex items-center gap-4">
              <p className="text-[#CFCBCA]">{hotel.city}, {hotel.country}</p>
              {hotel.rating != null && (
                <div className="flex items-center gap-1 bg-[#1A1515]/50 px-3 py-1 rounded-lg">
                  <span className="text-[#EAD3CD]">★</span>
                  <span className="text-[#EAD3CD] font-semibold">{money(hotel.rating, 1)}</span>
                </div>
              )}
            </div>
          </div>
        </div>

        {/* Hotel Details */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
          <div className="lg:col-span-2">
            <div className="rounded-2xl border border-[#4E3B46] bg-[#383537] p-8 shadow-lg">
              <h2 className="text-2xl font-bold text-[#EAD3CD] mb-4">About This Hotel</h2>
              <p className="text-[#EAD3CD] leading-relaxed mb-6">{hotel.description}</p>

              <div className="grid grid-cols-2 gap-4 mb-6 pt-6 border-t border-[#4E3B46]">
                <div>
                  <h3 className="text-xs font-bold uppercase tracking-wider text-[#CFCBCA]">Address</h3>
                  <p className="text-[#EAD3CD] mt-1">{hotel.address}</p>
                </div>
                <div>
                  <h3 className="text-xs font-bold uppercase tracking-wider text-[#CFCBCA]">City</h3>
                  <p className="text-[#EAD3CD] mt-1">{hotel.city}, {hotel.country}</p>
                </div>
                <div>
                  <h3 className="text-xs font-bold uppercase tracking-wider text-[#CFCBCA]">Phone</h3>
                  <p className="text-[#EAD3CD] mt-1">{hotel.phone ?? 'N/A'}</p>
                </div>
                <div>
                  <h3 className="text-xs font-bold uppercase tracking-wider text-[#CFCBCA]">Email</h3>
                  <p className="text-[#EAD3CD] mt-1">{hotel.email ?? 'N/A'}</p>
                </div>
              </div>
            </div>

            {/* Guest Reviews */}
            {reviews.length > 0 && (
              <div className="rounded-2xl border border-[#4E3B46] bg-[#383537] p-8 mt-8 shadow-lg">
                <h2 className="text-2xl font-bold text-[#EAD3CD] mb-6">Guest Reviews</h2>
                <div className="space-y-4">
                  {reviews.slice(0, 8).map(review => (
                    <div key={review.id} className="border-b border-[#4E3B46] pb-4 last:border-b-0">
                      <div className="flex items-start justify-between mb-2">
                        <div>
                          <h3 className="font-semibold text-[#EAD3CD]">{review.user?.name}</h3>
                          <p className="text-xs text-[#CFCBCA]">{diffForHumans(review.created_at)}</p>
                        </div>
                        <div className="flex text-[#A0717F]">{'★'.repeat(review.rating)}</div>
                      </div>
                      <p className="text-[#EAD3CD] text-sm">{review.comment}</p>
                    </div>
                  ))}
                </div>
              </div>
            )}
          </div>

          {/* Sidebar Stats */}
          <div className="lg:col-span-1">
            <div className="rounded-2xl border border-[#4E3B46] bg-[#383537] p-8 sticky top-4 shadow-lg">
              <h3 className="text-xl font-bold text-[#EAD3CD] mb-4">Room Overview</h3>

              <div className="grid grid-cols-2 gap-3 mb-6">
                <div className="bg-[#2A2729] rounded-lg p-3 border border-[#4E3B46]">
                  <p className="text-xs text-[#CFCBCA] uppercase tracking-wider font-semibold">Available</p>
                  <p className="text-2xl font-bold text-[#EAD3CD] mt-1">{availableRooms.length}</p>
                </div>
                <div className="bg-[#2A2729] rounded-lg p-3 border border-[#4E3B46]">
                  <p className="text-xs text-[#CFCBCA] uppercase tracking-wider font-semibold">Room Types</p>
                  <p className="text-2xl font-bold text-[#EAD3CD] mt-1">{roomTypes}</p>
                </div>
              </div>

              {availableRooms.length > 0 && (
                <div className="bg-[#2A2729] rounded-lg p-4 border border-[#4E3B46]">
                  <p className="text-xs text-[#CFCBCA] uppercase tracking-wider font-semibold mb-2">Price Range</p>
                  {minPrice === maxPrice ? (
                    <p className="text-2xl font-bold text-[#A0717F]">${money(minPrice)}<span className="text-sm font-normal text-[#CFCBCA]">/night</span></p>
                  ) : (
                    <p className="text-lg font-bold text-[#A0717F]">${money(minPrice)} - ${money(maxPrice)}<span className="text-sm font-normal text-[#CFCBCA]">/night</span></p>
                  )}
                </div>
              )}
            </div>
          </div>
        </div>

        {/* Available Rooms Section */}
        <div className="rounded-2xl border border-[#4E3B46] bg-[#383537] p-8 shadow-lg">
          <h2 className="text-2xl font-bold text-[#EAD3CD] mb-8">Available Rooms</h2>

          {availableRooms.length > 0 ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {availableRooms.map(room => (
                <div key={room.id}
                  className="border border-[#4E3B46] bg-[#2A2729] rounded-xl overflow-hidden hover:border-[#A0717F] hover:shadow-lg transition group">
                  <div className="h-40 bg-gradient-to-br from-[#1A1515]/40 to-[#1A1515]/20 flex items-center justify-center overflow-hidden group-hover:from-[#1A1515]/60 group-hover:to-[#1A1515]/40 transition">
                    <Icon name="building" size="2xl" className="text-[#EAD3CD]/50" />
                  </div>

                  <div className="p-6">
                    <div className="flex items-start justify-between mb-3">
                      <div>
                        <h3 className="text-lg font-bold text-[#EAD3CD]">{room.room_type}</h3>
                        <p className="text-xs text-[#CFCBCA]">Room {room.room_number}</p>
                      </div>
                    </div>

                    <div className="text-sm text-[#EAD3CD] mb-4 space-y-1 pb-4 border-b border-[#4E3B46]">
                      <p>Capacity: {room.capacity} {room.capacity > 1 ? 'guests' : 'guest'}</p>
                      <p>Floor {room.floor ?? 'N/A'}</p>
                    </div>

                    <div className="mb-4">
                      <p className="text-xs text-[#CFCBCA] uppercase tracking-wider font-semibold">Price per Night</p>
                      <p className="text-2xl font-bold text-[#A0717F] mt-1">${money(room.price_per_night)}</p>
                    </div>

                    <Link to={`/guest/rooms/${room.id}/book`}
                      className="w-full block text-center bg-[#A0717F] hover:bg-[#8F6470] text-[#EAD3CD] py-2 rounded-lg font-semibold transition border border-[#A0717F]">
                      Book Now
                    </Link>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div className="text-center py-16">
              <p className="text-[#EAD3CD] text-lg">No rooms currently available at this hotel.</p>
            </div>
          )}
        </div>
      </div>
    </Layout>
  )
}
