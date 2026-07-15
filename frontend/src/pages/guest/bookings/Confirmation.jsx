import { useEffect, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import api from '../../../lib/api'
import Layout from '../../../components/Layout'
import { money, fmtDate, nightsBetween, ucfirst } from './../fmt'

export default function GuestBookingsConfirmation() {
  const { id } = useParams()
  const [booking, setBooking] = useState(null)

  useEffect(() => {
    api.get(`/guest/bookings/${id}/confirmation`).then(res => setBooking(res.data.booking)).catch(() => setBooking(false))
  }, [id])

  if (booking === null) return <Layout><p className="text-[#CFCBCA]">Loading…</p></Layout>
  if (booking === false) return <Layout><p className="text-[#CFCBCA]">Booking not found.</p></Layout>

  const firstItem = booking.booking_items?.[0]
  const room = firstItem?.room
  const nights = nightsBetween(booking.check_in_date, booking.check_out_date)
  const pricePerNight = firstItem?.price_per_night ?? 0

  return (
    <Layout>
      <div className="max-w-2xl mx-auto px-4 py-12">
        {/* Booking Details Card */}
        <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-8 mb-8">
          <h3 className="text-2xl font-bold text-[#EAD3CD] mb-6">Booking Details</h3>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
              <h4 className="text-sm font-semibold text-[#CFCBCA] mb-3">Hotel & Room</h4>
              <div className="space-y-2">
                <div>
                  <p className="text-xs text-[#CFCBCA]">Hotel</p>
                  <p className="text-lg font-semibold text-[#EAD3CD]">{booking.hotel?.name}</p>
                </div>
                {room && (
                  <>
                    <div>
                      <p className="text-xs text-[#CFCBCA]">Room Number</p>
                      <p className="text-[#EAD3CD]">{room.room_number} ({room.room_type})</p>
                    </div>
                    <div>
                      <p className="text-xs text-[#CFCBCA]">Capacity</p>
                      <p className="text-[#EAD3CD]">{room.capacity} Guests</p>
                    </div>
                  </>
                )}
              </div>
            </div>

            <div>
              <h4 className="text-sm font-semibold text-[#CFCBCA] mb-3">Stay Dates</h4>
              <div className="space-y-2">
                <div>
                  <p className="text-xs text-[#CFCBCA]">Check-in</p>
                  <p className="text-lg font-semibold text-[#EAD3CD]">{fmtDate(booking.check_in_date)}</p>
                </div>
                <div>
                  <p className="text-xs text-[#CFCBCA]">Check-out</p>
                  <p className="text-lg font-semibold text-[#EAD3CD]">{fmtDate(booking.check_out_date)}</p>
                </div>
                <div>
                  <p className="text-xs text-[#CFCBCA]">Duration</p>
                  <p className="text-[#EAD3CD]">{nights} nights</p>
                </div>
              </div>
            </div>
          </div>

          {booking.special_requests && (
            <div className="mt-6 pt-6 border-t border-[#4E3B46]">
              <h4 className="text-sm font-semibold text-[#CFCBCA] mb-2">Special Requests</h4>
              <p className="text-[#EAD3CD]">{booking.special_requests}</p>
            </div>
          )}
        </div>

        {/* Cost Breakdown */}
        <div className="bg-[#383537] border border-[#4E3B46] rounded-lg shadow-lg p-8 mb-8">
          <h3 className="text-lg font-bold text-[#EAD3CD] mb-6">Cost Breakdown</h3>

          <div className="space-y-3 pb-4 border-b border-[#4E3B46]">
            <div className="flex justify-between">
              <span className="text-[#CFCBCA]">{nights} nights × ${money(pricePerNight)}/night</span>
              <span className="font-semibold text-[#EAD3CD]">${money(booking.total_amount)}</span>
            </div>
          </div>

          <div className="flex justify-between items-center pt-4">
            <span className="text-lg font-semibold text-[#EAD3CD]">Total Amount</span>
            <span className="text-3xl font-bold text-[#A0717F]">${money(booking.total_amount)}</span>
          </div>
        </div>

        {/* Status Card */}
        <div className="bg-[#2A2729] border border-[#A0717F] rounded-lg p-6 mb-8">
          <h4 className="text-lg font-semibold text-[#EAD3CD] mb-2">Booking Status</h4>
          <div className="flex items-center gap-2 mb-3">
            <span className="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-[#1A1515] text-yellow-400 border border-yellow-500">
              {ucfirst(booking.status)}
            </span>
          </div>
          <p className="text-[#CFCBCA] text-sm">Your booking is currently <strong className="text-[#EAD3CD]">pending</strong>. You need to complete
            the payment to confirm your reservation.</p>
        </div>

        <div className="flex gap-4">
          <Link to={`/guest/bookings/${booking.id}/payment`}
            className="flex-1 bg-[#A0717F] hover:bg-[#8F6470] text-[#EAD3CD] px-6 py-3 rounded-lg font-semibold text-center transition">
            Make Payment
          </Link>
          <Link to="/guest/hotels"
            className="flex-1 bg-[#4E3B46] hover:bg-[#68525F] text-[#EAD3CD] border border-[#4E3B46] px-6 py-3 rounded-lg font-semibold text-center transition">
            Continue Shopping
          </Link>
        </div>
      </div>
    </Layout>
  )
}
